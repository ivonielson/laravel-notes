@extends('layouts.main_layout')

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col">

                @include('layouts/top_bar')

                    <div class="card p-4">
                        @if ($isAdmin)
                            <h4 class="mb-4">User Management <span class="badge bg-dark">Admin</span></h4>
                        @else
                            <h4 class="mb-4">My Profile</h4>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Username</th>
                                        <th>Role</th>
                                        <th>Last Login</th>
                                        @if ($isAdmin)
                                            <th>Actions</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $user)
                                        <tr>
                                            <td>{{ $user['id'] }}</td>
                                            <td>{{ $user['username'] }}</td>
                                            <td>
                                                <span
                                                    class="badge {{ $user['role'] === 'admin' ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ ucfirst($user['role']) }}
                                                </span>
                                            </td>
                                            <td>
                                                {{ $user['last_login'] ? \Carbon\Carbon::parse($user['last_login'])->format('d/m/Y H:i') : 'Never' }}
                                            </td>
                                            @if ($isAdmin)
                                                <td>
                                                    <a href="{{ route('users.edit', ['id' => Crypt::encrypt($user['id'])]) }}"
                                                        class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>


                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if (!$isAdmin)
                            <div class="mt-3 text-end">
                                {{-- <a href="{{ route('home', ['id' => encrypt(session('user.id'))]) }}" --}}
                                <a href="{{ route('users.edit', ['id' => Crypt::encrypt($user['id'])]) }}"
                                    class="btn btn-primary">
                                    <i class="fas fa-user-edit"></i> Edit My Profile
                                </a>
                            </div>
                        @endif
                    </div>

            </div>
        </div>
    </div>
@endsection
