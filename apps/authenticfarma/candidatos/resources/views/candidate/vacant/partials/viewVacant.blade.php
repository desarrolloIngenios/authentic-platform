<div class="descripcion-vacante px-4 py-3">
    {{-- Título --}}
    <h4 class="mb-3 text-dark text-center fw-bold">{{ $vacante->titulo }}</h4>

    {{-- Información general --}}
    <ul class="list-unstyled mb-4">
        <li><strong>📍 Ciudad:</strong> {{ $vacante->ciudad->nombre ?? 'N/D' }}</li>
        <li><strong>💼 Cargo:</strong> {{ $vacante->cargo->nombre ?? 'N/D' }}</li>
        <li><strong>🎓 Educación mínima:</strong> {{ $vacante->nivelEducacion->descripcion ?? 'N/D' }}</li>
        <li><strong>🕒 Experiencia requerida:</strong> {{ $vacante->tiempoExperiencia->descripcion ?? 'N/D' }}</li>
        <li><strong>🌐 Idioma:</strong> {{ $vacante->idioma->nombre ?? 'Ninguno' }} ({{ $vacante->nivelIdioma->nombre ?? '' }})</li>
        <li><strong>💰 Salario:</strong> 
            {{ number_format($vacante->rangoSalarial->minimo, 0, ',', '.') }} - 
            {{ number_format($vacante->rangoSalarial->maximo, 0, ',', '.') }} COP
        </li>
    </ul>
    <hr>
    {{-- Descripción del puesto --}}
   <h5 class="text-dark fw-bold text-center">📝 Descripción del puesto</h5>
    <div class="descripcion-contenido mb-4">
       {!! nl2br(e($vacante->descripcion)) !!}
    </div>
    <hr>
    {{-- Información de la empresa --}}
    <h5 class="text-dark text-center fw-bold mb-3">🏢 Acerca de la empresa</h5>

    <div class="row align-items-start g-3 mt-4">
        {{-- Columna del logo (cuadrado responsive) --}}
        <div class="col-md-3 text-center">
            <div class="logo-cuadrado mx-auto">
                @if ($vacante->is_confidencial == 0)
                    {{-- Mostrar logo de la empresa si no es confidencial --}}
                    <img src="{{ $vacante->empresa->logourl }}" alt="Logo de la empresa {{ $vacante->empresa->nombre }}">
                @else
                    <img src="{{ asset('images/confidencial.png') }}" alt="Empresa confidencial">
                @endif
            </div>
        </div>

        {{-- Columna del nombre y descripción --}}
        <div class="col-md-9">
            <h6 class="fw-semibold mb-2">
                {{ $vacante->is_confidencial == 0 ? $vacante->empresa->nombre : 'Confidencial' }}
            </h6>
            <p class="mb-0 empresa-descripcion">
                {{ $vacante->is_confidencial == 0 ? ($vacante->empresa->descripcion ?? 'Sin descripción disponible') : 'Información no disponible por confidencialidad.' }}
            </p>
        </div>
    </div>
    {{-- Botón de postulación --}}
    {{-- <div class="text-center mt-4">
        @if ($vacante->id_estado == 1)
            <a href="{{ route('candidato.postular', $vacante->id) }}" class="btn btn-primary-custom">
            <a href="#" class="btn btn-primary-custom">
                Aplicar a esta vacante
            </a>
        @else
            <button class="btn btn-secondary" disabled>
                Vacante no disponible
            </button>
        @endif
    </div> --}}




</div>
