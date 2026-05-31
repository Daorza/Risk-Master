@extends('layouts.app')
@section('title', 'Input Nilai Matrix')
@section('header', 'Input Nilai Matrix — ' . $assessment->title)

@section('content')
<div class="space-y-5">
    {{-- Panduan --}}
    <div class="bg-blue-50 border border-blue-200 rounded-xl px-5 py-4">
        <h3 class="font-semibold text-blue-800 text-sm mb-2">Panduan Penilaian (Skala 1–10)</h3>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-2">
            @foreach($criteria as $c)
                <div class="bg-white rounded-lg border border-blue-100 px-3 py-2">
                    <div class="flex items-center gap-2 mb-0.5">
                        <span class="text-xs font-bold {{ $c->isBenefit() ? 'text-blue-600' : 'text-orange-500' }}">
                            {{ strtoupper($c->type) }}
                        </span>
                        <span class="text-xs font-semibold text-gray-700">{{ $c->name }}</span>
                        <span class="text-xs text-gray-400 ml-auto">{{ $c->weight_percent }}</span>
                    </div>
                    @if($c->description)
                        <p class="text-xs text-gray-500">{{ Str::limit($c->description, 100) }}</p>
                    @endif
                </div>
            @endforeach
        </div>
        <p class="text-xs text-blue-700 mt-3">
            <strong>Benefit</strong>: nilai lebih tinggi = lebih baik &nbsp;|&nbsp;
            <strong>Cost</strong>: nilai lebih tinggi = lebih buruk (lebih mahal/kompleks)
        </p>
    </div>

    {{-- Form --}}
    @if($alternatives->isEmpty())
        <div class="bg-white rounded-xl border border-gray-200 px-6 py-12 text-center">
            <p class="text-gray-500 text-sm">Tidak ada alternatif. Tambahkan alternatif ke assessment terlebih dahulu.</p>
            <a href="{{ route('assessments.edit', $assessment) }}"
               class="mt-2 inline-block text-blue-600 hover:underline text-sm">Edit assessment →</a>
        </div>
    @else
        <form method="POST" action="{{ route('assessments.values.store', $assessment) }}">
            @csrf

            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="text-left px-6 py-3 font-semibold text-gray-700 whitespace-nowrap">Alternatif</th>
                                @foreach($criteria as $c)
                                    <th class="text-center px-3 py-3 font-semibold text-gray-700 whitespace-nowrap min-w-[120px]">
                                        <div>{{ $c->name }}</div>
                                        <div class="font-normal text-xs {{ $c->isBenefit() ? 'text-blue-500' : 'text-orange-500' }}">
                                            {{ strtoupper($c->type) }} · {{ $c->weight_percent }}
                                        </div>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($alternatives as $alt)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-3 font-medium text-gray-800 whitespace-nowrap">{{ $alt->name }}</td>
                                    @foreach($criteria as $c)
                                        <td class="px-3 py-3 text-center">
                                            <input type="number"
                                                   name="values[{{ $alt->id }}][{{ $c->id }}]"
                                                   value="{{ old("values.{$alt->id}.{$c->id}", $valueMap[$alt->id][$c->id] ?? '') }}"
                                                   min="0" step="0.01" required
                                                   class="w-20 text-center border border-gray-200 rounded-lg px-2 py-1.5 text-sm
                                                          focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent
                                                          @error("values.{$alt->id}.{$c->id}") border-red-400 @enderror">
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
                    <a href="{{ route('assessments.show', $assessment) }}"
                       class="text-sm text-gray-500 hover:text-gray-700">← Kembali</a>

                    <div class="flex items-center gap-3">
                        <span class="text-xs text-gray-400">
                            {{ $alternatives->count() }} alternatif × {{ $criteria->count() }} kriteria
                            = {{ $alternatives->count() * $criteria->count() }} sel
                        </span>
                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition">
                            Simpan Nilai
                        </button>
                    </div>
                </div>
            </div>
        </form>
    @endif
</div>
@endsection
