@extends('layouts.app')
@section('title', 'Tambah Kriteria')
@section('header', 'Tambah Kriteria Baru')

@section('content')
<div class="max-w-lg mx-auto">
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.criteria.store') }}" style="display:flex; flex-direction:column; gap:1.25rem;">
                @csrf

                <div class="form-group">
                    <label class="form-label form-label-required">Nama Kriteria</label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="Contoh: Efektivitas Mitigasi"
                           class="form-input {{ $errors->has('name') ? 'is-error' : '' }}">
                    @error('name') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" rows="3"
                              placeholder="Penjelasan kriteria dan cara penilaiannya..."
                              class="form-input">{{ old('description') }}</textarea>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                    <div class="form-group">
                        <label class="form-label form-label-required">Tipe</label>
                        <select name="type" required class="form-input">
                            <option value="benefit" @selected(old('type') === 'benefit')>Benefit (lebih tinggi = lebih baik)</option>
                            <option value="cost" @selected(old('type') === 'cost')>Cost (lebih rendah = lebih baik)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label form-label-required">Bobot (0–1)</label>
                        <input type="number" name="weight" value="{{ old('weight') }}" required
                               min="0.0001" max="1" step="0.0001" placeholder="Contoh: 0.3000"
                               class="form-input {{ $errors->has('weight') ? 'is-error' : '' }}">
                        @error('weight') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                </div>

                <p style="font-size:var(--font-size-xs); color:var(--color-text-muted);">Total semua bobot kriteria harus = 1.0000 agar kalkulasi EDAS valid.</p>

                <div style="display:flex; align-items:center; gap:0.75rem; margin-top:0.5rem;">
                    <button type="submit" class="btn btn-primary">Tambah Kriteria</button>
                    <a href="{{ route('admin.criteria.index') }}" class="btn btn-ghost">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
