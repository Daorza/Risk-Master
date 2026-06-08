@extends('layouts.app')
@section('title', 'Edit Kriteria')
@section('header', 'Edit Kriteria')

@section('content')
<div class="max-w-lg mx-auto">
    <div class="card">
        <div class="card-body">
            {{-- Fix: ['criterium' => $criteria] agar cocok dengan route {criterium} --}}
            <form method="POST" action="{{ route('admin.criteria.update', ['criterium' => $criteria]) }}" style="display:flex; flex-direction:column; gap:1.25rem;">
                @csrf @method('PUT')

                <div class="form-group">
                    <label class="form-label form-label-required">Nama Kriteria</label>
                    <input type="text" name="name" value="{{ old('name', $criteria->name) }}" required
                           class="form-input {{ $errors->has('name') ? 'is-error' : '' }}">
                    @error('name') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" rows="3"
                              class="form-input">{{ old('description', $criteria->description) }}</textarea>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                    <div class="form-group">
                        <label class="form-label form-label-required">Tipe</label>
                        <select name="type" class="form-input">
                            <option value="benefit" @selected(old('type', $criteria->type) === 'benefit')>Benefit</option>
                            <option value="cost" @selected(old('type', $criteria->type) === 'cost')>Cost</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label form-label-required">Bobot (0–1)</label>
                        <input type="number" name="weight" value="{{ old('weight', $criteria->weight) }}"
                               min="0.0001" max="1" step="0.0001"
                               class="form-input {{ $errors->has('weight') ? 'is-error' : '' }}">
                        @error('weight') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div style="display:flex; align-items:center; gap:0.75rem; margin-top:0.5rem;">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="{{ route('admin.criteria.index') }}" class="btn btn-ghost">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
