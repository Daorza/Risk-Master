@extends('layouts.app')
@section('title', 'Detail Log')
@section('header', 'Detail Log #' . $log->id)

@section('content')
<div style="max-width:48rem; display:flex; flex-direction:column; gap:1.25rem;">

    <a href="{{ route('admin.logs.index') }}" class="btn btn-ghost"
       style="display:inline-flex; align-items:center; gap:0.5rem; align-self:flex-start;">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali ke Logs
    </a>

    {{-- Info utama --}}
    <div class="card">
        <div class="card-body">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.25rem;">
                <div>
                    <p style="font-size:var(--font-size-xs); color:var(--color-text-subtle); margin-bottom:0.25rem; text-transform:uppercase; letter-spacing:0.06em;">Waktu</p>
                    <p style="font-weight:500; color:var(--color-text);">{{ $log->created_at->format('d M Y, H:i:s') }}</p>
                    <p style="font-size:var(--font-size-xs); color:var(--color-text-muted);">{{ $log->created_at->diffForHumans() }}</p>
                </div>
                <div>
                    <p style="font-size:var(--font-size-xs); color:var(--color-text-subtle); margin-bottom:0.25rem; text-transform:uppercase; letter-spacing:0.06em;">User</p>
                    @if($log->user)
                        <p style="font-weight:500; color:var(--color-text);">{{ $log->user->name }}</p>
                        <p style="font-size:var(--font-size-xs); color:var(--color-text-muted);">{{ $log->user->email }}</p>
                    @else
                        <p style="color:var(--color-text-muted); font-style:italic;">Sistem</p>
                    @endif
                </div>
                <div>
                    <p style="font-size:var(--font-size-xs); color:var(--color-text-subtle); margin-bottom:0.375rem; text-transform:uppercase; letter-spacing:0.06em;">Aksi</p>
                    @php
                        $bc = match(true) {
                            str_contains($log->action, 'create')    => 'badge-success',
                            str_contains($log->action, 'update')    => 'badge-primary',
                            str_contains($log->action, 'delete')    => 'badge-danger',
                            str_contains($log->action, 'login')     => 'badge-primary',
                            str_contains($log->action, 'logout')    => 'badge-neutral',
                            str_contains($log->action, 'calculate') => 'badge-warning',
                            default                                 => 'badge-neutral',
                        };
                    @endphp
                    <span class="badge {{ $bc }}">{{ $log->action }}</span>
                </div>
                <div>
                    <p style="font-size:var(--font-size-xs); color:var(--color-text-subtle); margin-bottom:0.25rem; text-transform:uppercase; letter-spacing:0.06em;">IP Address</p>
                    <p style="font-family:var(--font-mono); font-size:var(--font-size-sm); color:var(--color-text);">{{ $log->ip_address ?? '—' }}</p>
                </div>
                <div>
                    <p style="font-size:var(--font-size-xs); color:var(--color-text-subtle); margin-bottom:0.25rem; text-transform:uppercase; letter-spacing:0.06em;">Tabel</p>
                    <p style="font-family:var(--font-mono); font-size:var(--font-size-sm); color:var(--color-text);">{{ $log->table_name }}</p>
                </div>
                <div>
                    <p style="font-size:var(--font-size-xs); color:var(--color-text-subtle); margin-bottom:0.25rem; text-transform:uppercase; letter-spacing:0.06em;">Record ID</p>
                    <p style="font-family:var(--font-mono); font-size:var(--font-size-sm); color:var(--color-text);">{{ $log->record_id ?? '—' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Old data --}}
    @if($log->old_data)
        <div class="card" style="overflow:hidden;">
            <div style="padding:0.75rem 1.25rem; border-bottom:1px solid var(--color-border);
                        display:flex; align-items:center; gap:0.625rem;">
                <span style="width:0.5rem; height:0.5rem; border-radius:9999px;
                             background-color:var(--color-danger-text); flex-shrink:0;"></span>
                <h3 style="font-weight:600; font-size:var(--font-size-sm); color:var(--color-text);">Data Sebelum</h3>
            </div>
            <div style="padding:1.25rem;">
                <pre style="font-size:var(--font-size-xs); color:var(--color-text);
                            background:var(--glass-bg); border:1px solid var(--color-border);
                            border-radius:var(--radius-lg); padding:1rem;
                            overflow-x:auto; line-height:1.7;
                            max-height:20rem; overflow-y:auto;
                            scrollbar-width:thin;">{{ json_encode($log->old_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
        </div>
    @endif

    {{-- New data --}}
    @if($log->new_data)
        <div class="card" style="overflow:hidden;">
            <div style="padding:0.75rem 1.25rem; border-bottom:1px solid var(--color-border);
                        display:flex; align-items:center; gap:0.625rem;">
                <span style="width:0.5rem; height:0.5rem; border-radius:9999px;
                             background-color:var(--color-success-text); flex-shrink:0;"></span>
                <h3 style="font-weight:600; font-size:var(--font-size-sm); color:var(--color-text);">Data Sesudah</h3>
            </div>
            <div style="padding:1.25rem;">
                <pre style="font-size:var(--font-size-xs); color:var(--color-text);
                            background:var(--glass-bg); border:1px solid var(--color-border);
                            border-radius:var(--radius-lg); padding:1rem;
                            overflow-x:auto; line-height:1.7;
                            max-height:20rem; overflow-y:auto;
                            scrollbar-width:thin;">{{ json_encode($log->new_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
        </div>
    @endif

    @if(!$log->old_data && !$log->new_data)
        <div class="card" style="padding:3rem 1.5rem; text-align:center;">
            <p style="color:var(--color-text-subtle); font-size:var(--font-size-sm);">
                Tidak ada data perubahan yang dicatat untuk log ini.
            </p>
        </div>
    @endif

</div>
@endsection
