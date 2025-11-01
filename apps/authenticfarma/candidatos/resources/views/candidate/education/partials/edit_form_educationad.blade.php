<form id="formEditarEducacionad" method="POST" action="{{ route('educationad.update', $registro->idhvcan_form_ad) }}" novalidate>
    @csrf
    @method('PUT')
    <div class="custom-modal-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-floating-group">
                    <input type="text" name="titulo" value="{{ $registro->titulo }}" placeholder=" " required autocomplete="off" />
                    <span class="floating-label">Título obtenido *</span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-floating-group">
                    <input type="text" name="institucion" value="{{ $registro->institucion }}" placeholder=" " required autocomplete="organization" />
                    <span class="floating-label">Institución educativa *</span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-floating-group">
                    <input type="date" name="fecha_inicio" value="{{ \Carbon\Carbon::parse($registro->fecha_inicio)->format('Y-m-d') }}" placeholder=" " required />
                    <span class="floating-label">Fecha de inicio *</span>
                </div>
            </div>
            <div class="col-md-6" id="fecha_fin">
                <div class="form-floating-group">
                    <input type="date" name="fecha_fin" value="{{ $registro->fecha_fin ? \Carbon\Carbon::parse($registro->fecha_fin)->format('Y-m-d') : '' }}" placeholder=" " />
                    <span class="floating-label">Fecha de finalización</span>
                </div>
            </div>
            <div class="col-12">
                <div class="form-floating-group" style="margin-bottom: 0;">
                    <input type="checkbox" name="actualmente_estudio" value="1" id="actualmente_estudio" {{ is_null($registro->fecha_fin) ? 'checked' : '' }} />
                        <label for="actualmente_estudio">Actualmente estoy cursando esta formación</label>
                </div>
            </div>
        </div>
    </div>

    <div class="custom-modal-footer d-flex justify-content-center">
        <button type="button" class="btn btn-outline btn-close-custom">Cancelar</button>
        <button type="submit" class="btn btn-secondary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="20,6 9,17 4,12"></polyline>
            </svg>
            Actualizar Cambios
        </button>
    </div>
</form>
{{-- <p><input type="checkbox" name="actualmente_estudio" {{ $registro->actualmente_estudio ? 'checked' : '' }} id="actualmente_estudio"><label for="actualmente_estudio">Actualmente estoy cursando esta formación</label></p> --}}