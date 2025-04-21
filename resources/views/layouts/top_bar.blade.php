@php use Illuminate\Support\Str; @endphp

<div class="row mb-3 align-items-center">
    <!-- Logo -->
    <div class="col-md-4 d-flex align-items-center">
        <a href="{{ route('home') }}">
            <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" style="height: 40px;">
        </a>
    </div>

    <!-- Título Central -->
    <div class="col-md-4 text-center">
        <h5 class="mb-0">A simple <span class="text-warning">Laravel</span> project!</h5>
    </div>

    <!-- Dropdown do Usuário -->
    <div class="col-md-4 d-flex justify-content-end align-items-center">
        <div class="dropdown">
            <a class="btn btn-outline-secondary dropdown-toggle d-flex align-items-center" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa-solid fa-user-circle fa-lg text-secondary me-2"></i>
                {{ Str::before(session('user.username'), '@') }}
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                <li>
                    <h6 class="dropdown-header">
                        <i class="fa-solid fa-user text-primary me-1"></i>
                        {{ session('user.username') }}
                    </h6>
                </li>
                <li><hr class="dropdown-divider"></li>

                @if(session('user.role') === 'admin')
                    <li>
                        <a class="dropdown-item" href="{{ route('audit_log') }}">
                            <i class="fa-solid fa-clipboard-list me-2"></i> Logs de Auditoria
                        </a>
                    </li>
                @endif
                <li>
                    <a class="dropdown-item" href="{{ route('user_list') }}">
                        <i class="fa-solid fa-clipboard-list me-2"></i> Perfil
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('logout') }}">
                        <i class="fa-solid fa-arrow-right-from-bracket me-2 text-danger"></i> Sair
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>



<hr>

