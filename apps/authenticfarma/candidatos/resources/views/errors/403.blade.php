@extends('layout.error')

@section('title', '403 - Acceso denegado')

@section('content')
<div class="error-card">
    <div class="error-header">
        <div class="error-code">403</div>
        <h1 class="error-title">¡Acceso denegado!</h1>
    </div>
   <div class="error-body bg-light">
        <p class="error-message">
            No tienes los permisos necesarios para acceder a esta sección. 
            Si crees que esto es un error, por favor contacta con el administrador.
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
