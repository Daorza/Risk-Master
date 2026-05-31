@extends('layouts.app')
@section('title', 'Alternatif')
@section('header', 'Daftar Alternatif')

@section('content')
<div class="space-y-5">
    <div class="flex flex-wrap gap-3 items-center justify-between">
        <form method="GET" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari alternatif..."
                   class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-56">
            <select name="source" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua sumber</option>
                <option value="admin" @selected(request('source') === 'admin')>Template Admin</option>
                <option value="user" @selected(request('source') === 'user')>Input User</option>
            </select>
            <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition">Filter</button>
        </form>
        <a href="{{ route('alternatives.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Alternatif
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        @if($alternatives->isEmpty())
            <div class="py-14 text-center">
                <p class="text-gray-400 text-sm">Tidak ada alternatif ditemukan.</p>
            </div>
        @else
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-6 py-3 font-semibold text-gray-600">Nama</th>
                        <th class="text-left px-6 py-3 font-semibold text-gray-600">Deskripsi</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600">Sumber</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600">Dibuat oleh</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($alternatives as $alt)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $alt->name }}</td>
                            <td class="px-6 py-4 text-gray-500 max-w-xs">
                                <span class="line-clamp-2">{{ $alt->description ?? '—' }}</span>
                            </td>
                            <td class="px-4 py-4">
                                <span class="text-xs px-2 py-1 rounded-full font-medium
                                    {{ $alt->source === 'admin'
                                        ? 'bg-blue-50 text-blue-700 border border-blue-200'
                                        : 'bg-purple-50 text-purple-700 border border-purple-200' }}">
                                    {{ $alt->source_label }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-gray-500 text-xs">{{ $alt->creator?->name ?? '—' }}</td>
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-3 justify-end">
                                    @if(auth()->user()->isAdmin() || $alt->created_by === auth()->id())
                                        <a href="{{ route('alternatives.edit', $alt) }}"
                                           class="text-xs text-blue-600 hover:underline">Edit</a>
                                        <form method="POST" action="{{ route('alternatives.destroy', $alt) }}"
                                              onsubmit="return confirm('Hapus alternatif ini?')">
                                            @csrf @method('DELETE')
                                            <button class="text-xs text-red-500 hover:underline">Hapus</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $alternatives->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
