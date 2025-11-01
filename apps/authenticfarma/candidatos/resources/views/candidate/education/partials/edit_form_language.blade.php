<form id="formEditarIdioma" method="POST" action="{{ route('language.update', $registro->idhvcan_idioma) }}">
    @csrf
    @method('PUT')

    <div class="custom-modal-body">
        <div class="row">

            <div class="col-md-6">
                <div class="form-floating-group">
                    <select name="id_idioma" required>
                        <option value="" disabled></option>
                        @foreach ($idiomas as $idioma)
                            <option value="{{ $idioma->id }}" {{ $registro->id_idioma == $idioma->id ? 'selected' : '' }}>
                                {{ $idioma->nombre }}
                            </option>
                        @endforeach
                    </select>
                    <span class="floating-label">Idioma *</span>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-floating-group">
                    <select name="id_nivel_idioma" required>
                        <option value="" disabled></option>
                        @foreach ($niveles as $nivel)
                            <option value="{{ $nivel->id }}" {{ $registro->id_nivel_idioma == $nivel->id ? 'selected' : '' }}>
                                {{ $nivel->nombre }}
                            </option>
                        @endforeach
                    </select>
                    <span class="floating-label">Nivel de idioma *</span>
                </div>
            </div>

            <div class="col-md-12">
                <div class="form-floating-group">
                    <textarea name="detalle" placeholder=" ">{{ $registro->detalle }}</textarea>
                    <span class="floating-label">Detalle</span>
                </div>
            </div>

            <div class="col-12">
                <div class="form-floating-group" style="margin-bottom: 0;">
                    <input type="checkbox" value="1" id="certificado" name="certificado" {{ $registro->certificado == 1 ? 'checked' : '' }}><label for="certificado">Tengo certificado</label>
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
