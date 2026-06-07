@extends('layouts.app')
@section('title', 'Audit Logs')
@section('header', 'Audit Logs')

@section('content')
<div class="space-y-5">

    {{-- Filter --}}
    <div class="card p-4">
        <form method="GET" class="grid grid-cols-2 lg:grid-cols-5 gap-3 overflow-auto pb-4 scrollbar-thumb-accent-600">

            <select name="action" class="form-input">
                <option value="">Semua Aksi</option>
                @foreach($actions as $action)
                    <option value="{{ $action }}" @selected(request('action') === $action)>
                        {{ $action }}
                    </option>
                @endforeach
            </select>

            <select name="table" class="form-input">
                <option value="">Semua Tabel</option>
                @foreach($tables as $table)
                    <option value="{{ $table }}" @selected(request('table') === $table)>
                        {{ $table }}
                    </option>
                @endforeach
            </select>

            <select name="user_id" class="form-input">
                <option value="">Semua User</option>
                <option value="null" @selected(request('user_id') === 'null')>Sistem</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" @selected(request('user_id') == $user->id)>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>

            <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-input">

            <div class="flex gap-2">
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-input flex-1">
                <button type="submit" class="btn btn-primary whitespace-nowrap">Filter</button>
                <a href="{{ route('admin.logs.index') }}" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @php
            $totalToday = \App\Models\AuditLog::whereDate('created_at', today())->count();
            $totalWeek  = \App\Models\AuditLog::where('created_at', '>=', now()->subDays(7))->count();
            $totalAll   = \App\Models\AuditLog::count();
        @endphp

        <div class="card p-4">
            <p style="font-size:var(--font-size-xs); color:var(--color-text-subtle);">Hari Ini</p>
            <p style="font-size:1.5rem; font-weight:700; color:var(--color-text); margin-top:0.25rem;">{{ number_format($totalToday) }}</p>
        </div>
        <div class="card p-4">
            <p style="font-size:var(--font-size-xs); color:var(--color-text-subtle);">7 Hari Terakhir</p>
            <p style="font-size:1.5rem; font-weight:700; color:var(--color-primary); margin-top:0.25rem;">{{ number_format($totalWeek) }}</p>
        </div>
        <div class="card p-4">
            <p style="font-size:var(--font-size-xs); color:var(--color-text-subtle);">Total Log</p>
            <p style="font-size:1.5rem; font-weight:700; color:var(--color-text); margin-top:0.25rem;">{{ number_format($totalAll) }}</p>
        </div>
        <div class="card p-4 flex items-center justify-between">
            <div>
                <p style="font-size:var(--font-size-xs); color:var(--color-text-subtle);">Hapus Log Lama</p>
                <p style="font-size:var(--font-size-xs); color:var(--color-text-muted); margin-top:0.125rem;">Bersihkan log usang</p>
            </div>
            <button onclick="document.getElementById('modal-clear').classList.remove('hidden')"
                    class="btn btn-ghost btn-xs" style="color:var(--color-danger-text);">
                Clear
            </button>
        </div>
    </div>

    {{-- Table --}}
    <div class="table-wrap">
        <div style="padding:1rem 1.5rem; border-bottom:1px solid var(--color-border); display:flex; align-items:center; justify-content:space-between;">
            <h2 style="font-weight:600; color:var(--color-text);">
                Daftar Log
                <span style="font-size:var(--font-size-sm); font-weight:normal; color:var(--color-text-subtle); margin-left:0.5rem;">
                    {{ number_format($logs->total()) }} total
                </span>
            </h2>
        </div>

        @if($logs->isEmpty())
            <div style="padding:4rem 0; text-align:center;">
                <svg class="w-12 h-12 mx-auto mb-3" style="color:var(--color-text-muted);" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <p style="font-size:var(--font-size-sm); color:var(--color-text-subtle);">Tidak ada log ditemukan.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="white-space:nowrap;">Waktu</th>
                            <th>User</th>
                            <th>Aksi</th>
                            <th>Tabel</th>
                            <th style="text-align:center;">Record ID</th>
                            <th>IP Address</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                            <tr>
                                <td style="color:var(--color-text-subtle); font-size:var(--font-size-xs); white-space:nowrap;">
                                    <div>{{ $log->created_at->format('d M Y') }}</div>
                                    <div style="color:var(--color-text-muted);">{{ $log->created_at->format('H:i:s') }}</div>
                                </td>
                                <td>
                                    @if($log->user)
                                        <div style="font-weight:500; color:var(--color-text); font-size:var(--font-size-xs);">{{ $log->user->name }}</div>
                                        <div style="color:var(--color-text-muted); font-size:var(--font-size-xs);">{{ $log->user->email }}</div>
                                    @else
                                        <span style="color:var(--color-text-muted); font-size:var(--font-size-xs); font-style:italic;">Sistem</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $badgeClass = match(true) {
                                            str_contains($log->action, 'create') => 'badge-success',
                                            str_contains($log->action, 'update') => 'badge-primary',
                                            str_contains($log->action, 'delete') => 'badge-danger',
                                            str_contains($log->action, 'login')  => 'badge-primary',
                                            str_contains($log->action, 'logout') => 'badge-neutral',
                                            str_contains($log->action, 'calculate') => 'badge-warning',
                                            default => 'badge-neutral',
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">
                                        {{ $log->action }}
                                    </span>
                                </td>
                                <td style="font-size:var(--font-size-xs); font-family:var(--font-mono); color:var(--color-text-subtle);">
                                    {{ $log->table_name }}
                                </td>
                                <td style="text-align:center; font-size:var(--font-size-xs); font-family:var(--font-mono); color:var(--color-text-muted);">
                                    {{ $log->record_id ?? '—' }}
                                </td>
                                <td style="font-size:var(--font-size-xs); font-family:var(--font-mono); color:var(--color-text-muted);">
                                    {{ $log->ip_address ?? '—' }}
                                </td>
                                <td>
                                    <a href="{{ route('admin.logs.show', $log) }}"
                                       class="btn btn-ghost btn-xs whitespace-nowrap">
                                        Detail →
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="padding:1rem 1.5rem; border-top:1px solid var(--color-border);">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Modal Clear Logs --}}
<div id="modal-clear" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
    <div class="card w-full max-w-sm mx-4">
        <div class="card-body">
            <h3 style="font-weight:700; color:var(--color-text); margin-bottom:0.25rem;">Hapus Log Lama</h3>
            <p style="font-size:var(--font-size-sm); color:var(--color-text-subtle); margin-bottom:1rem;">Log yang lebih tua dari jumlah hari yang ditentukan akan dihapus permanen.</p>

            <form method="POST" action="{{ route('admin.logs.clear') }}">
                @csrf @method('DELETE')
                <div class="form-group mb-4">
                    <label class="form-label">Hapus log lebih dari</label>
                    <div class="flex items-center gap-2">
                        <input type="number" name="older_than_days" value="30" min="1" max="365"
                               class="form-input w-24 text-center">
                        <span style="font-size:var(--font-size-sm); color:var(--color-text-subtle);">hari yang lalu</span>
                    </div>
                </div>
                <div class="flex gap-3 mt-4">
                    <button type="submit" class="btn flex-1" style="background-color:var(--color-danger-bg); color:var(--color-danger-text); border:1px solid var(--color-danger-text);">
                        Hapus
                    </button>
                    <button type="button"
                            onclick="document.getElementById('modal-clear').classList.add('hidden')"
                            class="btn btn-secondary flex-1">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
