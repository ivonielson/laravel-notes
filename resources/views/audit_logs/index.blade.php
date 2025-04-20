@extends('layouts.main_layout')

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col">
                @include('top_bar')
                <!-- Filtros -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" action="{{ route('audit_log') }}">
                            <div class="row">
                                <div class="col-md-3">
                                    <label>Ação</label>
                                    <select name="action" class="form-control">
                                        <option value="">Todas</option>
                                        @foreach ($stats['actions'] as $action)
                                            <option value="{{ $action->action }}"
                                                {{ request('action') == $action->action ? 'selected' : '' }}>
                                                {{ ucfirst($action->action) }} ({{ $action->total }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label>Modelo</label>
                                    <select name="model" class="form-control">
                                        <option value="">Todos</option>
                                        @foreach ($stats['models'] as $model)
                                            <option value="{{ $model->model }}"
                                                {{ request('model') == $model->model ? 'selected' : '' }}>
                                                {{ class_basename($model->model) }} ({{ $model->total }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label>Usuário</label>
                                    <select name="user_id" class="form-control">
                                        <option value="">Todos</option>
                                        @foreach ($stats['users'] as $user)
                                            <option value="{{ $user->id }}"
                                                {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->username }} ({{ $user->audit_logs_count }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 d-flex align-items-end gap-2">
                                    <button type="submit" class="btn btn-primary">Filtrar</button>
                                    <a href="{{ route('audit_log') }}" class="btn btn-secondary">Limpar</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Estatísticas -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <a href="{{ route('audit_log') }}" class="text-decoration-none">

                            <div class="card text-white bg-primary">
                                <div class="card-body">
                                    <h5 class="card-title">Total de Logs</h5>
                                    <p class="card-text display-4">{{ $stats['total'] }}</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    @foreach ($stats['actions']->take(10) as $action)
                        @php
                            $isActive = request('action') === $action->action;
                            $query = request()->except('page'); // remove paginação do querystring
                            $query['action'] = $action->action; // aplica filtro do card clicado
                        @endphp
                        <div class="col-md-3">
                            <a href="{{ route('audit_log', $query) }}" class="text-decoration-none">
                                <div class="card {{ $isActive ? 'border border-primary shadow' : '' }}">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ ucfirst($action->action) }}</h5>
                                        <p class="card-text display-4">{{ $action->total }}</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach

                </div>

                <!-- Tabela de Logs -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Data/Hora</th>
                                        <th>Ação</th>
                                        <th>Modelo</th>
                                        <th>Usuário</th>
                                        <th>IP</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($logs as $log)
                                        <tr>
                                            <td>{{ $log->id }}</td>
                                            <td>{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                                            <td>
                                                @php
                                                    $badgeClass = match($log->action) {
                                                        'update' => 'info',
                                                        'delete' => 'danger',
                                                        'create' => 'success',
                                                        default => 'warning'
                                                    };
                                                @endphp
                                                <span class="badge bg-{{ $badgeClass }}">
                                                    {{ $log->action }}
                                                </span>
                                            </td>
                                            <td>{{ class_basename($log->model) }}</td>
                                            <td>{{ $log->user->username ?? 'Sistema' }}</td>
                                            <td>{{ $log->ip_address ?? '-' }}</td>
                                            <td>
                                                <a href="{{ route('audit_log_show', $log->id) }}"
                                                    class="btn btn-sm btn-info">
                                                    Detalhes
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">Nenhum log encontrado.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginação -->
                        <div class="d-flex justify-content-center">
                            {{ $logs->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
