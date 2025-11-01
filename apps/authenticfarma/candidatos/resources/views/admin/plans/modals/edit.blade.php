{{-- Modal Editar Plan --}}

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
    #detailsContainerEdit .detail-input-group input.detail-input {
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

<div id="modalEditarPlan" class="custom-modal-overlay" role="dialog" aria-labelledby="tituloEditarPlan" aria-hidden="true">
  <div class="custom-modal custom-modal--xlarge">
    <div class="custom-modal-header">
      <h2 id="tituloEditarPlan" class="custom-modal-title">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 8px;">
          <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
          <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
        </svg>
        Editar Plan
      </h2>
      <button class="custom-modal-close">&times;</button>
    </div>
    <form id="formEditarPlan" action="{{ route('plans.update', ['id' => 'PLAN_ID']) }}" method="POST">
      @csrf
      @method('PUT')
      <input type="hidden" name="id" id="edit_plan_id" />
      <div class="custom-modal-body">
        <div class="row">
          <div class="col-md-4">
            <div class="form-floating-group">
              <input type="text" name="name" id="edit_plan_name" placeholder=" " required maxlength="200" />
              <span class="floating-label">Nombre del plan *</span>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-floating-group">
              <input type="text" name="price" id="edit_plan_price" placeholder=" " required min="0" step="0.01" />
              <span class="floating-label">Precio *</span>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-floating-group">
              <select name="status" id="edit_plan_status" required>
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
                <button type="button" class="btn btn-sm btn-add-detail btn-primary-custom" id="btnAddDetailEdit"><i class="la la-plus"></i> Agregar detalle</button>
              </div>
              <div id="detailsContainerEdit"></div>
            </div>
          </div>
          
        </div>
        
      </div>
      <div class="custom-modal-footer d-flex justify-content-center">
        <span id="detailsErrorEdit" class="text-danger me-3" style="display:none;"></span>
        <button type="button" class="btn btn-outline btn-close-custom">Cancelar</button>
        <button type="submit" class="btn btn-secondary">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="20,6 9,17 4,12"></polyline>
          </svg>
          Guardar Cambios
        </button>
      </div>
    </form>
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('[data-modal-target="modalEditarPlan"]').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      const planId = this.getAttribute('data-id');
      // AJAX para obtener datos del plan
      fetch(`/admin/plans/${planId}`)
        .then(response => response.json())
        .then(plan => {
          document.getElementById('edit_plan_id').value = plan.id;
          document.getElementById('edit_plan_name').value = plan.name;
          document.getElementById('edit_plan_price').value = plan.price;
          document.getElementById('edit_plan_status').value = plan.status;
          // Actualizar la acción del formulario
          const form = document.getElementById('formEditarPlan');
          form.action = form.action.replace('PLAN_ID', plan.id);
          // Limpiar detalles previos
          const detailsContainer = document.getElementById('detailsContainerEdit');
          detailsContainer.innerHTML = '';
          if (plan.details && Array.isArray(plan.details)) {
            plan.details.forEach(function(detail) {
              addDetailFieldEdit(detail.description);
            });
          }
          document.getElementById('modalEditarPlan').classList.add('show');
          document.body.classList.add('modal-open');
        });
    });
  });
  const closeBtn = document.querySelector('#modalEditarPlan .custom-modal-close');
  const btnCancel = document.querySelector('#modalEditarPlan .btn-close-custom');
  const btnAddDetail = document.getElementById('btnAddDetailEdit');
  const detailsContainer = document.getElementById('detailsContainerEdit');
  closeBtn.addEventListener('click', function () {
    document.getElementById('modalEditarPlan').classList.remove('show');
    document.body.classList.remove('modal-open');
  });
  btnCancel.addEventListener('click', function () {
    document.getElementById('modalEditarPlan').classList.remove('show');
    document.body.classList.remove('modal-open');
  });
  btnAddDetail.addEventListener('click', function () {
    addDetailFieldEdit();
  });
  function addDetailFieldEdit(value = '') {
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
  const formEdit = document.getElementById('formEditarPlan');
  const detailsErrorEdit = document.getElementById('detailsErrorEdit');
  formEdit.addEventListener('submit', function(e) {
    detailsErrorEdit.style.display = 'none';
    const details = detailsContainer.querySelectorAll('input[name="details[][description]"]');
    if (details.length === 0) {
      e.preventDefault();
      detailsErrorEdit.textContent = 'Agrega al menos un detalle del plan.';
      detailsErrorEdit.style.display = 'inline';
      return false;
    }
    for (let i = 0; i < details.length; i++) {
      if (!details[i].value.trim()) {
        e.preventDefault();
        detailsErrorEdit.textContent = 'Ningún detalle puede estar vacío.';
        detailsErrorEdit.style.display = 'inline';
        return false;
      }
    }
  });
  // Formatear el input de precio en formato COP
  const priceInputEdit = document.getElementById('edit_plan_price');
  if (priceInputEdit) {
    priceInputEdit.addEventListener('input', function(e) {
      let value = this.value.replace(/[^\d]/g, '');
      if (value) {
        value = parseInt(value, 10).toLocaleString('es-CO');
        this.value = value;
      } else {
        this.value = '';
      }
    });
    priceInputEdit.addEventListener('blur', function(e) {
      let value = this.value.replace(/[^\d]/g, '');
      if (value) {
        value = parseInt(value, 10).toLocaleString('es-CO');
        this.value = value;
      }
    });
  }
});
</script>
