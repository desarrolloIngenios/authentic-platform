<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateEducationAdRequest;
use App\Models\HvCanFormAd;
use Exception;


class EducationAdController extends Controller
{

    public function store(CreateEducationAdRequest $request)
    {

        try {
            $idCandidato = session('candidato');
            if (!$idCandidato) {
                toastr()->error('No se encontró el candidato en la sesión.');
                return back();
            }

            HvCanFormAd::create([
                'fecha_creacion' => now(),
                'fecha_inicio' => $request['fecha_inicio'] ?? null,
                'fecha_fin' => $request['fecha_fin'] ?? null,
                'institucion' => $request['institucion'] ?? null,
                'titulo' => $request['titulo'] ?? null,
                'id_candidato' => $idCandidato,
                'id_estado' => 1
            ]);

            toastr()->success('Formación académica adicional guardada correctamente.');
            return back();
        } catch (Exception $e) {
            toastr()->error('Error al registrar nueva formación academica adicional.');
            return back();
        }
    }

    public function update(CreateEducationAdRequest $request, $id)
    {

        try {
            $registro = HvCanFormAd::find($id);
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
                'titulo' => $request['titulo'] ?? null
            ]);
            toastr()->success('Formación académica adicional actualizada correctamente.');
            return back();
        } catch (Exception $e) {
            toastr()->error('Error al actualizar nueva formación academica adicional');
            return back();
        }
    }

    public function destroy($id)
    {
        try {
            $registro = HvCanFormAd::find($id);
            if (!$registro) {
                toastr()->error('Formación académica adicional no encontrada.');
                return back();
            }

            $idCandidato = session('candidato');
            if ($registro->id_candidato != $idCandidato) {
                toastr()->error('No tienes permiso para eliminar este registro.');
                return back();
            }

            $registro->delete();
            toastr()->success('Formación académica adicional eliminada correctamente.');
            return back();
        } catch (Exception $e) {
            toastr()->error('Error al eliminar la formación académica adicional.');
            return back();
        }
    }

    public function modalCreate()
    {
        return view('candidate.education.partials.create_form_educationad');
    }

    public function modalEdit($id)
    {
        $registro = HvCanFormAd::find($id);
        return view('candidate.education.partials.edit_form_educationad', compact('registro'));
    }
}
