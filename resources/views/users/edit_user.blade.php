@extends('layouts.main_layout')

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col">

                @include('layouts.top_bar')

                <!-- label and cancel -->
                <div class="row">
                    <div class="col">
                        <p class="display-6 mb-0">EDIT USER</p>
                    </div>
                    <div class="col text-end">
                        <a href="{{ route('user_list') }}" class="btn btn-outline-danger">
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    </div>
                </div>

                <!-- form -->
                <form action="{{ route('editUserSubmit') }}" method="post">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ Crypt::encrypt($user->id) }}">

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <!-- Email -->
                            <div class="mb-3">
                                <label for="text_username" class="form-label">Email</label>
                                <input type="email" class="form-control" value="{{ $user->username }}" readonly disabled>


                                @error('text_username')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- New Password -->
                            <div class="mb-3">
                                <label for="text_password" class="form-label">Nova Senha (deixe em branco para
                                    manter)</label>
                                <input type="password" class="form-control bg-primary text-white" name="text_password">
                                @error('text_password')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div class="mb-3">
                                <label for="text_password_confirmation" class="form-label">Confirmar Nova Senha</label>
                                <input type="password" class="form-control bg-primary text-white"
                                    name="text_password_confirmation">
                                @error('text_password_confirmation')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Role (apenas se for admin) -->
                            @if ($isAdmin)
                                <div class="mb-3">
                                    <label class="form-label">Função</label>
                                    <select name="text_role" class="form-select bg-primary text-white">
                                        <option value="usuario" {{ $user->role == 'usuario' ? 'selected' : '' }}>Usuário
                                        </option>
                                        <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Administrador
                                        </option>
                                    </select>
                                </div>
                            @endif

                            <!-- Buttons -->
                            <div class="text-end mt-4">
                                <a href="{{ route('user_list') }}" class="btn btn-primary px-5">
                                    <i class="fa-solid fa-ban me-2"></i>Cancelar
                                </a>
                                <button type="submit" class="btn btn-secondary px-5">
                                    <i class="fa-regular fa-circle-check me-2"></i>Atualizar
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection
