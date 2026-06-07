@extends('layouts.app')
@section('title', 'Kelola Pengguna')
@section('header', 'Kelola Pengguna')

@section('content')
<div class="space-y-5">
    <div class="flex flex-wrap gap-3 items-center justify-between">
        <form method="GET" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email..."
                   class="form-input px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-60">
            <select name="role" class="form-input px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua role</option>
                <option value="user" @selected(request('role') === 'user')>User</option>
                <option value="admin" @selected(request('role') === 'admin')>Admin</option>
            </select>
            <button type="submit" class="btn btn-secondary">Filter</button>
        </form>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah User
        </a>
    </div>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Bergabung</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td style="font-weight:500; color:var(--color-text);">{{ $user->name }}</td>
                        <td style="color:var(--color-text-subtle);">{{ $user->email }}</td>
                        <td>
                            <span class="badge {{ $user->role === 'admin' ? 'badge-danger' : 'badge-primary' }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td style="color:var(--color-text-muted); font-size:var(--font-size-xs);">{{ $user->created_at->format('d M Y') }}</td>
                        <td>
                            <div class="flex items-center gap-3 justify-end">
                                <a href="{{ route('admin.users.edit', $user) }}"
                                   class="btn btn-ghost btn-xs">Edit</a>
                                @if($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                          onsubmit="return confirm('Hapus user ini?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-ghost btn-xs" style="color:var(--color-danger-text);">Hapus</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align:center; padding:3rem 1.5rem; color:var(--color-text-subtle);">Tidak ada pengguna ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div style="padding:1rem 1.5rem; border-top:1px solid var(--color-border);">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
