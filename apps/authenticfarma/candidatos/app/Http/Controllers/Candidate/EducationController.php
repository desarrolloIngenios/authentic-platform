<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateEducationRequest;
use App\Models\HvCanFormAc;
use App\Models\NivelEducacion;
use App\Models\Pais;
use App\Models\Usuario;
use Exception;

use Illuminate\Support\Facades\Auth;

class EducationController extends Controller
{
   
    public function index()
    {
        // dd(session('candidato'));
        $user = Auth::user();
        $perfil = Usuario::with([
            'hojaVida.candidato.formacionacademica',
            'hojaVida.candidato.formacionacademicaad',
            'hojaVida.candidato.HvCanIdioma.nivelIdioma',
            'hojaVida.candidato.HvCanIdioma.idioma',
        ])->find($user->id);

        $formacionacademicaadOrdenadas = $formacionacademicaOrdenadas = $idiomas  = [];

        if ($perfil && $perfil->hojaVida && $perfil->hojaVida->candidato) {
            
            $formacionacademicaad = $perfil->hojaVida->candidato->formacionacademicaad;       
            $formacionacademicaadOrdenadas = $formacionacademicaad->sortBy(['fecha_inicio', 'desc']);


            $formacionacademica = $perfil->hojaVida->candidato->formacionacademica;
            $formacionacademicaOrdenadas = $formacionacademica->sortBy(['fecha_inicio', 'desc']); 

            $idiomas = $perfil->hojaVida->candidato->HvCanIdioma;
            
        }
        return view('candidate.education.index',compact('user','formacionacademicaadOrdenadas','formacionacademicaOrdenadas','idiomas'));
    }

    public function store(CreateEducationRequest $request)
    {
        
        try {
            $idCandidato = session('candidato');
            if (!$idCandidato) {
                toastr()->error('No se encontró el candidato en la sesión.');
                return back();
            }

            HvCanFormAc::create([
                'fecha_creacion' => now(),
                'fecha_inicio' => $request['fecha_inicio'] ?? null,
                'fecha_fin' => $request['fecha_fin'] ?? null,
                'institucion' => $request['institucion'] ?? null,
                'titulo' => $request['titulo'] ?? null,
                'id_candidato' => $idCandidato,
                'id_estado' => 1,
                'id_nivel_educacion' => $request['nivel_educacion'],
                'id_pais' => $request['pais'],
            ]);

            toastr()->success('Formación académica guardada correctamente.');
            return back();
        } catch (Exception $e) {
            toastr()->error('Error al registrar nueva formación academica.');
            return back();
        }
        
        
    }
   
    public function update(CreateEducationRequest $request, $id)
    {

        try {
           $registro = HvCanFormAc::find($id);
            if (!$registro) {
                    toastr()->error('Hoja de vida no encontrada.');
                    return back();
            }
    
            $idCandidato = session('candidato');
            if ($registro->id_candidato != $idCandidato) {
                toastr()->error('No tienes permiso para modificar este registro.');
                return back();
            }

            $registro->update([
                'fecha_inicio' => $request['fecha_inicio'] ?? null,
                'fecha_fin' => $request['fecha_fin'] ?? null,
                'institucion' => $request['institucion'] ?? null,
                'titulo' => $request['titulo'] ?? null,
                'id_nivel_educacion' => $request['nivel_educacion'],
                'id_pais' => $request['pais'],
            ]);
            toastr()->success('Formación académica actualizada correctamente.');
            return back();
        } catch (Exception $e) {
            toastr()->error('Error al actualizar nueva formación academica');
            return back();
        }
        

    }

    public function destroy($id)
    {
        try {
            $registro = HvCanFormAc::find($id);
            if (!$registro) {
                toastr()->error('Formación académica no encontrada.');
                return back();
            }

            $idCandidato = session('candidato');
            if ($registro->id_candidato != $idCandidato) {
                toastr()->error('No tienes permiso para eliminar este registro.');
                return back();
            }
            
            $registro->delete();
            toastr()->success('Formación académica eliminada correctamente.');
            return back();
        } catch (Exception $e) {
            toastr()->error('Error al eliminar la formación académica.');
            return back();
        }
    }

     public function modalCreate()
    {
        $nivelesEducacion = NivelEducacion::all();
        $paises = Pais::all();

        return view('candidate.education.partials.create_form_education', compact('nivelesEducacion', 'paises'));
    }
    
    public function modalEdit($id)
    {
        $registro = HvCanFormAc::find($id);
        $nivelesEducacion = NivelEducacion::all();
        $paises = Pais::all();

        return view('candidate.education.partials.edit_form_education', compact('registro', 'nivelesEducacion', 'paises'));
    }
}
