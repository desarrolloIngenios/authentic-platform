@extends('layout.admin')

@section('title', 'Dashboard')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4 fw-bold text-dark">
        <i class="la la-chart-bar me-2"></i>Panel de Administración
    </h2>

    <!-- KPIs -->
    <div class="row g-4 mb-4">
        <!-- Tarjetas de KPIs aquí (sin cambios) -->
        @foreach ([
            ['icon' => 'users', 'color' => 'text-color', 'label' => 'Total Usuarios', 'value' => $totalUsuarios],
            ['icon' => 'female', 'color' => '', 'style' => 'color:#0057b8;', 'label' => 'Total Mujeres', 'value' => $totalMujeres],
            ['icon' => 'male', 'color' => 'text-color', 'label' => 'Total Hombres', 'value' => $totalHombres],
            ['icon' => 'briefcase', 'color' => '', 'style' => 'color:#0057b8;', 'label' => 'Vacantes Abiertas', 'value' => $vacantesAbiertas],
            ['icon' => 'building', 'color' => 'text-color', 'label' => 'Total Empresas', 'value' => $totalEmpresas ?? '--'],
            ['icon' => 'file-alt', 'color' => '', 'style' => 'color:#0057b8;', 'label' => 'Total Postulaciones', 'value' => $totalPostulaciones ?? '--'],
        ] as $kpi)
        <div class="col-12 col-sm-6 col-md-4 col-lg-2">
            <div class="card shadow-sm h-100" style="border:2px solid #00a86b !important;">
                <div class="card-body text-center">
                    <i class="la la-{{ $kpi['icon'] }} {{ $kpi['color'] ?? '' }}" style="font-size:2.5rem;{{ $kpi['style'] ?? '' }}"></i>
                    <h6 class="mt-2 mb-1">{{ $kpi['label'] }}</h6>
                    <div class="display-6 fw-bold">{{ $kpi['value'] }}</div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Grid de gráficas y tabla -->
    <div class="row g-4">
        <div class="col-lg-6 d-flex flex-column gap-4">
            <div class="card shadow-sm flex-fill" style="border:2px solid #00a86b !important;">
                <div class="card-header bg-white fw-bold">Usuarios registrados por mes (últimos 12 meses)</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <canvas id="chart-usuarios-mes" style="min-height: 300px; height: 300px;"></canvas>
                    </div>
                </div>
            </div>
            <div class="card shadow-sm flex-fill mt-4" style="border:2px solid #00a86b !important;">
                <div class="card-header bg-white fw-bold">Vacantes publicadas por mes (últimos 12 meses)</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <canvas id="chart-vacantes-mes" style="min-height: 300px; height: 300px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 d-flex flex-column gap-4">
            <div class="card shadow-sm flex-fill" style="border:2px solid #00a86b !important;">
                <div class="card-header bg-white fw-bold">Candidatos por nivel educativo</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <canvas id="chart-nivel-educativo" style="min-height: 300px; height: 300px;"></canvas>
                    </div>
                </div>
            </div>
            <!-- Tabla de últimos usuarios -->
            <div class="card shadow border-2 flex-fill mt-4" style="border:2px solid #00a86b !important;">
                <div class="card-header bg-white fw-bold d-flex align-items-center" style="font-size:1.15rem;">
                    <i class="la la-users text-color me-2" style="font-size:1.5rem;"></i>
                    Últimos usuarios registrados
                </div>
                <div class="card-body p-0 table-responsive">
                    <table class="table table-hover align-middle mb-0 border-success">
                        <thead class="table-light">
                            <tr>
                                <th style="width:35%;">Nombre</th>
                                <th style="width:35%;">Email</th>
                                <th style="width:30%;">Profesión</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ultimosUsuarios ?? [] as $usuario)
                                <tr>
                                    <td>
                                        <span class="fw-semibold">
                                            {{ $usuario->name ?? $usuario['name'] ?? $usuario->nombre ?? '' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-muted" style="font-size:0.97em;">
                                            {{ $usuario->email ?? $usuario['email'] ?? '' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border" style="font-size:0.98em;">
                                            {{ $usuario->profesion ?? 'Sin información' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">
                                        <i class="la la-user-times" style="font-size:1.5rem;"></i><br>
                                        No hay registros recientes
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const labels = @json($labels);
const data = @json($datos);
const vacantesData = @json($vacantesDatos);
const nivelLabels = @json($nivelesLabels);
const nivelData = @json($nivelesDatos);

const ctx = document.getElementById('chart-usuarios-mes').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: labels,
        datasets: [{
            label: 'Usuarios registrados',
            data: data,
            borderColor: '#0057b8',
            backgroundColor: 'rgba(0,87,184,0.1)',
            fill: true,
            tension: 0.3
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            title: { display: false }
        },
        scales: {
            y: { beginAtZero: true, precision:0 }
        }
    }
});

const ctxVacantes = document.getElementById('chart-vacantes-mes').getContext('2d');
new Chart(ctxVacantes, {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: 'Vacantes publicadas',
            data: vacantesData,
            backgroundColor: '#00a86b',
            borderColor: '#008f5a',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            title: { display: false }
        },
        scales: {
            y: { beginAtZero: true, precision:0 }
        }
    }
});

const ctxNivel = document.getElementById('chart-nivel-educativo').getContext('2d');
new Chart(ctxNivel, {
    type: 'bar',
    data: {
        labels: nivelLabels,
        datasets: [{
            label: 'Candidatos',
            data: nivelData,
            backgroundColor: '#0057b8',
            borderColor: '#003974',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            title: { display: false }
        },
        scales: {
            y: { beginAtZero: true, precision:0 }
        }
    }
});
</script>
@endpush
