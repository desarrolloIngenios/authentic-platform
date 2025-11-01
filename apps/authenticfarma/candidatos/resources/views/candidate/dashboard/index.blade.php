@extends('layout.app') 

@section('title', 'Mi tablero')

@section('styles')
    <style>
        .dashboard-section {
            padding: 2rem 0;
            max-width: 1400px;
            margin: 0 auto;
            position: relative;
            width: 100%;
        }

        .dashboard-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100vw;
            height: 100%;
            background: linear-gradient(180deg, rgba(0, 168, 107, 0.05), transparent);
            z-index: -1;
        }

        .padding-left {
            padding: 0 1rem;
        }

        .section-title {
            color: #000000;
            text-align: left;
            margin-bottom: 0.5rem;
            font-size: 1.8rem;
            font-weight: 600;
            position: relative;
            display: inline-block;
            background: linear-gradient(45deg, #000000, #000000);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.5px;
        }

        .section-title2 {
            color: #000000;
            text-align: left;
            margin-bottom: 0.5rem;
            font-size: 1.2rem;
            font-weight: 600;
            position: relative;
            display: inline-block;
            background: linear-gradient(45deg, #000000, #000000);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.5px;
        }

        .section-title::after {
            display: none;
        }

        .chart-container {
            width: 100%;
            max-width: 300px;
            margin: 0 auto;
            position: relative;
            height: 350px;
            padding: 1rem;
            background: white;
           
            transition: transform 0.3s ease;
        }

        .chart-container:hover {
            transform: translateY(-5px);
        }

        .chart-label {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            font-size: 2.5rem;
            font-weight: bold;
            color: #00a86b;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        }

        .chart-label-text {
            font-size: 1rem;
            color: #666;
            display: block;
            margin-top: 0.5rem;
            font-weight: normal;
        }

        .completion-details {
            display: flex;
            justify-content: space-between;
            flex-wrap: nowrap;
            gap: 1.5rem;
            margin-top: 3rem;
            padding: 0 1.5rem;
            overflow-x: auto;
            scrollbar-width: none;
            -ms-overflow-style: none;
            position: relative;
        }

        .completion-details::after {
            content: '';
            position: absolute;
            right: 0;
            top: 0;
            height: 100%;
            width: 60px;
            background: linear-gradient(to left, white, transparent);
            pointer-events: none;
            opacity: 0.8;
        }

        .completion-details::-webkit-scrollbar {
            display: none;
        }

        .completion-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.2rem;
            max-width: 600px;
            margin: 0 auto;
        }

        .completion-item {
            background: white;
            padding: 1.2rem;
            border-radius: 12px;
            text-align: center;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(0, 168, 107, 0.1);
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-decoration: none;
            cursor: pointer;
        }

        .completion-item:hover {
            transform: translateY(-2px);
            border-color: #00a86b;
            box-shadow: 0 4px 8px rgba(0, 168, 107, 0.15);
        }

        .completion-item-icon {
            display: flex;
            margin: 0 auto 0.8rem;
            width: 40px;
            height: 40px;
            background: linear-gradient(45deg, rgba(0, 168, 107, 0.1), rgba(0, 87, 184, 0.1));
            border-radius: 10px;
            align-items: center;
            justify-content: center;
            color: #00a86b;
            transition: all 0.3s ease;
        }

        .completion-item:hover .completion-item-icon {
            background: #00a86b;
            color: white;
        }

        .completion-item-title {
            font-weight: 600;
            color: #666;
            margin-bottom: 0.5rem;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .completion-item-percentage {
            font-size: 1.8rem;
            font-weight: bold;
            background: linear-gradient(45deg, #00a86b, #0057b8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin: 0;
            line-height: 1;
        }

        .banner {
            font-family: 'Open Sans', sans-serif;
            background: linear-gradient(to right, 
                rgba(0, 168, 107, 0.4), 
                rgba(0, 87, 184, 0.4)
            ), url('{{ asset('images/img-banner.jpg') }}');
            background-size: cover;
            background-position: center;
            min-height: 400px;
            width: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            text-align: center;
            padding: 4rem 2rem;
            margin: 0;
            position: relative;
            overflow: hidden;
        }

        .banner::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(to right, 
                transparent, 
                rgba(255, 255, 255, 0.3), 
                transparent
            );
        }

        .banner h1 {
            max-width: 800px;
            margin: 0 auto 2rem;
            font-size: 2.5rem;
            line-height: 1.2;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.15);
            position: relative;
            z-index: 1;
        }

        .btn-custom {
            padding: 12px 100px;
            font-size: 20px;
            background-color: #00a86b;
            border: none;
            border-radius: 30px;
            color: white;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 168, 107, 0.2);
        }

        .btn-custom:hover {
            background-color: #0057b8;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 87, 184, 0.3);
        }

        .btn-custom:active {
            transform: translateY(0);
            box-shadow: 0 4px 15px rgba(0, 87, 184, 0.2);
        }

        @media (max-width: 768px) {
            .banner {
                min-height: 300px;
                padding: 3rem 1rem;
            }

            .banner h1 {
                font-size: 1.8rem;
            }

            .chart-container {
                max-width: 250px;
                height: 250px;
            }

            .chart-label {
                font-size: 2rem;
            }

            .completion-item {
                min-width: 180px;
            }

            .padding-left {
                padding: 0 0.5rem;
            }
        }

        @media (max-width: 1200px) {
            .completion-details {
                justify-content: flex-start;
                padding-bottom: 1rem;
            }
            
            .completion-item {
                flex: 0 0 auto;
            }
        }

        .dashboard-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            align-items: start;
            margin-top: 0.5rem;
            width: 100%;
        }

        .chart-section {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 1rem;
        }

        .chart-container {
            width: 100%;
            max-width: 350px;
            margin: 0;
            position: relative;
            height: 350px;
            background: transparent;
            transition: all 0.3s ease;
        }

        .chart-label {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }

        .chart-label-percentage {
            font-size: 3rem;
            font-weight: bold;
            color: #00a86b;
            line-height: 1;
            margin-bottom: 0.5rem;
            background: linear-gradient(45deg, #00a86b, #0057b8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .chart-label-text {
            font-size: 1rem;
            color: #666;
            display: block;
            font-weight: normal;
        }

        .completion-section {
            padding: 1rem;
        }

        @media (max-width: 992px) {
            .dashboard-content {
                grid-template-columns: 1fr;
                gap: 2rem;
            }

            .chart-container {
                margin: 0 auto;
                max-width: 300px;
            }

            .completion-grid {
                grid-template-columns: repeat(2, 1fr);
                max-width: 800px;
                margin: 0 auto;
            }

            .vacante-card {
                flex-direction: column;
            }

            .vacante-col1 {
                width: 100%;
            }

            .vacante-col2 {
                width: 100%;
                align-items: flex-start;
            }

            .vacante-botones {
                width: 100%;
                justify-content: flex-start;
            }
        }

        @media (max-width: 768px) {
            .section-title {
                font-size: 1.5rem;
            }

            .section-title2 {
                font-size: 1.1rem;
            }

            .completion-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .completion-item {
                min-width: 100%;
            }

            .vacante-logo {
                width: 60px;
                height: 60px;
            }

            .vacante-info h4 {
                font-size: 1.1rem;
            }

            .vacante-info p {
                font-size: 0.9rem;
            }

            .btn-vacante-dashboard {
                width: 100%;
                text-align: center;
            }
        }

        @media (max-width: 576px) {
            .dashboard-section {
                padding: 1rem 0;
            }

            .chart-container {
                height: 250px;
                max-width: 250px;
            }

            .chart-label-percentage {
                font-size: 2rem;
            }

            .chart-label-text {
                font-size: 0.9rem;
            }

            .completion-item {
                padding: 1rem;
            }

            .completion-item-icon {
                width: 35px;
                height: 35px;
            }

            .completion-item-title {
                font-size: 0.8rem;
            }

            .completion-item-percentage {
                font-size: 1.5rem;
            }
        }

        .btn-vacante-dashboard {
            white-space: nowrap;
            padding: 0.8rem 1.5rem;
            font-size: 1rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 120px;
            border-radius: 6px;
            transition: all 0.3s ease;
            text-decoration: none;
            margin: 0.25rem;
            font-weight: 500;
        }

        .btn-vacante-dashboard.btn-aplicar {
            background-color: #00a86b;
            color: white;
            border: none;
        }

        .btn-vacante-dashboard.btn-ver {
            background-color: #f8f9fa;
            color: #333;
            border: 1px solid #ddd;
        }

        .btn-vacante-dashboard:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .btn-vacante-dashboard.btn-aplicar.aplicado {
            background-color: #00a86b;
            color: white;
            cursor: default;
            opacity: 0.9;
        }

        .btn-vacante-dashboard.btn-aplicar.aplicado:hover {
            transform: none;
            box-shadow: none;
        }

        .vacante-botones {
            display: flex;
            gap: 0.8rem;
            flex-wrap: wrap;
            justify-content: flex-end;
            align-items: center;
        }

        @media (max-width: 992px) {
            .btn-vacante-dashboard {
                padding: 0.7rem 1.2rem;
                font-size: 0.95rem;
                min-width: 110px;
            }

            .vacante-botones {
                justify-content: flex-start;
                width: 100%;
            }
        }

        @media (max-width: 768px) {
            .btn-vacante-dashboard {
                width: 100%;
                padding: 0.8rem;
                font-size: 1rem;
                margin: 0.25rem 0;
            }

            .vacante-botones {
                flex-direction: column;
                gap: 0.8rem;
            }
        }

        @media (max-width: 576px) {
            .btn-vacante-dashboard {
                font-size: 0.95rem;
                padding: 0.7rem;
            }
        }
    </style>
    <!-- Asegurarnos de que Chart.js se carga antes que nuestro código -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
@endsection

@section('content')
<div class="col-lg-12 column p-0">
    <div class="dashboard-section">
        <div class="padding-left">
            <h3 class="section-title">Mi Tablero</h3>
            
            <div class="dashboard-content">
                <div class="chart-section">
                    <div class="chart-container">
                        <canvas id="profileChart"></canvas>
                        <div class="chart-label">
                            <div class="chart-label-percentage">
                                {{ round(($experiencePercentage + $educationPercentage + $accountPercentage + $profilePercentage) / 4) }}%
                            </div>
                            <span class="chart-label-text">Completado hoja vida</span>
                        </div>
                    </div>
                </div>

                <div class="completion-section">
                    <div class="completion-grid">
                        <!-- Experiencia Laboral -->
                        <a href="{{ route('job.index') }}" class="completion-item">
                            <div class="completion-item-icon">
                                <i class="las la-briefcase la-2x"></i>
                            </div>
                            <div class="completion-item-title">Experiencia Laboral</div>
                            <div class="completion-item-percentage">{{ round($experiencePercentage) }}%</div>
                        </a>

                        <!-- Formación Académica -->
                        <a href="{{ route('educacion.index') }}" class="completion-item">
                            <div class="completion-item-icon">
                                <i class="las la-graduation-cap la-2x"></i>
                            </div>
                            <div class="completion-item-title">Formación Académica</div>
                            <div class="completion-item-percentage">{{ round($educationPercentage) }}%</div>
                        </a>

                        <!-- Cuenta -->
                        <a href="{{ route('account.index') }}" class="completion-item">
                            <div class="completion-item-icon">
                                <i class="las la-user la-2x"></i>
                            </div>
                            <div class="completion-item-title">Cuenta</div>
                            <div class="completion-item-percentage">{{ round($accountPercentage) }}%</div>
                        </a>

                        <!-- Perfil -->
                        <a href="{{ route('profile.index') }}" class="completion-item">
                            <div class="completion-item-icon">
                                <i class="las la-id-card la-2x"></i>
                            </div>
                            <div class="completion-item-title">Perfil</div>
                            <div class="completion-item-percentage">{{ round($profilePercentage) }}%</div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="padding-left">
        @if(count($recommendedOffers) > 0)
            <h6 class="section-title2 ">Ofertas laborales que te pueden interesar</h6>
            <div class="vacantes-list mt-4">
                @foreach($recommendedOffers as $vacante)   
                    <div class="vacante-card bg-white shadow-md p-4 "
                        data-cargo="{{ $vacante->cargo->nombre }}"
                        data-titulo="{{ $vacante->titulo }}"
                        data-ciudad="{{ $vacante->ciudad->id }}"
                        data-sectores="{{ $vacante->sectores->pluck('id')->join(',') }}"
                        data-areas="{{ $vacante->areas->pluck('id')->join(',') }}"                
                        data-nivel-id="{{ $vacante->nivelEducacion->id ?? '' }}"
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
        
                        
                        {{-- <span class="badge text-white" style="background-color: #00945e">Full Time</span> --}}
                        <div class="vacante-col2">
                            <div class="vacante-centro">
                                <br>
                                <small> {{ $vacante->fecha_creacion ? $vacante->fecha_creacion->diffForHumans() : 'Fecha no disponible' }}</small>
        
                            </div>                    
                            <div class="vacante-botones">
                                @if(in_array($vacante->idofoferta_laboral, $aplicaciones))
                                    <a href="#" class="btn-vacante-dashboard btn-aplicar aplicado" onclick="return false;">Aplicado</a>
                                @else
                                    <a href="#" class="btn-vacante-dashboard btn-aplicar" onclick="aplicarAVacante({{ $vacante->idofoferta_laboral }}, '{{ $vacante->cargo->nombre }}')">Aplicar</a>
                                @endif
                                <a href="#" class="btn-vacante-dashboard btn-ver" onclick="abrirDrawer({{ $vacante->idofoferta_laboral }})">Ver vacante</a>
                            </div>
                        </div>
                        @include('candidate.vacant.drawer.showVacant')
                        @include('candidate.vacant.modals.createAplication')
                    </div>
                @endforeach
            </div>    
        @else
            <h6 class="section-title2 ">Ofertas laborales en el sector</h6>
            <div class="vacantes-list mt-4">
                @foreach($recommendedOffersBySector as $vacante)   
                    <div class="vacante-card bg-white shadow-md p-4 "
                        data-cargo="{{ $vacante->cargo->nombre }}"
                        data-titulo="{{ $vacante->titulo }}"
                        data-ciudad="{{ $vacante->ciudad->id }}"
                        data-sectores="{{ $vacante->sectores->pluck('id')->join(',') }}"
                        data-areas="{{ $vacante->areas->pluck('id')->join(',') }}"                
                        data-nivel-id="{{ $vacante->nivelEducacion->id ?? '' }}"
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
        
                        
                        {{-- <span class="badge text-white" style="background-color: #00945e">Full Time</span> --}}
                        <div class="vacante-col2">
                            <div class="vacante-centro">
                                <br>
                                <small> {{ $vacante->fecha_creacion ? $vacante->fecha_creacion->diffForHumans() : 'Fecha no disponible' }}</small>
        
                            </div>                    
                            <div class="vacante-botones">
                                @if(in_array($vacante->idofoferta_laboral, $aplicaciones))
                                    <a href="#" class="btn-vacante-dashboard btn-aplicar aplicado" onclick="return false;">Aplicado</a>
                                @else
                                    <a href="#" class="btn-vacante-dashboard btn-aplicar" onclick="aplicarAVacante({{ $vacante->idofoferta_laboral }}, '{{ $vacante->cargo->nombre }}')">Aplicar</a>
                                @endif
                                <a href="#" class="btn-vacante-dashboard btn-ver" onclick="abrirDrawer({{ $vacante->idofoferta_laboral }})">Ver vacante</a>
                            </div>
                        </div>
                        @include('candidate.vacant.drawer.showVacant')
                        @include('candidate.vacant.modals.createAplication')
                    </div>
                @endforeach
            </div>    
        @endif     
    </div>
    
    <div class="vacants-section">
        
    </div>
    {{--<div class="banner">
        <h1 class="text-center">EN AUTHENTICFARMA SIEMPRE NOS ALEGRA TENERTE DE VUELTA</h1>
        <a href="{{ route('vacant.index') }}" class="btn btn-custom">Buscar ofertas</a>
    </div>--}}
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('profileChart').getContext('2d');
    var percentage = {{ round(($experiencePercentage + $educationPercentage + $accountPercentage + $profilePercentage) / 4) }};
    var remaining = 100 - percentage;
    
    var myChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            datasets: [{
                data: [percentage, remaining],
                backgroundColor: [
                    '#00a86b', // Verde de la plataforma
                    '#f4f6f8'  // Color de fondo suave
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '80%',
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    enabled: false
                }
            }
        }
    });
});
</script>
@endpush