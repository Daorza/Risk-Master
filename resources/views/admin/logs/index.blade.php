@extends('layouts.app')
@section('title', 'Audit Logs')
@section('header', 'Audit Logs')

@section('content')
<div style="display:flex; flex-direction:column; gap:1.25rem;">

    {{-- Filter --}}
    <div class="card" style="padding:1rem;">
        <form method="GET" style="display:flex; flex-wrap:wrap; gap:0.5rem; align-items:flex-end;">
            <select name="action" class="form-input" style="flex:1; min-width:9rem;">
                <option value="">Semua Aksi</option>
                @foreach($actions as $action)
                    <option value="{{ $action }}" @selected(request('action') === $action)>
                        {{ $action }}
                    </option>
                @endforeach
            </select>

            <select name="table" class="form-input" style="flex:1; min-width:9rem;">
                <option value="">Semua Tabel</option>
                @foreach($tables as $table)
                    <option value="{{ $table }}" @selected(request('table') === $table)>
                        {{ $table }}
                    </option>
                @endforeach
            </select>

            <select name="user_id" class="form-input" style="flex:1; min-width:9rem;">
                <option value="">Semua User</option>
                <option value="null" @selected(request('user_id') === 'null')>Sistem</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" @selected(request('user_id') == $user->id)>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>

            <input type="date" name="date_from" value="{{ request('date_from') }}"
                   class="form-input" style="flex:1; min-width:8rem;">
            <input type="date" name="date_to" value="{{ request('date_to') }}"
                   class="form-input" style="flex:1; min-width:8rem;">

            <div style="display:flex; gap:0.5rem; flex-shrink:0;">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('admin.logs.index') }}" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>

    {{-- Stats --}}
    <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:1rem;">
        @php
            $totalToday = \App\Models\AuditLog::whereDate('created_at', today())->count();
            $totalWeek  = \App\Models\AuditLog::where('created_at', '>=', now()->subDays(7))->count();
            $totalAll   = \App\Models\AuditLog::count();
        @endphp

        <div class="card" style="padding:1.25rem;">
            <p style="font-size:var(--font-size-xs); color:var(--color-text-subtle); text-transform:uppercase; letter-spacing:0.06em;">Hari Ini</p>
            <p style="font-size:1.75rem; font-weight:700; color:var(--color-text); margin-top:0.375rem; font-variant-numeric:tabular-nums;">
                {{ number_format($totalToday) }}
            </p>
        </div>
        <div class="card" style="padding:1.25rem;">
            <p style="font-size:var(--font-size-xs); color:var(--color-text-subtle); text-transform:uppercase; letter-spacing:0.06em;">7 Hari Terakhir</p>
            <p style="font-size:1.75rem; font-weight:700; color:var(--color-accent-400); margin-top:0.375rem; font-variant-numeric:tabular-nums;">
                {{ number_format($totalWeek) }}
            </p>
        </div>
        <div class="card" style="padding:1.25rem;">
            <p style="font-size:var(--font-size-xs); color:var(--color-text-subtle); text-transform:uppercase; letter-spacing:0.06em;">Total Log</p>
            <p style="font-size:1.75rem; font-weight:700; color:var(--color-text); margin-top:0.375rem; font-variant-numeric:tabular-nums;">
                {{ number_format($totalAll) }}
            </p>
        </div>
        <div class="card" style="padding:1.25rem; display:flex; align-items:center; justify-content:space-between;">
            <div>
                <p style="font-size:var(--font-size-xs); color:var(--color-text-subtle); text-transform:uppercase; letter-spacing:0.06em;">Bersihkan</p>
                <p style="font-size:var(--font-size-xs); color:var(--color-text-muted); margin-top:0.25rem;">Hapus log lama</p>
            </div>
            <button onclick="document.getElementById('modal-clear').style.display='flex'"
                    class="btn btn-ghost btn-xs" style="color:var(--color-danger-text);">
                Clear
            </button>
        </div>
    </div>

    {{-- Table — max-height + sticky header + scroll --}}
    <div class="table-wrap">
        <div style="padding:1rem 1.5rem; border-bottom:1px solid var(--color-border); display:flex; align-items:center; justify-content:space-between;">
            <h2 style="font-weight:600; color:var(--color-text); font-size:var(--font-size-sm);">
                Daftar Log
                <span style="font-weight:400; color:var(--color-text-subtle); margin-left:0.5rem;">
                    {{ number_format($logs->total()) }} total
                </span>
            </h2>
        </div>

        @if($logs->isEmpty())
            <div style="padding:4rem 0; text-align:center;">
                <svg width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"
                     style="color:var(--color-border); margin:0 auto 0.75rem; display:block;">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <p style="font-size:var(--font-size-sm); color:var(--color-text-subtle);">Tidak ada log ditemukan.</p>
            </div>
        @else
            {{-- table-scroll: max-height + sticky thead --}}
            <div class="table-scroll">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="white-space:nowrap; min-width:7rem;">Waktu</th>
                            <th style="min-width:9rem;">User</th>
                            <th style="min-width:8rem;">Aksi</th>
                            <th style="min-width:6rem;">Tabel</th>
                            <th style="text-align:center; min-width:5rem;">Record ID</th>
                            <th style="min-width:8rem;">IP Address</th>
                            <th style="min-width:4rem;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                            <tr>
                                <td style="white-space:nowrap;">
                                    <div style="font-size:var(--font-size-xs); color:var(--color-text-subtle);">
                                        {{ $log->created_at->format('d M Y') }}
                                    </div>
                                    <div style="font-size:var(--font-size-xs); color:var(--color-text-muted); font-family:var(--font-mono);">
                                        {{ $log->created_at->format('H:i:s') }}
                                    </div>
                                </td>
                                <td>
                                    @if($log->user)
                                        <div style="font-weight:500; font-size:var(--font-size-xs); color:var(--color-text);">{{ $log->user->name }}</div>
                                        <div style="font-size:var(--font-size-xs); color:var(--color-text-muted);">{{ $log->user->email }}</div>
                                    @else
                                        <span style="font-size:var(--font-size-xs); color:var(--color-text-muted); font-style:italic;">Sistem</span>
                                    @endif
                                </td>
                                <td>
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
                                    <span class="badge {{ $bc }}" style="white-space:nowrap;">{{ $log->action }}</span>
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
                                       class="btn btn-ghost btn-xs" style="white-space:nowrap;">
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

{{-- Modal Clear Logs — pakai style display flex/none, bukan hidden class --}}
<div id="modal-clear"
     style="display:none; position:fixed; inset:0; z-index:50;
            align-items:center; justify-content:center;
            background:oklch(0% 0 0 / 0.55); backdrop-filter:blur(4px);">
    <div class="card" style="width:100%; max-width:24rem; margin:1rem;">
        <div class="card-body">
            <h3 style="font-weight:700; color:var(--color-text); margin-bottom:0.25rem;">Hapus Log Lama</h3>
            <p style="font-size:var(--font-size-sm); color:var(--color-text-subtle); margin-bottom:1.25rem;">
                Log yang lebih tua dari jumlah hari yang ditentukan akan dihapus permanen.
            </p>
            <form method="POST" action="{{ route('admin.logs.clear') }}"
                  style="display:flex; flex-direction:column; gap:1rem;">
                @csrf @method('DELETE')
                <div class="form-group">
                    <label class="form-label">Hapus log lebih dari</label>
                    <div style="display:flex; align-items:center; gap:0.75rem;">
                        <input type="number" name="older_than_days" value="30" min="1" max="365"
                               class="form-input" style="width:6rem; text-align:center;">
                        <span style="font-size:var(--font-size-sm); color:var(--color-text-subtle);">hari yang lalu</span>
                    </div>
                </div>
                <div style="display:flex; gap:0.75rem;">
                    <button type="submit" class="btn btn-danger" style="flex:1;">Hapus</button>
                    <button type="button"
                            onclick="document.getElementById('modal-clear').style.display='none'"
                            class="btn btn-secondary" style="flex:1;">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
