@extends('layouts.app')
@section('title', 'Edit Kriteria')
@section('header', 'Edit Kriteria')

@section('content')
<div class="max-w-lg">
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        {{-- Fix: ['criterium' => $criteria] agar cocok dengan route {criterium} --}}
        <form method="POST" action="{{ route('admin.criteria.update', ['criterium' => $criteria]) }}" class="space-y-5">
            @csrf @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Kriteria <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $criteria->name) }}" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Deskripsi</label>
                <textarea name="description" rows="3"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none">{{ old('description', $criteria->description) }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tipe</label>
                    <select name="type" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="benefit" @selected(old('type', $criteria->type) === 'benefit')>Benefit</option>
                        <option value="cost" @selected(old('type', $criteria->type) === 'cost')>Cost</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Bobot (0–1)</label>
                    <input type="number" name="weight" value="{{ old('weight', $criteria->weight) }}"
                           min="0.0001" max="1" step="0.0001"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('weight') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition">
                    Simpan
                </button>
                <a href="{{ route('admin.criteria.index') }}" class="text-sm text-gray-500 hover:text-gray-700 self-center">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
