@extends('layout.error')

@section('title', '404 - Página no encontrada')

@section('content')
<div class="error-card">
    <div class="error-header">
        <div class="error-code">404</div>
        <h1 class="error-title">¡Página no encontrada!</h1>
    </div>
    <div class="error-body bg-light">
        <p class="error-message">
            La página que estás buscando no existe o ha sido movida a otra ubicación.
            Verifica la URL o utiliza los enlaces de navegación del sitio.
        </p>
        <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
            <a href="{{ url('/') }}" class="btn-custom btn-primary-gradient">
                <i class="la la-home me-2"></i>
                <span>Volver al inicio</span>
            </a>
            <a href="#" onclick="history.back()" class="btn-custom btn-outline">
                <i class="la la-arrow-left me-2"></i>
                <span>Regresar</span>
            </a>
        </div>
    </div>
</div>
@endsection
