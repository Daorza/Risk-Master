@extends('layouts.app')
@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('content')
<div style="display:flex; flex-direction:column; gap:1.5rem;">

    {{-- Stat cards --}}
    <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:1rem;">
        @php
            $statItems = [
                ['label' => 'Total Assessment', 'value' => $stats['total_assessments'],    'color' => 'var(--color-text)'],
                ['label' => 'Selesai',           'value' => $stats['completed_assessments'],'color' => 'var(--color-success-text)'],
                ['label' => 'Draft',             'value' => $stats['draft_assessments'],    'color' => 'var(--color-warning-text)'],
                ['label' => 'Alternatif',        'value' => $stats['total_alternatives'],   'color' => 'var(--color-primary-text)'],
            ];
        @endphp

        @foreach($statItems as $stat)
            <div class="card" style="padding:1.25rem;">
                <p style="font-size:var(--font-size-xs); font-weight:500; color:var(--color-text-subtle); text-transform:uppercase; letter-spacing:0.06em;">
                    {{ $stat['label'] }}
                </p>
                <p style="font-size:2rem; font-weight:700; color:{{ $stat['color'] }}; line-height:1; margin-top:0.5rem; font-variant-numeric:tabular-nums;">
                    {{ $stat['value'] }}
                </p>
            </div>
        @endforeach
    </div>

    <div style="display:grid; grid-template-columns:2fr 1fr; gap:1.5rem;">

        {{-- Recent assessments --}}
        <div class="card">
            <div class="card-header">
                <span>Assessment Terbaru</span>
                <a href="{{ route('assessments.create') }}" class="btn btn-primary btn-sm">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    Buat Baru
                </a>
            </div>

            @if($recentAssessments->isEmpty())
                <div style="padding:3rem 1.5rem; text-align:center;">
                    <svg width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"
                         style="color:var(--color-border); margin:0 auto 0.75rem;">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <p style="font-size:var(--font-size-sm); color:var(--color-text-subtle);">Belum ada assessment.</p>
                    <a href="{{ route('assessments.create') }}"
                       style="font-size:var(--font-size-sm); color:var(--color-primary-text); margin-top:0.5rem; display:inline-block;">
                        Buat assessment pertama →
                    </a>
                </div>
            @else
                @foreach($recentAssessments as $a)
                    <a href="{{ route('assessments.show', $a) }}"
                       style="display:flex; align-items:center; gap:1rem; padding:0.875rem 1.25rem;
                              border-bottom:1px solid var(--color-border-muted); text-decoration:none;
                              transition:background-color var(--duration-fast);"
                       onmouseover="this.style.backgroundColor='var(--color-bg-subtle)'"
                       onmouseout="this.style.backgroundColor=''">
                        <div style="flex:1; min-width:0;">
                            <p style="font-size:var(--font-size-sm); font-weight:500; color:var(--color-text); overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                {{ $a->title }}
                            </p>
                            <p style="font-size:var(--font-size-xs); color:var(--color-text-subtle); margin-top:0.125rem;">
                                {{ $a->alternatives_count }} alternatif
                                @if($a->owner) · {{ $a->owner->name }} @endif
                            </p>
                        </div>
                        <span class="badge {{ $a->status === 'completed' ? 'badge-success' : 'badge-warning' }}">
                            {{ $a->status_label }}
                        </span>
                    </a>
                @endforeach
                <div class="card-footer">
                    <a href="{{ route('assessments.index') }}"
                       style="font-size:var(--font-size-sm); color:var(--color-primary-text);">
                        Lihat semua →
                    </a>
                </div>
            @endif
        </div>

        {{-- Kriteria EDAS --}}
        <div class="card">
            <div class="card-header">
                <span>Kriteria EDAS</span>
                <span style="font-size:var(--font-size-xs); font-weight:500;
                             color:{{ abs($totalWeight - 1.0) <= 0.01 ? 'var(--color-success-text)' : 'var(--color-danger-text)' }};">
                    {{ number_format($totalWeight, 4) }}
                </span>
            </div>
            <div class="card-body" style="display:flex; flex-direction:column; gap:0.875rem;">
                @foreach($criteria as $c)
                    <div>
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.375rem;">
                            <span style="font-size:var(--font-size-sm); font-weight:500; color:var(--color-text); overflow:hidden; text-overflow:ellipsis; white-space:nowrap; max-width:65%;">
                                {{ $c->name }}
                            </span>
                            <div style="display:flex; align-items:center; gap:0.5rem; flex-shrink:0;">
                                <span class="badge {{ $c->isBenefit() ? 'badge-primary' : 'badge-warning' }}" style="font-size:10px; padding:0.1rem 0.4rem;">
                                    {{ strtoupper($c->type) }}
                                </span>
                                <span style="font-size:var(--font-size-xs); color:var(--color-text-subtle);">{{ $c->weight_percent }}</span>
                            </div>
                        </div>
                        <div class="progress progress-sm">
                            <div class="progress-bar {{ $c->isBenefit() ? 'progress-bar-primary' : 'progress-bar-warning' }}"
                                 style="width:{{ $c->weight * 100 }}%"></div>
                        </div>
                    </div>
                @endforeach

                @if(abs($totalWeight - 1.0) > 0.01)
                    <div class="alert alert-danger" style="font-size:var(--font-size-xs); padding:0.5rem 0.75rem;">
                        Total bobot ≠ 1.0. Kalkulasi EDAS akan gagal.
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
