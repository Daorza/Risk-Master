@extends('layouts.app')
@section('title', 'Assessment')
@section('header', 'Assessment')

@section('content')
<div style="display:flex; flex-direction:column; gap:1.25rem;">

    <div style="display:flex; align-items:center; justify-content:space-between; gap:0.75rem; flex-wrap:wrap;">
        <form method="GET" style="display:flex; gap:0.5rem; flex-wrap:wrap;">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari assessment..." class="form-input" style="width:15rem;">
            <select name="status" class="form-input" style="width:auto;">
                <option value="">Semua status</option>
                <option value="draft"     @selected(request('status') === 'draft')>Draft</option>
                <option value="completed" @selected(request('status') === 'completed')>Selesai</option>
            </select>
            <button type="submit" class="btn btn-secondary">Filter</button>
            @if(request('search') || request('status'))
                <a href="{{ route('assessments.index') }}" class="btn btn-ghost">Reset</a>
            @endif
        </form>
        <a href="{{ route('assessments.create') }}" class="btn btn-primary">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Buat Assessment
        </a>
    </div>

    <div class="table-wrap">
        @if($assessments->isEmpty())
            <div style="padding:4rem 1.5rem; text-align:center;">
                <svg width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"
                     style="color:var(--color-border); margin:0 auto 0.75rem; display:block;">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <p style="color:var(--color-text-subtle); font-size:var(--font-size-sm);">Tidak ada assessment ditemukan.</p>
                <a href="{{ route('assessments.create') }}" class="btn btn-ghost btn-sm" style="margin-top:0.75rem;">
                    Buat pertama →
                </a>
            </div>
        @else
            <table class="table">
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Pemilik</th>
                        <th style="text-align:center;">Alt.</th>
                        <th>Status</th>
                        <th>Dibuat</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($assessments as $a)
                        <tr>
                            <td>
                                <a href="{{ route('assessments.show', $a) }}"
                                   style="font-weight:500; color:var(--color-text); text-decoration:none; transition:color var(--duration-base);"
                                   onmouseover="this.style.color='var(--color-accent-400)'"
                                   onmouseout="this.style.color='var(--color-text)'">
                                    {{ $a->title }}
                                </a>
                                @if($a->description)
                                    <p style="font-size:var(--font-size-xs); color:var(--color-text-subtle); margin-top:0.125rem; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; max-width:20rem;">
                                        {{ $a->description }}
                                    </p>
                                @endif
                            </td>
                            <td style="color:var(--color-text-muted);">{{ $a->owner?->name ?? '—' }}</td>
                            <td style="text-align:center; font-variant-numeric:tabular-nums;">{{ $a->alternatives_count }}</td>
                            <td>
                                <span class="badge badge-dot {{ $a->status === 'completed' ? 'badge-success' : 'badge-warning' }}">
                                    {{ $a->status_label }}
                                </span>
                            </td>
                            <td style="font-size:var(--font-size-xs); color:var(--color-text-subtle);">
                                {{ $a->created_at->format('d M Y') }}
                            </td>
                            <td>
                                <div style="display:flex; align-items:center; gap:0.375rem; justify-content:flex-end;">
                                    <a href="{{ route('assessments.show', $a) }}" class="btn btn-ghost btn-xs">Detail</a>
                                    @if($a->status === 'draft')
                                        <a href="{{ route('assessments.values.edit', $a) }}" class="btn btn-outline-primary btn-xs">Input Nilai</a>
                                    @else
                                        <a href="{{ route('assessments.results', $a) }}" class="btn btn-xs btn-success">Hasil</a>
                                    @endif
                                    <form method="POST" action="{{ route('assessments.destroy', $a) }}"
                                          onsubmit="return confirm('Hapus assessment ini?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-ghost btn-xs" style="color:var(--color-danger-text);">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div style="padding:1rem 1.5rem; border-top:1px solid var(--color-border);">
                {{ $assessments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
