@extends('layouts.main_layout')

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            @include('layouts/top_bar')
            <div class="col">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h2 class="mb-0">Detalhes do Log #{{ $log->id }}</h2>
                        <a href="{{ route('audit_log') }}" class="btn btn-sm btn-secondary">Voltar</a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Informações Básicas -->
                            <div class="col-md-6">
                                <h4>Informações Básicas</h4>
                                <table class="table table-bordered">
                                    <tr>
                                        <th>ID:</th>
                                        <td>{{ $log->id }}</td>
                                    </tr>
                                    <tr>
                                        <th>Data/Hora:</th>
                                        <td>{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Ação:</th>
                                        <td>
                                            <span
                                                class="badge bg-{{ $log->action == 'delete' ? 'danger' : ($log->action == 'create' ? 'success' : 'warning') }}">
                                                {{ $log->action }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Tipo de Visualização:</th>
                                        <td>{{ $log->view_type ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Modelo:</th>
                                        <td>{{ $log->model }}</td>
                                    </tr>
                                    <tr>
                                        <th>ID do Modelo:</th>
                                        <td>{{ $log->model_id ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Usuário:</th>
                                        <td>{{ $log->user->username ?? 'Sistema' }}</td>
                                    </tr>
                                    <tr>
                                        <th>IP:</th>
                                        <td>{{ $log->ip_address }}</td>
                                    </tr>

                                </table>
                            </div>


                        </div>
                        <!-- Dados (Old/New Values) -->
                        <div class="col-12 mt-4">
                            <div class="card">
                                <div class="card-body p-0">
                                    <div class="row g-0">
                                        @if ($log->old_values)
                                            <div class="col-md-12 mb-4">
                                                <div class="p-3 bg-light">
                                                    <h5 class="card-title mb-3">
                                                        <i class="fas fa-history me-2"></i>Valores Antigos
                                                    </h5>
                                                    <div class="json-container bg-white p-3 rounded border"
                                                        style="height: 300px; overflow-y: auto;">
                                                        <pre class="m-0"><code>{{ json_encode(json_decode($log->old_values), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        @if ($log->new_values)
                                            <div class="col-md-12">
                                                <div class="p-3 bg-light">
                                                    <h5 class="card-title mb-3">
                                                        <i class="fas fa-edit me-2"></i>Valores Novos
                                                    </h5>
                                                    <div class="json-container bg-white p-3 rounded border"
                                                        style="height: 300px; overflow-y: auto;">
                                                        <pre class="m-0"><code>{{ json_encode(json_decode($log->new_values), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
