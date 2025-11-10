@extends('layout.dashboard')

@section('title', 'Vacantes disponibles')

@section('content')


@include('candidate.vacant.drawer.showVacant')
@include('candidate.vacant.modals.createAplication')




<div class="container-fluid vacantes-container">
    <div class="vacantes-header d-flex align-items-center justify-content-between flex-wrap mb-3">
        <div class="d-flex align-items-center mb-2 mb-md-0">
            <i class="la la-briefcase text-bold" style="color: #00945e; font-size: 30px"></i>
            <h3 class="mb-0 ml-2">
                Vacantes disponibles
                <small class="text-muted ml-2" style="font-size: 0.7rem">
                    <i>Encuentra tu próxima oportunidad laboral</i>
                </small>
            </h3>
        </div>        <!-- Buscador -->
        <div class="boton-search mr-2">
            <input type="text" id="filtro-cargo" name="cargo" placeholder="Buscar por cargo o título..." class="form-control">
        </div>

        <!-- Botón flotante Mi perfil -->
        <a href="/account" class="btn d-flex align-items-center perfil-float-btn pulse-anim"
           style="background: #00945e; color: #fff; white-space: nowrap; font-weight: 500; position: fixed; left: 30px; bottom: 30px; z-index: 1050; box-shadow: 0 4px 16px rgba(0,0,0,0.15); padding: 14px 26px; border-radius: 30px; border: none;">
            <i class="la la-user mr-1" style="font-size: 1.2rem;"></i> Mi perfil
        </a>

        <style>
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(0, 184, 116, 0.5); }
            70% { box-shadow: 0 0 0 12px rgba(0,148,94, 0); }
            100% { box-shadow: 0 0 0 0 rgba(0,148,94, 0); }
        }
        .pulse-anim {
            animation: pulse 1.5s infinite;
        }
        </style>
    </div>

    <hr class="mb-4">
    <!-- Filtros -->
    <div class="filtros-container">
        <div class="filtro">            <select id="filtro-ciudad" name="ciudad">
                <option value="">Ciudad</option>
                @foreach($ciudades as $ciudad)
                    <option value="{{ $ciudad->id }}">{{ $ciudad->ciudad_departamento_pais }}</option>
                @endforeach
            </select>
        </div>
        <div class="filtro">            <select id="filtro-sector" name="sector">
                <option value="">Sector</option>
                @foreach($sectores as $sector)
                    <option value="{{ $sector->id }}">{{ $sector->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="filtro">            <select id="filtro-area" name="area">
                <option value="">Área</option>
                @foreach($areas as $area)
                    <option value="{{ $area->id }}">{{ $area->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="filtro">            <select id="filtro-nivel" name="nivel_educacion">
                <option value="">Nivel de educación</option>
                @foreach($nivelesEducacion as $nivel)
                    <option value="{{ $nivel->id }}">{{ $nivel->descripcion }}</option>
                @endforeach
            </select>
        </div>        <div class="filtro">
            <select id="filtro-salario" name="salario">
                <option value="">Salario</option>
                @foreach($rangosSalariales as $rango)
                    <option value="{{ $rango['id'] }}">{{ $rango['texto'] }}</option>
                @endforeach
            </select>
        </div>
        
        <!-- Botón para restaurar filtros -->
        <div class="filtro">
            <button type="button" id="btn-restaurar-filtros" class="btn btn-outline-secondary btn-sm" onclick="restaurarFiltros()">
                <i class="la la-refresh mr-1"></i>
                Restaurar filtros
            </button>
        </div>
    </div>

    <!-- Filtros activos (chips) -->
    <div id="filtros-activos" class="mt-3"></div>

    <!-- Lista vacantes -->
    <div class="vacantes-list mt-4">
        @foreach($todas_vacantes as $vacante)            
            <div class="vacante-card bg-white shadow-md p-4 "
                data-cargo="{{ $vacante->cargo->nombre }}"
                data-titulo="{{ $vacante->titulo }}"
                data-ciudad="{{ $vacante->ciudad->id }}"
                data-sectores="{{ $vacante->sectores->pluck('id')->join(',') }}"
                data-areas="{{ $vacante->areas->pluck('id')->join(',') }}"                data-nivel-id="{{ $vacante->nivelEducacion->id ?? '' }}"
                data-salario-id="{{ $vacante->rangoSalarial->id ?? '' }}"
                style="display: flex">
                <!-- Columna 1 -->
                <div class="vacante-col1">
                    <div class="vacante-logo">
                        @if ($vacante->is_confidencial == 0)
                            <img src="{{ $vacante->empresa->logourl }}" alt="Logo de la empresa {{ $vacante->empresa->nombre }}">
                        @else
                            <img src="{{ asset('images/confidencial.png') }}" alt="Empresa confidencial">
                        @endif
                    </div>
                    <div class="vacante-info">
                        <h4 onclick="abrirDrawer({{ $vacante->idofoferta_laboral }})" style="cursor: pointer">{{ $vacante->titulo }}</h4>
                        <p>{{ $vacante->cargo->nombre }}</p>
                        <p><i class="la la-map-marker mr-1"></i> {{ $vacante->ciudad->ciudad_departamento_pais ?? '-' }} | <i class="la la-building mr-1"></i> {{ $vacante->is_confidencial == 0 ? $vacante->empresa->nombre : 'Confidencial' }}
                        </p>
                    </div>
                </div>

                <!-- Columna 2 -->
                <div class="vacante-col2">
                    <div class="vacante-centro">
                        {{-- <span class="badge text-white" style="background-color: #00945e">Full Time</span> --}}
                        <br>
                        <small> {{ $vacante->fecha_creacion ? $vacante->fecha_creacion->diffForHumans() : 'Fecha no disponible' }}</small>

                    </div>                    <div class="vacante-botones">
                        @if(in_array($vacante->idofoferta_laboral, $aplicaciones))
                            <a href="#" class="btn-vacante btn-aplicar aplicado" onclick="return false;">Aplicado</a>
                        @else
                            <a href="#" class="btn-vacante btn-aplicar" onclick="aplicarAVacante({{ $vacante->idofoferta_laboral }}, '{{ $vacante->cargo->nombre }}')">Aplicar</a>
                        @endif
                        <a href="#" class="btn-vacante btn-ver" onclick="abrirDrawer({{ $vacante->idofoferta_laboral }})">Ver vacante</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>    <!-- Paginación -->
    <div class="paginacion-container mt-4 d-flex justify-content-center align-items-center">
        <nav class="paginacion-nav">
            <ul class="pagination">
                <li class="page-item" id="prev-page">
                    <a class="page-link" href="#" aria-label="Anterior">
                        <i class="la la-angle-left"></i>
                    </a>
                </li>
                <li class="page-numbers d-flex"></li>
                <li class="page-item" id="next-page">
                    <a class="page-link" href="#" aria-label="Siguiente">
                        <i class="la la-angle-right"></i>
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    
</div>

@endsection
