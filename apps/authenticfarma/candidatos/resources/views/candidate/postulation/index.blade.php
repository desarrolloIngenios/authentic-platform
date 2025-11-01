@extends('layout.app') 

@section('title', 'Mis Postulaciones')


@section('content')
@include('candidate.postulation.drawer.showVacant')
<div class="container-fluid vacantes-container">
    <div class=""><h4 class="pt-1 pl-4 font-weight-bold"><i class="las la-briefcase icon-blue"></i>Ofertas a las que me he Postulado</h4></div>
    <hr>    
    <div class="vacantes-list">

        @forelse ($postulaciones as $postulacion)
            <div class="vacante-card">
                <!-- Columna 1 -->
                <div class="vacante-col1">
                    <div class="vacante-logo">
                        @if ($postulacion->ofertaLaboral->is_confidencial == 0)
                            <img src="{{ $postulacion->ofertaLaboral->empresa->logourl }}" alt="Logo de la empresa {{ $postulacion->ofertaLaboral->empresa->nombre }}">
                        @else
                            <img src="{{ asset('images/confidencial.png') }}" alt="Empresa confidencial">
                        @endif
                    </div>
                    <div class="vacante-info">
                        <h4 onclick="abrirDrawer({{ $postulacion->ofertaLaboral->idofoferta_laboral }})" style="cursor: pointer">
                            {{ $postulacion->ofertaLaboral->titulo }}
                        </h4>
                        <p style="line-height: 1.5;">
                            {{ $postulacion->ofertaLaboral->cargo->nombre }} <br>
                            <i class="la la-clock mr-1"></i> Postulado: {{ $postulacion->fecha_creacion->diffForHumans() }} <br>
                            <i class="la la-map-marker mr-1"></i> 
                            {{ $postulacion->ofertaLaboral->ciudad->ciudad_departamento_pais ?? '-' }} | 
                            <i class="la la-building mr-1"></i> 
                            {{ $postulacion->ofertaLaboral->is_confidencial == 0 ? $postulacion->ofertaLaboral->empresa->nombre : 'Confidencial' }}
                        </p>
                  
                       
                    </div>
                </div>

                <!-- Columna 2 -->
                <div class="vacante-col2">
                    <div class="vacante-centro">
                        {{-- <span class="badge {{ $postulacion->estado == 'Pendiente' ? 'bg-warning' : 
                            ($postulacion->estado == 'Seleccionado' ? 'bg-success' : 
                            ($postulacion->estado == 'Rechazado' ? 'bg-danger' : 'bg-info')) }} text-white">
                            {{ $postulacion->estado }}
                        </span> --}}
                    </div>
                    <div class="vacante-botones">
                        <a href="#" class="btn-vacante btn-ver" onclick="abrirDrawer({{ $postulacion->ofertaLaboral->idofoferta_laboral }})">
                            Ver detalles
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="d-flex flex-column align-items-center justify-content-center h-100">
                <p class="text-dark"><i>No tienes postulaciones realizadas hasta el momento...</i>😞</p>
                <a class="btn btn-secondary" href="{{route('vacant.index')}}">Ver vacantes</a>
            </div>
        @endforelse
    </div>
</div>
@endsection