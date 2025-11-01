@extends('layout.admin')

@section('title', 'Vacantes disponibles')

@section('content')

@include('admin.vacant.drawer.showVacant')

<div class="container-fluid vacantes-container" style="background-color: #fff">
    <div class="d-flex align-items-center justify-content-between flex-wrap mb-3">
        <div class="d-flex align-items-center mb-2 mb-md-0">
            <i class="la la-briefcase text-bold" style="color: #00945e; font-size: 30px"></i>
            <h3 class="mb-0 ml-2">
                Vacantes disponibles
                <small class="text-muted ml-2" style="font-size: 0.7rem">
                    <i>Oportunidades laborales</i>
                </small>
            </h3>
        </div>        <!-- Buscador -->
        <div class="d-flex align-items-center mt-2 mt-md-0" style="min-width:260px;">
            <input type="text" id="filtro-cargo" name="cargo" placeholder="Buscar por cargo o título..." class="form-control" style="border: 1.5px solid #ced4da; border-radius: 18px; box-shadow: none; color: #222; font-size: 1rem; padding-left: 1rem; height: 36px; line-height: 1.2; background: #fff;">
        </div>
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
    </div>

    <!-- Filtros activos (chips) -->
    <div id="filtros-activos" class="mt-3"></div>

    <!-- Lista vacantes -->
    <div class="vacantes-list mt-4">
        @forelse($todas_vacantes as $vacante)
            <div class="vacante-card bg-white shadow-md p-2 "
                    data-cargo="{{ $vacante->cargo->nombre }}"
                    data-titulo="{{ $vacante->titulo }}"
                    data-ciudad="{{ $vacante->ciudad->id }}"
                    data-sectores="{{ $vacante->sectores->pluck('id')->join(',') }}"
                    data-areas="{{ $vacante->areas->pluck('id')->join(',') }}"                data-nivel-id="{{ $vacante->nivelEducacion->id ?? '' }}"
                    data-salario-id="{{ $vacante->rangoSalarial->id ?? '' }}"
                    style="display: flex; position: relative; border-radius: 14px; min-height: 90px;">
                <div class="vacante-col1" style="min-width: 0;">
                    <div class="vacante-logo" style="width: 48px; height: 48px;">
                        @if ($vacante->is_confidencial == 0)
                            <img src="{{ $vacante->empresa->logourl }}" alt="Logo de la empresa {{ $vacante->empresa->nombre }}" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                        @else
                            <img src="{{ asset('images/confidencial.png') }}" alt="Empresa confidencial" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                        @endif
                    </div>
                    <div class="vacante-info" style="padding-left: 10px;">
                        <h5 onclick="abrirDrawer({{ $vacante->idofoferta_laboral }})" style="cursor: pointer; font-size: 1.05rem; margin-bottom: 2px;">{{ $vacante->titulo }}</h5>
                        <p style="font-size: 0.95rem; margin-bottom: 2px;">{{ $vacante->cargo->nombre }}</p>
                        <p style="font-size: 0.85rem; color: #888; margin-bottom: 0;"><i class="la la-map-marker mr-1"></i> {{ $vacante->ciudad->ciudad_departamento_pais ?? '-' }} | <i class="la la-building mr-1"></i> {{ $vacante->is_confidencial == 0 ? $vacante->empresa->nombre : 'Confidencial' }}
                        </p>
                    </div>
                </div>

                <!-- Columna 2 -->
                <div class="vacante-col2 d-flex flex-column align-items-end justify-content-between" style="min-width:150px;">
                    <div class="vacante-centro mb-2">
                        <small> {{ $vacante->fecha_creacion ? $vacante->fecha_creacion->diffForHumans() : 'Fecha no disponible' }}</small>
                    </div>
                    <div class="vacante-botones d-flex">
                        <a href="#" class=" btn-ver" onclick="abrirDrawer({{ $vacante->idofoferta_laboral }})" style="font-size:0.95rem; padding: 4px 14px; border-radius:16px;">Ver vacante</a>
                        <a href="{{ route('admin.vacant.postulados', $vacante->idofoferta_laboral) }}" class="btn-ver btn-postulados animate-postulados" style="background:#00a86b; color:#fff; margin-left:8px; font-size:0.95rem; padding: 4px 14px; border-radius:16px; transition: background 0.2s, transform 0.2s;">Ver postulados</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center text-muted">No hay vacantes disponibles.</div>
        @endforelse
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

@push('styles')
<style>
.animate-postulados {
    transition: background 0.2s, transform 0.2s, box-shadow 0.2s, filter 0.2s;
}
.animate-postulados:hover, .animate-postulados:focus {
    background: #00945e !important;
    color: #fff !important;
    transform: scale(1.09) translateY(-2px) rotate(-2deg);
    box-shadow: 0 6px 18px rgba(0,168,107,0.18);
    filter: brightness(1.08) saturate(1.2);
}
</style>
@endpush
