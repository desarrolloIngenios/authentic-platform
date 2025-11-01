
<div id="drawer-vacante" class="drawer-overlay">
    <div class="drawer-panel">
        <div class="drawer-header">
            <h5 id="drawer-titulo">Detalles de la Vacante</h5>
            <button class="drawer-close" onclick="cerrarDrawer()">&times;</button>
        </div>
        <div class="drawer-body" id="drawer-contenido">
            <img src="{{asset('images/loader.gif')}}" alt=""> <p class="text-dark">Cargando información...</p>
        </div>
    </div>
</div>

<script>
    function abrirDrawer(vacanteId) {
    const drawer = document.getElementById('drawer-vacante');
    drawer.style.display = 'flex';
    document.getElementById('drawer-contenido').innerHTML = '<img width="100" src="{{asset('images/logo_loader.gif')}}" alt=""> <p class="text-dark">Cargando información...</p>';
    fetch(`/vacante/${vacanteId}`)
        .then(res => res.ok ? res.text() : 'Error al cargar la vacante')
        .then(html => {
            document.getElementById('drawer-contenido').innerHTML = html;
        })
        .catch(err => {
            document.getElementById('drawer-contenido').innerHTML = '<p>Error al cargar información.</p>';
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
