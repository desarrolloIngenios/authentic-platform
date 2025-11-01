@extends('layout.error')

@section('title', '502 - Error del servidor')

@section('content')
<div class="error-card">
    <div class="error-header">
    <div class="error-code">502</div>
    <h1 class="error-title">¡El servidor está tardando más de lo esperado!</h1>
    </div>
    <div class="error-body bg-light">
        <p class="error-message">
            El servidor está procesando tu solicitud, pero está tomando más tiempo de lo habitual.<br>
            Si subiste un archivo grande o el sistema está ocupado, espera unos segundos y vuelve a intentarlo.
        </p>
        <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center mb-4">
            <a href="{{ url('/') }}" class="btn-custom btn-primary-gradient">
                <i class="la la-home me-2"></i>
                <span>Volver al inicio</span>
            </a>
            <a href="#" onclick="window.location.reload()" class="btn-custom btn-outline">
                <i class="la la-refresh me-2"></i>
                <span>Recargar página</span>
            </a>
        </div>
        <p class="text-muted small mt-3">
            Si el problema persiste, por favor contacta con soporte técnico.
        </p>
    </div>
</div>
@endsection
