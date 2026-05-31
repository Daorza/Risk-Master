<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Alternative;
use App\Models\AlternativeValue;
use App\Models\Assessment;
use App\Models\Criteria;
use App\Services\EdasService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class AssessmentController extends Controller
{
    public function __construct(private readonly EdasService $edasService) {}

    public function index(Request $request)
    {
        $user = $request->user();

        $assessments = Assessment::withSummary()
            ->when(! $user->isAdmin(), fn($q) => $q->forUser($user->id))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('assessments.index', compact('assessments'));
    }

    public function create()
    {
        $alternatives = Alternative::orderBy('name')->get();

        return view('assessments.create', compact('alternatives'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'             => ['required', 'string', 'max:200'],
            'description'       => ['nullable', 'string'],
            'alternative_ids'   => ['nullable', 'array'],
            'alternative_ids.*' => ['integer', 'exists:alternatives,id'],
        ]);

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

        return redirect()
            ->route('assessments.values.edit', $assessment)
            ->with('success', 'Assessment berhasil dibuat. Silakan input nilai matrix.');
    }

    public function show(Request $request, Assessment $assessment)
    {
        $this->authorizeAssessment($request, $assessment);

        $assessment->loadMissing([
            'owner:id,name',
            'alternatives',
            'rankedResults.alternative',
            'alternativeValues',
        ]);

        $criteria = Criteria::orderBy('id')->get();

        $valueMap = $assessment->alternativeValues
            ->groupBy('alternative_id')
            ->map(fn($vals) => $vals->keyBy('criteria_id')->map(fn($v) => $v->value));

        return view('assessments.show', compact('assessment', 'criteria', 'valueMap'));
    }

    public function edit(Request $request, Assessment $assessment)
    {
        $this->authorizeAssessment($request, $assessment);

        $alternatives = Alternative::orderBy('name')->get();
        $selectedIds  = $assessment->alternatives()->pluck('alternatives.id')->toArray();

        return view('assessments.edit', compact('assessment', 'alternatives', 'selectedIds'));
    }

    public function update(Request $request, Assessment $assessment)
    {
        $this->authorizeAssessment($request, $assessment);

        $validated = $request->validate([
            'title'             => ['required', 'string', 'max:200'],
            'description'       => ['nullable', 'string'],
            'alternative_ids'   => ['nullable', 'array'],
            'alternative_ids.*' => ['integer', 'exists:alternatives,id'],
        ]);

        DB::transaction(function () use ($assessment, $validated) {
            $assessment->update([
                'title'       => $validated['title'],
                'description' => $validated['description'] ?? null,
            ]);

            $newIds = $validated['alternative_ids'] ?? [];
            $assessment->alternatives()->sync($newIds);

            if (! empty($newIds)) {
                AlternativeValue::where('assessment_id', $assessment->id)
                    ->whereNotIn('alternative_id', $newIds)
                    ->delete();
            }
        });

        return redirect()
            ->route('assessments.show', $assessment)
            ->with('success', 'Assessment berhasil diperbarui.');
    }

    public function destroy(Request $request, Assessment $assessment)
    {
        $this->authorizeAssessment($request, $assessment);

        $assessment->delete();

        return redirect()
            ->route('assessments.index')
            ->with('success', 'Assessment berhasil dihapus.');
    }

    public function editValues(Request $request, Assessment $assessment)
    {
        $this->authorizeAssessment($request, $assessment);

        $criteria     = Criteria::orderBy('id')->get();
        $alternatives = $assessment->alternatives()->orderBy('name')->get();

        $valueMap = $assessment->alternativeValues()
            ->get()
            ->groupBy('alternative_id')
            ->map(fn($vals) => $vals->keyBy('criteria_id')->map(fn($v) => $v->value));

        return view('assessments.input-nilai', compact('assessment', 'criteria', 'alternatives', 'valueMap'));
    }

    public function storeValues(Request $request, Assessment $assessment)
    {
        $this->authorizeAssessment($request, $assessment);

        if ($assessment->isCompleted()) {
            return back()->with('error', 'Assessment sudah selesai dikalkulasi.');
        }

        $request->validate([
            'values'     => ['required', 'array'],
            'values.*.*' => ['required', 'numeric', 'min:0'],
        ]);

        $now        = now();
        $upsertData = [];

        foreach ($request->values as $altId => $criteriaValues) {
            foreach ($criteriaValues as $critId => $value) {
                $upsertData[] = [
                    'assessment_id'  => $assessment->id,
                    'alternative_id' => (int) $altId,
                    'criteria_id'    => (int) $critId,
                    'value'          => (float) $value,
                    'input_by'       => $request->user()->id,
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ];
            }
        }

        AlternativeValue::upsert(
            $upsertData,
            uniqueBy: ['assessment_id', 'alternative_id', 'criteria_id'],
            update: ['value', 'input_by', 'updated_at'],
        );

        return redirect()
            ->route('assessments.show', $assessment)
            ->with('success', 'Nilai berhasil disimpan.');
    }

    public function calculate(Request $request, Assessment $assessment)
    {
        $this->authorizeAssessment($request, $assessment);

        try {
            $this->edasService->calculate($assessment);

            return redirect()
                ->route('assessments.results', $assessment)
                ->with('success', 'Kalkulasi EDAS berhasil!');
        } catch (InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', 'Kalkulasi gagal. Silakan coba lagi.');
        }
    }

    public function results(Request $request, Assessment $assessment)
    {
        $this->authorizeAssessment($request, $assessment);

        if ($assessment->isDraft()) {
            return redirect()
                ->route('assessments.show', $assessment)
                ->with('error', 'Assessment belum dikalkulasi.');
        }

        $results  = $assessment->rankedResults()->with('alternative:id,name,description')->get();
        $criteria = Criteria::orderBy('id')->get();

        return view('assessments.results', compact('assessment', 'results', 'criteria'));
    }

    public function reportPdf(Request $request, Assessment $assessment)
    {
        $this->authorizeAssessment($request, $assessment);

        if ($assessment->isDraft()) {
            return back()->with('error', 'Assessment belum dikalkulasi.');
        }

        $results  = $assessment->rankedResults()->with('alternative:id,name,description')->get();
        $criteria = Criteria::orderBy('id')->get();

        $pdf = Pdf::loadView('reports.edas-pdf', [
            'assessment' => $assessment->load('owner:id,name,email'),
            'results'    => $results,
            'criteria'   => $criteria,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('hasil-edas-' . $assessment->id . '.pdf');
    }

    private function authorizeAssessment(Request $request, Assessment $assessment): void
    {
        if ($request->user()->isAdmin()) {
            return;
        }

        if ($assessment->user_id !== $request->user()->id) {
            abort(403, 'Anda tidak memiliki akses ke assessment ini.');
        }
    }
}
