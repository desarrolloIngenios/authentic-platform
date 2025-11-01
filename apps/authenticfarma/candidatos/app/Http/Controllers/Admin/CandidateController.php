<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CandidatePdfRequest;
use App\Models\Genero;
use App\Models\HvCandidato;
use App\Models\HvHojaVida;
use App\Models\HvCanCorreo;
use App\Models\HvCanTelefono;
use App\Models\HvCanUbicacion;
use App\Models\Ciudad;
use App\Models\Departamento;
use App\Models\HvCanExpLab;
use App\Models\HvCanFormAc;
use App\Models\HvCanIdioma;
use App\Models\Pais;
use App\Models\TipoDocumento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Services\PDFProcessingService;
use App\Services\ExperienceLaboralService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CandidateController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (auth()->check() && session('user_role')==='ROLE_ADMIN') {
            $query = $request->input('buscar');
            $candidatos = \App\Models\HvCandidato::with([
                'correo', 'telefono', 'ubicacion.ciudadResidencia', 'genero', 'tipoDocumento', 'experienciasLaborales'
            ])
                ->when($query, function($q) use ($query) {
                    $q->where(function($sub) use ($query) {
                        $sub->whereRaw('CONCAT(nombres, " ", apellidos) LIKE ?', ["%$query%"])
                            ->orWhereHas('experienciasLaborales', function($exp) use ($query) {
                                $exp->where('nombre_cargo', 'like', "%$query%")
                                    ->orWhere('descripcion_cargo', 'like', "%$query%")
                                    ->orWhere('empresa', 'like', "%$query%")
                                    ;
                            })
                            ->orWhereHas('correo', function($correo) use ($query) {
                                $correo->where('email', 'like', "%$query%")
                                    ;
                            });
                    });
                })
                ->orderByDesc('id_candidato')
                ->paginate(50);

            // Si es AJAX, devolver solo el HTML parcial
            if ($request->ajax()) {
                return response()->view('admin.candidates.index', compact('candidatos', 'user'));
            }
            return view('admin.candidates.index', compact('candidatos','user'));
        }


    }


    public function show($id)
    {
        $user = Auth::user();
        if (auth()->check() && session('user_role')==='ROLE_ADMIN') {
            $candidato = \App\Models\HvCandidato::with([
                'correo', 'telefono', 'ubicacion.ciudadResidencia', 'genero', 'tipoDocumento',
                'experienciasLaborales.tipoCargo', 'experienciasLaborales.area', 'experienciasLaborales.sector',
                'formacionacademica', 'formacionacademicaad', 'HvCanIdioma', 'skills', 'areasPreferidas', 'perfil', 'sector', 'hojaVida', 'nuevoTrabajo'
            ])->findOrFail($id);
            return view('admin.candidates.show', compact('candidato', 'user'));
        }
        abort(403);
    }
}
