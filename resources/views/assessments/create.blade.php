@extends('layouts.app')
@section('title', 'Buat Assessment')
@section('header', 'Buat Assessment Baru')

@section('content')
<div class="max-w-2xl">
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('assessments.store') }}" style="display:flex; flex-direction:column; gap:1.25rem;">
                @csrf

                <div class="form-group">
                    <label class="form-label form-label-required">Judul Assessment</label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                           placeholder="Contoh: Analisis Risiko Jaringan Q3 2026"
                           class="form-input {{ $errors->has('title') ? 'is-error' : '' }}">
                    @error('title') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description"
                              placeholder="Konteks dan latar belakang assessment..."
                              class="form-input">{{ old('description') }}</textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Pilih Alternatif (Opsional — bisa ditambah nanti)</label>
                    <div style="border:1px solid var(--color-border); border-radius:var(--radius-lg); max-height:13rem; overflow-y:auto; display:flex; flex-direction:column;">
                        @foreach($alternatives as $alt)
                            <label style="display:flex; align-items:flex-start; gap:0.75rem; padding:0.75rem 1rem; border-bottom:1px solid var(--color-border); cursor:pointer; transition:background-color 0.2s;"
                                   onmouseover="this.style.backgroundColor='var(--color-bg-subtle)'"
                                   onmouseout="this.style.backgroundColor='transparent'">
                                <input type="checkbox" name="alternative_ids[]" value="{{ $alt->id }}"
                                       style="margin-top:0.125rem; accent-color:var(--color-primary);"
                                       @checked(in_array($alt->id, old('alternative_ids', [])))>
                                <div style="flex:1;">
                                    <p style="font-size:var(--font-size-sm); font-weight:500; color:var(--color-text);">{{ $alt->name }}</p>
                                    @if($alt->description)
                                        <p style="font-size:var(--font-size-xs); color:var(--color-text-subtle); margin-top:0.125rem;">{{ Str::limit($alt->description, 80) }}</p>
                                    @endif
                                </div>
                            </label>
                        @endforeach
                    </div>
                    <p style="font-size:var(--font-size-xs); color:var(--color-text-muted); margin-top:0.375rem;">{{ $alternatives->count() }} alternatif tersedia</p>
                </div>

                <div style="display:flex; align-items:center; gap:0.75rem; margin-top:0.5rem;">
                    <button type="submit" class="btn btn-primary">Buat Assessment</button>
                    <a href="{{ route('assessments.index') }}" class="btn btn-ghost">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
