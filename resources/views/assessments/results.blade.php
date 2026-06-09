@extends('layouts.app')
@section('title', 'Hasil EDAS — ' . $assessment->title)
@section('header', 'Hasil EDAS')

@section('content')
<div style="display:flex; flex-direction:column; gap:1.5rem;">

    {{-- Actions --}}
    <div style="display:flex; align-items:center; gap:0.5rem; justify-content:flex-end; flex-wrap:wrap;">
        <a href="{{ route('assessments.show', $assessment) }}" class="btn btn-secondary">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            Detail
        </a>

        <form method="POST" action="{{ route('assessments.recalculate', $assessment) }}"
              onsubmit="return confirm('Reset hasil dan edit nilai? Hasil kalkulasi akan dihapus.')">
            @csrf
            <button type="submit" class="btn"
                    style="background:var(--color-warning-bg); color:var(--color-warning-text); border:1px solid var(--color-warning-border);">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit & Hitung Ulang
            </button>
        </form>

        <a href="{{ route('assessments.report.excel', $assessment) }}" class="btn btn-secondary"
           style="color:var(--color-success-text);">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Excel
        </a>

        <a href="{{ route('assessments.report.pdf', $assessment) }}" target="_blank" class="btn btn-secondary"
           style="color:var(--color-danger-text);">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            PDF
        </a>
    </div>

    {{-- Top recommendation --}}
    @if($results->isNotEmpty())
    @php $top = $results->first(); @endphp
    <div style="border-radius:var(--radius-xl); padding:1.75rem;
                background:linear-gradient(135deg, var(--color-mist-600) 0%, var(--color-mist-700) 100%);
                color:var(--color-mist-50); position:relative; overflow:hidden;">
        <div style="position:absolute; inset:0; opacity:0.05;
                    background:radial-gradient(circle at 80% 20%, white 0%, transparent 60%);"></div>
        <p style="font-size:var(--font-size-xs); font-weight:600; letter-spacing:0.08em;
                  text-transform:uppercase; opacity:0.75; margin-bottom:0.375rem;">
            🏆 Rekomendasi Utama
        </p>
        <h2 style="font-size:var(--font-size-2xl); font-weight:700; letter-spacing:-0.02em; margin-bottom:0.25rem;">
            {{ $top->alternative?->name }}
        </h2>
        @if($top->alternative?->description)
            <p style="font-size:var(--font-size-sm); opacity:0.65; margin-bottom:1.25rem; margin-right:2rem; line-height:1.5;">
                {{ Str::limit($top->alternative->description, 255) }}
            </p>
        @endif
        <div style="display:flex; gap:2rem; flex-wrap:wrap; margin-top:1rem;">
            @foreach([
                'AS Score' => number_format($top->appraisal_score, 4),
                'NSP'      => number_format($top->nsp, 4),
                'NSN'      => number_format($top->nsn, 4),
            ] as $lbl => $val)
                <div>
                    <p style="font-size:var(--font-size-xs); opacity:0.6; margin-bottom:0.25rem;">{{ $lbl }}</p>
                    <p style="font-size:1.25rem; font-weight:700; font-family:var(--font-mono);
                               font-variant-numeric:tabular-nums;">{{ $val }}</p>
                </div>
            @endforeach
            <div>
                <p style="font-size:var(--font-size-xs); opacity:0.6; margin-bottom:0.375rem;">Kualitas</p>
                <span class="badge badge-success">{{ $top->quality_label }}</span>
            </div>
        </div>
    </div>
    @endif

    {{-- Ranking table — full horizontal scroll --}}
    <div class="table-wrap">
        <div style="padding:0.875rem 1.25rem; border-bottom:1px solid var(--color-border);
                    display:flex; align-items:center; justify-content:space-between;">
            <h2 style="font-size:var(--font-size-sm); font-weight:600; color:var(--color-text);">
                Peringkat Lengkap EDAS
            </h2>
            <span style="font-size:var(--font-size-xs); color:var(--color-text-subtle);">
                {{ $results->count() }} alternatif
            </span>
        </div>

        {{-- Scroll horizontal terisolasi --}}
        <div style="overflow-x:auto;
                    scrollbar-width:thin;
                    scrollbar-color:var(--color-mist-700) transparent;">
            <table class="table" style="min-width:max-content;">
                <thead>
                    <tr>
                        {{-- Rank + nama alternatif sticky kiri --}}
                        <th style="position:sticky; left:0; z-index:3;
                                   background:oklch(14.8% 0.004 228.8);
                                   text-align:center; width:3.5rem;
                                   box-shadow:1px 0 0 0 var(--glass-border);">
                            Rank
                        </th>
                        <th style="position:sticky; left:3.5rem; z-index:3;
                                   background:oklch(14.8% 0.004 228.8);
                                   min-width:14rem; max-width:20rem;
                                   box-shadow:1px 0 0 0 var(--glass-border);">
                            Alternatif
                        </th>
                        {{-- Kolom EDAS — bisa di-scroll --}}
                        <th class="table-num" style="min-width:5.5rem;">PDA</th>
                        <th class="table-num" style="min-width:5.5rem;">NDA</th>
                        <th class="table-num" style="min-width:5.5rem;">SP</th>
                        <th class="table-num" style="min-width:5.5rem;">SN</th>
                        <th class="table-num" style="min-width:5.5rem; color:var(--color-accent-400);">NSP</th>
                        <th class="table-num" style="min-width:5.5rem; color:var(--color-accent-400);">NSN</th>
                        <th class="table-num" style="min-width:6.5rem;">AS Score</th>
                        <th style="min-width:10rem; white-space:nowrap;">Kualitas</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($results as $r)
                        <tr class="{{ $r->rank === 1 ? 'table-rank-1' : '' }}">
                            {{-- Rank sticky --}}
                            <td style="position:sticky; left:0; z-index:2;
                                       background:{{ $r->rank === 1 ? 'oklch(52% 0.14 138 / 0.1)' : 'var(--color-bg)' }};
                                       text-align:center;
                                       box-shadow:1px 0 0 0 var(--glass-border);">
                                <span class="rank-medal {{ $r->rank <= 3 ? 'rank-'.$r->rank : 'rank-n' }}">
                                    {{ $r->rank }}
                                </span>
                            </td>
                            {{-- Nama sticky --}}
                            <td style="position:sticky; left:3.5rem; z-index:2;
                                       background:{{ $r->rank === 1 ? 'oklch(52% 0.14 138 / 0.1)' : 'var(--color-bg)' }};
                                       box-shadow:1px 0 0 0 var(--glass-border);">
                                <p style="font-weight:500; color:var(--color-text); white-space:nowrap;
                                          overflow:hidden; text-overflow:ellipsis; max-width:18rem;">
                                    {{ $r->alternative?->name }}
                                </p>
                                @if($r->alternative?->description)
                                    <p style="font-size:var(--font-size-xs); color:var(--color-text-subtle);
                                              margin-top:0.125rem; white-space:nowrap; overflow:hidden;
                                              text-overflow:ellipsis; max-width:18rem;">
                                        {{ Str::limit($r->alternative->description, 50) }}
                                    </p>
                                @endif
                            </td>
                            {{-- Data EDAS --}}
                            <td class="table-num">{{ number_format($r->pda, 4) }}</td>
                            <td class="table-num">{{ number_format($r->nda, 4) }}</td>
                            <td class="table-num">{{ number_format($r->sp, 4) }}</td>
                            <td class="table-num">{{ number_format($r->sn, 4) }}</td>
                            <td class="table-num" style="color:var(--color-accent-400); font-weight:600;">
                                {{ number_format($r->nsp, 4) }}
                            </td>
                            <td class="table-num" style="color:var(--color-accent-400); font-weight:600;">
                                {{ number_format($r->nsn, 4) }}
                            </td>
                            <td class="table-num"
                                style="font-size:var(--font-size-base); font-weight:700; color:var(--color-text);">
                                {{ number_format($r->appraisal_score, 4) }}
                            </td>
                            <td>
                                <span class="badge
                                    @if($r->appraisal_score >= 0.8) badge-success
                                    @elseif($r->appraisal_score >= 0.6) badge-primary
                                    @elseif($r->appraisal_score >= 0.4) badge-warning
                                    @else badge-danger @endif"
                                    style="white-space:nowrap;">
                                    {{ $r->quality_label }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Legend --}}
    <div class="card" style="background:var(--color-bg-subtle);">
        <div class="card-body">
            <p style="font-size:var(--font-size-xs); font-weight:600; color:var(--color-text-subtle);
                      text-transform:uppercase; letter-spacing:0.06em; margin-bottom:0.75rem;">
                Keterangan Kolom
            </p>
            <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(11rem, 1fr)); gap:0.5rem;">
                @foreach([
                    ['PDA',    'Positive Distance from Average'],
                    ['NDA',    'Negative Distance from Average'],
                    ['SP',     'Weighted Sum of PDA'],
                    ['SN',     'Weighted Sum of NDA'],
                    ['NSP',    'Normalized SP (0–1)'],
                    ['NSN',    'Normalized SN (0–1)'],
                    ['AS',     '(NSP + NSN) / 2'],
                    ['Rank 1', 'AS tertinggi = terbaik'],
                ] as [$abbr, $desc])
                    <div style="font-size:var(--font-size-xs);">
                        <span style="font-weight:600; color:var(--color-text);">{{ $abbr }}</span>
                        <span style="color:var(--color-text-subtle);">: {{ $desc }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

</div>
@endsection
