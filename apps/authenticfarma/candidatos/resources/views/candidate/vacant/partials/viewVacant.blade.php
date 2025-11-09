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

    <div class="d-flex justify-content-center align-items-center mt-5" style="width: 100%;">
        @if(isset($aplicaciones) && in_array(($vacante->idofoferta_laboral ?? $vacante->id), $aplicaciones))
            <button class="btn" style="background: #bdbdbd; color: #fff; font-weight: 500; padding: 14px 32px; border-radius: 30px; font-size: 1.1rem; min-width: 260px; cursor: not-allowed;" disabled>
                <i class="la la-check mr-1"></i> Ya aplicaste a esta vacante
            </button>
        @else
            <button class="btn" style="background: #00945e; color: #fff; font-weight: 500; padding: 14px 32px; border-radius: 30px; font-size: 1.1rem; box-shadow: 0 4px 16px rgba(0,148,94,0.10); transition: background 0.2s; min-width: 260px;" onclick="aplicarAVacante({{ $vacante->idofoferta_laboral ?? $vacante->id }}, '{{ $vacante->cargo->nombre ?? '' }}')">
                <i class="la la-paper-plane mr-1"></i> Aplicar a esta vacante
            </button>
        @endif
    </div>




</div>
