<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\AuditLog;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $users = User::query()
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%")
                ->orWhere('email', 'like', "%{$request->search}%"))
            ->when($request->role, fn($q) => $q->where('role', $request->role))
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 15);

        return $this->success([
            'users' => UserResource::collection($users->items()),
            'meta' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'total' => $users->total(),
                'per_page' => $users->perPage(),
            ],
        ]);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required',  Password::defaults()],
            'role' => ['sometimes', Rule::in(['user', 'admin'])],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'] ?? 'user',
        ]);

        AuditLog::record(
            action: 'create_user',
            tableName: 'users',
            recordId: $user->id,
            newData: ['name' => $user->name, 'email' => $user->email, 'role' => $user->role],
        );

        return $this->created(new UserResource($user), 'User berhasil dibuat');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $user = User::find($id);

        if (! $user) {
            return $this->notFound('User tidak ditemukan.');
        }

        return $this->success(new UserResource($user));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $user = User::find($id);

        if (! $user) {
            return $this->notFound('User tidak ditemukan.');
        }

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['sometimes', Password::defaults()],
            'role' => ['sometimes', Rule::in(['user', 'admin'])],
        ]);

        $old = $user->toArray();

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        AuditLog::record(
            action: 'update_user',
            tableName: 'users',
            recordId: $user->id,
            oldData: $old,
            newData: $user->fresh()->toArray(),
        );

        return $this->success(new UserResource($user->fresh()), 'User berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $user = User::find($id);

        if (! $user) {
            return $this->notFound('User tidak ditemukan.');
        }

        if ($user->id === request()->user()->id) {
            return $this->error('Anda tidak dapat menghapus sendiri.', 422);
        }

        AuditLog::record(
            action: 'delete_user',
            tableName: 'users',
            recordId: $user->id,
            oldData: $user->toArray(),
        );

        $user->tokens()->delete();
        $user->delete();

        return $this->success(null, 'User berhasil dihapus.');
    }
}
