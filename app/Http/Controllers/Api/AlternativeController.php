<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AlternativeResource;
use App\Models\Alternative;
use App\Models\AuditLog;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AlternativeController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $alternatives = Alternative::query()
            ->with('creator:id,name')
            ->when($request->source, fn($q) => $q->where('source', $request->source))
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->orderBy('name')
            ->paginate($request->per_page ?? 20);

        return $this->success([
            'alternatives' => AlternativeResource::collection($alternatives->items()),
            'meta' => [
                'current_page' => $alternatives->currentPage(),
                'last_page' => $alternatives->lastPage(),
                'total' => $alternatives->total(),
                'per_page' => $alternatives->perPage(),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
        ]);

        $user = $request->user();

        $alternative = Alternative::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'source' => $user->role == 'admin' ? Alternative::SOURCE_ADMIN : Alternative::SOURCE_USER,
            'created_by' => $user->id,
        ]);

        AuditLog::record(
            action: 'create_alternative',
            tableName: 'alternatives',
            recordId: $alternative->id,
            newData: $alternative->toArray(),
        );

        return $this->created(
            new AlternativeResource($alternative->load('creator:id,name')),
            'Alternatif berhasil ditambahkan.',
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $alternative = Alternative::with('creator:id,name')->find($id);

        if (! $alternative) {
            return $this->notFound('Alternatif tidak ditemukan.');
        }

        return $this->success(new AlternativeResource($alternative));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $alternative = Alternative::find($id);

        if (! $alternative) {
            return $this->notFound('Alternatif tidak ditemukan.');
        }

        $user = $request->user();

        if ($user->role !== 'admin' && $alternative->created_by !== $user->id) {
            return $this->forbidden('Anda tidak dapat mengubah alternatif ini.');
        }

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
        ]);

        $old = $alternative->toArray();
        $alternative->update($validated);

        AuditLog::record(
            action: 'update_alternative',
            tableName: 'alternatives',
            recordId: $alternative->id,
            oldData: $old,
            newData: $alternative->toArray(),
        );

        return $this->success(
            new AlternativeResource($alternative->fresh()->load('creator:id,name')),
            'Alternatif berhasil diperbarui.',
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $alternative = Alternative::find($id);

        if (! $alternative) {
            return $this->notFound('Alternatif tidak ditemukan.');
        }

        $user = $request->user();

        if ($user->role !== 'admin' && $alternative->created_by !== $user->id) {
            return $this->forbidden('Anda tidak dapat menghapus alternatif ini.');
        }

        AuditLog::record(
            action: 'delete_alternative',
            tableName: 'alternatives',
            recordId: $alternative->id,
            oldData: $alternative->toArray(),
        );

        $alternative->delete();

        return $this->success(null, 'Alternatif berhasil dihapus.');
    }
}
