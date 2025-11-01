@extends('layout.admin')

@section('title', 'Postulados a la vacante')

@section('content')
<div class="container-fluid mt-4">
    <div class="d-flex align-items-center mb-4">
        <i class="la la-users" style="font-size:2rem;color:#00a86b;"></i>
        <h2 class="fw-bold mb-0 ms-2">Postulados a: <span class="text-dark">{{ $vacante->titulo ?? $vacante->cargo->nombre }}</span></h2>
    </div>
    <div class="p-3 mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div><strong>Empresa:</strong> {{ $vacante->empresa->nombre ?? '-' }}</div>
                <div><strong>Cargo:</strong> {{ $vacante->cargo->nombre ?? '-' }}</div>
                <div><strong>Ubicación:</strong> {{ $vacante->ciudad?->nombre ?? '-' }}</div>
                <div><strong>Salario:</strong> {{ $vacante->rangoSalarial ? '$'.number_format($vacante->rangoSalarial->minimo, 0, ',', '.') . ' - $'.number_format($vacante->rangoSalarial->maximo, 0, ',', '.') : '-' }}</div>
                <div><strong>Fecha de publicación:</strong> {{ $vacante->fecha_creacion ? $vacante->fecha_creacion->format('d/m/Y') : '-' }}</div>
                <div><strong>Área:</strong> {{ $vacante->areas && $vacante->areas->count() ? $vacante->areas->pluck('nombre')->join(', ') : '-' }}</div>
                <div><strong>Sector:</strong> {{ $vacante->sectores && $vacante->sectores->count() ? $vacante->sectores->pluck('nombre')->join(', ') : '-' }}</div>
            </div>
            <div class="col-md-4 d-flex justify-content-md-end justify-content-center mt-3 mt-md-0">
                <span class="badge" style="background-color:#00a86b;font-size:1.1rem;padding:10px 18px;border-radius:18px; color:#fff;">
                    <i class="la la-user-friends me-1"></i> {{ $postulados->count() }} postulados
                </span>
            </div>
        </div>
    </div>
    <div class="vacantes-list mt-4">
        @forelse($postulados as $i => $postulado)
            <div class="vacante-card bg-white shadow-md p-0 mb-3 d-flex flex-column flex-md-row align-items-center justify-content-center"
                style="border-radius: 16px; border: 1.5px solid #e0e0e0; transition: box-shadow .2s;">
                <div class="row w-100 g-0 text-center">
                    <div class="col-12 col-md-3 d-flex flex-column justify-content-center align-items-center p-3 border-end">
                        <span class="fw-bold text-dark">{{ $postulado->candidato?->nombres ?? '-' }} {{ $postulado->candidato?->apellidos ?? '' }}</span>
                    </div>
                    <div class="col-12 col-md-3 d-flex flex-column justify-content-center align-items-center p-3 border-end">
                        <span class="text-muted small"><i class="la la-envelope me-1"></i> {{ $postulado->candidato?->correo->email ?? '-' }}</span>
                        <span class="text-muted small"><i class="la la-phone me-1"></i> {{ $postulado->candidato?->telefono->numero_telefono ?? '-' }}</span>
                    </div>
                    <div class="col-12 col-md-3 d-flex flex-column justify-content-center align-items-center p-3 border-end">
                        <span class="text-muted small"><i class="la la-calendar me-1"></i> Postulado el {{ $postulado->fecha_creacion ? \Carbon\Carbon::parse($postulado->fecha_creacion)->format('d/m/Y') : '-' }}</span>
                    </div>
                    <div class="col-12 col-md-3 d-flex flex-column justify-content-center align-items-center p-3">
                        @if($postulado->candidato)
                            <a href="{{ route('candidates.show', ['id' => $postulado->candidato->id_candidato, 'return' => request()->fullUrl()]) }}" class="btn btn-outline-success3"><i class="la la-eye"></i></a>
                        @else
                            <span class="text-muted">Sin datos</span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center text-muted">No hay postulados para esta vacante.</div>
        @endforelse
    </div>
    <a href="{{ route('admin.vacant.index') }}" class="btn btn-outline-secondary mt-3"><i class="la la-arrow-left"></i> Volver a vacantes</a>
</div>
@endsection
