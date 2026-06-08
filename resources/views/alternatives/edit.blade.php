@extends('layouts.app')
@section('title', 'Edit Alternatif')
@section('header', 'Edit Alternatif')

@section('content')
<div style="max-width:36rem; margin:0 auto;">
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('alternatives.update', $alternative) }}"
                  style="display:flex; flex-direction:column; gap:1.25rem;">
                @csrf @method('PUT')

                <div class="form-group">
                    <label class="form-label form-label-required">Nama</label>
                    <input type="text" name="name" value="{{ old('name', $alternative->name) }}" required
                           class="form-input {{ $errors->has('name') ? 'is-error' : '' }}">
                    @error('name') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" rows="4"
                              class="form-input">{{ old('description', $alternative->description) }}</textarea>
                </div>

                <div style="display:flex; gap:0.75rem; margin-top:0.25rem;">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="{{ route('alternatives.index') }}" class="btn btn-ghost">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
