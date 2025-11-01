<form id="formCrearTrabajo" action="{{ route('job.store') }}" method="POST">
    @csrf
    <div class="custom-modal-body">
        <div class="row">
            <div class="col-md-4">
                <div class="form-floating-group">
                    <input type="text" name="empresa" placeholder=" " required maxlength="200" value="{{ old('empresa') }}" />
                    <span class="floating-label">Empresa *</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-floating-group">
                    <input type="text" name="nombre_cargo" placeholder=" " required maxlength="200" value="{{ old('nombre_cargo') }}" />
                    <span class="floating-label">Nombre del cargo *</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-floating-group">
                    <select name="id_tipo_cargo" required>
                        <option value=""></option>
                        @foreach($tipoCargos as $tipoCargo)
                            <option value="{{ $tipoCargo->id }}">{{ $tipoCargo->descripcion }}</option>
                        @endforeach
                    </select>
                    <span class="floating-label">Cargo *</span>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-floating-group">
                    <textarea name="descripcion_cargo" placeholder=" " required maxlength="1000">{{ old('descripcion_cargo') }}</textarea>
                    <span class="floating-label">Descripción de Funciones *</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-floating-group">
                    <select name="id_area" required>
                        <option value=""></option>
                        @foreach($areas as $area)
                            <option value="{{ $area->id }}">{{ $area->descripcion }}</option>
                        @endforeach
                    </select>
                    <span class="floating-label">Área cargo *</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-floating-group">
                    <select name="id_sector" required>
                        <option value=""></option>
                        @foreach($sectores as $sector)
                            <option value="{{ $sector->id }}">{{ $sector->descripcion }}</option>
                        @endforeach
                    </select>
                    <span class="floating-label">Sector *</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-floating-group">
                    <select name="id_pais" required>
                        <option value=""></option>
                        @foreach($paises as $pais)
                            <option value="{{ $pais->id }}">{{ $pais->nombre }}</option>
                        @endforeach
                    </select>
                    <span class="floating-label">País *</span>
                </div>
            </div>
            <div class="col-md-4 pt-2">
                <div class="form-floating-group">
                    <input type="date" name="fecha_inicio" placeholder=" " required value="{{ old('fecha_inicio') }}" />
                    <span class="floating-label">Fecha de inicio *</span>
                </div>
            </div>
            <div class="col-md-4  pt-2" id="fecha_fin">
                <div class="form-floating-group">
                    <input type="date" name="fecha_fin" placeholder=" " value="{{ old('fecha_fin') }}" />
                    <span class="floating-label">Fecha de finalización</span>
                </div>
            </div>
            <div class="col-md-4  pt-3">
                <div class="" style="margin-bottom: 0;">
                    <input type="checkbox" value="1" id="trabajo_actual">
                    <label for="trabajo_actual">Actualmente trabajo en este cargo</label>
                </div>
            </div>
        </div>
    </div>

    <div class="custom-modal-footer d-flex justify-content-center">
        <button type="button" class="btn btn-outline btn-close-custom">
            Cancelar
        </button>
        <button type="submit" class="btn btn-secondary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="20,6 9,17 4,12"></polyline>
            </svg>
            Guardar Experiencia
        </button>
    </div>
</form>



