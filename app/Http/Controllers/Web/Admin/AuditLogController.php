<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = AuditLog::with('user:id,name,email')
            ->when($request->action, fn($q) => $q->where('action', $request->action))
            ->when($request->table, fn($q) => $q->where('table_name', $request->table))
            ->when($request->user_id, fn($q) => $q->where('user_id', $request->user_id))
            ->when($request->date_from, fn($q) =>
                $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->date_to, fn($q) =>
                $q->whereDate('created_at', '<=', $request->date_to))
            ->orderBy('created_at', 'desc')
            ->paginate(25)
            ->withQueryString();

        // Untuk filter dropdown
        $actions = AuditLog::select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        $tables = AuditLog::select('table_name')
            ->distinct()
            ->orderBy('table_name')
            ->pluck('table_name');

        $users = \App\Models\User::select('id', 'name', 'email')
            ->orderBy('name')
            ->get();

        return view('admin.logs.index', compact('logs', 'actions', 'tables', 'users'));
    }

    public function show(AuditLog $log)
    {
        $log->load('user:id,name,email');

        return view('admin.logs.show', compact('log'));
    }

    public function clear(Request $request)
    {
        $request->validate([
            'older_than_days' => ['required', 'integer', 'min:1', 'max:365'],
        ]);

        $deleted = AuditLog::where('created_at', '<',
            now()->subDays($request->older_than_days))
                ->delete();

        return redirect()
            ->route('admin.logs.index')
            ->with('success', "Berhasil menghapus $deleted log yang lebih lama dari {$request->older_than_days} hari.");
    }
}
