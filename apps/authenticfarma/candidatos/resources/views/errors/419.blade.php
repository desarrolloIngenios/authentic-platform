@extends('layout.error')

@section('title', '419 - Sesión expirada')

@section('content')
<div class="error-card">
    <div class="error-header">
        <div class="error-code">419</div>
        <h1 class="error-title">¡Sesión expirada!</h1>
    </div>
   <div class="error-body bg-light">
        <p class="error-message">
            Por seguridad, tu sesión ha finalizado o ha expirado.<br>
            Por favor, inicia sesión nuevamente para continuar usando la plataforma.
        </p>
        <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
            <a href="{{ route('login') }}" class="btn-custom btn-primary-gradient">
                <i class="la la-sign-in-alt me-2"></i>
                <span>Ir al login</span>
            </a>
            <a href="{{ url('/') }}" class="btn-custom btn-outline">
                <i class="la la-home me-2"></i>
                <span>Volver al inicio</span>
            </a>
        </div>
    </div>
</div>
<script>
    setTimeout(function() {
        window.location.href = "{{ route('login') }}";
    }, 6000);
</script>
@endsection
