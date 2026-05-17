<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')->latest('created_at');

        if ($action = $request->get('action')) {
            $query->where('action', $action);
        }

        if ($from = $request->get('from')) {
            $query->whereDate('created_at', '>=', $from);
        }

        if ($to = $request->get('to')) {
            $query->whereDate('created_at', '<=', $to);
        }

        $logs    = $query->paginate(20)->withQueryString();
        $actions = ActivityLog::distinct()->pluck('action')->sort()->values();

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'data' => $logs]);
        }

        return view('admin.logs', compact('logs', 'actions'));
    }

    public function export(Request $request)
    {
        $logs = ActivityLog::with('user')->latest()->get();

        $csv = "ID,Aksi,Tipe Entitas,ID Entitas,Deskripsi,IP,Waktu,Admin\n";

        foreach ($logs as $log) {
            $csv .= implode(',', [
                $log->id,
                $log->action,
                $log->entity_type ?? '',
                $log->entity_id ?? '',
                '"' . str_replace('"', '""', $log->description ?? '') . '"',
                $log->ip_address ?? '',
                $log->created_at->format('Y-m-d H:i:s'),
                '"' . ($log->user?->name ?? 'System') . '"',
            ]) . "\n";
        }

        return response($csv, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="activity-logs-' . now()->format('Ymd-His') . '.csv"',
        ]);
    }
}