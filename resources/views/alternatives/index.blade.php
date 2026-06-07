@extends('layouts.app')
@section('title', 'Alternatif')
@section('header', 'Daftar Alternatif')

@section('content')
<div class="space-y-5">
    <div class="flex flex-wrap gap-3 items-center justify-between">
        <form method="GET" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari alternatif..."
                   class="form-input w-56">
            <select name="source" class="form-input">
                <option value="">Semua sumber</option>
                <option value="admin" @selected(request('source') === 'admin')>Template Admin</option>
                <option value="user" @selected(request('source') === 'user')>Input User</option>
            </select>
            <button type="submit" class="btn btn-secondary">Filter</button>
        </form>
        <a href="{{ route('alternatives.create') }}"
           class="btn btn-primary">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Alternatif
        </a>
    </div>

    <div class="table-wrap">
        @if($alternatives->isEmpty())
            <div class="py-14 text-center">
                <p class="text-[var(--color-text-subtle)] text-sm">Tidak ada alternatif ditemukan.</p>
            </div>
        @else
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Deskripsi</th>
                        <th>Sumber</th>
                        <th>Dibuat oleh</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($alternatives as $alt)
                        <tr>
                            <td class="font-medium text-[var(--color-text)]">{{ $alt->name }}</td>
                            <td class="max-w-xs">
                                <span class="line-clamp-2 text-[var(--color-text-subtle)]">{{ $alt->description ?? '—' }}</span>
                            </td>
                            <td>
                                <span class="badge {{ $alt->source === 'admin' ? 'badge-primary' : 'badge-neutral' }}">
                                    {{ $alt->source_label }}
                                </span>
                            </td>
                            <td class="text-xs text-[var(--color-text-subtle)]">{{ $alt->creator?->name ?? '—' }}</td>
                            <td>
                                <div class="flex items-center gap-3 justify-end">
                                    @if(auth()->user()->isAdmin() || $alt->created_by === auth()->id())
                                        <a href="{{ route('alternatives.edit', $alt) }}"
                                           class="btn btn-ghost btn-xs">Edit</a>
                                        <form method="POST" action="{{ route('alternatives.destroy', $alt) }}"
                                              onsubmit="return confirm('Hapus alternatif ini?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-ghost btn-xs text-[var(--color-danger-text)]">Hapus</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-6 py-4 border-t border-[var(--color-border)]">
                {{ $alternatives->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
