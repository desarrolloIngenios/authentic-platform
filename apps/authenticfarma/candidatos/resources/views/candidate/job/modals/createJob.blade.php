{{-- Modal Agregar Experiencia Laboral --}}
<div id="modalAgregarTrabajo" class="custom-modal-overlay" role="dialog" aria-labelledby="tituloAgregarTrabajo" aria-hidden="true">
  <div class="custom-modal custom-modal--large">
    <div class="custom-modal-header">
      <h2 id="tituloAgregarTrabajo" class="custom-modal-title">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 8px;">
          <path d="M20 7H4a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2Z"/>
          <path d="M16 7V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v3"/>
        </svg>
        Agregar Experiencia Laboral
      </h2>
      <button class="custom-modal-close">&times;</button>
    </div>    <div class="modal-body" id="crearTrabajoBody">
        <!-- Spinner mientras carga -->
        <div class="text-center py-4" id="loadingCrear">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Cargando...</span>
          </div>
        </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const trigger = document.querySelector('[data-modal-target="modalAgregarTrabajo"]');
    const modal = document.getElementById('modalAgregarTrabajo');
    const modalBody = document.getElementById('crearTrabajoBody');
    const loading = document.getElementById('loadingCrear');
    const closeBtn = modal.querySelector('.custom-modal-close');
    

    

    trigger.addEventListener('click', function (e) {
      e.preventDefault();
      modal.classList.add('show'); // o el que uses para mostrar tu modal
      document.body.classList.add('modal-open');

      loading.style.display = 'block';
      modalBody.innerHTML = '';      fetch('/job/modal-create')
        .then(response => response.text())
        .then(html => {
          modalBody.innerHTML = html;
          loading.style.display = 'none';
          inicializarCheckboxTrabajo();
        })
        .catch(() => {
          modalBody.innerHTML = '<p class="text-danger">Error al cargar el formulario.</p>';
          loading.style.display = 'none';
        });
    });

    closeBtn.addEventListener('click', function () {
      modal.classList.remove('show');
      document.body.classList.remove('modal-open');
    });
  });

  function inicializarCheckboxTrabajo() {
    const checkbox = document.getElementById('trabajo_actual');
    const divFechaFin = document.getElementById('fecha_fin');
    const inputFechaFin = divFechaFin?.querySelector('input[name="fecha_fin"]');

    if (!checkbox || !divFechaFin || !inputFechaFin) return;

    function actualizarVisibilidad() {
      if (checkbox.checked) {
        divFechaFin.style.display = 'none';
        inputFechaFin.value = '';
      } else {
        divFechaFin.style.display = '';
      }
    }

    checkbox.addEventListener('change', actualizarVisibilidad);
    actualizarVisibilidad(); 
  }
</script>

