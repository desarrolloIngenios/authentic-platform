@extends('layout.app') 

@section('title', 'Mi perfil')


@section('styles')    

<style>        
        .form-floating-group {
            position: relative;
            margin-bottom: 20px;
        }

        .input-with-tooltip {
            position: relative;
            width: 100%;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .input-with-tooltip textarea,
        .input-with-tooltip .floating-label {
            flex: 1;
        }

        .tooltip-icon {
            position: absolute;
            right: -30px;
            top: 10px;
            color: #0057b8;
            cursor: help;
            font-size: 18px;
            transition: color 0.3s ease;
        }

            /* Removed hover state as it's handled by Tippy.js */

        .form-floating-group input,
        .form-floating-group select,
        .form-floating-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #fff;
            transition: all 0.3s ease;
        }

        /* Chosen select styling */
        .chosen-container {
            font-size: 14px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #fff;
        }

        .chosen-container-multi .chosen-choices {
            padding: 5px;
            border: none;
            background-image: none;
            min-height: 42px;
        }

        .chosen-container-multi .chosen-choices li.search-field input[type="text"] {
            height: 30px;
            font-family: inherit;
            color: #495057;
        }

        .chosen-container-multi .chosen-choices li.search-choice {
            padding: 5px 25px 5px 8px;
            margin: 3px;
            border: 1px solid #00a86b;
            background: #f8f9fa;
            color: #00a86b;
            border-radius: 3px;
        }

        .chosen-container .chosen-drop {
            border-color: #ddd;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .chosen-container-multi .chosen-choices li.search-choice .search-choice-close {
            top: 6px;
        }        /* Form group and label styling */
        .form-floating-group .floating-label {
            position: absolute;
            left: 10px;
            top: -10px;
            font-size: 12px;
            color: #666;
            background: #fff;
            padding: 0 5px;
            z-index: 1;
            display: flex;
            align-items: center;
            gap: 5px;
            white-space: nowrap;
        }.icon-blue {
            color: #0057b8;
            margin-right: 8px;
            cursor: help;
            font-size: 16px;
            display: inline-flex;
            align-items: center;
        }/* Custom tooltip styling */
        .tippy-box[data-theme~='custom'] {
            background-color: #ffffff;
            color: #333333;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            font-size: 14px;
            line-height: 1.6;
            padding: 16px;
            max-width: 350px;
            transition: all 0.3s ease;
        }

        .tippy-box[data-theme~='custom'][data-placement^='top'] > .tippy-arrow::before {
            border-top-color: #ffffff;
        }

        .tippy-box[data-theme~='custom'][data-placement^='bottom'] > .tippy-arrow::before {
            border-bottom-color: #ffffff;
        }

        .tippy-box[data-theme~='custom'] .tippy-content {
            padding: 0;
        }

        .custom-tooltip {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .tooltip-title {
            font-weight: 600;
            color: #00a86b;
            font-size: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .tooltip-content {
            color: #666;
            line-height: 1.6;
        }

        .profile-section {
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 1px solid #f0f0f0;
        }

        .profile-section:last-child {
            border-bottom: none;
        }
    </style>
@endsection

@section('content')
<div class="col-lg-12 column">
    <div class="padding-left">
        <div class="profile-form-edit">
            
            <div class="border-title"><h3><i class="las la-user-edit icon-blue" style="font-size: 1.5rem"></i>Mi perfil</h3></div>
            <div class="edu-history-sec contact-edit">
                <form action="{{ route('profile.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <!-- Descripción del trabajo -->
                    <div class="row profile-section">
                        <div class="col-lg-12">
                            <div class="form-floating-group">
                                {{-- <textarea name="descripcion_perfil" id="descripcion_perfil" placeholder=" " rows="2">{{ $perfil->descripcion_perfil ?? '' }}</textarea> --}}
                                <textarea name="descripcion_perfil" id="descripcion_perfil" placeholder=" " rows="2">{{ old('descripcion_perfil', $perfil->descripcion_perfil ?? '') }}</textarea>
                                <span class="floating-label">¿Cómo quieres que sea tu próximo trabajo?*</span>
                    
                            </div>
                        </div>
                    </div>

                    <!-- Sectores y áreas -->
                    <div class="row profile-section">
                        <div class="col-lg-12">
                            <div class="form-floating-group">
                                <select data-placeholder="Selecciona sectores" class="chosen" name="sectores[]" multiple>
                                    @foreach($sectores as $sector)
                                        <option value="{{ $sector->id }}" 
                                            {{-- {{ $perSectores->contains('id_sector', $sector->id) ? 'selected' : '' }}> --}}
                                            {{ (collect(old('sectores', $perSectores->pluck('id_sector')->toArray()))->contains($sector->id)) ? 'selected' : '' }}>
                                            {{ $sector->descripcion }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="floating-label">Escoge hasta 3 sectores donde te gustaría trabajar</span>
                            </div>
                        </div>
                        <div class="col-lg-12 mt-4">
                            <div class="form-floating-group">
                                <select data-placeholder="Selecciona áreas" class="chosen" name="areas[]" multiple>
                                    @foreach($areas as $area)
                                        <option value="{{ $area->id }}"
                                            {{-- {{ $perAreas->contains('id_area', $area->id) ? 'selected' : '' }}> --}}
                                            {{ (collect(old('areas', $perAreas->pluck('id_area')->toArray()))->contains($area->id)) ? 'selected' : '' }}>
                                            {{ $area->descripcion }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="floating-label">Escoge hasta 3 áreas donde te gustaría trabajar</span>
                            </div>
                        </div>
                    </div>

                    <!-- Preferencias laborales -->
                    <div class="row profile-section">
                        <div class="col-lg-6">
                            <div class="form-floating-group">
                                <select data-placeholder="Selecciona rango" class="chosen" name="id_rango_salario">
                                    <option value="" disabled {{ !$newJob || !$newJob->id_rango_salario ? 'selected' : '' }}>Selecciona un rango</option>
                                    @foreach($rangos_salario as $rango)
                                        {{-- <option value="{{ $rango->id }}" {{ $newJob && $newJob->id_rango_salario == $rango->id ? 'selected' : '' }}> --}}
                                        <option value="{{ $rango->id }}" {{ old('id_rango_salario', $newJob->id_rango_salario ?? '') == $rango->id ? 'selected' : '' }}>
                                            {{ number_format($rango->minimo, 0, ',', '.') }} - {{ number_format($rango->maximo, 0, ',', '.') }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="floating-label">Rango de salario</span>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-floating-group">
                                <select data-placeholder="Selecciona modalidad" class="chosen" name="id_tipo_trabajo">
                                    <option value="" disabled {{ !$newJob || !$newJob->id_tipo_trabajo ? 'selected' : '' }}>Selecciona modalidad</option>
                                    @foreach($tipos_trabajo as $tipo)
                                        {{-- <option class="text-dark" value="{{ $tipo->id }}" {{ $newJob && $newJob->id_tipo_trabajo == $tipo->id ? 'selected' : '' }}>
                                            {{ $tipo->descripcion }}
                                        </option> --}}
                                         <option value="{{ $tipo->id }}" {{ old('id_tipo_trabajo', $newJob->id_tipo_trabajo ?? '') == $tipo->id ? 'selected' : '' }}>
                                            {{ $tipo->descripcion }}
                                        </option>
                                    @endforeach 
                                </select>
                                <span class="floating-label">Modalidad</span>
                            </div>
                        </div>
                        <div class="col-lg-12 mt-4">
                            <div class="form-floating-group">
                                {{-- <input type="text" name="nombre_cargo" value="{{ $newJob->nombre_cargo ?? '' }}" placeholder=" " /> --}}
                                <input type="text" name="nombre_cargo" value="{{ old('nombre_cargo', $newJob->nombre_cargo ?? '') }}" placeholder=" " />
                                <span class="floating-label">Cargo*</span>
                            </div>
                        </div>
                    </div>

                    <!-- Disponibilidad -->
                    <div class="row profile-section">
                        <div class="col-lg-12">
                            <span class="pf-title">¿Tienes permiso para trabajar en Colombia?*</span>
                            <div class="pf-field text-center">
                                <div class="form-check form-check-inline">
                                    {{-- <input class="form-check-input" type="radio" name="pregunta1" id="permiso11" value="1"
                                        {{ ($newJob && $newJob->pregunta1) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="permiso11">Si</label> --}}
                                      <input class="form-check-input" type="radio" name="pregunta1" id="permiso11" value="1"
                                        {{ old('pregunta1', $newJob->pregunta1 ?? '') == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="permiso11">Si</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    {{-- <input class="form-check-input" type="radio" name="pregunta1" id="pregunta12" value="0"
                                        {{ ($newJob && !$newJob->pregunta1) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="pregunta12">No</label> --}}
                                     <input class="form-check-input" type="radio" name="pregunta1" id="pregunta12" value="0"
                                        {{ old('pregunta1', $newJob->pregunta1 ?? '') === '0' || old('pregunta1', $newJob->pregunta1 ?? '') === 0 ? 'checked' : '' }}>
                                    <label class="form-check-label" for="pregunta12">No</label>
                                </div>
                            </div>
                        </div>


                        <div class="col-lg-12">
                            <span class="pf-title">¿Tienes posibilidades de trasladarte dentro de Colombia?</span>
                            <div class="pf-field text-center">
                                <div class="form-check form-check-inline">
                                    {{-- <input class="form-check-input" type="radio" name="pregunta2" id="pregunta21" value="1"
                                        {{ ($newJob && $newJob->pregunta2) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="pregunta21">Si</label> --}}
                                    <input class="form-check-input" type="radio" name="pregunta2" id="pregunta21" value="1"
                                        {{ old('pregunta2', $newJob->pregunta2 ?? '') == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="pregunta21">Si</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    {{-- <input class="form-check-input" type="radio" name="pregunta2" id="pregunta22" value="0"
                                        {{ ($newJob && !$newJob->pregunta2) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="pregunta22">No</label> --}}
                                    <input class="form-check-input" type="radio" name="pregunta2" id="pregunta22" value="0"
                                    {{ old('pregunta2', $newJob->pregunta2 ?? '') === '0' || old('pregunta2', $newJob->pregunta2 ?? '') === 0 ? 'checked' : '' }}>
                                <label class="form-check-label" for="pregunta22">No</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <span class="pf-title">¿Te gustaría trasladarte a otros países?</span>
                            <div class="pf-field text-center">
                                <div class="form-check form-check-inline">
                                    {{-- <input class="form-check-input" type="radio" name="pregunta3" id="pregunta31" value="1"
                                        {{ ($newJob && $newJob->pregunta3) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="pregunta31">Si</label> --}}
                                     <input class="form-check-input" type="radio" name="pregunta3" id="pregunta31" value="1"
                                        {{ old('pregunta3', $newJob->pregunta3 ?? '') == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="pregunta31">Si</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    {{-- <input class="form-check-input" type="radio" name="pregunta3" id="pregunta32" value="0"
                                        {{ ($newJob && !$newJob->pregunta3) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="pregunta32">No</label> --}}
                                    <input class="form-check-input" type="radio" name="pregunta3" id="pregunta32" value="0"
                                        {{ old('pregunta3', $newJob->pregunta3 ?? '') === '0' || old('pregunta3', $newJob->pregunta3 ?? '') === 0 ? 'checked' : '' }}>
                                    <label class="form-check-label" for="pregunta32">No</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Perfil profesional -->
                    <div class="row profile-section">
                        <div class="col-lg-12">
                            <div class="form-floating-group">
                                {{-- <textarea name="texto1" id="texto1" placeholder=" " rows="2">{{ $newJob->texto1 ?? '' }}</textarea> --}}
                                 <textarea name="texto1" id="texto1" placeholder=" " rows="2">{{ old('texto1', $newJob->texto1 ?? '') }}</textarea>
                                <span class="floating-label">Logros / Proyectos*</span>
                                <script>document.getElementById('texto1').setAttribute('maxlength', 1000);</script>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-floating-group">
                                {{-- <textarea name="texto2" id="texto2" placeholder=" " rows="2">{{ $newJob->texto2 ?? '' }}</textarea> --}}
                                <textarea name="texto2" id="texto2" placeholder=" " rows="2">{{ old('texto2', $newJob->texto2 ?? '') }}</textarea>
                                <span class="floating-label">¿Qué buscas de una organización?*</span>
                                <script>document.getElementById('texto2').setAttribute('maxlength', 1000);</script>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-floating-group">
                                {{-- <textarea name="texto3" id="texto3" placeholder=" " rows="2">{{ $newJob->texto3 ?? '' }}</textarea> --}}
                                <textarea name="texto3" id="texto3" placeholder=" " rows="2">{{ old('texto3', $newJob->texto3 ?? '') }}</textarea>
                                <span class="floating-label">¿Qué buscas de un líder?*</span>
                                <script>document.getElementById('texto3').setAttribute('maxlength', 1000);</script>
                            </div>
                        </div>
                        <div class="col-lg-12">                            
                            <div class="form-floating-group">
                                <div class="input-with-tooltip">
                                    <textarea name="texto4" id="texto4"  rows="2"
                                    placeholder="📋 Detalla tus metas, estrategias, tácticas y proyecciones, así como los instrumentos, herramientas que utilizarás para alcanzarlos, en tu rol profesional y personal." maxlength="1000">{{ old('texto4', $newJob->texto4 ?? '') }}</textarea>
                                    <span class="floating-label">Mi plan de desarrollo</span>                                    
                                   
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">                            
                            <div class="form-floating-group">
                                <div class="input-with-tooltip">
                                    <textarea name="texto5" id="texto5" rows="2"
                                    placeholder="🎯 Detalla características asociadas de tu comportamiento que te hacen exitoso(a) (Liderazgo, Compromiso, Integridad, Aprendizaje continuo, Negociación, Pensamiento numérico, entre otras)." maxlength="1000">{{ old('texto5', $newJob->texto5 ?? '') }}</textarea>
                                    <span class="floating-label">Mis Competencias Comportamentales</span>
                                   
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">                            
                            <div class="form-floating-group">
                                <div class="input-with-tooltip">
                                    <textarea name="texto6" id="texto6" rows="2"
                                    placeholder="💻Detalla tus aptitudes y habilidades necesarias de conocimientos (Ofimática, Herramientas colaborativas y de comunicación, ERP y CRM, entre otras)." maxlength="1000">{{ old('texto6', $newJob->texto6 ?? '') }}</textarea>
                                    <span class="floating-label">Mis Competencias Tecnológicas</span>
                                    
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">                            
                            <div class="form-floating-group">
                                <div class="input-with-tooltip">
                                    <textarea name="texto7" id="texto7"  rows="2"
                                    placeholder="⭐ Detalla aquellas habilidades o capacidades que consideras desarrollas con facilidad, entendimiento, creatividad, pasión y rapidez." maxlength="1000">{{ old('texto7', $newJob->texto7 ?? '') }}</textarea>
                                    <span class="floating-label">Mis Talentos</span>
                                   
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Visibilidad -->
                    <div class="row profile-section">
                        <div class="col-lg-12">
                            <span class="pf-title">¿Estas buscando ofertas laborales actualmente?</span>
                            <div class="pf-field text-center">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="is_buscando_ofertas" id="buscando1" value="1"
                                        {{ old('is_buscando_ofertas', $newJob->is_buscando_ofertas ?? '') == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="buscando1">Si</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="is_buscando_ofertas" id="buscando2" value="0"
                                        {{ old('is_buscando_ofertas', $newJob->is_buscando_ofertas ?? '') === '0' || old('is_buscando_ofertas', $newJob->is_buscando_ofertas ?? '') === 0 ? 'checked' : '' }}>
                                    <label class="form-check-label" for="buscando2">No</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <span class="pf-title">¿Quiero ser visible para reclutadores?</span>
                            <div class="pf-field text-center">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="is_visible_reclutadores" id="visible1" value="1"
                                        {{ old('is_visible_reclutadores', $newJob->is_visible_reclutadores ?? '') == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="visible1">Si</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="is_visible_reclutadores" id="visible2" value="0"
                                        {{ old('is_visible_reclutadores', $newJob->is_visible_reclutadores ?? '') === '0' || old('is_visible_reclutadores', $newJob->is_visible_reclutadores ?? '') === 0 ? 'checked' : '' }}>
                                    <label class="form-check-label" for="visible2">No</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="col-lg-12">
                        <button type="submit" class="btn btn-primary">Guardar cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>




@endsection



