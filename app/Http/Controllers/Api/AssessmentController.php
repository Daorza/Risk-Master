<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAssessmentRequest;
use App\Http\Requests\UpdateAssessmentRequest;
use App\Http\Requests\StoreAlternativeValueRequest;
use App\Http\Resources\AssessmentResource;
use App\Models\Alternative;
use App\Models\AlternativeValue;
use App\Models\Assessment;
use App\Models\AuditLog;
use App\Models\Criteria;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssessmentController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $assessments = Assessment::withSummary()
            ->when($user->role !== 'admin', fn($q) => $q->forUser($user->id))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->search, fn($q) => $q->where('title', 'like', "%{$request->search}%"))
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 10);

        return $this->success([
            'assessments' => AssessmentResource::collection($assessments->items()),
            'meta'        => [
                'current_page' => $assessments->currentPage(),
                'last_page'    => $assessments->lastPage(),
                'total'        => $assessments->total(),
                'per_page'     => $assessments->perPage(),
            ],
        ]);
    }

    public function store(StoreAssessmentRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $assessment = DB::transaction(function () use ($request, $validated) {
            $assessment = Assessment::create([
                'user_id'     => $request->user()->id,
                'title'       => $validated['title'],
                'description' => $validated['description'] ?? null,
            ]);

            if (! empty($validated['alternative_ids'])) {
                $assessment->alternatives()->attach($validated['alternative_ids']);
            }

            return $assessment;
        });

        return $this->created(
            new AssessmentResource($assessment->load(['owner:id,name,email', 'alternatives:id,name'])),
            'Assessment berhasil dibuat.',
        );
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $user       = $request->user();
        $assessment = Assessment::withFullDetail()->find($id);

        if (! $assessment) {
            return $this->notFound('Assessment tidak ditemukan.');
        }

        if ($user->role !== 'admin' && $assessment->user_id !== $user->id) {
            return $this->forbidden('Anda tidak memiliki akses.');
        }

        $criteria = Criteria::orderBy('id')->get();

        $valueMap = $assessment->alternativeValues
            ->groupBy('alternative_id')
            ->map(fn($vals) => $vals->keyBy('criteria_id')->map(fn($v) => $v->value));

        return $this->success([
            'assessment'     => new AssessmentResource($assessment),
            'criteria'       => $criteria->map(fn($c) => [
                'id'     => $c->id,
                'name'   => $c->name,
                'type'   => $c->type,
                'weight' => $c->weight,
            ]),
            'matrix'         => $valueMap,
            'is_complete'    => $assessment->isMatrixComplete(),
            'filled_count'   => $assessment->alternativeValues->count(),
            'expected_count' => $assessment->alternatives->count() * $criteria->count(),
        ]);
    }

    public function update(UpdateAssessmentRequest $request, string $id): JsonResponse
    {
        $user       = $request->user();
        $assessment = Assessment::find($id);

        if (! $assessment) {
            return $this->notFound('Assessment tidak ditemukan.');
        }

        if ($user->role !== 'admin' && $assessment->user_id !== $user->id) {
            return $this->forbidden('Anda tidak memiliki akses.');
        }

        $validated = $request->validated();

        DB::transaction(function () use ($assessment, $validated) {
            $fillable = array_filter([
                'title'       => $validated['title'] ?? null,
                'description' => $validated['description'] ?? null,
            ], fn($v) => $v !== null);

            if (! empty($fillable)) {
                $assessment->update($fillable);
            }

            if (array_key_exists('alternative_ids', $validated)) {
                $newIds = $validated['alternative_ids'] ?? [];
                $assessment->alternatives()->sync($newIds);

                if (! empty($newIds)) {
                    AlternativeValue::where('assessment_id', $assessment->id)
                        ->whereNotIn('alternative_id', $newIds)
                        ->delete();
                }
            }
        });

        return $this->success(
            new AssessmentResource($assessment->fresh()->load(['owner:id,name,email', 'alternatives:id,name'])),
            'Assessment berhasil diperbarui.',
        );
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        $user       = $request->user();
        $assessment = Assessment::find($id);

        if (! $assessment) {
            return $this->notFound('Assessment tidak ditemukan.');
        }

        if ($user->role !== 'admin' && $assessment->user_id !== $user->id) {
            return $this->forbidden('Anda tidak memiliki akses.');
        }

        AuditLog::record(
            action: 'delete_assessment',
            tableName: 'assessments',
            recordId: $assessment->id,
            oldData: ['title' => $assessment->title, 'status' => $assessment->status],
        );

        $assessment->delete();

        return $this->success(null, 'Assessment berhasil dihapus.');
    }

    // ── Matrix Values ─────────────────────────────────────────────────────────

    public function storeValues(StoreAlternativeValueRequest $request, Assessment $assessment): JsonResponse
    {
        if ($assessment->isCompleted()) {
            return $this->error('Assessment sudah selesai. Buat assessment baru untuk analisis ulang.', 422);
        }

        $validated = $request->validated();
        $now       = now();

        $upsertData = collect($validated['values'])->map(fn($v) => [
            'assessment_id'  => $assessment->id,
            'alternative_id' => $v['alternative_id'],
            'criteria_id'    => $v['criteria_id'],
            'value'          => $v['value'],
            'input_by'       => $request->user()->id,
            'created_at'     => $now,
            'updated_at'     => $now,
        ])->toArray();

        AlternativeValue::upsert(
            $upsertData,
            uniqueBy: ['assessment_id', 'alternative_id', 'criteria_id'],
            update: ['value', 'input_by', 'updated_at'],
        );

        return $this->success([
            'filled_count'   => $assessment->alternativeValues()->count(),
            'expected_count' => $assessment->alternatives()->count() * Criteria::count(),
            'is_complete'    => $assessment->fresh()->isMatrixComplete(),
        ], 'Nilai berhasil disimpan.');
    }

    public function showValues(Request $request, Assessment $assessment): JsonResponse
    {
        $user = $request->user();

        if ($user->role !== 'admin' && $assessment->user_id !== $user->id) {
            return $this->forbidden('Anda tidak memiliki akses.');
        }

        $values = $assessment->alternativeValues()
            ->with(['alternative:id,name', 'criteria:id,name,type,weight'])
            ->get()
            ->map(fn($v) => [
                'alternative_id' => $v->alternative_id,
                'alternative'    => $v->alternative?->name,
                'criteria_id'    => $v->criteria_id,
                'criteria'       => $v->criteria?->name,
                'value'          => $v->value,
            ]);

        return $this->success([
            'values'         => $values,
            'filled_count'   => $values->count(),
            'expected_count' => $assessment->alternatives()->count() * Criteria::count(),
            'is_complete'    => $assessment->isMatrixComplete(),
        ]);
    }

    public function attachAlternatives(Request $request, Assessment $assessment): JsonResponse
    {
        $user = $request->user();

        if ($user->role !== 'admin' && $assessment->user_id !== $user->id) {
            return $this->forbidden('Anda tidak memiliki akses.');
        }

        $validated = $request->validate([
            'alternative_ids'   => ['required', 'array', 'min:1'],
            'alternative_ids.*' => ['integer', 'exists:alternatives,id'],
        ]);

        $assessment->alternatives()->syncWithoutDetaching($validated['alternative_ids']);

        return $this->success([
            'alternatives_count' => $assessment->alternatives()->count(),
        ], 'Alternatif berhasil ditambahkan ke assessment.');
    }

    public function detachAlternative(Request $request, Assessment $assessment, Alternative $alternative): JsonResponse
    {
        $user = $request->user();

        if ($user->role !== 'admin' && $assessment->user_id !== $user->id) {
            return $this->forbidden('Anda tidak memiliki akses.');
        }

        $assessment->alternatives()->detach($alternative->id);

        AlternativeValue::where('assessment_id', $assessment->id)
            ->where('alternative_id', $alternative->id)
            ->delete();

        return $this->success(null, 'Alternatif berhasil dilepas dari assessment.');
    }
}
