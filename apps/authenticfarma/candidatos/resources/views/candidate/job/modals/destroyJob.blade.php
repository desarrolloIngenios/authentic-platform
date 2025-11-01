
<div id="modalEliminarJob" class="custom-modal-overlay" role="dialog" aria-labelledby="tituloEliminarJob" aria-hidden="true">
  <div class="custom-modal custom-modal--small">
    <div class="custom-modal-header">
      <h2 id="tituloEliminarJob" class="custom-modal-title">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 8px;">
          <polyline points="3,6 5,6 21,6"></polyline>
          <path d="M19,6v14a2,2 0 0,1-2,2H7a2,2 0 0,1-2-2V6m3,0V4a2,2 0 0,1,2-2h4a2,2 0 0,1,2,2v2"></path>
          <line x1="10" y1="11" x2="10" y2="17"></line>
          <line x1="14" y1="11" x2="14" y2="17"></line>
        </svg>
        Confirmar Eliminación
      </h2>
      <button class="custom-modal-close" type="button">&times;</button>
    </div>

    <div class="custom-modal-body">
      <div style="display: flex; align-items: flex-start; gap: 1rem;">
        <div style="flex-shrink: 0; margin-top: 2px;">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="15" y1="9" x2="9" y2="15"></line>
            <line x1="9" y1="9" x2="15" y2="15"></line>
          </svg>
        </div>
        <div>
          <h3 style="margin: 0 0 0.5rem 0; font-size: 1rem; font-weight: 600; color: var(--modal-text);">
            ¿Eliminar experiencia laboral?
          </h3>
          <p id="textoEliminar" style="margin: 0; color: var(--modal-text-secondary); line-height: 1.5;">
            Esta acción no se puede deshacer. La información de la experiencia laboral será eliminada permanentemente.
          </p>
        </div>
      </div>
    </div>

    <div class="custom-modal-footer d-flex justify-content-center">
      <button type="button" class="btn btn-outline btn-close-custom">Cancelar</button>
      <button type="button" class="btn btn-danger" id="confirmarEliminar">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <polyline points="3,6 5,6 21,6"></polyline>
          <path d="M19,6v14a2,2 0 0,1-2,2H7a2,2 0 0,1-2-2V6m3,0V4a2,2 0 0,1,2-2h4a2,2 0 0,1,2,2v2"></path>
        </svg>
        Eliminar
      </button>
    </div>
  </div>
</div>


<form id="formEliminarJob" method="POST" style="display: none;">
  @csrf
  @method('DELETE')
</form>


<script>
  document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('modalEliminarJob');
    const btnCerrar = modal.querySelector('.custom-modal-close');
    const btnCancelar = modal.querySelector('.btn-close-custom');
    const btnConfirmar = document.getElementById('confirmarEliminar');
    const textoEliminar = document.getElementById('textoEliminar');
    const formEliminar = document.getElementById('formEliminarJob');

    let idSeleccionado = null;

    // Activar modal al hacer clic en el botón eliminar
    document.querySelectorAll('.btn-eliminar-job').forEach(btn => {
      btn.addEventListener('click', function () {
        idSeleccionado = this.dataset.id;
        const cargo = this.dataset.cargo;        textoEliminar.textContent = `¿Estás seguro de que deseas eliminar la experiencia laboral ? Esta acción no se puede deshacer.`;
        modal.classList.add('show');
        document.body.classList.add('modal-open');
      });
    });

    // Cerrar modal
    [btnCerrar, btnCancelar].forEach(btn => {
      btn.addEventListener('click', function() {
        modal.classList.remove('show');
        document.body.classList.remove('modal-open');
      });
    });

    // Confirmar eliminación
    btnConfirmar.addEventListener('click', function () {
      if (idSeleccionado) {
        formEliminar.setAttribute('action', `/job/${idSeleccionado}`);
        formEliminar.submit();
      }
    });
  });
</script>