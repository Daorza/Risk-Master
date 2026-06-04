@extends('layouts.app')
@section('title', 'Audit Logs')
@section('header', 'Audit Logs')

@section('content')
<div class="space-y-5">

    {{-- Filter --}}
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <form method="GET" class="grid grid-cols-2 lg:grid-cols-5 gap-3">

            <select name="action"
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Aksi</option>
                @foreach($actions as $action)
                    <option value="{{ $action }}" @selected(request('action') === $action)>
                        {{ $action }}
                    </option>
                @endforeach
            </select>

            <select name="table"
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Tabel</option>
                @foreach($tables as $table)
                    <option value="{{ $table }}" @selected(request('table') === $table)>
                        {{ $table }}
                    </option>
                @endforeach
            </select>

            <select name="user_id"
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua User</option>
                <option value="null" @selected(request('user_id') === 'null')>Sistem</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" @selected(request('user_id') == $user->id)>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>

            <input type="date" name="date_from" value="{{ request('date_from') }}"
                   class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">

            <div class="flex gap-2">
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                       class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition whitespace-nowrap">
                    Filter
                </button>
                <a href="{{ route('admin.logs.index') }}"
                   class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-3 py-2 rounded-lg text-sm transition">
                    Reset
                </a>
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

        <div class="bg-white rounded-xl border border-gray-200 px-5 py-4">
            <p class="text-xs text-gray-500">Hari Ini</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($totalToday) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 px-5 py-4">
            <p class="text-xs text-gray-500">7 Hari Terakhir</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">{{ number_format($totalWeek) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 px-5 py-4">
            <p class="text-xs text-gray-500">Total Log</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($totalAll) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 px-5 py-4 flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500">Hapus Log Lama</p>
                <p class="text-xs text-gray-400 mt-0.5">Bersihkan log usang</p>
            </div>
            <button onclick="document.getElementById('modal-clear').classList.remove('hidden')"
                    class="bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 px-3 py-1.5 rounded-lg text-xs font-medium transition">
                Clear
            </button>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-semibold text-gray-800">
                Daftar Log
                <span class="text-sm font-normal text-gray-400 ml-2">
                    {{ number_format($logs->total()) }} total
                </span>
            </h2>
        </div>

        @if($logs->isEmpty())
            <div class="py-16 text-center">
                <svg class="w-12 h-12 text-gray-200 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <p class="text-gray-400 text-sm">Tidak ada log ditemukan.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600 whitespace-nowrap">Waktu</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600">User</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600">Aksi</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600">Tabel</th>
                            <th class="text-center px-4 py-3 font-semibold text-gray-600">Record ID</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600">IP Address</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($logs as $log)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 text-gray-500 text-xs whitespace-nowrap">
                                    <div>{{ $log->created_at->format('d M Y') }}</div>
                                    <div class="text-gray-400">{{ $log->created_at->format('H:i:s') }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    @if($log->user)
                                        <div class="font-medium text-gray-800 text-xs">{{ $log->user->name }}</div>
                                        <div class="text-gray-400 text-xs">{{ $log->user->email }}</div>
                                    @else
                                        <span class="text-gray-400 text-xs italic">Sistem</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $actionColor = match(true) {
                                            str_contains($log->action, 'create') => 'bg-green-50 text-green-700 border-green-200',
                                            str_contains($log->action, 'update') => 'bg-blue-50 text-blue-700 border-blue-200',
                                            str_contains($log->action, 'delete') => 'bg-red-50 text-red-700 border-red-200',
                                            str_contains($log->action, 'login')  => 'bg-purple-50 text-purple-700 border-purple-200',
                                            str_contains($log->action, 'logout') => 'bg-gray-50 text-gray-600 border-gray-200',
                                            str_contains($log->action, 'calculate') => 'bg-amber-50 text-amber-700 border-amber-200',
                                            default => 'bg-gray-50 text-gray-600 border-gray-200',
                                        };
                                    @endphp
                                    <span class="text-xs px-2 py-1 rounded-full font-medium border {{ $actionColor }}">
                                        {{ $log->action }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-xs font-mono text-gray-600">
                                    {{ $log->table_name }}
                                </td>
                                <td class="px-4 py-3 text-center text-xs font-mono text-gray-500">
                                    {{ $log->record_id ?? '—' }}
                                </td>
                                <td class="px-4 py-3 text-xs font-mono text-gray-400">
                                    {{ $log->ip_address ?? '—' }}
                                </td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('admin.logs.show', $log) }}"
                                       class="text-xs text-blue-600 hover:underline whitespace-nowrap">
                                        Detail →
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-gray-100">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Modal Clear Logs --}}
<div id="modal-clear" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40">
    <div class="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-sm mx-4">
        <h3 class="font-bold text-gray-900 mb-1">Hapus Log Lama</h3>
        <p class="text-sm text-gray-500 mb-4">Log yang lebih tua dari jumlah hari yang ditentukan akan dihapus permanen.</p>

        <form method="POST" action="{{ route('admin.logs.clear') }}">
            @csrf @method('DELETE')
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Hapus log lebih dari
                </label>
                <div class="flex items-center gap-2">
                    <input type="number" name="older_than_days" value="30" min="1" max="365"
                           class="w-24 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    <span class="text-sm text-gray-500">hari yang lalu</span>
                </div>
            </div>
            <div class="flex gap-3">
                <button type="submit"
                        class="flex-1 bg-red-600 hover:bg-red-700 text-white py-2 rounded-lg text-sm font-medium transition">
                    Hapus
                </button>
                <button type="button"
                        onclick="document.getElementById('modal-clear').classList.add('hidden')"
                        class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 py-2 rounded-lg text-sm font-medium transition">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
