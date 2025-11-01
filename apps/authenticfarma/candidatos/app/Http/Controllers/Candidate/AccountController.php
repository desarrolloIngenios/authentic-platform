<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Http\Requests\AccountRequest;
use App\Models\Ciudad;
use App\Models\Departamento;
use App\Models\Genero;
use App\Models\HvCanCorreo;
use App\Models\HvCandidato;
use App\Models\HvCanTelefono;
use App\Models\HvCanUbicacion;
use App\Models\HvHojaVida;
use App\Models\Pais;
use App\Models\TipoDocumento;
use App\Models\Usuario;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $perfil = Usuario::with([
            'hojaVida.candidato.genero',
            'hojaVida.candidato.tipoDocumento',
            'hojaVida.candidato.telefono',
            'hojaVida.candidato.correo',
            'hojaVida.candidato.ubicacion' => function($query) {
                $query->with([
                    'paisNacimiento',
                    'paisResidencia',
                    'departamentoNacimiento',
                    'departamentoResidencia',
                    'ciudadNacimiento',
                    'ciudadResidencia'
                ]);
            }
        ])->find($user->id);
        $datosPersonales = null; 

        $generos = Genero::all();
        $tiposDocumento = TipoDocumento::all();
        $paises = Pais::all();
        $departamentos = Departamento::all();
        $ciudades = Ciudad::all();
        
        if ($perfil && $perfil->hojaVida && $perfil->hojaVida->candidato) {
            $datosPersonales = $perfil->hojaVida->candidato;
        }

        // dd($datosPersonales);
        return view('candidate.account.index',compact('user','datosPersonales','generos','tiposDocumento','paises','departamentos','ciudades'));
    }



    public function store(AccountRequest $request)
    {
        
        try {
            DB::beginTransaction();
            if (session('candidato')) {
                // Update existing candidate
                $candidato = HvCandidato::where('id_candidato',session('candidato'))->first();
                if (!$candidato) {
                    return back()->with('error', 'Candidato no encontrado');
                }
                $mensage = 'Información actualizada correctamente';
            } else {
                // Create new candidate
                $candidato = new HvCandidato();
                $candidato->fecha_creacion = now();
                $candidato->id_estado = 1;
                $mensage = 'Información guardada correctamente';
            }

            // Update candidate personal info
            $candidato->nombres = ucwords($request->nombres);
            $candidato->apellidos = ucwords($request->apellidos);
            $candidato->fecha_nacimiento = $request->fecha_nacimiento;
            $candidato->numero_documento = $request->numero_documento;
            $candidato->id_genero = $request->genero_id;
            $candidato->id_tipo_documento = $request->tipo_documento_id;
            $candidato->save();

            // Handle hojaVida relation
            if (!session('candidato')) {

                $hojavida = HvHojaVida::where('id_hoja_vida', session('hoja_vida'))->first();
                $hojavida->id_candidato = $candidato->id_candidato;
                $hojavida->save();

                session(['candidato' => $candidato->id_candidato]);
            }

            // Update or create email
            $correo = $candidato->correo ?? new HvCanCorreo();
            $correo->email = $request->email;
            $correo->fecha_creacion = $correo->fecha_creacion ?? now();
            $correo->principal = 0;
            $correo->id_candidato = $candidato->id_candidato;
            $correo->id_estado = 1;
            $correo->save();

            // Update or create phone
            $telefono = $candidato->telefono ?? new HvCanTelefono();
            $telefono->numero_telefono = $request->telefono;
            $telefono->otro_numero_telefono = $request->otro_telefono;
            $telefono->fecha_creacion = $telefono->fecha_creacion ?? now();
            $telefono->principal = 0;
            $telefono->id_candidato = $candidato->id_candidato;
            $telefono->id_estado = 1;
            $telefono->save();

            // Update or create location
            $ubicacion = $candidato->ubicacion ?? new HvCanUbicacion();
            $ubicacion->fecha_creacion = $ubicacion->fecha_creacion ?? now();
            $ubicacion->direccion = $request->direccion;
            $ubicacion->principal = 0;
            $ubicacion->id_candidato = $candidato->id_candidato;
            $ubicacion->id_ciudad_residencia = $request->ciudad_residencia_id;
            $ubicacion->id_departamento_residencia = $request->departamento_residencia_id;
            $ubicacion->id_pais_residencia = $request->pais_residencia_id;
            $ubicacion->id_ciudad_nacimiento = $request->ciudad_nacimiento_id;
            $ubicacion->id_departamento_nacimiento = $request->departamento_nacimiento_id;
            $ubicacion->id_pais_nacimiento = $request->pais_nacimiento_id;
            $ubicacion->id_estado = 1;
            $ubicacion->save();

            DB::commit();
            toastr()->success($mensage);
            return back();
        } catch (Exception $e) {
            DB::rollBack();
            toastr()->error('No se pudo actualizar la información. Por favor, inténtelo de nuevo más tarde.');
            return back();
        }
        
        
    }

    
}
