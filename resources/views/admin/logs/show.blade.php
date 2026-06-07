@extends('layouts.app')
@section('title', 'Detail Log')
@section('header', 'Detail Log #' . $log->id)

@section('content')
<div class="max-w-3xl space-y-5">

    {{-- Back --}}
    <a href="{{ route('admin.logs.index') }}" class="btn btn-ghost" style="display:inline-flex; align-items:center; gap:0.5rem;">
        ← Kembali ke Logs
    </a>

    {{-- Info utama --}}
    <div class="card">
        <div class="card-body">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; font-size:var(--font-size-sm);">
                <div>
                    <p style="font-size:var(--font-size-xs); color:var(--color-text-subtle); margin-bottom:0.25rem;">Waktu</p>
                    <p style="font-weight:500; color:var(--color-text);">{{ $log->created_at->format('d M Y, H:i:s') }}</p>
                    <p style="font-size:var(--font-size-xs); color:var(--color-text-muted);">{{ $log->created_at->diffForHumans() }}</p>
                </div>
                <div>
                    <p style="font-size:var(--font-size-xs); color:var(--color-text-subtle); margin-bottom:0.25rem;">User</p>
                    @if($log->user)
                        <p style="font-weight:500; color:var(--color-text);">{{ $log->user->name }}</p>
                        <p style="font-size:var(--font-size-xs); color:var(--color-text-muted);">{{ $log->user->email }}</p>
                    @else
                        <p style="font-size:var(--font-size-sm); color:var(--color-text-muted); font-style:italic;">Sistem</p>
                    @endif
                </div>
                <div>
                    <p style="font-size:var(--font-size-xs); color:var(--color-text-subtle); margin-bottom:0.25rem;">Aksi</p>
                    @php
                        $badgeClass = match(true) {
                            str_contains($log->action, 'create')   => 'badge-success',
                            str_contains($log->action, 'update')   => 'badge-primary',
                            str_contains($log->action, 'delete')   => 'badge-danger',
                            str_contains($log->action, 'login')    => 'badge-primary',
                            str_contains($log->action, 'logout')   => 'badge-neutral',
                            str_contains($log->action, 'calculate')=> 'badge-warning',
                            default => 'badge-neutral',
                        };
                    @endphp
                    <span class="badge {{ $badgeClass }}">
                        {{ $log->action }}
                    </span>
                </div>
                <div>
                    <p style="font-size:var(--font-size-xs); color:var(--color-text-subtle); margin-bottom:0.25rem;">IP Address</p>
                    <p style="font-size:var(--font-size-sm); font-family:var(--font-mono); color:var(--color-text);">{{ $log->ip_address ?? '—' }}</p>
                </div>
                <div>
                    <p style="font-size:var(--font-size-xs); color:var(--color-text-subtle); margin-bottom:0.25rem;">Tabel</p>
                    <p style="font-size:var(--font-size-sm); font-family:var(--font-mono); color:var(--color-text);">{{ $log->table_name }}</p>
                </div>
                <div>
                    <p style="font-size:var(--font-size-xs); color:var(--color-text-subtle); margin-bottom:0.25rem;">Record ID</p>
                    <p style="font-size:var(--font-size-sm); font-family:var(--font-mono); color:var(--color-text);">{{ $log->record_id ?? '—' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Old data --}}
    @if($log->old_data)
        <div class="card overflow-hidden">
            <div style="padding:0.75rem 1.25rem; border-bottom:1px solid var(--color-border); display:flex; align-items:center; gap:0.5rem;">
                <span style="width:0.5rem; height:0.5rem; border-radius:9999px; background-color:var(--color-danger-text);"></span>
                <h3 style="font-weight:600; font-size:var(--font-size-sm); color:var(--color-text);">Data Sebelum</h3>
            </div>
            <div style="padding:1.25rem;">
                <pre style="font-size:var(--font-size-xs); color:var(--color-text); background:var(--glass-bg); border:1px solid var(--color-border); border-radius:0.5rem; padding:1rem; overflow-x:auto; line-height:1.6;">{{ json_encode($log->old_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
        </div>
    @endif

    {{-- New data --}}
    @if($log->new_data)
        <div class="card overflow-hidden">
            <div style="padding:0.75rem 1.25rem; border-bottom:1px solid var(--color-border); display:flex; align-items:center; gap:0.5rem;">
                <span style="width:0.5rem; height:0.5rem; border-radius:9999px; background-color:var(--color-success-text);"></span>
                <h3 style="font-weight:600; font-size:var(--font-size-sm); color:var(--color-text);">Data Sesudah</h3>
            </div>
            <div style="padding:1.25rem;">
                <pre style="font-size:var(--font-size-xs); color:var(--color-text); background:var(--glass-bg); border:1px solid var(--color-border); border-radius:0.5rem; padding:1rem; overflow-x:auto; line-height:1.6;">{{ json_encode($log->new_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
        </div>
    @endif

    {{-- Tidak ada perubahan data --}}
    @if(!$log->old_data && !$log->new_data)
        <div class="card p-8 text-center">
            <p style="font-size:var(--font-size-sm); color:var(--color-text-subtle);">Tidak ada data perubahan yang dicatat untuk log ini.</p>
        </div>
    @endif

</div>
@endsection
