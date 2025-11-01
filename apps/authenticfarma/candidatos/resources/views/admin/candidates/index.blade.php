@extends('layout.admin')

@section('title', 'Candidatos')

@section('content')
<div class="container-fluid">
    <div class="row mt-4 mb-3">
        <div class="col-md-6">
            <h2 class="mb-0 fw-bold text-dark d-flex align-items-center" style="font-size:2rem;">
                <i class="la la-users mr-1" style="color:#198754;"></i> Listado de Candidatos
            </h2>
        </div>
        <div class="col-md-6">
            <form class="d-flex justify-content-end" id="form-buscar-candidato" method="GET" action="{{ route('candidates.index') }}">
                <input type="text" name="buscar" id="input-buscar-candidato" class="input-search-candidate me-2" placeholder="Buscar por nombre, cargo o correo..." style="max-width: 320px;">
            </form>
        </div>
    </div>
    <div class="row g-4">
        @forelse($candidatos as $candidato)
        <div class="col-12">
            <div class="card shadow-sm mb-2 custom-candidate-card">
                <div class="card-body py-3 px-4">
                    <div class="row align-items-center text-center">
                        <div class="col-6 col-md-4">
                            <div class="fw-bold text-dark"><i class="la la-user me-1"></i> {{ $candidato->nombres }} {{ $candidato->apellidos }}</div>
                            <div class="text-muted small"><span class="fw-semibold"><i class="la la-briefcase me-1"></i> Último cargo:</span> {{ optional($candidato->experienciasLaborales->sortByDesc('fecha_fin')->first())->tipoCargo->descripcion ?? 'Sin registro' }}</div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="text-muted small"><i class="la la-envelope me-1"></i> {{ $candidato->correo->email ?? '-' }}</div>
                            <div class="text-muted small"><i class="la la-phone me-1"></i> {{ $candidato->telefono->numero_telefono ?? '-' }}</div>
                        </div>
                        <div class="col-12 col-md-3 mt-2 mt-md-0">
                            <div class="text-muted small"><span class="fw-semibold"><i class="la la-venus-mars me-1"></i> Género:</span> {{ $candidato->genero->descripcion ?? '-' }}</div>
                            <div class="text-muted small"><span class="fw-semibold"><i class="la la-map-marker me-1"></i> Ciudad:</span> {{ $candidato->ubicacion?->ciudadResidencia?->nombre ?? '-' }}</div>
                        </div>
                        <div class="col-12 col-md-2 mt-3 mt-md-0 d-flex justify-content-center gap-2">
                            <a href="{{ route('candidates.show', $candidato->id_candidato) }}" class="btn btn-outline-success2 d-inline-flex align-items-center justify-content-center" title="Ver" style="width:36px;height:36px;padding:0;border-radius:50%;">
                                <i class="la la-eye"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-warning text-center">No hay candidatos registrados.</div>
        </div>
        @endforelse
    </div>
    <div class="d-flex justify-content-center mt-4">
        {{ $candidatos->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('input-buscar-candidato');
    const form = document.getElementById('form-buscar-candidato');
    let timeout = null;
    let loader = null;

    // Crear loader si no existe
    function showLoader() {
        if (!loader) {
            loader = document.createElement('div');
            loader.className = 'candidate-loader';
            loader.innerHTML = '<span class="spinner-border spinner-border-sm text-success" role="status" aria-hidden="true"></span> <span class="ms-2">Buscando...</span>';
            loader.style.display = 'flex';
            loader.style.justifyContent = 'center';
            loader.style.alignItems = 'center';
            loader.style.margin = '1.5rem 0';
            document.querySelector('.row.g-4').innerHTML = '';
            document.querySelector('.row.g-4').appendChild(loader);
        }
    }
    function hideLoader() {
        if (loader && loader.parentNode) {
            loader.parentNode.removeChild(loader);
            loader = null;
        }
    }

    input.addEventListener('input', function() {
        clearTimeout(timeout);
        timeout = setTimeout(function() {
            buscarCandidatos(input.value);
        }, 400);
    });

    function buscarCandidatos(query) {
        showLoader();
        const url = form.getAttribute('action') + '?buscar=' + encodeURIComponent(query);
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newList = doc.querySelector('.row.g-4');
                const newPagination = doc.querySelector('.d-flex.justify-content-center.mt-4');
                document.querySelector('.row.g-4').innerHTML = newList ? newList.innerHTML : '';
                document.querySelector('.d-flex.justify-content-center.mt-4').innerHTML = newPagination ? newPagination.innerHTML : '';
                hideLoader();
            })
            .catch(() => {
                hideLoader();
            });
    }

    // Captura clicks en la paginación para hacer AJAX
    document.addEventListener('click', function(e) {
        const target = e.target.closest('.d-flex.justify-content-center.mt-4 .pagination a');
        if (target) {
            e.preventDefault();
            showLoader();
            fetch(target.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newList = doc.querySelector('.row.g-4');
                    const newPagination = doc.querySelector('.d-flex.justify-content-center.mt-4');
                    document.querySelector('.row.g-4').innerHTML = newList ? newList.innerHTML : '';
                    document.querySelector('.d-flex.justify-content-center.mt-4').innerHTML = newPagination ? newPagination.innerHTML : '';
                    hideLoader();
                })
                .catch(() => {
                    hideLoader();
                });
        }
    });
});
</script>
@endpush


