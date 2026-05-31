@extends('layouts.app')
@section('title', 'Assessment')
@section('header', 'Daftar Assessment')

@section('content')
<div class="space-y-5">
    {{-- Toolbar --}}
    <div class="flex flex-wrap items-center gap-3 justify-between">
        <form method="GET" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari assessment..."
                   class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-60">
            <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua status</option>
                <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                <option value="completed" @selected(request('status') === 'completed')>Selesai</option>
            </select>
            <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition">
                Filter
            </button>
        </form>
        <a href="{{ route('assessments.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Buat Assessment
        </a>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        @if($assessments->isEmpty())
            <div class="py-16 text-center">
                <p class="text-gray-400 text-sm">Tidak ada assessment ditemukan.</p>
                <a href="{{ route('assessments.create') }}" class="mt-2 inline-block text-blue-600 hover:underline text-sm">Buat pertama →</a>
            </div>
        @else
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-6 py-3 font-semibold text-gray-600">Judul</th>
                        <th class="text-left px-6 py-3 font-semibold text-gray-600">Pemilik</th>
                        <th class="text-center px-4 py-3 font-semibold text-gray-600">Alternatif</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600">Status</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600">Dibuat</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($assessments as $a)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <a href="{{ route('assessments.show', $a) }}"
                                   class="font-medium text-gray-900 hover:text-blue-600">{{ $a->title }}</a>
                                @if($a->description)
                                    <p class="text-xs text-gray-400 mt-0.5 truncate max-w-xs">{{ $a->description }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-600">{{ $a->owner?->name ?? '—' }}</td>
                            <td class="px-4 py-4 text-center text-gray-600">{{ $a->alternatives_count }}</td>
                            <td class="px-4 py-4">
                                <span class="text-xs px-2.5 py-1 rounded-full font-medium
                                    {{ $a->status === 'completed'
                                        ? 'bg-green-50 text-green-700 border border-green-200'
                                        : 'bg-yellow-50 text-yellow-700 border border-yellow-200' }}">
                                    {{ $a->status_label }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-gray-500 text-xs">{{ $a->created_at->format('d M Y') }}</td>
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-2 justify-end">
                                    <a href="{{ route('assessments.show', $a) }}"
                                       class="text-xs text-blue-600 hover:underline">Detail</a>
                                    @if($a->status === 'draft')
                                        <a href="{{ route('assessments.values.edit', $a) }}"
                                           class="text-xs text-indigo-600 hover:underline">Input Nilai</a>
                                    @else
                                        <a href="{{ route('assessments.results', $a) }}"
                                           class="text-xs text-green-600 hover:underline">Hasil</a>
                                    @endif
                                    <form method="POST" action="{{ route('assessments.destroy', $a) }}"
                                          onsubmit="return confirm('Hapus assessment ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs text-red-500 hover:underline">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="px-6 py-4 border-t border-gray-100">
                {{ $assessments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
