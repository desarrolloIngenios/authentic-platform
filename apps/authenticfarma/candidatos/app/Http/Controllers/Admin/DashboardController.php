<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HvHojaVida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Usuario;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $totalUsuarios = Usuario::count();

        // Total de mujeres y hombres registrados (en hv_candidato)
        $totalMujeres = \App\Models\HvCandidato::where('id_genero', 2)->count();
        $totalHombres = \App\Models\HvCandidato::where('id_genero', 1)->count();

        // Usar el modelo HvHojaVida para obtener registros por mes
        $registrosPorMes = \App\Models\HvHojaVida::selectRaw('DATE_FORMAT(fecha_creacion, "%Y-%m") as mes, COUNT(*) as total')
            ->where('fecha_creacion', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('mes')
            ->orderBy('mes')
            ->pluck('total', 'mes');

        // Generar los últimos 12 meses (YYYY-MM)
        $meses = collect();
        $labels = collect();
        for ($i = 11; $i >= 0; $i--) {
            $fecha = now()->subMonths($i);
            $mes = $fecha->format('Y-m');
            $meses->push($mes);
            $labels->push($fecha->translatedFormat('M Y'));
        }
        $datos = $meses->map(fn($mes) => $registrosPorMes[$mes] ?? 0);

        $vacantesPorMes = \App\Models\OfOfertaLaboral::selectRaw('DATE_FORMAT(fecha_creacion, "%Y-%m") as mes, COUNT(*) as total')
            ->where('fecha_creacion', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('mes')
            ->orderBy('mes')
            ->pluck('total', 'mes');
        $vacantesDatos = $meses->map(fn($mes) => $vacantesPorMes[$mes] ?? 0);
        $vacantesAbiertas = \App\Models\OfOfertaLaboral::where('id_estado', 1)->where('fecha_cierre_at',null)->count();

        // Candidatos por nivel educativo
        $niveles = \App\Models\HvCanFormAc::selectRaw('id_nivel_educacion, COUNT(*) as total')
            ->groupBy('id_nivel_educacion')
            ->pluck('total', 'id_nivel_educacion');
        // Obtener nombres de los niveles educativos
        $nombresNiveles = \App\Models\NivelEducacion::whereIn('id', $niveles->keys())
            ->pluck('nombre', 'id');
        $nivelesLabels = $niveles->keys()->map(fn($id) => $nombresNiveles[$id] ?? 'Desconocido');
        $nivelesDatos = $niveles->values();

        // NUEVOS DATOS PARA EL DASHBOARD
        $totalEmpresas = \App\Models\Empresa::count();
        $totalPostulaciones = \App\Models\HvOfCanOfertum::count();
        $ultimosUsuarios = Usuario::orderBy('id', 'desc')->take(5)->get()->map(function($usuario) {
            // Obtener la hoja de vida asociada
            $hojaVida = $usuario->hojaVida;
            $profesion = null;
            if ($hojaVida && $hojaVida->candidato) {
                // Buscar la formación académica más reciente
                $formacion = $hojaVida->candidato->formacionacademica()->orderByDesc('fecha_fin')->first();
                if ($formacion) {
                    $profesion = $formacion->titulo;
                }
            }
            // Agregar la profesión como atributo
            $usuario->profesion = $profesion;
            return $usuario;
        });

        return view('admin.dashboard.index', compact(
            'totalUsuarios','user','labels','datos','totalMujeres','totalHombres',
            'vacantesDatos','vacantesAbiertas','nivelesLabels','nivelesDatos',
            'totalEmpresas','totalPostulaciones','ultimosUsuarios'
        ));
    }
}
