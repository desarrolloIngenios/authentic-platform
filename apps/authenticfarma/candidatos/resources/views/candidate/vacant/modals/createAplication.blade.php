<div id="modalAplicar" class="app-modal">
    <div class="app-modal-content">
        <div class="app-modal-header text-center">
            <h5 class="modal-title text-center"><strong>Confirmar Aplicación</strong></h5>
            <button type="button" class="modal-close text-dark" onclick="cerrarModal()">&times;</button>
        </div>
        <div class="app-modal-body text-center">
            <div class="mb-4">
                <i class="la la-briefcase" style="font-size: 48px; color: #00945e;"></i>
            </div>
            <h4 class="cargo-titulo mb-3 text-dark">¿Deseas aplicar a esta vacante?</h4>
            <p class="texto-cargo text-dark"></p>
            <p class="text-dark">Esta acción no se puede deshacer.</p>
            <form id="formAplicar" method="POST" action="" class="mt-4">
                @csrf
                <input type="hidden" name="idofoferta_laboral" id="idofoferta_laboral">
                <div class="button-group">
                    <button type="button" class="btn btn-outline-secondary" onclick="cerrarModal()">
                        <i class="la la-times text-dark"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="la la-check"></i> Confirmar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>