<?php

namespace App\Http\Controllers\Admin;



use App\Http\Controllers\Controller;
use App\Models\OfOfertaLaboral;
use App\Models\HvOfCanOfertum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VacantController extends Controller
{

    public function index(Request $request)
    {
        $user = Auth::user();
        $vacanteId = $request->query('vacante_id');
        
        // Obtener las aplicaciones del usuario
        $aplicaciones = HvOfCanOfertum::where('id_hoja_vida', session('hoja_vida'))
            ->pluck('idofoferta_laboral')
            ->toArray();

        // Si hay un ID de vacante específico
        if ($vacanteId) {
            $vacantes_query = OfOfertaLaboral::with([
                'empresa', 'cargo', 'idioma', 'nivelIdioma', 'nivelEducacion', 
                'ciudad', 'tiempoExperiencia', 'rangoSalarial', 'sectores', 'areas'            
            ])->where('id_estado', 1)
              ->whereNull('fecha_cierre_at')
              ->where('idofoferta_laboral', $vacanteId);


            $vacantes = $vacantes_query->paginate(1);
            $vacante_especifica = $vacantes->first();

            if ($vacante_especifica) {
                $todas_vacantes = collect([$vacante_especifica]);
                $ciudades = collect([$vacante_especifica->ciudad])->filter()->values();
                $sectores = $vacante_especifica->sectores;
                $areas = $vacante_especifica->areas;
                $nivelesEducacion = collect([$vacante_especifica->nivelEducacion])->filter()->values();
                $rangosSalariales = collect([$vacante_especifica->rangoSalarial])
                    ->filter()
                    ->map(function($rango) {
                        return [
                            'id' => $rango->id,
                            'texto' => number_format($rango->minimo, 0, ',', '.') . ' - ' . number_format($rango->maximo, 0, ',', '.')
                        ];
                    });
                $totalVacantes = 1;

                return view('admin.vacant.index', compact(
                    'user', 'vacantes', 'todas_vacantes', 'totalVacantes',
                    'ciudades', 'sectores', 'areas', 'nivelesEducacion',
                    'rangosSalariales', 'aplicaciones'
                ));
            }
        }

        // Si no hay ID específico o no se encontró la vacante, mostrar todas las vacantes
        $vacantes_activas = OfOfertaLaboral::with([
            'empresa', 'cargo', 'idioma', 'nivelIdioma', 'nivelEducacion', 
            'ciudad', 'tiempoExperiencia', 'rangoSalarial', 'sectores', 'areas'            
        ])->where('id_estado',1)->whereNull('fecha_cierre_at')
          ->orderBy('idofoferta_laboral', 'desc');
        
        $todas_vacantes = $vacantes_activas->get();
        $vacantes = $vacantes_activas->paginate(20);
        $ciudades = $todas_vacantes->pluck('ciudad')->unique()->filter()->values();
        $sectores = $todas_vacantes->flatMap(function($vacante) {
            return $vacante->sectores;
        })->unique('id')->values();
        $areas = $todas_vacantes->flatMap(function($vacante) {
            return $vacante->areas;
        })->unique('id')->values();
        $nivelesEducacion = $todas_vacantes->pluck('nivelEducacion')->unique()->filter()->values();
        $rangosSalariales = $todas_vacantes->pluck('rangoSalarial')
            ->filter()
            ->unique('id')
            ->sortBy('minimo')
            ->values()
            ->map(function($rango) {
                return [
                    'id' => $rango->id,
                    'texto' => number_format($rango->minimo, 0, ',', '.') . ' - ' . number_format($rango->maximo, 0, ',', '.')
                ];
            });

        $totalVacantes = $todas_vacantes->count();

        return view('admin.vacant.index', compact(
            'user', 'vacantes', 'todas_vacantes', 'totalVacantes',
            'ciudades', 'sectores', 'areas', 'nivelesEducacion',
            'rangosSalariales', 'aplicaciones'
        ));
    }

     public function showDrawerAdmin($id)
    {
        $vacante = OfOfertaLaboral::with([
            'empresa', 'cargo', 'idioma', 'nivelIdioma', 'nivelEducacion', 'ciudad', 'tiempoExperiencia', 'rangoSalarial', 'sectores', 'areas',            
        ])->findOrFail($id); 
        return view('candidate.vacant.partials.viewVacant', compact('vacante'));
    }

    /**
     * Mostrar los postulados de una vacante (admin)
     */
    public function postulados($id)
    {
        $user = Auth::user();
        $vacante = OfOfertaLaboral::with(['empresa', 'cargo'])->findOrFail($id);
        $postulados = \App\Models\HvOfCanOfertum::with(['hojaVida.candidato'])
            ->where('idofoferta_laboral', $id)
            ->get();
        return view('admin.vacant.postulados', compact('vacante', 'postulados','user'));
    }
}
