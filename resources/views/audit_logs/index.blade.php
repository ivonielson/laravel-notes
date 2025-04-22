@extends('layouts.main_layout')
@section('styles')
    <style>
        #actionChart {
            cursor: pointer;
            transition: all 0.2s ease;
        }

        #actionChart:hover {
            opacity: 0.9;
        }

        .chart-container {
            background-color: #f8f9fa;
            border-radius: 4px;
            padding: 10px;
        }
    </style>
@endsection
@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col">
                @include('layouts/top_bar')
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
                                        <option value="null" {{ request('user_id') === 'null' ? 'selected' : '' }}>
                                            Anônimo ({{ $stats['null_user_count'] }})
                                        </option>
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

                <!-- Gráfico de Logs por Ação -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Distribuição de Logs por Ação</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container" style="position: relative; height:300px;">
                            <canvas id="actionChart"></canvas>
                        </div>
                    </div>
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
                                                    $badgeClass = match ($log->action) {
                                                        'update' => 'info',
                                                        'delete' => 'danger',
                                                        'create' => 'success',
                                                        default => 'warning',
                                                    };
                                                @endphp
                                                <span class="badge bg-{{ $badgeClass }}">
                                                    {{ $log->action }}
                                                </span>
                                            </td>
                                            <td>{{ class_basename($log->model) }}</td>
                                            <td>{{ $log->user->username ?? 'Anônimo' }}</td>
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

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('actionChart').getContext('2d');

        // Preparar dados do gráfico
        const actionData = @json(
            $stats['actions']->map(function ($item) {
                return [
                    'action' => ucfirst($item->action),
                    'total' => $item->total,
                ];
            }));

        // Ordenar por total (opcional)
        actionData.sort((a, b) => b.total - a.total);

        // Cores para as barras
        const backgroundColors = actionData.map(item => {
            switch (item.action.toLowerCase()) {
                case 'create':
                    return 'rgba(40, 167, 69, 0.7)';
                case 'update':
                    return 'rgba(23, 162, 184, 0.7)';
                case 'delete':
                    return 'rgba(220, 53, 69, 0.7)';
                default:
                    return 'rgba(108, 117, 125, 0.7)';
            }
        });

        const borderColors = backgroundColors.map(color => color.replace('0.7', '1'));

        // Criar o gráfico
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: actionData.map(item => item.action),
                datasets: [{
                    label: 'Total de Logs',
                    data: actionData.map(item => item.total),
                    backgroundColor: backgroundColors,
                    borderColor: borderColors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.dataset.label}: ${context.raw}`;
                            }
                        }
                    },
                    legend: {
                        display: false
                    }
                },
                onClick: (e, elements) => {
                    if (elements.length > 0) {
                        const index = elements[0].index;
                        const action = actionData[index].action.toLowerCase();
                        window.location.href = `{{ route('audit_log') }}?action=${action}`;
                    }
                }
            }
        });
    });
</script>
@endsection
