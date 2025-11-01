{{-- Modal Agregar Formación Académica --}}
<div id="modalAgregarLanguage" class="custom-modal-overlay" role="dialog" aria-labelledby="tituloAgregarIdioma" aria-hidden="true">
  <div class="custom-modal custom-modal--large">
    <div class="custom-modal-header">
      <h2 id="tituloAgregarIdioma" class="custom-modal-title">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 8px;">
          <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
          <path d="M6 12v5c3 3 9 3 12 0v-5"/>
        </svg>
        Agregar Idioma
      </h2>
      <button class="custom-modal-close">&times;</button>
    </div>
    <div class="modal-body" id="crearLanguageBody">
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
    const trigger = document.querySelector('[data-modal-target="modalAgregarLanguage"]');
    const modal = document.getElementById('modalAgregarLanguage');
    const modalBody = document.getElementById('crearLanguageBody');
    const loading = document.getElementById('loadingCrear');
    const closeBtn = modal.querySelector('.custom-modal-close');

    trigger.addEventListener('click', function (e) {
      e.preventDefault();
      modal.classList.add('show'); // o el que uses para mostrar tu modal
      document.body.classList.add('modal-open');

      loading.style.display = 'block';
      modalBody.innerHTML = '';

      fetch('/language/modal-create')
        .then(response => response.text())
        .then(html => {
          modalBody.innerHTML = html;
          loading.style.display = 'none';
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
</script>

