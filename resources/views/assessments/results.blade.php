@extends('layouts.app')
@section('title', 'Hasil EDAS')
@section('header', 'Hasil EDAS — ' . $assessment->title)

@section('content')
<div class="space-y-6">

    {{-- Actions --}}
    <div class="flex items-center gap-3 justify-end">
        <a href="{{ route('assessments.show', $assessment) }}"
           class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition">
            ← Detail Assessment
        </a>

            {{-- Edit & Hitung Ulang --}}
        <form method="POST" action="{{ route('assessments.recalculate', $assessment) }}"
            onsubmit="return confirm('Reset hasil EDAS dan edit nilai? Hasil kalkulasi saat ini akan dihapus.')">
            @csrf
            <button type="submit"
                    class="bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit & Hitung Ulang
            </button>
        </form>

        {{-- Export Excel --}}
        <a href="{{ route('assessments.report.excel', $assessment) }}"
           class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Download Excel
        </a>

        {{-- Export PDF --}}
        <a href="{{ route('assessments.report.pdf', $assessment) }}" target="_blank"
           class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Download PDF
        </a>
    </div>

    {{-- Top recommendation --}}
    @if($results->isNotEmpty())
    @php $top = $results->first(); @endphp
    <div class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-2xl p-6 text-white">
        <p class="text-sm text-blue-200 font-medium mb-1">🏆 Rekomendasi Utama</p>
        <h2 class="text-2xl text-blue-100 font-bold">{{ $top->alternative?->name }}</h2>
        @if($top->alternative?->description)
            <p class="text-blue-200 text-sm mt-1">{{ $top->alternative->description }}</p>
        @endif
        <div class="mt-4 flex items-center gap-6 text-sm">
            <div>
                <p class="text-blue-300 text-xs">Appraisal Score</p>
                <p class="text-blue-300 font-bold text-xl">{{ number_format($top->appraisal_score, 4) }}</p>
            </div>
            <div>
                <p class="text-blue-300 text-xs">NSP</p>
                <p class="text-blue-300 font-semibold">{{ number_format($top->nsp, 4) }}</p>
            </div>
            <div>
                <p class="text-blue-300 text-xs">NSN</p>
                <p class="text-blue-300 font-semibold">{{ number_format($top->nsn, 4) }}</p>
            </div>
        </div>
    </div>
    @endif

    {{-- Full ranking table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-800">Peringkat Lengkap EDAS</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-center px-4 py-3 font-semibold text-gray-600">Rank</th>
                        <th class="text-left px-6 py-3 font-semibold text-gray-600">Alternatif</th>
                        <th class="text-center px-4 py-3 font-semibold text-gray-600">PDA</th>
                        <th class="text-center px-4 py-3 font-semibold text-gray-600">NDA</th>
                        <th class="text-center px-4 py-3 font-semibold text-gray-600">SP</th>
                        <th class="text-center px-4 py-3 font-semibold text-gray-600">SN</th>
                        <th class="text-center px-4 py-3 font-semibold text-gray-600">NSP</th>
                        <th class="text-center px-4 py-3 font-semibold text-gray-600">NSN</th>
                        <th class="text-center px-4 py-3 font-semibold text-gray-600">AS Score</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600">Kualitas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($results as $r)
                        <tr class="{{ $r->rank === 1 ? 'bg-blue-50' : 'hover:bg-gray-50' }} transition">
                            <td class="px-4 py-4 text-center">
                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full text-sm font-bold
                                    {{ $r->rank === 1 ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600' }}">
                                    {{ $r->rank }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-medium text-gray-900">{{ $r->alternative?->name }}</p>
                                @if($r->alternative?->description)
                                    <p class="text-xs text-gray-400 mt-0.5">{{ Str::limit($r->alternative->description, 60) }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-center font-mono text-xs text-gray-600">{{ number_format($r->pda, 4) }}</td>
                            <td class="px-4 py-4 text-center font-mono text-xs text-gray-600">{{ number_format($r->nda, 4) }}</td>
                            <td class="px-4 py-4 text-center font-mono text-xs text-gray-600">{{ number_format($r->sp, 4) }}</td>
                            <td class="px-4 py-4 text-center font-mono text-xs text-gray-600">{{ number_format($r->sn, 4) }}</td>
                            <td class="px-4 py-4 text-center font-mono text-xs text-blue-600 font-semibold">{{ number_format($r->nsp, 4) }}</td>
                            <td class="px-4 py-4 text-center font-mono text-xs text-indigo-600 font-semibold">{{ number_format($r->nsn, 4) }}</td>
                            <td class="px-4 py-4 text-center font-mono text-sm font-bold text-gray-900">
                                {{ number_format($r->appraisal_score, 4) }}
                            </td>
                            <td class="px-4 py-4">
                                <span class="text-xs px-2 py-1 rounded-full font-medium {{ $r->quality_color }}">
                                    {{ $r->quality_label }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Formula legend --}}
    <div class="bg-gray-50 rounded-xl border border-gray-200 p-5">
        <h3 class="font-semibold text-gray-700 text-sm mb-3">Keterangan Kolom</h3>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs text-gray-600">
            <div><strong>PDA</strong>: Positive Distance from Average</div>
            <div><strong>NDA</strong>: Negative Distance from Average</div>
            <div><strong>SP</strong>: Weighted Sum of PDA</div>
            <div><strong>SN</strong>: Weighted Sum of NDA</div>
            <div><strong>NSP</strong>: Normalized SP (0–1)</div>
            <div><strong>NSN</strong>: Normalized SN (0–1)</div>
            <div><strong>AS</strong>: Appraisal Score = (NSP + NSN) / 2</div>
            <div><strong>Rank 1</strong>: AS score tertinggi = terbaik</div>
        </div>
    </div>

</div>
@endsection
