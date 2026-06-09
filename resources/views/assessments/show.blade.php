@extends('layouts.app')
@section('title', $assessment->title)
@section('header', $assessment->title)

@section('content')
<div style="display:flex; flex-direction:column; gap:1.5rem;">

    {{-- Header info --}}
    <div class="card" style="padding:1.5rem;">
        <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:1rem; flex-wrap:wrap;">
            <div style="flex:1; min-width:0;">
                <div style="display:flex; align-items:center; gap:0.75rem; margin-bottom:0.5rem; flex-wrap:wrap;">
                    <span class="badge {{ $assessment->status === 'completed' ? 'badge-success' : 'badge-warning' }} badge-dot">
                        {{ $assessment->status_label }}
                    </span>
                    <span style="font-size:var(--font-size-xs); color:var(--color-text-subtle);">
                        {{ $assessment->created_at->format('d M Y, H:i') }}
                    </span>
                    <span style="font-size:var(--font-size-xs); color:var(--color-text-subtle);">
                        Pemilik: {{ $assessment->owner?->name }}
                    </span>
                </div>
                @if($assessment->description)
                    <p style="font-size:var(--font-size-sm); color:var(--color-text-muted); line-height:1.6;">
                        {{ $assessment->description }}
                    </p>
                @endif
            </div>

            <div style="display:flex; flex-wrap:wrap; align-items:center; gap:0.5rem; flex-shrink:0;">
                @if($assessment->isDraft())
                    <a href="{{ route('assessments.values.edit', $assessment) }}" class="btn btn-primary">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Input Nilai
                    </a>
                    @if($assessment->isMatrixComplete())
                        <form method="POST" action="{{ route('assessments.calculate', $assessment) }}">
                            @csrf
                            <button type="submit" class="btn btn-primary"
                                    style="background:var(--color-accent-500); border-color:var(--color-accent-600);">
                                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                Hitung EDAS
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('assessments.edit', $assessment) }}" class="btn btn-secondary">Edit</a>
                @else
                    <a href="{{ route('assessments.results', $assessment) }}" class="btn btn-primary">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        Lihat Hasil EDAS
                    </a>
                    <a href="{{ route('assessments.report.excel', $assessment) }}"
                       class="btn btn-secondary" style="color:var(--color-success-text);">
                        Excel
                    </a>
                    <a href="{{ route('assessments.report.pdf', $assessment) }}" target="_blank"
                       class="btn btn-secondary" style="color:var(--color-danger-text);">
                        PDF
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- Matrix progress --}}
    @php
        $altCount  = $assessment->alternatives->count();
        $critCount = $criteria->count();
        $expected  = $altCount * $critCount;
        $filled    = $assessment->alternativeValues->count();
        $pct       = $expected > 0 ? round($filled / $expected * 100) : 0;
        $complete  = $filled === $expected && $expected > 0;
    @endphp

    <div class="card" style="padding:1.25rem 1.5rem;">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:0.75rem;">
            <h2 style="font-size:var(--font-size-sm); font-weight:600; color:var(--color-text);">
                Progres Matrix Keputusan
            </h2>
            <span style="font-size:var(--font-size-xs); font-weight:500;
                         color:{{ $complete ? 'var(--color-success-text)' : 'var(--color-text-subtle)' }};">
                {{ $filled }}/{{ $expected }} sel ({{ $pct }}%)
            </span>
        </div>
        <div class="progress">
            <div class="progress-bar {{ $complete ? 'progress-bar-success' : 'progress-bar-primary' }}"
                 style="width:{{ $pct }}%"></div>
        </div>
        @if($filled < $expected && $assessment->isDraft())
            <p style="font-size:var(--font-size-xs); color:var(--color-warning-text); margin-top:0.5rem;">
                ⚠ {{ $expected - $filled }} sel belum diisi — kalkulasi EDAS belum bisa dijalankan.
            </p>
        @elseif($complete && $assessment->isDraft())
            <p style="font-size:var(--font-size-xs); color:var(--color-success-text); margin-top:0.5rem;">
                ✓ Semua nilai sudah diisi — siap untuk kalkulasi EDAS.
            </p>
        @endif
    </div>

    {{-- Matrix tabel --}}
    @if($altCount > 0)
        <div class="table-wrap">
            <div style="padding:0.875rem 1.25rem; border-bottom:1px solid var(--color-border);
                        display:flex; align-items:center; justify-content:space-between;">
                <h2 style="font-size:var(--font-size-sm); font-weight:600; color:var(--color-text);">
                    Matrix Keputusan
                </h2>
                <span style="font-size:var(--font-size-xs); color:var(--color-text-subtle);">
                    {{ $altCount }} alternatif × {{ $critCount }} kriteria
                </span>
            </div>

            {{-- Scroll horizontal terisolasi — tidak mempengaruhi scroll halaman --}}
            <div style="overflow-x:auto;
                        scrollbar-width:thin;
                        scrollbar-color:var(--color-mist-700) transparent;">
                <table class="table" style="min-width:max-content;">
                    <thead>
                        <tr>
                            {{-- Kolom pertama: nama alternatif — sticky kiri --}}
                            <th style="position:sticky; left:0; z-index:3;
                                       background:oklch(14.8% 0.004 228.8);
                                       min-width:14rem; max-width:18rem;
                                       white-space:nowrap;
                                       box-shadow:1px 0 0 0 var(--glass-border);">
                                Alternatif
                            </th>
                            @foreach($criteria as $c)
                                <th style="text-align:center; min-width:8rem; white-space:nowrap;">
                                    <div>{{ $c->name }}</div>
                                    <div style="font-weight:400; font-size:10px; margin-top:0.125rem;
                                                color:{{ $c->isBenefit() ? 'var(--color-accent-400)' : 'var(--color-warning-text)' }};">
                                        {{ strtoupper($c->type) }} · {{ $c->weight_percent }}
                                    </div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($assessment->alternatives as $alt)
                            <tr>
                                <td style="position:sticky; left:0; z-index:2;
                                           background:var(--color-bg);
                                           font-weight:500; color:var(--color-text);
                                           white-space:nowrap; max-width:18rem;
                                           overflow:hidden; text-overflow:ellipsis;
                                           box-shadow:1px 0 0 0 var(--glass-border);">
                                    {{ $alt->name }}
                                </td>
                                @foreach($criteria as $c)
                                    <td style="text-align:center; white-space:nowrap;">
                                        @if(isset($valueMap[$alt->id][$c->id]))
                                            <span style="font-family:var(--font-mono);
                                                         font-size:var(--font-size-sm);
                                                         color:var(--color-text-muted);">
                                                {{ number_format($valueMap[$alt->id][$c->id], 2) }}
                                            </span>
                                        @else
                                            <span style="color:var(--color-danger-text);
                                                         font-size:var(--font-size-xs);">—</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="card" style="padding:3rem 1.5rem; text-align:center;">
            <svg width="36" height="36" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"
                 style="color:var(--color-border); margin:0 auto 0.75rem; display:block;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 6h18M3 14h18M3 18h18"/>
            </svg>
            <p style="color:var(--color-text-subtle); font-size:var(--font-size-sm);">
                Belum ada alternatif yang dipilih.
            </p>
            <a href="{{ route('assessments.edit', $assessment) }}"
               class="btn btn-ghost btn-sm" style="margin-top:0.75rem; display:inline-flex;">
                Edit assessment →
            </a>
        </div>
    @endif

</div>
@endsection
