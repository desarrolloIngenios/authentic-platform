@extends('layout.auth')

@section('title', $vacante->cargo->nombre)

@section('styles')
<style>
    .company-logo {
        width: 50px;
        height: 50px;
        object-fit: contain;
    }
    .job-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        background: #f8fafc;
        padding: 1.5rem;
        border-radius: 12px;
        margin: 1.5rem 0;
    }
    .meta-item {
        display: flex;
        align-items: center;
        background: white;
        padding: 0.75rem 1rem;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        min-width: 220px;
        font-size: 0.95rem;
        font-weight: 500;
        color: #4a5568;
    }
    .meta-item i {
        font-size: 1.3rem;
        color: #00a86b;
        margin-right: 0.5rem;
    }
    .tags-container {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 0.75rem;
    }
    .tag {
        background: #f3f4f6;
        color: #4b5563;
        padding: 5px 14px;
        border-radius: 20px;
        font-size: 0.85rem;
        border: 1px solid #e5e7eb;
    }
    .tag:hover {
        background: #00a86b;
        color: white;
        border-color: #00a86b;
    }
    .apply-section {
        text-align: center;
        padding: 2rem;
        background: linear-gradient(to bottom, #f8fafc, #ffffff);
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        margin-top: 2rem;
    }
    .apply-button {
        background: linear-gradient(45deg, #00a86b, #00bf77);
        color: white;
        padding: 0.75rem 2rem;
        font-weight: 600;
        border-radius: 8px;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(0,168,107,0.2);
    }
    .apply-button:hover {
        transform: translateY(-2px);
        background: linear-gradient(45deg, #009960, #00a86b);
        box-shadow: 0 6px 16px rgba(0,168,107,0.3);
        color: white;
    }
</style>
@endsection

@section('content')
<div class="container my-5">
    <div class="card shadow-sm border-0 rounded-lg">
        <div class="card-body p-4">

            {{-- Encabezado --}}
            <div class="d-flex align-items-center justify-content-center mb-4 flex-wrap">
                <div class="mr-4 mb-3">
                    <div class="bg-white p-3 rounded shadow-sm">
                        @if($vacante->empresa && $vacante->empresa->logo)
                            <img src="{{ asset('storage/'.$vacante->empresa->logo) }}" width="150" alt="Logo empresa" class="company-logo">
                        @else
                            <img  src="{{ asset('images/confidencial.png') }}" width="150" alt="Logo por defecto" class="company-logo">
                        @endif
                    </div>
                </div>
                <div>
                    <h2 class="mb-1">{{ $vacante->cargo->nombre ?? 'Sin cargo' }}</h2>
                    <p class="text-muted mb-0">{{ $vacante->empresa->razon_social ?? 'Empresa Confidencial' }}</p>
                    <div class="meta-item"><i class="la la-map-marker"></i> {{ $vacante->ciudad->nombre ?? 'Ubicación no especificada' }}</div>
                    @if($vacante->rangoSalarial)
                    <div class="meta-item"><i class="la la-money"></i> {{ number_format($vacante->rangoSalarial->minimo, 0, ',', '.') }} - {{ number_format($vacante->rangoSalarial->maximo, 0, ',', '.') }}</div>
                    @endif
                    @if($vacante->tiempoExperiencia)
                    <div class="meta-item"><i class="la la-clock-o"></i> {{ $vacante->tiempoExperiencia->nombre }}</div>
                    @endif
                </div>
            </div>

            
            <hr>
            {{-- Descripción --}}
            <div class="mb-4">
                <div class="row d-flex justify-content-between align-items-center">
                    <h5 class="font-weight-bold text-center">Descripción del cargo</h5>
                    @if($vacante->descripcion)
                    <p class="text-muted">{!! nl2br(e($vacante->descripcion)) !!}</p>
                    @endif
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-3">
                         @if($vacante->sectores->count() > 0)
                            <h5 class="font-weight-bold mt-4">Sectores</h5>
                            <div class="tags-container">
                                @foreach($vacante->sectores as $sector)
                                    <span class="tag">{{ $sector->nombre }}</span>
                                @endforeach
                            </div>
                         @endif
                    </div>
                    <div class="col-md-3">
                        @if($vacante->areas->count() > 0)
                            <h5 class="font-weight-bold mt-4">Áreas</h5>
                            <div class="tags-container">
                                @foreach($vacante->areas as $area)
                                    <span class="tag">{{ $area->nombre }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <div class="col-md-3">
                         @if($vacante->idioma)
                            <h5 class="font-weight-bold mt-4">Idioma requerido</h5>
                            <p class="text-muted">{{ $vacante->idioma->nombre }} - {{ $vacante->nivelIdioma->nombre ?? 'Nivel no especificado' }}</p>
                        @endif
                    </div>
                    <div class="col-md-3">
                        @if($vacante->nivelEducacion)
                            <h5 class="font-weight-bold mt-4">Nivel de educación requerido</h5>
                            <p class="text-muted">{{ $vacante->nivelEducacion->nombre }}</p>
                        @endif
                    </div>
                </div>
               
            </div>

            <div class="apply-section text-center">                    
                <a href="{{ route('login.index') }}?redirect_to={{ urlencode(route('vacant.index', ['vacante_id' => $vacante->idofoferta_laboral])) }}" class="btn btn-primary-custom">
                        Iniciar sesión para aplicar
                 </a>
            </div>
        </div>
    </div>
</div>
@endsection
