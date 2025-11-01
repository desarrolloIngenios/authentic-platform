<div id="drawer-vacante" class="drawer-overlay">
    <div class="drawer-panel">
        <div class="drawer-header">
            <h5 id="drawer-titulo">Detalles de la Vacante</h5>
            <button class="drawer-close" onclick="cerrarDrawer()">&times;</button>
        </div>
        <div class="drawer-body" id="drawer-contenido">
            <img src="{{asset('images/loader.gif')}}" alt=""> <p class="text-dark">Cargando información...</p>
        </div>
        {{-- <div class="estado-postulacion px-4 py-3 border-top">
            <h5 class="text-dark fw-bold">📊 Estado de tu postulación</h5>
            <div class="d-flex align-items-center justify-content-between">
                <span class="badge {{ $postulacion->estado == 'Pendiente' ? 'bg-warning' : 
                    ($postulacion->estado == 'Seleccionado' ? 'bg-success' : 
                    ($postulacion->estado == 'Rechazado' ? 'bg-danger' : 'bg-info')) }} text-white">
                    {{ $postulacion->estado }}
                </span>
                <small class="text-muted">
                    <i class="las la-calendar"></i>
                     Postulado el {{ $postulacion->fecha_creacion->format('d/m/Y') }} 
                </small>
            </div>
        </div> --}}
    </div>
</div>

<script>
    function abrirDrawer(vacanteId) {
        const drawer = document.getElementById('drawer-vacante');
        drawer.style.display = 'flex';
        document.getElementById('drawer-contenido').innerHTML = '<img width="100" src="{{asset('images/logo_loader.gif')}}" alt=""> <p class="text-dark">Cargando información...</p>';
        
        fetch(`/postulation/${vacanteId}/vacant`)
            .then(res => res.ok ? res.text() : Promise.reject('Error al cargar la vacante'))
            .then(html => {
                document.getElementById('drawer-contenido').innerHTML = html;
            })
            .catch(err => {
                document.getElementById('drawer-contenido').innerHTML = '<div class="alert alert-danger">Error al cargar la información de la vacante.</div>';
                console.error(err);
            });
    }

    function cerrarDrawer() {
        document.getElementById('drawer-vacante').style.display = 'none';
    }

    document.getElementById('drawer-vacante').addEventListener('click', function(event) {
        const panel = this.querySelector('.drawer-panel');
        if (!panel.contains(event.target)) {
            cerrarDrawer();
        }
    });
</script>
