@extends('layouts.app')
@section('title', 'Detail Log')
@section('header', 'Detail Log #' . $log->id)

@section('content')
<div class="max-w-3xl space-y-5">

    {{-- Back --}}
    <a href="{{ route('admin.logs.index') }}"
       class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700">
        ← Kembali ke Logs
    </a>

    {{-- Info utama --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-gray-400 text-xs mb-1">Waktu</p>
                <p class="font-medium text-gray-800">{{ $log->created_at->format('d M Y, H:i:s') }}</p>
                <p class="text-xs text-gray-400">{{ $log->created_at->diffForHumans() }}</p>
            </div>
            <div>
                <p class="text-gray-400 text-xs mb-1">User</p>
                @if($log->user)
                    <p class="font-medium text-gray-800">{{ $log->user->name }}</p>
                    <p class="text-xs text-gray-400">{{ $log->user->email }}</p>
                @else
                    <p class="text-gray-400 italic text-sm">Sistem</p>
                @endif
            </div>
            <div>
                <p class="text-gray-400 text-xs mb-1">Aksi</p>
                @php
                    $actionColor = match(true) {
                        str_contains($log->action, 'create')   => 'bg-green-50 text-green-700 border-green-200',
                        str_contains($log->action, 'update')   => 'bg-blue-50 text-blue-700 border-blue-200',
                        str_contains($log->action, 'delete')   => 'bg-red-50 text-red-700 border-red-200',
                        str_contains($log->action, 'login')    => 'bg-purple-50 text-purple-700 border-purple-200',
                        str_contains($log->action, 'logout')   => 'bg-gray-50 text-gray-600 border-gray-200',
                        str_contains($log->action, 'calculate')=> 'bg-amber-50 text-amber-700 border-amber-200',
                        default => 'bg-gray-50 text-gray-600 border-gray-200',
                    };
                @endphp
                <span class="text-xs px-2.5 py-1 rounded-full font-medium border {{ $actionColor }}">
                    {{ $log->action }}
                </span>
            </div>
            <div>
                <p class="text-gray-400 text-xs mb-1">IP Address</p>
                <p class="font-mono text-sm text-gray-700">{{ $log->ip_address ?? '—' }}</p>
            </div>
            <div>
                <p class="text-gray-400 text-xs mb-1">Tabel</p>
                <p class="font-mono text-sm text-gray-700">{{ $log->table_name }}</p>
            </div>
            <div>
                <p class="text-gray-400 text-xs mb-1">Record ID</p>
                <p class="font-mono text-sm text-gray-700">{{ $log->record_id ?? '—' }}</p>
            </div>
        </div>
    </div>

    {{-- Old data --}}
    @if($log->old_data)
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-100 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-red-400"></span>
                <h3 class="font-semibold text-gray-800 text-sm">Data Sebelum</h3>
            </div>
            <div class="p-5">
                <pre class="text-xs text-gray-700 bg-gray-50 rounded-lg p-4 overflow-x-auto leading-relaxed">{{ json_encode($log->old_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
        </div>
    @endif

    {{-- New data --}}
    @if($log->new_data)
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-100 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-green-400"></span>
                <h3 class="font-semibold text-gray-800 text-sm">Data Sesudah</h3>
            </div>
            <div class="p-5">
                <pre class="text-xs text-gray-700 bg-gray-50 rounded-lg p-4 overflow-x-auto leading-relaxed">{{ json_encode($log->new_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
        </div>
    @endif

    {{-- Tidak ada perubahan data --}}
    @if(!$log->old_data && !$log->new_data)
        <div class="bg-white rounded-xl border border-gray-200 px-6 py-8 text-center">
            <p class="text-gray-400 text-sm">Tidak ada data perubahan yang dicatat untuk log ini.</p>
        </div>
    @endif

</div>
@endsection
