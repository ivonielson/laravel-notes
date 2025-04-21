<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\AuditLogger;

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

        if ($userId === 'null') {
            $query->whereNull('user_id');
        } elseif ($userId) {
            $query->where('user_id', $userId);
        }

        // Paginação
        $logs = $query->paginate(10);

        // Estatísticas
        $stats = [
            'total' => AuditLog::count(),
            'actions' => AuditLog::select('action', DB::raw('count(*) as total'))
                ->groupBy('action')
                ->orderBy('total', 'desc')
                ->get(),
            'models' => AuditLog::select('model', DB::raw('count(*) as total'))
                ->groupBy('model')
                ->get(),
            'users' => $this->getUsersWithLogs(),
            'null_user_count' => AuditLog::whereNull('user_id')->count()
        ];

        AuditLogger::logCollectionView(
            AuditLog::class,
            $logs->count()
        );

        return view('audit_logs.index', compact('logs', 'stats'));
    }

    public function show($id)
    {
        $log = AuditLog::with('user')->findOrFail($id);
        AuditLogger::log('view', AuditLog::class, $log->id, null, [
            'log_id_visualizado' => $log->id
        ]);

        return view('audit_logs.show', compact('log'));
    }

    /**
     * Obtém usuários com logs e contagem, incluindo a opção para registros sem usuário
     */
    protected function getUsersWithLogs()
    {
        return User::select('users.id', 'users.username')
            ->join('audit_logs', 'users.id', '=', 'audit_logs.user_id')
            ->selectRaw('users.id, users.username, count(audit_logs.id) as audit_logs_count')
            ->groupBy('users.id', 'users.username')
            ->orderBy('audit_logs_count', 'desc')
            ->take(5)
            ->get();
    }
}
