@extends('layout.admin')

@section('title', 'Detalle de Candidato')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center mt-4">
        <div class="col-12">
            <div class="p-4 mb-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="me-3">
                        <i class="la la-user-circle" style="font-size:3rem;color:#00a86b;"></i>
                    </div>
                    <div>
                        <h2 class="fw-bold mb-0 text-dark">{{ $candidato->nombres }} {{ $candidato->apellidos }}</h2>
                        <div class="text-muted small">{{ $candidato->correo->email ?? '-' }} | {{ $candidato->telefono->numero_telefono ?? '-' }}</div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6 mb-2">
                        <span class="fw-semibold">Género:</span> {{ $candidato->genero->descripcion ?? '-' }}<br>
                        <span class="fw-semibold">Documento:</span> {{ $candidato->tipoDocumento->descripcion ?? '-' }} {{ $candidato->numero_documento ?? '' }}
                    </div>
                    <div class="col-md-6 mb-2">
                        <span class="fw-semibold">Ciudad:</span> {{ $candidato->ubicacion->ciudadResidencia->nombre ?? '-' }}<br>
                        <span class="fw-semibold">Dirección:</span> {{ $candidato->ubicacion->direccion ?? '-' }}
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6 mb-2">
                        <span class="fw-semibold">Fecha de nacimiento:</span> {{ $candidato->fecha_nacimiento ? $candidato->fecha_nacimiento->format('d/m/Y') : '-' }}
                    </div>
                </div>
                <hr>
                @if(($candidato->perfil && $candidato->perfil->descripcion_perfil) || ($candidato->nuevoTrabajo && $candidato->nuevoTrabajo->rangoSalario))
                    @if($candidato->perfil && $candidato->perfil->descripcion_perfil)
                        <h5 class="fw-bold mb-3" style="color:#00a86b;"><i class="la la-trophy me-2"></i>Logros y Proyectos</h5>
                        <div class="edu-history-sec mb-4">
                            <div class="edu-history mb-2">
                                <i class="la la-trophy"></i>
                                <div class="edu-hisinfo">
                                    <span>{{ $candidato->perfil->descripcion_perfil }}</span>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if($candidato->nuevoTrabajo && $candidato->nuevoTrabajo->rangoSalario)
                        <h5 class="fw-bold mb-3" style="color:#00a86b;"><i class="la la-money-bill-wave me-2"></i>Salario Aspirado</h5>
                        <div class="edu-history-sec mb-4">
                            <div class="edu-history mb-2">
                                <i class="la la-money-bill-wave"></i>
                                <div class="edu-hisinfo">
                                    <span>Entre ${{ number_format($candidato->nuevoTrabajo->rangoSalario->minimo, 0, ',', '.') }} y ${{ number_format($candidato->nuevoTrabajo->rangoSalario->maximo, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    @endif
                @else
                    <div class="mb-4">
                        <h5 class="fw-bold mb-3" style="color:#00a86b;"><i class="la la-trophy me-2"></i>Logros y Proyectos</h5>
                        <div class="text-muted">Sin registro.</div>
                        <h5 class="fw-bold mb-3 mt-4" style="color:#00a86b;"><i class="la la-money-bill-wave me-2"></i>Salario Aspirado</h5>
                        <div class="text-muted">Sin registro.</div>
                    </div>
                @endif
                <h5 class="fw-bold mb-3" style="color:#00a86b;"><i class="la la-briefcase me-2"></i>Experiencia Laboral</h5>
                <div class="edu-history-sec mb-4">
                    @forelse($candidato->experienciasLaborales->sortByDesc('fecha_fin') as $exp)
                        <div class="edu-history style2 mb-2">
                            <i></i>
                            <div class="edu-hisinfo">
                                <h3>{{ $exp->empresa ?? '-' }} <span>{{ $exp->nombre_cargo ?? ($exp->tipoCargo->descripcion ?? '-') }}</span></h3>
                                <i>{{ $exp->fecha_inicio ? $exp->fecha_inicio->isoFormat('MMMM YYYY') : 'Fecha no disponible' }} - {{ $exp->fecha_fin ? $exp->fecha_fin->isoFormat('MMMM YYYY') : 'Actual' }}</i>
                                <i><strong class="font-green">Descripción: </strong>{{ $exp->descripcion_cargo ?? '-' }}</i>
                                <i><strong class="font-green">Área: </strong>{{ $exp->area->descripcion ?? '-' }}</i>
                                <i><strong class="font-green">Sector: </strong>{{ $exp->sector->descripcion ?? '-' }}</i>
                            </div>
                        </div>
                    @empty
                        <div class="text-muted">Sin experiencia registrada.</div>
                    @endforelse
                </div>
                <h5 class="fw-bold mb-3" style="color:#00a86b;"><i class="la la-graduation-cap me-2"></i>Formación Académica</h5>
                <div class="edu-history-sec mb-4">
                    @forelse($candidato->formacionacademica as $edu)
                        <div class="edu-history mb-2">
                            <i class="la la-graduation-cap"></i>
                            <div class="edu-hisinfo">
                                <h3>{{ $edu->titulo ?? '-' }}</h3>
                                <span>{{ $edu->institucion ?? '-' }}</span>
                                <i>{{ $edu->fecha_inicio ? $edu->fecha_inicio->isoFormat('MMMM YYYY') : 'Fecha no disponible' }} - {{ $edu->fecha_fin ? $edu->fecha_fin->isoFormat('MMMM YYYY') : 'Actual' }}</i>
                            </div>
                        </div>
                    @empty
                        <div class="text-muted">Sin formación registrada.</div>
                    @endforelse
                </div>
                <h5 class="fw-bold mb-3" style="color:#00a86b;"><i class="la la-language me-2"></i>Idiomas</h5>
                <div class="edu-history-sec mb-4">
                    @forelse($candidato->HvCanIdioma as $idioma)
                        <div class="edu-history mb-2">
                            <i class="la la-language"></i>
                            <div class="edu-hisinfo">
                                <h3>{{ $idioma->idioma->descripcion ?? '-' }}</h3>
                                <span>Nivel: {{ $idioma->nivelIdioma->descripcion ?? '-' }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-muted">Sin idiomas registrados.</div>
                    @endforelse
                </div>
                @php $returnUrl = request('return'); @endphp
                <a href="{{ $returnUrl ? $returnUrl : route('candidates.index') }}" class="btn btn-outline-success3 mt-2"><i class="la la-arrow-left"></i> Volver</a>
            </div>
        </div>
    </div>
</div>
@endsection
