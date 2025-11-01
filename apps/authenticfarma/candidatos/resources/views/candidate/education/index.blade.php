@extends('layout.app') 

@section('title', 'Mi formación académica')


@section('content')
    
    <div class="col-lg-12 column">
        <div class="padding-left">
            <div class="manage-jobs-sec">
                <div class="border-title"><h3><i class="las la-graduation-cap icon-blue" style="font-size: 1.5rem"></i> Formación academica</h3><a href="#"  title="Agregar educación" data-modal-target="modalAgregarEducacion"><i class="la la-plus"></i> Agregar educación</a></div>
                <div class="edu-history-sec">
                    @isset($formacionacademicaOrdenadas)
                        @foreach ($formacionacademicaOrdenadas as $formacionacademica)     
                        <div class="edu-history">
                            <i class="la la-graduation-cap"></i>
                            <div class="edu-hisinfo">
                                    <h3>{{ $formacionacademica->titulo }} </h3>
                                    <span>{{ $formacionacademica->institucion }}</span>
                                    <i>{{ $formacionacademica->fecha_inicio ? $formacionacademica->fecha_inicio->isoFormat('MMMM YYYY') : 'Fecha no disponible' }}- {{ $formacionacademica->fecha_fin ? $formacionacademica->fecha_fin->isoFormat('MMMM YYYY'): 'Actual' }}</i>
                                </div>
                                <ul class="action_job">
                                    <li><span>Edit</span><a href="#" class="editar-educacion" title="Agregar educación" data-modal-target="modalEditarEducacion" data-id="{{ $formacionacademica->idhvcan_form_ac }}"><i class="la la-pencil"></i></a></li>
                                    <li><span>Delete</span><a href="#" class="btn-eliminar-educacion" title="Agregar educación" data-modal-target="modalEliminarEducacion" data-id="{{ $formacionacademica->idhvcan_form_ac }}"><i class="la la-trash-o"></i></a></li>
                                </ul>
                            </div>
                        @endforeach
                    @else    
                            No hay formación académica registrada
                    @endisset
                </div>
            </div>
            @include('candidate.education.modals.editEducation')
            @include('candidate.education.modals.createEducation')
            @include('candidate.education.modals.destroyEducation')
            <div class="manage-jobs-sec">
                <div class="border-title"><h3><i class="las la-graduation-cap icon-blue" style="font-size: 1.5rem"></i> Formación academica adicional</h3><a href="#"  title="Agregar educación" data-modal-target="modalAgregarEducacionAd"><i class="la la-plus"></i>Agregar educación</a></div>
                <div class="edu-history-sec">
                    @isset($formacionacademicaadOrdenadas)
                        @foreach ($formacionacademicaadOrdenadas as $formacionacademica)     
                            <div class="edu-history">
                                <i class="la la-graduation-cap"></i>
                                <div class="edu-hisinfo">
                                    <h3>{{ $formacionacademica->titulo }}</h3>
                                    <span>{{ $formacionacademica->institucion }}</span>
                                    <i>{{ $formacionacademica->fecha_inicio ? $formacionacademica->fecha_inicio->isoFormat('MMMM YYYY') : 'Fecha no disponible' }}- {{ $formacionacademica->fecha_fin ? $formacionacademica->fecha_fin->isoFormat('MMMM YYYY'): 'Actual' }}</i>
                                </div>
                                <ul class="action_job">
                                    <li><span>Edit</span><a href="#" class="editar-educacionad" title="Agregar educación adicional" data-modal-target="modalEditarEducacionAd" data-id="{{ $formacionacademica->idhvcan_form_ad }}"><i class="la la-pencil"></i></a></li>
                                    <li><span>Delete</span><a href="#" class="btn-eliminar-educacionad" title="Agregar educación" data-modal-target="modalEliminarEducacionAd" data-id="{{ $formacionacademica->idhvcan_form_ad }}"><i class="la la-trash-o"></i></a></li>
                                </ul>
                            </div>
                        @endforeach
                    @else   
                        No hay formación académica adicional registrada
                    @endisset
                </div>
            </div>
            @include('candidate.education.modals.editEducationAd')
            @include('candidate.education.modals.createEducationAd')
            @include('candidate.education.modals.destroyEducationAd')
            <div class="manage-jobs-sec">
                <div class="border-title"><h3><i class="las la-book icon-blue" style="font-size: 1.5rem"></i> Idiomas</h3><a href="#"  title="Agregar idioma" data-modal-target="modalAgregarLanguage"><i class="la la-plus"></i>Agregar idioma</a></div>
                <div class="edu-history-sec">
                    @isset($idiomas)
                        @foreach ($idiomas as $idioma)
                            <div class="edu-history">
                                <i class="la la-graduation-cap"></i>
                                <div class="edu-hisinfo">
                                    <i>{{ $idioma->idioma->descripcion }}</i>
                                    <h3>{{ $idioma->nivelIdioma->descripcion }}</h3>
                                </div>
                                <ul class="action_job">
                                    <li><span>Edit</span><a href="#" class="editar-language" title="Editar idioma" data-modal-target="modalEditarLanguage" data-id="{{ $idioma->idhvcan_idioma }}"><i class="la la-pencil"></i></a></li>
                                    <li><span>Delete</span><a href="#" class="btn-eliminar-language" title="Eliminar idioma" data-modal-target="modalEliminarLanguage" data-id="{{ $idioma->idhvcan_idioma }}"><i class="la la-trash-o"></i></a></li>
                                 
                                </ul>
                            </div>
                        @endforeach
                    @else
                        No hay idiomas registrados    
                    @endisset
                   
                </div>
            </div>
            @include('candidate.education.modals.editLanguage')
            @include('candidate.education.modals.createLanguage')
            @include('candidate.education.modals.destroyLanguage')
        </div>
    </div>
@endsection


