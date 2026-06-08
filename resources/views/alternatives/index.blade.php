@extends('layouts.app')
@section('title', 'Alternatif')
@section('header', 'Daftar Alternatif')

@section('content')
<div style="display:flex; flex-direction:column; gap:1.25rem;">

    <div style="display:flex; align-items:center; justify-content:space-between; gap:0.75rem; flex-wrap:wrap;">
        <form method="GET" style="display:flex; gap:0.5rem; flex-wrap:wrap;">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari alternatif..." class="form-input" style="width:14rem;">
            <select name="source" class="form-input" style="width:auto;">
                <option value="">Semua sumber</option>
                <option value="admin" @selected(request('source') === 'admin')>Template Admin</option>
                <option value="user"  @selected(request('source') === 'user')>Input User</option>
            </select>
            <button type="submit" class="btn btn-secondary">Filter</button>
            @if(request('search') || request('source'))
                <a href="{{ route('alternatives.index') }}" class="btn btn-ghost">Reset</a>
            @endif
        </form>
        <a href="{{ route('alternatives.create') }}" class="btn btn-primary">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Alternatif
        </a>
    </div>

    <div class="table-wrap">
        @if($alternatives->isEmpty())
            <div style="padding:4rem 1.5rem; text-align:center;">
                <svg width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"
                     style="color:var(--color-border); margin:0 auto 0.75rem; display:block;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h7"/>
                </svg>
                <p style="color:var(--color-text-subtle); font-size:var(--font-size-sm);">Tidak ada alternatif ditemukan.</p>
                <a href="{{ route('alternatives.create') }}" class="btn btn-ghost btn-sm" style="margin-top:0.75rem;">
                    Tambah alternatif →
                </a>
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
                            <td style="font-weight:500; color:var(--color-text); white-space:nowrap;">
                                {{ $alt->name }}
                            </td>
                            <td style="max-width:20rem;">
                                <span style="display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; color:var(--color-text-subtle); font-size:var(--font-size-sm);">
                                    {{ $alt->description ?? '—' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $alt->source === 'admin' ? 'badge-primary' : 'badge-neutral' }}">
                                    {{ $alt->source_label }}
                                </span>
                            </td>
                            <td style="font-size:var(--font-size-xs); color:var(--color-text-subtle);">
                                {{ $alt->creator?->name ?? '—' }}
                            </td>
                            <td>
                                <div style="display:flex; align-items:center; gap:0.5rem; justify-content:flex-end;">
                                    @if(auth()->user()->isAdmin() || $alt->created_by === auth()->id())
                                        <a href="{{ route('alternatives.edit', $alt) }}" class="btn btn-ghost btn-xs">Edit</a>
                                        <form method="POST" action="{{ route('alternatives.destroy', $alt) }}"
                                              onsubmit="return confirm('Hapus alternatif ini?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-ghost btn-xs"
                                                    style="color:var(--color-danger-text);">Hapus</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div style="padding:1rem 1.5rem; border-top:1px solid var(--color-border);">
                {{ $alternatives->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
