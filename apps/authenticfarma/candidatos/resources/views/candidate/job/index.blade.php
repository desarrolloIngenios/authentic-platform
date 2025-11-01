@extends('layout.app') 

@section('title', 'Mi experiencia laboral')


@section('content')
    <div class="col-lg-12 column">
        <div class="padding-left">
            <div class="manage-jobs-sec">
                
                <div class="border-title"><h4 class="pt-1 pl-4 font-weight-bold"><i class="las la-briefcase icon-blue"></i>Experiencia laboral</h4><a href="#"  title="Agregar experiencia" data-modal-target="modalAgregarTrabajo"><i class="la la-plus"></i> Agregar experiencia</a></div>

                <div class="edu-history-sec">
                    @isset($experienciasOrdenadas) 
                        @foreach ($experienciasOrdenadas as $experiencia)     
                            <div class="edu-history style2">
                                <i></i>
                                <div class="edu-hisinfo">
                                    <h3>{{ $experiencia->empresa }} <span>{{ $experiencia->nombre_cargo }}</span></h3>
                                    <i>{{ $experiencia->fecha_inicio ? $experiencia->fecha_inicio->isoFormat('MMMM YYYY') : 'Fecha no disponible' }}- {{ $experiencia->fecha_fin ? $experiencia->fecha_fin->isoFormat('MMMM YYYY'): 'Actual' }}</i>
                                    <i><strong class="font-green"> Descripción: </strong>{{ $experiencia->descripcion_cargo }}</i>
                                    <i><strong class="font-green"> Area: </strong>{{ $experiencia->area->descripcion }}</i>
                                    <i><strong class="font-green"> Sector: </strong>{{ $experiencia->sector->descripcion }}</i>
                                </div>
                                <ul class="action_job">
        
                                    <li><span>Edit</span><a href="#" class="editar-job" title="Editar experiencia" data-modal-target="modalEditarTrabajo" data-id="{{ $experiencia->idhvcan_exp_laboral }}"><i class="la la-pencil"></i></a></li>
                                    <li><span>Delete</span><a href="#" class="btn-eliminar-job" title="Eliminar experiencia" data-modal-target="modalEliminarJob" data-id="{{ $experiencia->idhvcan_exp_laboral }}"><i class="la la-trash-o"></i></a></li>
                                </ul>
                            </div>
                        @endforeach
                    @endisset
                </div>
                @include('candidate.job.modals.editJob')
                @include('candidate.job.modals.createJob')
                @include('candidate.job.modals.destroyJob')

            </div>
        </div>
    </div>
@endsection


