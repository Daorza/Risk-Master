@extends('layouts.app')
@section('title', $assessment->title)
@section('header', $assessment->title)

@section('content')
<div class="space-y-6">

    {{-- Header info --}}
    <div class="card" style="padding:1.5rem;">
        <div class="flex items-start justify-between gap-4 flex-wrap">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span class="badge {{ $assessment->status === 'completed' ? 'badge-success' : 'badge-warning' }}">
                        {{ $assessment->status_label }}
                    </span>
                    <span style="font-size:var(--font-size-xs); color:var(--color-text-subtle);">{{ $assessment->created_at->format('d M Y, H:i') }}</span>
                </div>
                @if($assessment->description)
                    <p style="font-size:var(--font-size-sm); color:var(--color-text-muted); margin-top:0.25rem;">{{ $assessment->description }}</p>
                @endif
                <p style="font-size:var(--font-size-xs); color:var(--color-text-subtle); margin-top:0.5rem;">Pemilik: {{ $assessment->owner?->name }}</p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                @if($assessment->isDraft())
                    <a href="{{ route('assessments.values.edit', $assessment) }}" class="btn btn-primary">
                        Input Nilai Matrix
                    </a>
                    @if($assessment->isMatrixComplete())
                        <form method="POST" action="{{ route('assessments.calculate', $assessment) }}">
                            @csrf
                            <button type="submit" class="btn" style="background:var(--color-accent-500); color:white; border-color:var(--color-accent-600);">
                                Hitung EDAS
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('assessments.edit', $assessment) }}" class="btn btn-secondary">
                        Edit
                    </a>
                @else
                    <a href="{{ route('assessments.results', $assessment) }}" class="btn btn-primary">
                        Lihat Hasil EDAS
                    </a>
                    <a href="{{ route('assessments.report.excel', $assessment) }}" target="_blank" class="btn btn-secondary" style="color:var(--color-success-text);">
                        Download Excel
                    </a>
                    <a href="{{ route('assessments.report.pdf', $assessment) }}" target="_blank" class="btn btn-secondary" style="color:var(--color-danger-text);">
                        Download PDF
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
    @endphp

    <div class="card" style="padding:1.5rem;">
        <div class="flex items-center justify-between mb-3">
            <h2 style="font-weight:600; color:var(--color-text);">Progres Matrix Keputusan</h2>
            <span style="font-size:var(--font-size-sm); font-weight:500; color:{{ $filled === $expected ? 'var(--color-success-text)' : 'var(--color-text-subtle)' }};">
                {{ $filled }}/{{ $expected }} sel terisi ({{ $pct }}%)
            </span>
        </div>
        <div style="width:100%; background:var(--glass-bg); border:1px solid var(--glass-border); border-radius:9999px; height:0.5rem; overflow:hidden;">
            <div style="height:100%; transition:width 0.3s ease; background:{{ $filled === $expected ? 'var(--color-success-text)' : 'var(--color-primary)' }}; width: {{ $pct }}%"></div>
        </div>
        @if($filled < $expected && $assessment->isDraft())
            <p style="font-size:var(--font-size-xs); color:var(--color-warning-text); margin-top:0.5rem;">
                Masih {{ $expected - $filled }} sel yang belum diisi sebelum dapat menghitung EDAS.
            </p>
        @endif
    </div>

    {{-- Matrix tabel --}}
    @if($altCount > 0)
    <div class="table-wrap table-sticky-col">
    <div style="padding:1rem 1.5rem; border-bottom:1px solid var(--color-border);">
        <h2 style="font-weight:600; color:var(--color-text); font-size:var(--font-size-sm);">Matrix Keputusan</h2>
    </div>
    <div class="table-scroll">
        <table class="table">
            <thead>
                <tr>
                    <th style="white-space:nowrap; min-width:12rem;">Alternatif</th>
                    @foreach($criteria as $c)
                        <x-criteria-header :criteria="$c" />
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($assessment->alternatives as $alt)
                    <tr>
                        <td style="font-weight:500; color:var(--color-text); white-space:nowrap;">{{ $alt->name }}</td>
                        @foreach($criteria as $c)
                            <td style="text-align:center;">
                                @if(isset($valueMap[$alt->id][$c->id]))
                                    <span style="font-family:var(--font-mono); color:var(--color-text-muted);">
                                        {{ number_format($valueMap[$alt->id][$c->id], 2) }}
                                    </span>
                                @else
                                    <span style="color:var(--color-danger-text);">—</span>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
        <div class="card" style="padding:2.5rem 1.5rem; text-align:center;">
            <p style="color:var(--color-text-subtle); font-size:var(--font-size-sm);">Belum ada alternatif yang dipilih.</p>
            <a href="{{ route('assessments.edit', $assessment) }}" class="btn btn-ghost btn-sm" style="margin-top:0.5rem;">Edit assessment →</a>
        </div>
    @endif

</div>
@endsection
