@extends('layouts.app')
@section('title', 'Tambah Alternatif')
@section('header', 'Tambah Alternatif Baru')

@section('content')
<div style="max-width:36rem; margin:0 auto;">
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('alternatives.store') }}"
                  style="display:flex; flex-direction:column; gap:1.25rem;">
                @csrf

                <div class="form-group">
                    <label class="form-label form-label-required">Nama Alternatif</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           placeholder="Contoh: Web Application Firewall (WAF)"
                           class="form-input {{ $errors->has('name') ? 'is-error' : '' }}">
                    @error('name') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" rows="4"
                              placeholder="Penjelasan alternatif, cara implementasi, dan konteks penggunaannya..."
                              class="form-input">{{ old('description') }}</textarea>
                </div>

                <div style="display:flex; gap:0.75rem; margin-top:0.25rem;">
                    <button type="submit" class="btn btn-primary">Tambah Alternatif</button>
                    <a href="{{ route('alternatives.index') }}" class="btn btn-ghost">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
