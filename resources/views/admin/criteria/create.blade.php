@extends('layouts.app')
@section('title', 'Tambah Kriteria')
@section('header', 'Tambah Kriteria Baru')

@section('content')
<div class="max-w-lg">
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <form method="POST" action="{{ route('admin.criteria.store') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Kriteria <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="Contoh: Efektivitas Mitigasi"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500
                              @error('name') border-red-400 @enderror">
                @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Deskripsi</label>
                <textarea name="description" rows="3"
                          placeholder="Penjelasan kriteria dan cara penilaiannya..."
                          class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none">{{ old('description') }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tipe <span class="text-red-500">*</span></label>
                    <select name="type" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="benefit" @selected(old('type') === 'benefit')>Benefit (lebih tinggi = lebih baik)</option>
                        <option value="cost" @selected(old('type') === 'cost')>Cost (lebih rendah = lebih baik)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Bobot (0–1) <span class="text-red-500">*</span></label>
                    <input type="number" name="weight" value="{{ old('weight') }}" required
                           min="0.0001" max="1" step="0.0001" placeholder="Contoh: 0.3000"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500
                                  @error('weight') border-red-400 @enderror">
                    @error('weight') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <p class="text-xs text-gray-400">Total semua bobot kriteria harus = 1.0000 agar kalkulasi EDAS valid.</p>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition">
                    Tambah Kriteria
                </button>
                <a href="{{ route('admin.criteria.index') }}" class="text-sm text-gray-500 hover:text-gray-700 self-center">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
