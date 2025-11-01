@extends('layout.app') 

@section('title', 'Mi cuenta')

@section('styles')
    <style>
        .file-drop-area {
            border: 2px dashed #007bff;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            background-color: #f9f9f9;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .file-drop-area:hover {
            background-color: #e9ecef;
        }

        .file-drop-area.dragover {
            background-color: #d1ecf1;
            border-color: #17a2b8;
        }

        .file-input {
            display: none;
        }

        .file-msg {
            font-size: 16px;
            color: #0057b8;
        }
        .icon-blue {
            color: #0057b8;
        }

    </style>
   
@endsection

@section('content')
    <div id="loader-overlay">
        <div class="loader-content">
            <img src="{{ asset('images/logo_loader.gif') }}" alt="Cargando..." class="loader-gif">
            <p class="loader-message">Procesando PDF, por favor espera...</p>
        </div>
    </div>
    <div class="col-lg-12 column">
        <div class="">
            
            <div class="contact-edit ">
                <form id="pdfForm" action="{{ route('candidate.store') }}" method="POST" enctype="multipart/form-data">
                    <h4 class="pt-4 font-weight-bold"><i class="las la-cloud-upload-alt icon-blue"></i>PDF Hoja de vida</h4>
                    @csrf
                    <div class="row justify-content-center mt-4">
                        <div class="col-md-12">
                            <div class="file-drop-area" id="fileDropArea">
                                <span class="file-msg">Arrastra y suelta tu archivo aquí o haz clic para seleccionar</span>
                                <input type="file" class="file-input" id="fileInput" name="pdf" multiple>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12 mt-4">
                        <button type="submit" class="btn btn-primary-custom"> <i class="las la-arrow-up"></i>Cargar</button>
                    </div>
                </form>
            </div>
            {{-- <hr> --}}
            
            <div class="contact-edit ">
                <form action="{{ route('account.store') }}" method="POST" >
                    @csrf
                    <h4 class="font-weight-bold"><i class="las la-user-cog icon-blue"></i> Mi cuenta</h4>
                    <div class="row pt-3">


                       
                        <div class="col-lg-4">
                            <div class="form-floating-group">
                                <input type="text" id="nombres" name="nombres" placeholder=" " value="{{ old('nombres', $datosPersonales->nombres ?? '') }}" required />
                                <span class="floating-label">Nombre(s)*</span>
                            </div>

                        </div>
                        <div class="col-lg-4">
                            <div class="form-floating-group">
                                <input type="text" id="apellidos" name="apellidos" placeholder=" " value="{{ old('apellidos', $datosPersonales->apellidos ?? '') }}" required />
                                <span class="floating-label">Apellidos(s)*</span>
                            </div>
                           
                        </div>
                        <div class="col-lg-4">
                            <div class="form-floating-group">
                                <select name="genero_id" required>
                                    <option value="" disabled {{ old('genero_id', optional($datosPersonales)->id_genero) ? '' : 'selected' }} hidden></option>
                                    @foreach ($generos as $genero)
                                        <option value="{{ $genero->id }}" {{ old('genero_id', optional($datosPersonales)->id_genero) == $genero->id ? 'selected' : '' }}>
                                            {{ $genero->descripcion }}
                                        </option>
                                    @endforeach

                                </select>
                                <span class="floating-label">Género*</span>
                            </div>
                        </div>
                        <div class="col-lg-4">  
                            <div class="form-floating-group">
                                <select name="tipo_documento_id" required>
                                <option value="" disabled {{ old('tipo_documento_id', optional($datosPersonales)->id_tipo_documento) ? '' : 'selected' }} hidden></option>
                                @foreach ($tiposDocumento as $documento)
                                    <option value="{{ $documento->id }}" {{ old('tipo_documento_id', optional($datosPersonales)->id_tipo_documento) == $documento->id ? 'selected' : '' }}>
                                        {{ $documento->descripcion }}
                                    </option>
                                @endforeach

                                </select>
                                <span class="floating-label">Tipo de documento*</span>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-floating-group">
                                <input type="number" name="numero_documento" placeholder=" " value="{{ old('numero_documento', $datosPersonales->numero_documento ?? '') }}" required />
                                <span class="floating-label">Número de documento*</span>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-floating-group">
                                <input type="date" name="fecha_nacimiento" placeholder=" " value="{{ old('fecha_nacimiento', isset($datosPersonales->fecha_nacimiento) ? \Carbon\Carbon::parse($datosPersonales->fecha_nacimiento)->format('Y-m-d') : '') }}" required />
                                <span class="floating-label">Fecha de nacimiento*</span>
                            </div>
                        </div>


                        <div class="form-section-divider">
                            <span>¿Cómo te pueden contactar?</span>
                        </div> 
                        <div class="col-lg-3">
                            <div class="form-floating-group">
                                <input type="number" name="telefono" placeholder=" " value="{{ old('telefono', $datosPersonales->telefono->numero_telefono ?? '')}}" required />
                                <span class="floating-label">Teléfono*</span>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-floating-group">
                                <input type="number" name="otro_telefono" placeholder=" " value="{{ old('otro_telefono', $datosPersonales->telefono->otro_numero_telefono ?? '')}}" />
                                <span class="floating-label">Otro teléfono</span>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-floating-group">
                                <input type="email" name="email" placeholder=" " value="{{ old('email', $datosPersonales->correo->email ?? '')}}" required />
                                <span class="floating-label">Correo*</span>
                            </div>
                        </div>


                        <div class="form-section-divider">
                            <span>¿Dónde naciste?</span>
                        </div> 
                        <div class="col-lg-4">
                            <div class="form-floating-group">                                <select name="pais_nacimiento_id" required>
                                <option value="" disabled {{ old('pais_nacimiento_id', optional(optional($datosPersonales)->ubicacion)->id_pais_nacimiento) ? '' : 'selected' }} hidden></option>
                                @foreach ($paises as $pais)
                                    <option value="{{ $pais->id }}" {{ old('pais_nacimiento_id', optional(optional($datosPersonales)->ubicacion)->id_pais_nacimiento) == $pais->id ? 'selected' : '' }}>
                                        {{ $pais->descripcion }}
                                    </option>
                                @endforeach
                                </select>
                                <span class="floating-label">País nacimiento*</span>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-floating-group">                                <select name="departamento_nacimiento_id" required>
                                <option value="" disabled {{ old('departamento_nacimiento_id', optional(optional($datosPersonales)->ubicacion)->id_departamento_nacimiento) ? '' : 'selected' }} hidden></option>
                                @foreach ($departamentos as $departamento)
                                    <option value="{{ $departamento->id }}" {{ old('departamento_nacimiento_id', optional(optional($datosPersonales)->ubicacion)->id_departamento_nacimiento) == $departamento->id ? 'selected' : '' }}>
                                        {{ $departamento->descripcion }}
                                    </option>
                                @endforeach
                                </select>
                                <span class="floating-label">Departamento nacimiento*</span>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-floating-group">                                <select name="ciudad_nacimiento_id" required>
                                <option value="" disabled {{ old('ciudad_nacimiento_id', optional(optional($datosPersonales)->ubicacion)->id_ciudad_nacimiento) ? '' : 'selected' }} hidden></option>
                                @foreach ($ciudades as $ciudade)
                                    <option value="{{ $ciudade->id }}" {{ old('ciudad_nacimiento_id', optional(optional($datosPersonales)->ubicacion)->id_ciudad_nacimiento) == $ciudade->id ? 'selected' : '' }}>
                                        {{ $ciudade->descripcion }}
                                    </option>
                                @endforeach
                                </select>
                                <span class="floating-label">Ciudad nacimiento*</span>
                            </div>
                        </div>


                        <div class="form-section-divider">
                            <span>¿Dónde vives?</span>
                        </div> 
                        <div class="col-lg-4">
                            <div class="form-floating-group">                                <select name="pais_residencia_id" required>
                                <option value="" disabled selected hidden></option>
                                @foreach ($paises as $pais)
                                    <option value="{{ $pais->id }}"
                                        {{ $pais->id == optional(optional($datosPersonales)->ubicacion)->id_pais_residencia ? 'selected' : '' }}>
                                        {{ $pais->descripcion }}
                                    </option>
                                @endforeach
                                </select>
                                <span class="floating-label">País residencia*</span>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-floating-group">                                <select name="departamento_residencia_id" required>
                                <option value="" disabled selected hidden></option>
                                @foreach ($departamentos as $departamento)
                                    <option value="{{ $departamento->id }}"
                                        {{ $departamento->id == optional(optional($datosPersonales)->ubicacion)->id_departamento_residencia ? 'selected' : '' }}>
                                        {{ $departamento->descripcion }}
                                    </option>
                                @endforeach
                                </select>
                                <span class="floating-label">Departamento residencia*</span>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-floating-group">                                <select name="ciudad_residencia_id" required>
                                <option value="" disabled selected hidden></option>
                                @foreach ($ciudades as $ciudade)
                                    <option value="{{ $ciudade->id }}"
                                        {{ $ciudade->id == optional(optional($datosPersonales)->ubicacion)->id_ciudad_residencia ? 'selected' : '' }}>
                                        {{ $ciudade->descripcion }}
                                    </option>
                                @endforeach
                                </select>
                                <span class="floating-label">Ciudad residencia*</span>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-floating-group">
                                <input type="text" name="direccion" placeholder=" " value="{{ $datosPersonales->ubicacion->direccion ?? ''}}" required />
                                <span class="floating-label">Dirección*</span>
                            </div>
                        </div>

                    </div>
                   
                    <div class="col-md-12mt-4">
                        <button type="submit" class="btn btn-primary-custom">
                            <i class="las la-arrow-up"></i> Guardar
                        </button>
                    </div>
                    
                </form>
            </div>
        </div>
    </div>
@endsection
{{-- @section('content')
    <div class="col-lg-9 column">
        <div class="padding-left">
            <div class="profile-form-edit">
                <div class="border-title"><h3>Cargar hoja de vida</h3>
                    <div class="contact-edit container mt-5">
                        <form action="{{ route('candidate.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row justify-content-center">
                                <div class="col-md-6">
                                    <div class="file-drop-area" id="fileDropArea">
                                        <span class="file-msg">Arrastra y suelta tu archivo aquí o haz clic para seleccionar</span>
                                        <input type="file" class="file-input" id="fileInput" name="pdf" multiple>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <button type="submit">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="contact-edit">
                <form>
                    <h4>Mi cuenta</h4>
                    <div class="row">
                        <div class="col-lg-6">
                            <span class="pf-title">Tipo de  documento</span>
                            <div class="pf-field">
                                <select data-placeholder="Allow In Search" class="chosen">
                                    @foreach ($tiposDocumento as $documento )
                                        <option value="{{ $documento->id }}" {{ $documento->id == $datosPersonales->tipo_documento_id ? 'selected' : '' }}>
                                            {{ $documento->descripcion }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <span class="pf-title">Número de documento</span>
                            <div class="pf-field">
                                <input type="number" placeholder="" value="{{ $datosPersonales->numero_documento ?? '' }}"/>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <span class="pf-title">Nombre(s)</span>
                            <div class="pf-field">
                                <input type="text" placeholder="" value="{{ $datosPersonales->nombres ?? '' }}"/>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <span class="pf-title">Apellido(s)</span>
                            <div class="pf-field">
                                <input type="text" placeholder="" value="{{ $datosPersonales->apellidos ?? '' }}"/>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <span class="pf-title">Genero</span>
                            <div class="pf-field">
                                <select data-placeholder="Allow In Search" class="chosen">
                                    @foreach ($generos as $genero )
                                        <option value="{{ $genero->id }}" {{ $genero->id == $datosPersonales->genero_id ? 'selected' : '' }}>
                                            {{ $genero->descripcion }}
                                        </option>
                                    @endforeach
                               </select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <span class="pf-title">Fecha de nacimiento</span>
                            <div class="pf-field">
                                
                                <input type="date" value="{{ isset($datosPersonales->fecha_nacimiento) ? \Carbon\Carbon::parse($datosPersonales->fecha_nacimiento)->format('Y-m-d') : '' }}" />
                            </div>
                        </div>
                    </div>
                    <h4 class="mt-4">¿Cómo te pueden contactar?</h4>
                    <div class="row">
                        <div class="col-lg-3">
                            <span class="pf-title">Teléfono</span>
                            <div class="pf-field">
                                <input type="number" placeholder="" value="{{ $datosPersonales->telefono->numero_telefono ?? '' }}"/>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <span class="pf-title">Otro teléfono</span>
                            <div class="pf-field">
                                <input type="number" placeholder="" value="{{ $datosPersonales->telefono->otro_numero_telefonos ?? '' }}"/>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <span class="pf-title">Correo</span>
                            <div class="pf-field">
                                <input type="email" placeholder="" value="{{ $datosPersonales->correo->email ?? '' }}" readonly />
                            </div>
                        </div>
                    </div>
                    <h4 class="mt-4">¿Dónde naciste?</h4>
                    <div class="row">
                        <div class="col-lg-4">
                            <span class="pf-title">País</span>
                            <div class="pf-field">
                                <select data-placeholder="Allow In Search" class="chosen">
                                   @foreach ($paises as $pais )
                                        <option value="{{ $pais->id }}" {{ $pais->id == $datosPersonales->pais_nacimiento_id ? 'selected' : '' }}>
                                            {{ $pais->descripcion }}
                                        </option>
                                   @endforeach
                               </select>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <span class="pf-title">Departamento/Provincia/Estado</span>
                            <div class="pf-field">
                                <select data-placeholder="Allow In Search" class="chosen">
                                    @foreach ($departamentos as $departamento )
                                        <option value="{{ $departamento->id }}" {{ $departamento->id == $datosPersonales->departamento_nacimiento_id ? 'selected' : '' }}>
                                            {{ $departamento->descripcion }}
                                        </option>
                                    @endforeach
                               </select>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <span class="pf-title">Ciudad</span>
                            <div class="pf-field">
                                <select data-placeholder="Allow In Search" class="chosen">
                                    @foreach ($ciudades as $ciudade )
                                        <option value="{{ $ciudade->id }}" {{ $ciudade->id == $datosPersonales->ciudad_nacimiento_id ? 'selected' : '' }}>
                                            {{ $ciudade->descripcion }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <h4 class="mt-4">¿Dónde vives?</h4>
                    <div class="row">
                        <div class="col-lg-4">
                            <span class="pf-title">País</span>
                            <div class="pf-field">
                                <select data-placeholder="Allow In Search" class="chosen">
                                    @foreach ($paises as $pais )
                                        <option value="{{ $pais->id }}" {{ $pais->id == $datosPersonales->pais_residencia_id ? 'selected' : '' }}>
                                            {{ $pais->descripcion }}
                                        </option>
                                @endforeach
                               </select>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <span class="pf-title">Departamento/Provincia/Estado</span>
                            <div class="pf-field">
                                <select data-placeholder="Allow In Search" class="chosen">
                                    @foreach ($departamentos as $departamento )
                                        <option value="{{ $departamento->id }}" {{ $departamento->id == $datosPersonales->departamento_residencia_id ? 'selected' : '' }}>
                                            {{ $departamento->descripcion }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <span class="pf-title">Ciudad</span>
                            <div class="pf-field">
                                <select data-placeholder="Allow In Search" class="chosen">
                                    @foreach ($ciudades as $ciudade )
                                        <option value="{{ $ciudade->id }}" {{ $ciudade->id == $datosPersonales->ciudad_residencia_id ? 'selected' : '' }}>
                                            {{ $ciudade->descripcion }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <span class="pf-title">Dirección</span>
                            <div class="pf-field">
                                <input type="text" placeholder="" value="{{ $datosPersonales->ubicacion->direccion ?? '' }}"/>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <button type="submit">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection --}}


@section('javascript')
    <script>
        document.getElementById('pdfForm').addEventListener('submit', function () {
            document.getElementById('loader-overlay').style.display = 'flex';
        });
        const fileDropArea = document.getElementById('fileDropArea');
        const fileInput = document.getElementById('fileInput');

        fileDropArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            fileDropArea.classList.add('dragover');
        });

        fileDropArea.addEventListener('dragleave', () => {
            fileDropArea.classList.remove('dragover');
        });

        fileDropArea.addEventListener('drop', (e) => {
            e.preventDefault();
            fileDropArea.classList.remove('dragover');
            fileInput.files = e.dataTransfer.files;
            updateFileMessage();
        });

        fileDropArea.addEventListener('click', () => {
            fileInput.click();
        });

        fileInput.addEventListener('change', () => {
            updateFileMessage();
        });

        function updateFileMessage() {
            const files = fileInput.files;
            if (files.length > 0) {
                const fileNames = Array.from(files).map(file => file.name).join(', ');
                fileDropArea.querySelector('.file-msg').textContent = `Archivos seleccionados: ${fileNames}`;
            } else {
                fileDropArea.querySelector('.file-msg').textContent = 'Arrastra y suelta tu archivo aquí o haz clic para seleccionar';
            }
        }
    </script>
@endsection

