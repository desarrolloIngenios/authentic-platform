@extends('layout.auth')

@section('title', 'Recuperar contraseña')

@section('content')
<div class="top-logo d-none d-md-block">
    <a href="https://www.authenticfarma.com/"><img src="{{ asset("images/logo-authenticfarma-white.png") }}" alt="Logo Authentic" /></a>
</div>

<div class="login-wrapper">
    <div class="login-card text-center">
        <h4>Restablecer Contraseña</h4>
        <p class="text-muted mb-4">Ingresa tu correo electrónico y te enviaremos instrucciones para restablecer tu contraseña.</p>

        <form action="{{ route('password.email') }}" method="POST">
            @csrf
            
            <div class="form-group text-left">
                <input type="email" class="form-control" name="email" id="email" placeholder="Correo electrónico" required />
            </div>

            <button type="submit" class="btn btn-primary btn-block mt-3">Enviar enlace</button>

            <div class="mt-3">
                <a href="{{ route('login') }}" class="text-muted">Volver al inicio de sesión</a>
            </div>
        </form>
    </div>
</div>
@endsection
