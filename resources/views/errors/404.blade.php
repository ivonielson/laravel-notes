@extends('layouts.main_layout')
@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col">

                @include('layouts/top_bar')
                <div class="container text-center py-5">
                    <h1 class="display-1 text-danger">404</h1>
                    <h2 class="mb-4">Página não encontrada</h2>
                    <p class="lead">Desculpe, a página que você está procurando não existe.</p>
                    <a href="{{ url('/') }}" class="btn btn-primary mt-3">
                        Voltar para a página inicial
                    </a>
                </div>


            </div>
        </div>
    </div>
@endsection
