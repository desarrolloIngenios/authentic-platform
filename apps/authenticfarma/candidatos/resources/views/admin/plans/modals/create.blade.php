@section('styles')
<style>
    .btn-add-detail {
        background: linear-gradient(90deg, #00a86b 60%, #00c97b 100%);
        color: #fff !important;
        border: none;
        border-radius: 20px;
        font-weight: 600;
        box-shadow: 0 2px 8px rgba(0,168,107,0.08);
        transition: background 0.2s, transform 0.2s, box-shadow 0.2s;
        padding: 0.4rem 1.2rem;
        font-size: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .btn-add-detail:hover {
        background: linear-gradient(90deg, #00c97b 60%, #00a86b 100%);
        color: #fff;
        transform: translateY(-2px) scale(1.04);
        box-shadow: 0 4px 16px rgba(0,168,107,0.15);
    }
    .detail-input-group {
        display: flex !important;
        align-items: center !important;
        gap: 0.5rem !important;
        margin-bottom: 0.5rem !important;
        flex-wrap: nowrap !important;
        width: 100% !important;
        overflow: hidden !important;
        background: none !important;
        border: none !important;
        padding: 0 !important;
    }
    .detail-input {
        flex: 1 1 0% !important;
        min-width: 0 !important;
        width: 1% !important;
        height: 38px !important;
        border-radius: 8px !important;
        border: 1px solid #e0e0e0 !important;
        padding: 0.5rem 0.75rem !important;
        font-size: 1rem !important;
        background: #f8f9fa !important;
        transition: border 0.2s !important;
        color: #222 !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
        margin: 0 !important;
    }
    .detail-input:focus {
        border-color: #00a86b;
        outline: none;
        background: #fff;
    }
    .btn-remove-detail {
        width: 38px !important;
        height: 38px !important;
        min-width: 38px !important;
        min-height: 38px !important;
        max-width: 38px !important;
        max-height: 38px !important;
        border-radius: 8px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 0 !important;
        font-size: 1.2rem !important;
        background: #f44336 !important;
        color: #fff !important;
        border: none !important;
        transition: background 0.2s, box-shadow 0.2s !important;
        box-shadow: 0 2px 6px rgba(244,67,54,0.08) !important;
        flex-shrink: 0 !important;
        margin: 0 !important;
    }
    .btn-remove-detail:hover {
        background: #c62828;
        color: #fff;
    }
    @media (max-width: 600px) {
      .detail-input-group {
        flex-wrap: nowrap !important;
      }
      .detail-input {
        min-width: 0 !important;
      }
      .btn-remove-detail {
        min-width: 38px;
        max-width: 38px;
      }
    }
    .detail-input-group .detail-input,
    #detailsContainer .detail-input-group input.detail-input {
        width: auto !important;
        flex: 1 1 0% !important;
        min-width: 0 !important;
        max-width: 100% !important;
        min-height: 38px !important;
        height: 38px !important;
        padding: 0.5rem 0.75rem !important;
        box-sizing: border-box !important;
        margin: 0 !important;
    }
</style>
@endsection 
{{-- Modal Crear Plan --}}
<div id="modalCrearPlan" class="custom-modal-overlay" role="dialog" aria-labelledby="tituloCrearPlan" aria-hidden="true">
  <div class="custom-modal custom-modal--xlarge">
    <div class="custom-modal-header">
      <h2 id="tituloCrearPlan" class="custom-modal-title">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 8px;">
          <path d="M20 7H4a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2Z"/>
          <path d="M16 7V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v3"/>
        </svg>
        Crear Plan
      </h2>
      <button class="custom-modal-close">&times;</button>
    </div>
    <form id="formCrearPlan" action="{{ route('plans.store') }}" method="POST">
      @csrf
      <div class="custom-modal-body">
        <div class="row">
          <div class="col-md-4">
            <div class="form-floating-group">
              <input type="text" name="name" placeholder=" " required maxlength="200" />
              <span class="floating-label">Nombre del plan *</span>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-floating-group">
              <input type="text" name="price" placeholder=" " required min="0" step="0.01" />
              <span class="floating-label">Precio *</span>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-floating-group">
              <select name="status" required>
                <option value="active">Activo</option>
                <option value="inactive">Inactivo</option>
              </select>
              <span class="floating-label">Estado *</span>
            </div>
          </div>
          <div class="col-md-12">
            <div class="form-floating-group2">
              <div class="mb-2 d-flex align-items-center justify-content-between">
                <p class="mb-0"><strong>Detalles del producto</strong></p>
                <button type="button" class="btn btn-sm btn-add-detail btn-primary-custom" id="btnAddDetail"><i class="la la-plus"></i> Agregar detalle</button>
              </div>
              <div id="detailsContainer"></div>
            </div>
          </div>
        </div>
      </div>
      <div class="custom-modal-footer d-flex justify-content-center">
        <span id="detailsError" class="text-danger me-3" style="display:none;"></span>
        <button type="button" class="btn btn-outline btn-close-custom">Cancelar</button>
        <button type="submit" class="btn btn-secondary">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="20,6 9,17 4,12"></polyline>
          </svg>
          Guardar Plan
        </button>
      </div>
    </form>
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const trigger = document.querySelector('[data-modal-target="modalCrearPlan"]');
  const modal = document.getElementById('modalCrearPlan');
  const closeBtn = modal.querySelector('.custom-modal-close');
  const btnCancel = modal.querySelector('.btn-close-custom');
  const btnAddDetail = document.getElementById('btnAddDetail');
  const detailsContainer = document.getElementById('detailsContainer');

  trigger.addEventListener('click', function (e) {
    e.preventDefault();
    modal.classList.add('show');
    document.body.classList.add('modal-open');
  });
  closeBtn.addEventListener('click', function () {
    modal.classList.remove('show');
    document.body.classList.remove('modal-open');
  });
  btnCancel.addEventListener('click', function () {
    modal.classList.remove('show');
    document.body.classList.remove('modal-open');
  });

  btnAddDetail.addEventListener('click', function () {
    addDetailField();
  });

  function addDetailField(value = '') {
    const wrapper = document.createElement('div');
    wrapper.className = 'detail-input-group';
    wrapper.innerHTML = `
      <input type="text" name="details[][description]" class="detail-input" placeholder="Detalle del producto" required maxlength="255" value="${value ? value.replace(/"/g, '&quot;') : ''}">
      <button type="button" class="btn-remove-detail btn-primary-custom" tabindex="-1"><i class="la la-trash"></i></button>
    `;
    wrapper.querySelector('.btn-remove-detail').onclick = function () {
      wrapper.remove();
    };
    detailsContainer.appendChild(wrapper);
  }

  const form = document.getElementById('formCrearPlan');
  const detailsError = document.getElementById('detailsError');
  form.addEventListener('submit', function(e) {
    detailsError.style.display = 'none';
    const details = detailsContainer.querySelectorAll('input[name="details[][description]"]');
    if (details.length === 0) {
      e.preventDefault();
      detailsError.textContent = 'Agrega al menos un detalle del plan.';
      detailsError.style.display = 'inline';
      return false;
    }
    for (let i = 0; i < details.length; i++) {
      if (!details[i].value.trim()) {
        e.preventDefault();
        detailsError.textContent = 'Ningún detalle puede estar vacío.';
        detailsError.style.display = 'inline';
        return false;
      }
    }
  });

  // Formatear el input de precio en formato COP
  const priceInput = document.querySelector('input[name="price"]');
  if (priceInput) {
    priceInput.addEventListener('input', function(e) {
      let value = this.value.replace(/[^\d]/g, '');
      if (value) {
        value = parseInt(value, 10).toLocaleString('es-CO');
        this.value = value;
      } else {
        this.value = '';
      }
    });
    priceInput.addEventListener('blur', function(e) {
      let value = this.value.replace(/[^\d]/g, '');
      if (value) {
        value = parseInt(value, 10).toLocaleString('es-CO');
        this.value = value;
      }
    });
  }
});
</script>

