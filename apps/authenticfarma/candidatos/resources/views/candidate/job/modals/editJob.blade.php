

<div id="modalEditarTrabajo" class="custom-modal-overlay" role="dialog" aria-labelledby="tituloEditarTrabajo" aria-hidden="true">
  <div class="custom-modal custom-modal--large">
    <div class="custom-modal-header">
      <h2 id="tituloEditarTrabajo" class="custom-modal-title">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 8px;">
          <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
          <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
        </svg>
        Editar Experiencia Laboral
      </h2>
      <button class="custom-modal-close">&times;</button>
    </div>
    <div class="modal-body" id="editarTrabajoBody">
        <!-- Spinner mientras carga -->
        <div class="text-center py-4" id="loadingCrear">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Cargando...</span>
          </div>
        </div>
    </div>
    
    
  </div>
</div>

<script>  document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('modalEditarTrabajo');
    const modalBody = document.getElementById('editarTrabajoBody');
    const loading = document.getElementById('loadingCrear');
    const closeBtn = modal.querySelector('.custom-modal-close');

    document.querySelectorAll('.editar-job').forEach(button => {
      button.addEventListener('click', function (e) {
        e.preventDefault();
        const id = this.getAttribute('data-id');
        modal.classList.add('show');
        document.body.classList.add('modal-open');

        loading.style.display = 'block';
        modalBody.innerHTML = '';

        fetch(`/job/modal-edit/${id}`)
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
    });

    closeBtn.addEventListener('click', function () {
      modal.classList.remove('show');
      document.body.classList.remove('modal-open');
    });
  });

  function inicializarCheckboxTrabajo() {
      const fechaFinDiv = document.getElementById('fecha_fin');
      const inputFechaFin = fechaFinDiv?.querySelector('input[name="fecha_fin"]');
      const checkbox = document.getElementById('trabajo_actual');

      if (!inputFechaFin || !checkbox) return;

      // Función para mostrar/ocultar según el estado del checkbox
      function actualizarVisibilidad() {
        if (checkbox.checked) {
          fechaFinDiv.style.display = 'none';
          inputFechaFin.value = '';
        } else {
          fechaFinDiv.style.display = '';
        }
      }

      // Inicialización: si el valor original es null, esconder y marcar el checkbox
      if (!inputFechaFin.value) {
        checkbox.checked = true;
        fechaFinDiv.style.display = 'none';
      }
      checkbox.addEventListener('change', actualizarVisibilidad);
      actualizarVisibilidad(); 
    }
</script> 


