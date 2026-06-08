@extends('layouts.app')
@section('title', 'Kelola Pengguna')
@section('header', 'Kelola Pengguna')

@section('content')
<div style="display:flex; flex-direction:column; gap:1.25rem;">

    <div style="display:flex; align-items:center; justify-content:space-between; gap:0.75rem; flex-wrap:wrap;">
        <form method="GET" style="display:flex; gap:0.5rem; flex-wrap:wrap;">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari nama atau email..." class="form-input" style="width:16rem;">
            <select name="role" class="form-input" style="width:auto;">
                <option value="">Semua role</option>
                <option value="user"  @selected(request('role') === 'user')>User</option>
                <option value="admin" @selected(request('role') === 'admin')>Admin</option>
            </select>
            <button type="submit" class="btn btn-secondary">Filter</button>
            @if(request('search') || request('role'))
                <a href="{{ route('admin.users.index') }}" class="btn btn-ghost">Reset</a>
            @endif
        </form>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
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
                        <td style="font-size:var(--font-size-xs); color:var(--color-text-subtle);">
                            {{ $user->created_at->format('d M Y') }}
                        </td>
                        <td>
                            <div style="display:flex; align-items:center; gap:0.5rem; justify-content:flex-end;">
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-ghost btn-xs">Edit</a>
                                @if($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                          onsubmit="return confirm('Hapus user ini?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-ghost btn-xs"
                                                style="color:var(--color-danger-text);">Hapus</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align:center; padding:3rem 1.5rem; color:var(--color-text-subtle);">
                            Tidak ada pengguna ditemukan.
                        </td>
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
