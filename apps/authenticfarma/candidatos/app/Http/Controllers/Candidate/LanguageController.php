<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateLanguageRequest;
use App\Models\HvCanIdioma;
use App\Models\Idioma;
use App\Models\NivelIdioma;
use Exception;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
     public function store(CreateLanguageRequest $request)
    {
     
        try {
            $idCandidato = session('candidato');
            if (!$idCandidato) {
                toastr()->error('No se encontró el candidato en la sesión.');
                return back();
            }

            HvCanIdioma::create([
                'fecha_creacion' => now(),
                'certificado' => $request->has('certificado') ? 1 : 0,
                'detalle' => $request['detalle'] ?? null,
                'id_idioma' => $request['id_idioma'],
                'id_nivel_idioma' => $request['id_nivel_idioma'],
                'id_candidato' => $idCandidato,
                'id_estado' => 1
            ]);

            toastr()->success('Idioma guardado correctamente.');
            return back();
        } catch (Exception $e) {
            toastr()->error('Error al registrar nuevo idioma.');
            return back();
        }
        
        
    }
   
    public function update(CreateLanguageRequest $request, $id)
    {

        try {
           $registro = HvCanIdioma::find($id);
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
                'certificado' => $request->has('certificado') ? 1 : 0,
                'detalle' => $request['detalle'] ?? null,
                'id_idioma' => $request['id_idioma'],
                'id_nivel_idioma' => $request['id_nivel_idioma'] ?? null,
                'fecha_modificacion' => now(),
            ]);
            toastr()->success('Idioma actualizado correctamente.');
            return back();
        } catch (Exception $e) {
            toastr()->error('Error al actualizar nuevo idioma');
            return back();
        }
        

    }

    public function destroy($id)
    {
        try {
            $registro = HvCanIdioma::find($id);
            if (!$registro) {
                toastr()->error('Idioma no encontrado.');
                return back();
            }

            $idCandidato = session('candidato');
            if ($registro->id_candidato != $idCandidato) {
                toastr()->error('No tienes permiso para eliminar este registro.');
                return back();
            }
            
            $registro->delete();
            toastr()->success('Idioma eliminado correctamente.');
            return back();
        } catch (Exception $e) {
            toastr()->error('Error al eliminar idioma.');
            return back();
        }
    }

     public function modalCreate()
    {
        $idiomas = Idioma::all();
        $niveles = NivelIdioma::all();
        return view('candidate.education.partials.create_form_language', compact('idiomas', 'niveles'));
    }
    
    public function modalEdit($id)
    {
        $idiomas = Idioma::all();
        $niveles = NivelIdioma::all();
        $registro = HvCanIdioma::find($id);
        return view('candidate.education.partials.edit_form_language', compact('registro','idiomas', 'niveles'));
    }
}
