<?php

namespace App\Http\Controllers\Candidate;

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

    protected $pdfService;
    protected $experienceService;


    public function __construct(PDFProcessingService $pdfService, ExperienceLaboralService $experienceService)
    {
        $this->pdfService = $pdfService;
        $this->experienceService = $experienceService;
    }

    public function store(CandidatePdfRequest $request)
    {

        if (!$request->hasFile('pdf') || !$request->file('pdf')->isValid()) {
            toastr()->error('El archivo no se ha cargado correctamente.');
            return back();
        }

        $file = $request->file('pdf');
        $tempPath = $file->getPathname();
        $fileName = $file->getClientOriginalName();
        $savedPath = public_path('temp/' . $fileName);

        try {
            try {
                $cv = $this->pdfService->processPdf($tempPath);
            } catch (\Exception $e) {
                toastr()->error('No se pudo procesar el CV. El servicio de IA tuvo un error interno. Intenta nuevamente más tarde o con otro archivo.');
                return back();
            }

            // Validar cada apartado extraído del PDF
            $errores = [];

            // Validar datos personales
            if (empty($cv['nombreCompleto']) && empty($cv['apellidoCompleto'])) {
                $errores[] = 'No se pudo extraer la información personal (nombre y apellido) del PDF';
            }

            // Validar información de contacto
            $tieneCorreo = !empty($cv['contacto']['correo'] ?? null);
            $tieneTelefono = !empty($cv['contacto']['telefono'] ?? null);

            if (!$tieneCorreo && !$tieneTelefono) {
                $errores[] = 'No se pudo extraer información de contacto (correo o teléfono) del PDF';
            }

            // Validar experiencia laboral
            if (empty($cv['experienciaLaboral'])) {
                $errores[] = 'No se pudo extraer experiencia laboral del PDF';
                Log::warning('No se encontró experiencia laboral en el CV extraído', [
                    'experienciaLaboral' => $cv['experienciaLaboral'] ?? 'null'
                ]);
            } else {
                Log::info('Experiencia laboral encontrada en CV', [
                    'total' => count($cv['experienciaLaboral']),
                    'primera_experiencia' => $cv['experienciaLaboral'][0] ?? 'null'
                ]);
            }

            // Validar educación
            if (empty($cv['educacion'])) {
                $errores[] = 'No se pudo extraer información educativa del PDF';
            }

            // Validar idiomas
            if (empty($cv['nivelIdioma'])) {
                $errores[] = 'No se pudo extraer información de idiomas del PDF';
            }

            // Si hay errores, mostrarlos y retornar
            // if (!empty($errores)) {
            //     $mensajeError = 'Errores en la extracción del PDF:<br>' . implode('<br>', $errores);
            //     toastr()->error($mensajeError);
            //     return back();
            // }

            // Extraer datos del CV
            $nombre = $cv['nombreCompleto'];
            $apellido = $cv['apellidoCompleto'];
            $genero = $cv['genero'];
            $educaciones = $cv['educacion'];
            $educacionAdicional = $cv['educacionAdicional'];
            $idiomas = $cv['nivelIdioma'];
            $contacto = $cv['contacto'];
            $certificaciones = $cv['certificaciones'];
            $tipoDocumento = $cv['tipoDocumento'];
            $documentoIdentidad = $cv['documentoIdentidad'];
            $fechaNacimiento = $cv['fechaNacimiento'];
            $direccion = $cv['direccion'];
            $experiences = $this->experienceService->validarCargosDesdePDF($cv['experienciaLaboral']);

            // dd($experiences);

            // dd($cv);

            if (session('candidato') == 0) {

                DB::beginTransaction();
                try {
                    $candidato = new HvCandidato();
                    $genero_can = Genero::where('descripcion', $genero)->first();
                    $tipo_doc = TipoDocumento::where('descripcion', $tipoDocumento)->orWhere('nombre', $tipoDocumento)->first();

                    // Procesar nombres con validación robusta
                    if (is_array($nombre)) {
                        $candidato->nombres = ucwords(trim(implode(' ', array_filter($nombre, 'strlen'))));
                    } elseif (is_string($nombre)) {
                        $candidato->nombres = ucwords(trim($nombre));
                    } else {
                        $candidato->nombres = 'Sin nombre';
                        Log::warning('Nombre no válido extraído del PDF', ['nombre' => $nombre]);
                    }

                    // Procesar apellidos con validación robusta
                    if (is_array($apellido)) {
                        $candidato->apellidos = ucwords(trim(implode(' ', array_filter($apellido, 'strlen'))));
                    } elseif (is_string($apellido)) {
                        $candidato->apellidos = ucwords(trim($apellido));
                    } else {
                        $candidato->apellidos = 'Sin apellido';
                        Log::warning('Apellido no válido extraído del PDF', ['apellido' => $apellido]);
                    }
                    $candidato->fecha_creacion = now();
                    $candidato->fecha_nacimiento = $fechaNacimiento ?? null;
                    $candidato->numero_documento = $documentoIdentidad ?? null;
                    $candidato->id_estado = 1;
                    $candidato->id_genero = $genero_can->id ?? 1;
                    $candidato->id_tipo_documento = $tipo_doc->id ?? 1;
                    $candidato->save();


                    $hojavida = HvHojaVida::where('id_hoja_vida', session('hoja_vida'))->first();
                    $hojavida->id_candidato = $candidato->id_candidato;
                    $hojavida->save();

                    // Guardar correo con validación específica
                    try {
                        $correoCan = new HvCanCorreo();
                        $correoCan->email = $contacto['correo'] ?? null;
                        $correoCan->fecha_creacion = now();
                        $correoCan->principal = 0;
                        $correoCan->id_candidato = $candidato->id_candidato;
                        $correoCan->id_estado = 1;
                        $correoCan->save();
                    } catch (\Exception $e) {
                        throw new \Exception('Error al guardar correo, verifique la información: ' . $e->getMessage());
                    }

                    // Guardar teléfono con validación específica
                    try {
                        $telefonoCan = new HvCanTelefono();
                        $telefonoCan->fecha_creacion = now();

                        // Verificar si existe la clave telefono y procesarla
                        if (isset($contacto['telefono'])) {
                            if (is_array($contacto['telefono'])) {
                                $telefonoCan->numero_telefono = !empty($contacto['telefono'][0]) ? preg_replace('/\D/', '', $contacto['telefono'][0]) : null;
                                $telefonoCan->otro_numero_telefono = !empty($contacto['telefono'][1]) ? preg_replace('/\D/', '', $contacto['telefono'][1]) : null;
                            } else {
                                $telefonoCan->numero_telefono = !empty($contacto['telefono']) ? preg_replace('/\D/', '', $contacto['telefono']) : null;
                                $telefonoCan->otro_numero_telefono = null;
                            }
                        } else {
                            // Si no hay teléfono en el CV, usar valores nulos
                            $telefonoCan->numero_telefono = null;
                            $telefonoCan->otro_numero_telefono = null;
                        }

                        $telefonoCan->principal = 0;
                        $telefonoCan->id_candidato = $candidato->id_candidato;
                        $telefonoCan->id_estado = 1;
                        $telefonoCan->save();
                    } catch (\Exception $e) {
                        throw new \Exception('Error al guardar teléfono, verifique la información: ' . $e->getMessage());
                    }

                    // Guardar ubicación con validación específica
                    try {
                        // Validar que $direccion sea array y tenga datos mínimos
                        if (!is_array($direccion)) {
                            throw new \Exception('No se encontró información de dirección en el CV.');
                        }

                        // Reglas de respaldo
                        $direccion['direccion'] = $direccion['direccion'] ?? $direccion['ciudad_residencia'] ?? '';
                        $direccion['ciudad_nacimiento'] = $direccion['ciudad_nacimiento'] ?? $direccion['ciudad_residencia'] ?? '';
                        $direccion['departamento_nacimiento'] = $direccion['departamento_nacimiento'] ?? $direccion['departamento_residencia'] ?? '';
                        $direccion['pais_nacimiento'] = $direccion['pais_nacimiento'] ?? $direccion['pais_residencia'] ?? '';

                        // Búsquedas en base de datos
                        $ciudadResidencia = !empty($direccion['ciudad_residencia']) ? Ciudad::where('nombre', $direccion['ciudad_residencia'])->first() : null;
                        $departamentoResidencia = !empty($direccion['departamento_residencia']) ? Departamento::where('nombre', $direccion['departamento_residencia'])->first() : null;
                        $paisResidencia = !empty($direccion['pais_residencia']) ? Pais::where('nombre', $direccion['pais_residencia'])->first() : null;
                        $ciudadNacimiento = !empty($direccion['ciudad_nacimiento']) ? Ciudad::where('nombre', $direccion['ciudad_nacimiento'])->first() : null;
                        $departamentoNacimiento = !empty($direccion['departamento_nacimiento']) ? Departamento::where('nombre', $direccion['departamento_nacimiento'])->first() : null;
                        $paisNacimiento = !empty($direccion['pais_nacimiento']) ? Pais::where('nombre', $direccion['pais_nacimiento'])->first() : null;

                        // Guardar en el modelo
                        $direccionCan = new HvCanUbicacion();
                        $direccionCan->fecha_creacion = now();
                        $direccionCan->direccion = $direccion['direccion'] ?? '';
                        $direccionCan->principal = 0;
                        $direccionCan->id_candidato = $candidato->id_candidato;
                        $direccionCan->id_ciudad_residencia = $ciudadResidencia->id ?? 1;
                        $direccionCan->id_departamento_residencia = $departamentoResidencia->id ?? null;
                        $direccionCan->id_pais_residencia = $paisResidencia->id ?? 1;
                        $direccionCan->id_ciudad_nacimiento = $ciudadNacimiento->id ?? 1;
                        $direccionCan->id_departamento_nacimiento = $departamentoNacimiento->id ?? null;
                        $direccionCan->id_pais_nacimiento = $paisNacimiento->id ?? 1;
                        $direccionCan->id_estado = 1;
                        $direccionCan->save();
                    } catch (\Exception $e) {
                        throw new \Exception('Error al guardar ubicación, verifique la información: ' . $e->getMessage());
                    }

                    // Guardar experiencia laboral con validación específica
                    Log::info('Iniciando guardado de experiencias laborales', [
                        'total_experiencias' => count($experiences),
                        'candidato_id' => $candidato->id_candidato
                    ]);

                    foreach ($experiences as $index => $exp) {
                        try {

                            $experiencia = new HvCanExpLab();
                            // Mapear claves alternativas para compatibilidad
                            $empresa = $exp['empresa'] ?? $exp['nombre_empresa'] ?? null;
                            
                            // Validar que empresa no sea null (columna NOT NULL en BD)
                            if (empty($empresa)) {
                                Log::warning('Experiencia laboral sin empresa, usando valor por defecto', [
                                    'experiencia' => $exp,
                                    'candidato_id' => $candidato->id_candidato
                                ]);
                                $empresa = 'Empresa no especificada';
                            }
                            
                            $experiencia->empresa = $empresa;
                            $experiencia->descripcion_cargo = $exp['descripcion'] ?? 'Descripción no encontrada';
                            $experiencia->fecha_inicio = $exp['fecha_inicio'] ?? now();
                            $experiencia->fecha_fin = $exp['fecha_fin'] ?? null;
                            $experiencia->nombre_cargo = $exp['puesto'] ?? null;

                            // tipo_cargo: buscar por descripcion, crear si no existe
                            $puestoNormalizado = $exp['puesto_normalizado'] ?? null;
                            if ($puestoNormalizado) {
                                $idTipoCargo = DB::table('tipo_cargo')
                                    ->where('descripcion', $puestoNormalizado)
                                    ->value('id') ?? 1;
                                if ($idTipoCargo === 1) {
                                    // Limitar longitud de descripcion y nombre
                                    $maxLen = 255;
                                    $desc = mb_substr($puestoNormalizado, 0, $maxLen);
                                    $nombre = mb_substr($puestoNormalizado, 0, $maxLen);
                                    $idTipoCargo = DB::table('tipo_cargo')->insertGetId([
                                        'descripcion' => $desc,
                                        'nombre' => $nombre,
                                        'fecha_creacion' => now(),
                                        'id_estado' => 1,
                                    ]);
                                }
                                $experiencia->id_tipo_cargo = $idTipoCargo;
                            }

                            // area: buscar por descripcion
                            $area = $exp['area_deducida'] ?? 'Otros';
                            if ($area !== "" && $area !== null) {
                                $idArea = DB::table('area')
                                    ->where('descripcion', $area)
                                    ->value('id') ?? 1;
                                $experiencia->id_area = $idArea;
                            } else {
                                $idArea = DB::table('area')
                                    ->where('descripcion', 'Otros')
                                    ->value('id') ?? 1;
                                $experiencia->id_area = $idArea;
                            }

                            // sector: buscar por descripcion
                            $sector = $exp['sector_deducido'] ?? 'Otras Industrias';
                            if ($sector !== "" && $sector !== null) {
                                $idSector = DB::table('sector')
                                    ->where('descripcion', $sector)
                                    ->value('id') ?? 1;
                                $experiencia->id_sector = $idSector;
                            } else {
                                $idSector = DB::table('sector')
                                    ->where('descripcion', 'Otras Industrias')
                                    ->value('id') ?? 1;
                                $experiencia->id_sector = $idSector;
                            }

                            // Asegurarse de que id_tipo_cargo tenga un valor por defecto
                            if (!isset($experiencia->id_tipo_cargo) || $experiencia->id_tipo_cargo === null) {
                                $experiencia->id_tipo_cargo = 1;
                            }

                            // Campos obligatorios adicionales
                            $experiencia->id_candidato = $candidato->id_candidato;
                            $experiencia->fecha_creacion = now();
                            $experiencia->id_pais = 1;
                            $experiencia->id_estado = 1;

                            // NO asignar idhvcan_exp_laboral
                            $experiencia->save();

                            Log::info("Experiencia laboral guardada", [
                                'empresa' => $experiencia->empresa
                            ]);
                        } catch (\Exception $e) {
                            Log::error('Error al guardar experiencia laboral para candidato ID ' . $candidato->id_candidato . ': ' . $e->getMessage(), [
                                'experiencia_data' => $exp ?? 'no_data',
                                'file' => $e->getFile(),
                                'line' => $e->getLine()
                            ]);
                            // Continúa con la siguiente experiencia
                        }
                    }

                    Log::info('Finalizando guardado de experiencias laborales', [
                        'candidato_id' => $candidato->id_candidato
                    ]);

                    // Guardar educación con validación específica
                    try {
                        foreach ($educaciones as $educacion) {
                            $educacionCan = new HvCanFormAc();
                            $educacionCan->fecha_creacion = now();
                            $educacionCan->fecha_inicio = $educacion['fecha_inicio'] ?? now();
                            $educacionCan->fecha_fin = $educacion['fecha_fin'] ?? null;
                            $educacionCan->institucion = $educacion['institucion'] ?? null;
                            $educacionCan->titulo = $educacion['titulo'] ?? null;
                            $educacionCan->id_candidato = $candidato->id_candidato;
                            $educacionCan->id_estado = 1;

                            // Siempre asignar id_nivel_educacion
                            $nivel = $educacion['nivel_educacion'] ?? null;
                            if ($nivel) {
                                $idNivel = DB::table('nivel_educacion')
                                    ->where('descripcion', $nivel)
                                    ->value('id');
                                $educacionCan->id_nivel_educacion = $idNivel ?? 1;
                            } else {
                                $educacionCan->id_nivel_educacion = 1; // Valor por defecto
                            }
                            $educacionCan->id_pais = 1;
                            $educacionCan->save();
                        }
                    } catch (\Exception $e) {
                        throw new \Exception('Error al guardar educación, verifique la información: ' . $e->getMessage());
                    }

                    // Guardar idiomas con validación específica
                    try {
                        foreach ($idiomas as $idioma => $nivel) {
                            $HvCanIdioma = new HvCanIdioma();
                            $HvCanIdioma->certificado = 0;
                            $HvCanIdioma->detalle = null;
                            $HvCanIdioma->fecha_creacion = now();
                            $HvCanIdioma->id_candidato = $candidato->id_candidato;
                            $HvCanIdioma->id_estado = 1;
                            $HvCanIdioma->id_idioma = DB::table('idioma')
                                ->where('descripcion', $idioma)
                                ->value('id') ?? 1;
                            $HvCanIdioma->id_nivel_idioma = DB::table('nivel_idioma')
                                ->where('descripcion', $nivel)
                                ->value('id') ?? 1;
                            $HvCanIdioma->save();
                        }
                    } catch (\Exception $e) {
                        throw new \Exception('Error al guardar idiomas, verifique la información: ' . $e->getMessage());
                    }

                    DB::commit();

                    // Limpiar datos temporales de la sesión que podrían causar overflow
                    session()->forget(['_old_input', '_flash']);

                    session(['candidato' => $candidato->id_candidato]);
                    toastr()->success('Datos del candidato guardados correctamente. Por favor, valide los datos actualizados.');
                    return redirect()->route('account.index');
                } catch (\Exception $e) {
                    DB::rollback();
                    Log::error('Error al guardar datos del candidato: ' . $e->getMessage());
                    toastr()->error($e->getMessage());
                    return back();
                }
            } else {
                DB::beginTransaction();
                try {
                    // Buscar candidato por id_candidato
                    $candidato = HvCandidato::with([
                        'correo',
                        'telefono',
                        'ubicacion',
                        'experienciasLaborales.tipoCargo',
                        'experienciasLaborales.area',
                        'experienciasLaborales.sector',
                        'formacionacademica.nivel_educacion',
                        'HvCanIdioma.idioma',
                        'HvCanIdioma.nivelIdioma',
                        'genero',
                        'tipoDocumento'
                    ])->where('id_candidato', session('candidato'))->first();

                    if (!$candidato) {
                        throw new \Exception('Candidato no encontrado con ID: ' . session('candidato'));
                    }

                    // Log para seguimiento
                    Log::info("Procesando actualización para candidato ID: {$candidato->id_candidato}");

                    // Flag para controlar si algo fue actualizado
                    $datosActualizados = false;

                    // Actualizar datos básicos con validación específica
                    try {
                        $datosBasicosIguales =
                            strtolower(trim($candidato->nombres)) === strtolower(trim($nombre)) &&
                            strtolower(trim($candidato->apellidos)) === strtolower(trim($apellido)) &&
                            $candidato->fecha_nacimiento == $fechaNacimiento &&
                            $candidato->numero_documento == $documentoIdentidad;

                        if (!$datosBasicosIguales) {
                            $candidato->nombres = ucwords(trim($nombre));
                            $candidato->apellidos = ucwords(trim($apellido));
                            $candidato->fecha_nacimiento = $fechaNacimiento;
                            $candidato->numero_documento = $documentoIdentidad;
                            $candidato->save();
                            $datosActualizados = true;
                            Log::info("Datos básicos actualizados para candidato ID: {$candidato->id_candidato}");
                        }
                    } catch (\Exception $e) {
                        throw new \Exception('Error al actualizar datos básicos, verifique la información: ' . $e->getMessage());
                    }

                    // Actualizar correo con validación específica
                    try {
                        $nuevoCorreo = $contacto['correo'] ?? null;
                        if ($nuevoCorreo && $nuevoCorreo !== $candidato->correo?->email) {
                            $correoCan = $candidato->correo ?? new HvCanCorreo();
                            $correoCan->email = $nuevoCorreo;
                            $correoCan->fecha_creacion = $correoCan->fecha_creacion ?? now();
                            $correoCan->principal = 0;
                            $correoCan->id_candidato = $candidato->id_candidato;
                            $correoCan->id_estado = 1;
                            $correoCan->save();
                            $datosActualizados = true;
                            Log::info("Correo actualizado para candidato ID: {$candidato->id_candidato}");
                        }
                    } catch (\Exception $e) {
                        throw new \Exception('Error al actualizar correo, verifique la información: ' . $e->getMessage());
                    }

                    // Actualizar teléfono con validación específica
                    try {
                        $nuevoTelefono = null;
                        $nuevoOtroTelefono = null;

                        // Verificar si existe la clave telefono y procesarla
                        if (isset($contacto['telefono'])) {
                            if (is_array($contacto['telefono'])) {
                                $nuevoTelefono = !empty($contacto['telefono'][0]) ? preg_replace('/\D/', '', $contacto['telefono'][0]) : null;
                                $nuevoOtroTelefono = !empty($contacto['telefono'][1]) ? preg_replace('/\D/', '', $contacto['telefono'][1]) : null;
                            } else {
                                $nuevoTelefono = !empty($contacto['telefono']) ? preg_replace('/\D/', '', $contacto['telefono']) : null;
                            }
                        }

                        // Solo actualizar si hay un teléfono válido o si hay cambios
                        if (
                            $nuevoTelefono ||
                            ($nuevoTelefono !== $candidato->telefono?->numero_telefono ||
                                $nuevoOtroTelefono !== $candidato->telefono?->otro_numero_telefono)
                        ) {

                            $telefonoCan = $candidato->telefono ?? new HvCanTelefono();
                            $telefonoCan->numero_telefono = $nuevoTelefono;
                            $telefonoCan->otro_numero_telefono = $nuevoOtroTelefono;
                            $telefonoCan->fecha_creacion = $telefonoCan->fecha_creacion ?? now();
                            $telefonoCan->principal = 0;
                            $telefonoCan->id_candidato = $candidato->id_candidato;
                            $telefonoCan->id_estado = 1;
                            $telefonoCan->save();
                            $datosActualizados = true;
                        }
                    } catch (\Exception $e) {
                        throw new \Exception('Error al actualizar teléfono, verifique la información: ' . $e->getMessage());
                    }

                    // Actualizar ubicación con validación específica
                    try {
                        $ciudadResidencia = Ciudad::where('nombre', $direccion['ciudad_residencia'])->first();
                        $departamentoResidencia = Departamento::where('nombre', $direccion['departamento_residencia'])->first();
                        $paisResidencia = Pais::where('nombre', $direccion['pais_residencia'])->first();
                        $ciudadNacimiento = Ciudad::where('nombre', $direccion['ciudad_nacimiento'] ?? $direccion['ciudad_residencia'])->first();
                        $departamentoNacimiento = Departamento::where('nombre', $direccion['departamento_nacimiento'] ?? $direccion['departamento_residencia'])->first();
                        $paisNacimiento = Pais::where('nombre', $direccion['pais_nacimiento'] ?? $direccion['pais_residencia'])->first();

                        if ($ciudadResidencia && $ciudadResidencia->id !== $candidato->ubicacion?->id_ciudad_residencia) {
                            $ubicacionCan = $candidato->ubicacion ?? new HvCanUbicacion();
                            $ubicacionCan->direccion = $direccion['direccion'] ?? $ubicacionCan->direccion;
                            $ubicacionCan->fecha_creacion = $ubicacionCan->fecha_creacion ?? now();
                            $ubicacionCan->principal = 0;
                            $ubicacionCan->id_candidato = $candidato->id_candidato;
                            $ubicacionCan->id_ciudad_residencia = $ciudadResidencia->id ?? $ubicacionCan->id_ciudad_residencia;
                            $ubicacionCan->id_departamento_residencia = $departamentoResidencia->id ?? $ubicacionCan->id_departamento_residencia;
                            $ubicacionCan->id_pais_residencia = $paisResidencia->id ?? $ubicacionCan->id_pais_residencia;
                            $ubicacionCan->id_ciudad_nacimiento = $ciudadNacimiento->id ?? $ubicacionCan->id_ciudad_nacimiento;
                            $ubicacionCan->id_departamento_nacimiento = $departamentoNacimiento->id ?? $ubicacionCan->id_departamento_nacimiento;
                            $ubicacionCan->id_pais_nacimiento = $paisNacimiento->id ?? $ubicacionCan->id_pais_nacimiento;
                            $ubicacionCan->id_estado = 1;
                            $ubicacionCan->save();
                            $datosActualizados = true;
                        }
                    } catch (\Exception $e) {
                        throw new \Exception('Error al actualizar ubicación, verifique la información: ' . $e->getMessage());
                    }

                    // Actualizar experiencia laboral con validación específica
                    try {
                        foreach ($experiences as $exp) {
                            // Validar que la experiencia tenga información mínima
                            if (!is_array($exp)) {
                                Log::warning('Experiencia no es un array en actualización, saltando...', ['experiencia' => $exp]);
                                continue;
                            }

                            // Log específico para cada experiencia individual si hay problemas
                            if (!isset($exp['empresa']) && !isset($exp['puesto'])) {
                                Log::warning('Experiencia sin empresa ni puesto en actualización, saltando...', [
                                    'experiencia_keys' => array_keys($exp),
                                    'experiencia_values' => $exp
                                ]);
                                continue;
                            }

                            // Normalizar datos para comparación
                            $empresaNormalizada = strtolower(trim($exp['empresa'] ?? ''));
                            $puestoNormalizado = strtolower(trim($exp['puesto'] ?? ''));
                            $fechaInicioNormalizada = isset($exp['fecha_inicio']) && $exp['fecha_inicio'] ? date('Y-m-d', strtotime($exp['fecha_inicio'])) : null;

                            // Buscar experiencia existente with criterios más precisos
                            $existingExp = $candidato->experienciasLaborales()
                                ->where(function ($query) use ($empresaNormalizada, $puestoNormalizado, $fechaInicioNormalizada) {
                                    $query->whereRaw('LOWER(TRIM(empresa)) = ?', [$empresaNormalizada])
                                        ->whereRaw('LOWER(TRIM(nombre_cargo)) = ?', [$puestoNormalizado]);
                                    if ($fechaInicioNormalizada) {
                                        $query->where('fecha_inicio', $fechaInicioNormalizada);
                                    }
                                })->first();

                            if (!$existingExp) {
                                $experiencia = new HvCanExpLab();
                                $experiencia->empresa = $exp['empresa'] ?? null;
                                $experiencia->descripcion_cargo = $exp['descripcion'] ?? 'Descripción no encontrada';
                                $experiencia->fecha_inicio = $exp['fecha_inicio'] ?? now();
                                $experiencia->fecha_fin = $exp['fecha_fin'] ?? null;
                                $experiencia->nombre_cargo = $exp['puesto'] ?? null;
                                $experiencia->id_candidato = $candidato->id_candidato;
                                $experiencia->fecha_creacion = now();
                                $experiencia->id_pais = 1;
                                $experiencia->id_estado = 1;

                                // Procesar cargo normalizado
                                $puestoNormalizadoUpdate = $exp['puesto_normalizado'] ?? null;
                                if ($puestoNormalizadoUpdate) {
                                    $idTipoCargo = DB::table('tipo_cargo')
                                        ->where('descripcion', $puestoNormalizadoUpdate)
                                        ->value('id') ?? 1;
                                    $experiencia->id_tipo_cargo = $idTipoCargo;
                                }

                                // Procesar área
                                $area = $exp['area_deducida'] ?? null;
                                if ($area !== "") {
                                    $idArea = DB::table('area')
                                        ->where('descripcion', $area)
                                        ->value('id') ?? 1;

                                    $experiencia->id_area = $idArea;
                                } else {
                                    $idArea = DB::table('area')
                                        ->where('descripcion', 'Otros')
                                        ->value('id') ?? 1;
                                    $experiencia->id_area = $idArea;
                                }

                                // Procesar sector
                                $sector = $exp['sector_deducido'] ?? null;
                                if ($sector !== "") {
                                    $idSector = DB::table('sector')
                                        ->where('descripcion', $sector)
                                        ->value('id') ?? 1;

                                    $experiencia->id_sector = $idSector;
                                } else {
                                    $idSector = DB::table('sector')
                                        ->where('descripcion', 'Otras Industrias')
                                        ->value('id') ?? 1;
                                    $experiencia->id_sector = $idSector;
                                }

                                $experiencia->save();
                                $datosActualizados = true;
                            }
                        }
                    } catch (\Exception $e) {
                        throw new \Exception('Error al actualizar experiencia laboral, verifique la información: ' . $e->getMessage());
                    }

                    // Actualizar formación académica con validación específica
                    try {
                        foreach ($educaciones as $educacion) {
                            // Normalizar datos para comparación
                            $institucionNormalizada = strtolower(trim($educacion['institucion'] ?? ''));
                            $tituloNormalizado = strtolower(trim($educacion['titulo'] ?? ''));

                            // Buscar educación existente with criterios más precisos
                            $existingEdu = $candidato->formacionacademica()
                                ->where(function ($query) use ($institucionNormalizada, $tituloNormalizado) {
                                    $query->whereRaw('LOWER(TRIM(institucion)) = ?', [$institucionNormalizada])
                                        ->whereRaw('LOWER(TRIM(titulo)) = ?', [$tituloNormalizado]);
                                })->first();

                            if (!$existingEdu) {
                                $educacionCan = new HvCanFormAc();
                                $educacionCan->fecha_creacion = now();
                                $educacionCan->fecha_inicio = $educacion['fecha_inicio'] ?? now();
                                $educacionCan->fecha_fin = $educacion['fecha_fin'] ?? null;
                                $educacionCan->institucion = $educacion['institucion'] ?? null;
                                $educacionCan->titulo = $educacion['titulo'] ?? null;
                                $educacionCan->id_candidato = $candidato->id_candidato;
                                $educacionCan->id_estado = 1;

                                if ($educacion['nivel_educacion'] ?? null) {
                                    $idNivel = DB::table('nivel_educacion')
                                        ->where('descripcion', $educacion['nivel_educacion'])
                                        ->value('id') ?? 1;
                                    $educacionCan->id_nivel_educacion = $idNivel;
                                }

                                $educacionCan->id_pais = 1;
                                $educacionCan->save();
                                $datosActualizados = true;
                            }
                        }
                    } catch (\Exception $e) {
                        throw new \Exception('Error al actualizar formación académica, verifique la información: ' . $e->getMessage());
                    }

                    // Actualizar idiomas con validación específica
                    try {
                        foreach ($idiomas as $idioma => $nivel) {
                            // Normalizar datos para comparación
                            $idiomaNormalizado = strtolower(trim($idioma));
                            $nivelNormalizado = strtolower(trim($nivel));

                            // Buscar idioma existente with validación más precisa
                            $existingIdioma = $candidato->HvCanIdioma()
                                ->whereHas('idioma', function ($q) use ($idiomaNormalizado) {
                                    $q->whereRaw('LOWER(TRIM(descripcion)) = ?', [$idiomaNormalizado]);
                                })
                                ->whereHas('nivelIdioma', function ($q) use ($nivelNormalizado) {
                                    $q->whereRaw('LOWER(TRIM(descripcion)) = ?', [$nivelNormalizado]);
                                })->first();

                            if (!$existingIdioma) {
                                $HvCanIdioma = new HvCanIdioma();
                                $HvCanIdioma->certificado = 0;
                                $HvCanIdioma->fecha_creacion = now();
                                $HvCanIdioma->id_candidato = $candidato->id_candidato;
                                $HvCanIdioma->id_estado = 1;
                                $HvCanIdioma->id_idioma = DB::table('idioma')
                                    ->where('descripcion', $idioma)
                                    ->value('id') ?? 1;
                                $HvCanIdioma->id_nivel_idioma = DB::table('nivel_idioma')
                                    ->where('descripcion', $nivel)
                                    ->value('id') ?? 1;
                                $HvCanIdioma->save();
                                $datosActualizados = true;
                            }
                        }
                    } catch (\Exception $e) {
                        throw new \Exception('Error al actualizar idiomas, verifique la información: ' . $e->getMessage());
                    }

                    DB::commit();

                    // Limpiar datos temporales de la sesión que podrían causar overflow
                    session()->forget(['_old_input', '_flash']);

                    if ($datosActualizados) {
                        Log::info("Actualización completada para candidato ID: {$candidato->id_candidato}");
                        toastr()->success('Los datos del candidato han sido actualizados correctamente. Por favor, valide los datos actualizados.');
                    } else {
                        Log::info("No se encontraron cambios para candidato ID: {$candidato->id_candidato}");
                        toastr()->info('No se encontraron cambios para actualizar.');
                    }

                    return redirect()->route('account.index');
                } catch (\Exception $e) {
                    DB::rollback();
                    Log::error('Error al actualizar el candidato: ' . $e->getMessage());
                    toastr()->error($e->getMessage());
                    return back();
                }
            }
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            // Manejar específicamente el error de sesión payload demasiado grande
            if ($e->getCode() === '22001' && str_contains($e->getMessage(), 'Data too long for column \'payload\'')) {
                Log::warning('Session payload too large during CV processing, clearing session', [
                    'error' => $e->getMessage(),
                    'user_id' => Auth::id()
                ]);

                // Limpiar la sesión y redirigir
                session()->flush();
                session()->regenerate();

                if (Auth::check()) {
                    Auth::logout();
                }

                toastr()->warning('Tu sesión ha expirado debido a demasiados datos almacenados. Por favor, inicia sesión nuevamente.');
                return redirect()->route('login');
            }

            // Para otros errores de base de datos
            Log::error('Database error al procesar el CV: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'sql' => $e->getSql() ?? 'N/A'
            ]);

            toastr()->error('Error de base de datos al procesar el CV. Intente nuevamente.');
            return back();
        } catch (\Throwable $th) {
            DB::rollback();
            Log::error('Error al procesar el CV: ' . $th->getMessage(), [
                'file' => $th->getFile(),
                'line' => $th->getLine(),
                'trace' => $th->getTraceAsString()
            ]);

            // Mensaje más específico para el usuario
            $userMessage = 'Error al procesar el CV. ';
            if (str_contains($th->getMessage(), 'array key')) {
                $userMessage .= 'La estructura del CV no es compatible. Por favor, verifique que el CV contenga toda la información requerida.';
            } else {
                $userMessage .= 'Intente nuevamente o contacte al soporte técnico.';
            }

            toastr()->error($userMessage);
            return back();
        }
    }
}
