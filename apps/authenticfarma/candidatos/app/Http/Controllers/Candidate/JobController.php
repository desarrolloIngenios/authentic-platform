<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateJobRequest;
use App\Models\HvCanExpLab;
use App\Models\Usuario;
use App\Models\Area;
use App\Models\TipoCargo;
use App\Models\Sector;
use App\Models\Pais;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class JobController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $perfil = Usuario::with('hojaVida.candidato.experienciasLaborales')->find($user->id);

        $experienciasOrdenadas = [];
        if ($perfil && $perfil->hojaVida && $perfil->hojaVida->candidato) {
            $experiencias = $perfil->hojaVida->candidato->experienciasLaborales;
            $experienciasOrdenadas = $experiencias->sortBy([
                ['fecha_inicio', 'desc']
            ]);
            $experienciasOrdenadas->load(['tipoCargo', 'sector', 'area', 'pais']);
        }

        $areas = Area::all();
        $tipoCargos = TipoCargo::all();
        $sectores = Sector::all();
        $paises = Pais::all();


        return view('candidate.job.index', compact('user', 'experienciasOrdenadas', 'areas', 'tipoCargos', 'sectores', 'paises'));
    }

    public function store(CreateJobRequest $request)
    {
        try {
            $idCandidato = session('candidato');
            if (!$idCandidato) {
                toastr()->error('No se encontró el candidato en la sesión.');
                return back();
            }

            HvCanExpLab::create([
                'descripcion_cargo' => $request->descripcion_cargo,
                'empresa' => $request->empresa,
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
                'fecha_creacion' => now(),
                'fecha_modificacion' => now(),
                'nombre_cargo' => $request->nombre_cargo,
                'usuario_creacion' => Auth::user()->name,
                'usuario_modificacion' => Auth::user()->name,
                'id_area' => $request->id_area,
                'id_candidato' => $idCandidato,
                'id_estado' => 1,
                'id_pais' => $request->id_pais,
                'id_sector' => $request->id_sector,
                'id_tipo_cargo' => $request->id_tipo_cargo
            ]);

            toastr()->success('Experiencia laboral guardada correctamente.');
            return back();
        } catch (Exception $e) {
            toastr()->error('Error al registrar la experiencia laboral.');
            return back();
        }
    }

    public function update(CreateJobRequest $request, $id)
    {
        try {
            $experiencia = HvCanExpLab::find($id);
            if (!$experiencia) {
                toastr()->error('Experiencia laboral no encontrada.');
                return back();
            }

            $idCandidato = session('candidato');
            if ($experiencia->id_candidato != $idCandidato) {
                toastr()->error('No tienes permiso para modificar este registro.');
                return back();
            }

            $experiencia->update([
                'descripcion_cargo' => $request->descripcion_cargo,
                'empresa' => $request->empresa,
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
                'fecha_modificacion' => now(),
                'nombre_cargo' => $request->nombre_cargo,
                'usuario_modificacion' => Auth::user()->name,
                'id_area' => $request->id_area,
                'id_pais' => $request->id_pais,
                'id_sector' => $request->id_sector,
                'id_tipo_cargo' => $request->id_tipo_cargo
            ]);

            toastr()->success('Experiencia laboral actualizada correctamente.');
            return back();
        } catch (Exception $e) {
            toastr()->error('Error al actualizar la experiencia laboral.');
            return back();
        }
    }

    public function destroy($id)
    {
        try {
            $experiencia = HvCanExpLab::find($id);
            if (!$experiencia) {
                toastr()->error('Experiencia laboral no encontrada.');
                return back();
            }

            $idCandidato = session('candidato');
            if ($experiencia->id_candidato != $idCandidato) {
                toastr()->error('No tienes permiso para eliminar este registro.');
                return back();
            }
            
            $experiencia->delete();
            toastr()->success('Experiencia laboral eliminada correctamente.');
            return back();
        } catch (Exception $e) {
            toastr()->error('Error al eliminar la experiencia laboral.');
            return back();
        }
    }

    public function modalCreate()
    {
        $areas = Area::all();
        $tipoCargos = TipoCargo::all();
        $sectores = Sector::all();
        $paises = Pais::all();
        return view('candidate.job.partials.create_form_job', compact('areas', 'tipoCargos', 'sectores', 'paises'));
    }
    
    public function modalEdit($id)
    {
        $areas = Area::all();
        $tipoCargos = TipoCargo::all();
        $sectores = Sector::all();
        $paises = Pais::all();
        $experiencia = HvCanExpLab::find($id);
        return view('candidate.job.partials.edit_form_job', compact('experiencia', 'areas', 'tipoCargos', 'sectores', 'paises'));
    }
}
