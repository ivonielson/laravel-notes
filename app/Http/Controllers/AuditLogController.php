<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        // Filtros
        $action = $request->input('action');
        $model = $request->input('model');
        $userId = $request->input('user_id');

        // Query base
        $query = AuditLog::with('user')
            ->orderBy('created_at', 'desc');

        // Aplicar filtros
        if ($action) {
            $query->where('action', $action);
        }

        if ($model) {
            $query->where('model', $model);
        }

        if ($userId) {
            $query->where('user_id', $userId);
        }

        // Paginação
        $logs = $query->paginate(10);

        // Estatísticas
        $stats = [
            'total' => AuditLog::count(),
            'actions' => AuditLog::select('action', DB::raw('count(*) as total'))
                ->groupBy('action')
                ->get(),
            'models' => AuditLog::select('model', DB::raw('count(*) as total'))
                ->groupBy('model')
                ->get(),
            'users' => User::has('auditLogs')
                ->withCount('auditLogs')
                ->orderBy('audit_logs_count', 'desc')
                ->take(5)
                ->get()
        ];

        return view('audit_logs.index', compact('logs', 'stats'));
    }

    public function show($id)
    {
        $log = AuditLog::with('user')->findOrFail($id);

        return view('audit_logs.show', compact('log'));
    }
}
