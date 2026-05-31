@extends('layouts.app')
@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('content')
<div class="space-y-6">

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-sm text-gray-500">Total Assessment</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['total_assessments'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-sm text-gray-500">Selesai</p>
            <p class="text-3xl font-bold text-green-600 mt-1">{{ $stats['completed_assessments'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-sm text-gray-500">Draft</p>
            <p class="text-3xl font-bold text-yellow-500 mt-1">{{ $stats['draft_assessments'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-sm text-gray-500">Alternatif Tersedia</p>
            <p class="text-3xl font-bold text-blue-600 mt-1">{{ $stats['total_alternatives'] }}</p>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        {{-- Recent assessments --}}
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="font-semibold text-gray-800">Assessment Terbaru</h2>
                <a href="{{ route('assessments.create') }}"
                   class="text-sm text-blue-600 hover:text-blue-700 font-medium">+ Buat Baru</a>
            </div>

            @if($recentAssessments->isEmpty())
                <div class="px-6 py-12 text-center">
                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <p class="text-gray-500 text-sm">Belum ada assessment.</p>
                    <a href="{{ route('assessments.create') }}"
                       class="mt-3 inline-block text-sm text-blue-600 hover:underline">Buat assessment pertama →</a>
                </div>
            @else
                <div class="divide-y divide-gray-50">
                    @foreach($recentAssessments as $assessment)
                        <a href="{{ route('assessments.show', $assessment) }}"
                           class="flex items-center gap-4 px-6 py-4 hover:bg-gray-50 transition">
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-gray-900 truncate">{{ $assessment->title }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    {{ $assessment->alternatives_count }} alternatif
                                    @if($assessment->owner)· {{ $assessment->owner->name }}@endif
                                </p>
                            </div>
                            <span class="shrink-0 text-xs px-2.5 py-1 rounded-full font-medium
                                {{ $assessment->status === 'completed'
                                    ? 'bg-green-50 text-green-700 border border-green-200'
                                    : 'bg-yellow-50 text-yellow-700 border border-yellow-200' }}">
                                {{ $assessment->status_label }}
                            </span>
                        </a>
                    @endforeach
                </div>
                <div class="px-6 py-3 border-t border-gray-100">
                    <a href="{{ route('assessments.index') }}"
                       class="text-sm text-blue-600 hover:text-blue-700">Lihat semua →</a>
                </div>
            @endif
        </div>

        {{-- Criteria overview --}}
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="font-semibold text-gray-800">Kriteria EDAS</h2>
                <span class="text-xs {{ abs($totalWeight - 1.0) <= 0.01 ? 'text-green-600' : 'text-red-500' }} font-medium">
                    Bobot: {{ number_format($totalWeight, 4) }}
                </span>
            </div>
            <div class="px-6 py-4 space-y-3">
                @foreach($criteria as $c)
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-sm font-medium text-gray-700 truncate">{{ $c->name }}</span>
                            <span class="text-xs text-gray-500 shrink-0 ml-2">{{ $c->weight_percent }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="flex-1 bg-gray-100 rounded-full h-1.5">
                                <div class="h-1.5 rounded-full {{ $c->isBenefit() ? 'bg-blue-500' : 'bg-orange-400' }}"
                                     style="width: {{ $c->weight * 100 }}%"></div>
                            </div>
                            <span class="text-xs shrink-0 {{ $c->isBenefit() ? 'text-blue-600' : 'text-orange-500' }}">
                                {{ strtoupper($c->type) }}
                            </span>
                        </div>
                    </div>
                @endforeach

                @if(abs($totalWeight - 1.0) > 0.01)
                    <div class="mt-3 rounded-lg bg-red-50 border border-red-200 px-3 py-2 text-xs text-red-700">
                        ⚠ Total bobot tidak sama dengan 1.0. Kalkukasi EDAS akan gagal.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
