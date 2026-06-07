@extends('layouts.app')
@section('title', 'Kelola Kriteria')
@section('header', 'Kelola Kriteria EDAS')

@section('content')
<div class="space-y-5">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <span style="font-size:var(--font-size-sm); color:var(--color-text-subtle);">Total Bobot:</span>
            <span style="font-size:var(--font-size-lg); font-weight:700; color:{{ abs($totalWeight - 1.0) <= 0.01 ? 'var(--color-success-text)' : 'var(--color-danger-text)' }};">
                {{ number_format($totalWeight, 4) }}
            </span>
            @if(abs($totalWeight - 1.0) <= 0.01)
                <span style="font-size:var(--font-size-xs); color:var(--color-success-text);">✓ Valid</span>
            @else
                <span style="font-size:var(--font-size-xs); color:var(--color-danger-text);">⚠ Harus = 1.0000</span>
            @endif
        </div>
        <a href="{{ route('admin.criteria.create') }}" class="btn btn-primary">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Kriteria
        </a>
    </div>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Nama Kriteria</th>
                    <th>Deskripsi</th>
                    <th style="text-align:center;">Tipe</th>
                    <th style="text-align:center;">Bobot</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($criteria as $c)
                    <tr>
                        <td style="font-weight:500; color:var(--color-text);">{{ $c->name }}</td>
                        <td style="font-size:var(--font-size-xs); color:var(--color-text-subtle); max-width:16rem;">
                            <span class="line-clamp-2">{{ $c->description ?? '—' }}</span>
                        </td>
                        <td style="text-align:center;">
                            <span class="badge {{ $c->isBenefit() ? 'badge-primary' : 'badge-warning' }}" style="font-weight:500;">
                                {{ strtoupper($c->type) }}
                            </span>
                        </td>
                        <td style="text-align:center;">
                            <div class="flex items-center justify-center gap-2">
                                <div style="width:4rem; background:var(--glass-bg); border-radius:9999px; height:0.375rem; overflow:hidden;">
                                    <div style="height:100%; border-radius:9999px; background:{{ $c->isBenefit() ? 'var(--color-primary)' : 'var(--color-warning-text)' }}; width: {{ $c->weight * 100 }}%"></div>
                                </div>
                                <span style="font-family:var(--font-mono); font-size:var(--font-size-xs); color:var(--color-text-muted);">{{ $c->weight_percent }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="flex items-center gap-3 justify-end">
                                {{-- Fix: pass ['criterium' => $c] agar nama parameter cocok dengan route {criterium} --}}
                                <a href="{{ route('admin.criteria.edit', ['criterium' => $c]) }}"
                                   class="btn btn-ghost btn-xs">Edit</a>
                                <form method="POST" action="{{ route('admin.criteria.destroy', ['criterium' => $c]) }}"
                                      onsubmit="return confirm('Hapus kriteria ini? Data EDAS terkait akan terpengaruh.')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-ghost btn-xs" style="color:var(--color-danger-text);">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
