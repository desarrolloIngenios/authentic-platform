@extends('layout.auth')

@section('title', 'Restablecer Contraseña')

@section('content')
<div class="top-logo d-none d-md-block">
    <a href="https://www.authenticfarma.com/"><img src="{{ asset("images/logo-authenticfarma-white.png") }}" alt="Logo Authentic" /></a>
</div>

<div class="login-wrapper">
    <div class="login-card text-center">
        <h4>Nueva Contraseña</h4>
        <p class="text-muted mb-4">Por favor ingresa tu nueva contraseña.</p>

        <form action="{{ route('password.update') }}" method="POST">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            
            <div class="form-group text-left">
                <input type="email" class="form-control" name="email" id="email" placeholder="Correo electrónico" required />
            </div>

            <div class="form-group text-left">
                <input type="password" class="form-control" name="password" id="password" placeholder="Nueva contraseña" required />
            </div>

            <div class="form-group text-left">
                <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" placeholder="Confirmar nueva contraseña" required />
            </div>

            <button type="submit" class="btn btn-primary btn-block mt-3">Actualizar Contraseña</button>
        </form>
    </div>
</div>
@endsection
