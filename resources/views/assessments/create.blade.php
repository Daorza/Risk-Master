@extends('layouts.app')
@section('title', 'Buat Assessment')
@section('header', 'Buat Assessment Baru')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <form method="POST" action="{{ route('assessments.store') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Judul Assessment <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" value="{{ old('title') }}" required
                       placeholder="Contoh: Analisis Risiko Jaringan Q3 2026"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500
                              @error('title') border-red-400 @enderror">
                @error('title') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Deskripsi</label>
                <textarea name="description" rows="3"
                          placeholder="Konteks dan latar belakang assessment..."
                          class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none">{{ old('description') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Pilih Alternatif (Opsional — bisa ditambah nanti)
                </label>
                <div class="border border-gray-200 rounded-lg max-h-52 overflow-y-auto divide-y divide-gray-100">
                    @foreach($alternatives as $alt)
                        <label class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 cursor-pointer">
                            <input type="checkbox" name="alternative_ids[]" value="{{ $alt->id }}"
                                   class="mt-0.5 rounded border-gray-300 text-blue-600"
                                   @checked(in_array($alt->id, old('alternative_ids', [])))>
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $alt->name }}</p>
                                @if($alt->description)
                                    <p class="text-xs text-gray-500 mt-0.5">{{ Str::limit($alt->description, 80) }}</p>
                                @endif
                            </div>
                        </label>
                    @endforeach
                </div>
                <p class="text-xs text-gray-400 mt-1">{{ $alternatives->count() }} alternatif tersedia</p>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition">
                    Buat Assessment
                </button>
                <a href="{{ route('assessments.index') }}"
                   class="text-sm text-gray-500 hover:text-gray-700">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
