@extends('layouts.main_layout')
@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-sm-8">
                <div class="card p-5">

                    <!-- logo -->
                    <div class="text-center p-3">
                        <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" style="height: 40px;">

                    </div>

                    <!-- form -->
                    <div class="row justify-content-center">
                        <div class="col-md-10 col-12">
                            <form action="{{ route('register.submit') }}" method="post" novalidate>
                                @csrf

                                <!-- Username/Email Field -->
                                <div class="mb-3">
                                    <label for="text_username" class="form-label">Email</label>
                                    <input type="email" class="form-control bg-dark text-info" name="text_username"
                                        value="{{ old('text_username') }}" required>
                                    @error('text_username')
                                        <div class="text-danger">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- Password Field -->
                                <div class="mb-3">
                                    <label for="text_password" class="form-label">Password</label>
                                    <input type="password" class="form-control bg-dark text-info" name="text_password"
                                        required minlength="6" maxlength="16">
                                    @error('text_password')
                                        <div class="text-danger">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                    <small class="text-secondary">(6-16 characters)</small>
                                </div>

                                <!-- Password Confirmation Field -->
                                <div class="mb-3">
                                    <label for="text_password_confirmation" class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control bg-dark text-info"
                                        name="text_password_confirmation" required minlength="6" maxlength="16">
                                    @error('text_password_confirmation')
                                        <div class="text-danger">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- Submit Button -->
                                <div class="mb-3">
                                    <button type="submit" class="btn btn-secondary w-100">REGISTER</button>
                                </div>

                                <!-- Login Link -->
                                <div class="text-center mt-3">
                                    <a href="{{ route('login') }}" class="text-secondary">
                                        Already have an account? Login here
                                    </a>
                                </div>
                            </form>

                            <!-- Error Messages -->
                            @if ($errors->any())
                                <div class="alert alert-danger mt-3">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if (session('error'))
                                <div class="alert alert-danger text-center mt-3">
                                    {{ session('error') }}
                                </div>
                            @endif

                            @if (session('success'))
                                <div class="alert alert-success text-center mt-3">
                                    {{ session('success') }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- copyright -->
                    <div class="text-center text-secondary mt-3">
                        <small>&copy; <?= date('Y') ?> Notes</small>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
