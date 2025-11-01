@extends('layout.error')

@section('title', $errorCode)

@section('content')
<div class="error-card">
    <div class="error-header">
        <div class="error-code">{{ $errorCode }}</div>
        <h1 class="error-title">{{ $errorMessage }}</h1>
    </div>
    <div class="error-body">
        <p class="error-message">{{ $errorDescription ?? 'Ha ocurrido un error inesperado.' }}</p>
        <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
            <a href="{{ url('/') }}" class="btn-custom btn-primary-gradient">
                <i class="la la-home"></i>
                <span>Volver al inicio</span>
            </a>
            <a href="#" onclick="history.back()" class="btn-custom btn-outline">
                <i class="la la-arrow-left"></i>
                <span>Regresar</span>
            </a>
        </div>
    </div>
</div>
@endsection
