@extends('layouts.app')
@section('title', $assessment->title)
@section('header', $assessment->title)

@section('content')
<div class="space-y-6">

    {{-- Header info --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-start justify-between gap-4 flex-wrap">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span class="text-sm px-3 py-1 rounded-full font-medium
                        {{ $assessment->status === 'completed'
                            ? 'bg-green-50 text-green-700 border border-green-200'
                            : 'bg-yellow-50 text-yellow-700 border border-yellow-200' }}">
                        {{ $assessment->status_label }}
                    </span>
                    <span class="text-xs text-gray-400">{{ $assessment->created_at->format('d M Y, H:i') }}</span>
                </div>
                @if($assessment->description)
                    <p class="text-sm text-gray-600 mt-1">{{ $assessment->description }}</p>
                @endif
                <p class="text-xs text-gray-400 mt-2">Pemilik: {{ $assessment->owner?->name }}</p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                @if($assessment->isDraft())
                    <a href="{{ route('assessments.values.edit', $assessment) }}"
                       class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                        Input Nilai Matrix
                    </a>
                    @if($assessment->isMatrixComplete())
                        <form method="POST" action="{{ route('assessments.calculate', $assessment) }}">
                            @csrf
                            <button type="submit"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                                Hitung EDAS
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('assessments.edit', $assessment) }}"
                       class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition">
                        Edit
                    </a>
                @else
                    <a href="{{ route('assessments.results', $assessment) }}"
                       class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                        Lihat Hasil EDAS
                    </a>
                    <a href="{{ route('assessments.report.pdf', $assessment) }}"
                       target="_blank"
                       class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
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

    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-3">
            <h2 class="font-semibold text-gray-800">Progres Matrix Keputusan</h2>
            <span class="text-sm font-medium {{ $filled === $expected ? 'text-green-600' : 'text-gray-500' }}">
                {{ $filled }}/{{ $expected }} sel terisi ({{ $pct }}%)
            </span>
        </div>
        <div class="w-full bg-gray-100 rounded-full h-2">
            <div class="h-2 rounded-full transition-all {{ $filled === $expected ? 'bg-green-500' : 'bg-blue-500' }}"
                 style="width: {{ $pct }}%"></div>
        </div>
        @if($filled < $expected && $assessment->isDraft())
            <p class="text-xs text-amber-600 mt-2">
                Masih {{ $expected - $filled }} sel yang belum diisi sebelum dapat menghitung EDAS.
            </p>
        @endif
    </div>

    {{-- Matrix tabel --}}
    @if($altCount > 0)
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-800">Matrix Keputusan</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-6 py-3 font-semibold text-gray-600 whitespace-nowrap">Alternatif</th>
                        @foreach($criteria as $c)
                            <th class="text-center px-4 py-3 font-semibold text-gray-600 whitespace-nowrap">
                                <div>{{ $c->name }}</div>
                                <div class="text-xs font-normal {{ $c->isBenefit() ? 'text-blue-500' : 'text-orange-500' }}">
                                    {{ strtoupper($c->type) }} · {{ $c->weight_percent }}
                                </div>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($assessment->alternatives as $alt)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3 font-medium text-gray-800 whitespace-nowrap">{{ $alt->name }}</td>
                            @foreach($criteria as $c)
                                <td class="px-4 py-3 text-center">
                                    @if(isset($valueMap[$alt->id][$c->id]))
                                        <span class="font-mono text-gray-700">{{ number_format($valueMap[$alt->id][$c->id], 2) }}</span>
                                    @else
                                        <span class="text-red-400 text-xs">—</span>
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
        <div class="bg-white rounded-xl border border-gray-200 px-6 py-10 text-center">
            <p class="text-gray-500 text-sm">Belum ada alternatif yang dipilih.</p>
            <a href="{{ route('assessments.edit', $assessment) }}"
               class="mt-2 inline-block text-blue-600 hover:underline text-sm">Edit assessment →</a>
        </div>
    @endif

</div>
@endsection
