@extends('layouts.app')
@section('title', 'Kelola Kriteria')
@section('header', 'Kelola Kriteria EDAS')

@section('content')
<div class="space-y-5">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <span class="text-sm text-gray-600">Total Bobot:</span>
            <span class="font-bold text-lg {{ abs($totalWeight - 1.0) <= 0.01 ? 'text-green-600' : 'text-red-500' }}">
                {{ number_format($totalWeight, 4) }}
            </span>
            @if(abs($totalWeight - 1.0) <= 0.01)
                <span class="text-xs text-green-600">✓ Valid</span>
            @else
                <span class="text-xs text-red-500">⚠ Harus = 1.0000</span>
            @endif
        </div>
        <a href="{{ route('admin.criteria.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Kriteria
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-6 py-3 font-semibold text-gray-600">Nama Kriteria</th>
                    <th class="text-left px-6 py-3 font-semibold text-gray-600">Deskripsi</th>
                    <th class="text-center px-4 py-3 font-semibold text-gray-600">Tipe</th>
                    <th class="text-center px-4 py-3 font-semibold text-gray-600">Bobot</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($criteria as $c)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $c->name }}</td>
                        <td class="px-6 py-4 text-gray-500 text-xs max-w-xs">
                            <span class="line-clamp-2">{{ $c->description ?? '—' }}</span>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <span class="text-xs px-2.5 py-1 rounded-full font-medium
                                {{ $c->isBenefit()
                                    ? 'bg-blue-50 text-blue-700 border border-blue-200'
                                    : 'bg-orange-50 text-orange-700 border border-orange-200' }}">
                                {{ strtoupper($c->type) }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <div class="w-16 bg-gray-100 rounded-full h-1.5">
                                    <div class="h-1.5 rounded-full {{ $c->isBenefit() ? 'bg-blue-500' : 'bg-orange-400' }}"
                                         style="width: {{ $c->weight * 100 }}%"></div>
                                </div>
                                <span class="font-mono text-xs text-gray-700">{{ $c->weight_percent }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-3 justify-end">
                                <a href="{{ route('admin.criteria.edit', $c) }}"
                                   class="text-xs text-blue-600 hover:underline">Edit</a>
                                <form method="POST" action="{{ route('admin.criteria.destroy', $c) }}"
                                      onsubmit="return confirm('Hapus kriteria ini? Data EDAS terkait akan terpengaruh.')">
                                    @csrf @method('DELETE')
                                    <button class="text-xs text-red-500 hover:underline">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
