<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CriteriaResource;
use App\Models\AuditLog;
use App\Models\Criteria;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CriteriaController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $criteria = Criteria::orderBy('id')->get();

        $totalWeight = $criteria->sum('weight');

        return $this->success([
            'criteria' => CriteriaResource::collection($criteria),
            'total_weight' => round($totalWeight, 4),
            'weight_valid' => abs($totalWeight - 1.0) <= 0.01,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:criteria'],
            'description' => ['nullable', 'string'],
            'type' => ['required', Rule::in(['benefit', 'cost'])],
            'weight' => ['required', 'numeric', 'min:0', 'max:1'],
        ]);

        $criteria = Criteria::create($validated);

        AuditLog::record(
            action: 'create_criteria',
            tableName: 'criteria',
            recordId: $criteria->id,
            newData: $criteria->toArray(),
        );

        return $this->created(new CriteriaResource($criteria), 'Kriteria berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $criteria = Criteria::find($id);

        if (! $criteria) {
            return $this->notFound('Kriteria tidak ditemukan.');
        }

        return $this->success(new CriteriaResource($criteria));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $criteria = Criteria::find($id);

        if (! $criteria) {
            return $this->notFound('Kriteria tidak ditemukan.');
        }

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:100', Rule::unique('criteria')->ignore($criteria->id)],
            'description' => ['nullable', 'string'],
            'type' => ['sometimes', Rule::in(['benefit', 'cost'])],
            'weight' => ['sometimes', 'numeric', 'min:0', 'max:1'],
        ]);

        $old = $criteria->toArray();
        $criteria->update($validated);

        AuditLog::record(
            action: 'update_criteria',
            tableName: 'criteria',
            recordId: $criteria->id,
            oldData: $old,
            newData: $criteria->fresh()->toArray(),
        );

        return $this->success(new CriteriaResource($criteria->fresh()), 'Kriteria berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $criteria = Criteria::find($id);

        if (! $criteria) {
            return $this->notFound('Kriteria tidak ditemukan.');
        }

        AuditLog::record(
            action: 'delete_criteria',
            tableName: 'criteria',
            recordId: $criteria->id,
            oldData: $criteria->toArray(),
        );

        $criteria->delete();

        return $this->success(null, 'Kriteria berhasil dihapus.');
    }
}
