<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Models\HvHojaVida;
use App\Models\User;
use App\Models\Usuario;
use App\Models\HvCanPerSector;
use App\Models\HvCanPerArea;
use App\Models\HvCanPerfil;
use App\Models\HvCanNewjob;
use App\Models\RangoSalario;
use App\Models\TipoTrabajo;
use App\Models\Area;
use App\Models\Sector;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\UpdatePhotoRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Google\Cloud\Storage\StorageClient;
use Carbon\Carbon;
use Google\Cloud\Storage\Connection\Rest;

class ProfileController extends Controller
{

    public function index()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                toastr()->error('Usuario no autenticado');
                return redirect()->route('login');
            }

            // Obtener hojaVida con las relaciones necesarias
            $hojaVida = $user->hojaVida;
            if (!$hojaVida) {
                toastr()->error('Error, primero debe completar su hoja de vida');
                return redirect()->route('home');
            }

            // Obtener candidato con todas sus relaciones necesarias
            $candidato = $hojaVida->candidato;
            if (!$candidato) {
                toastr()->error('Error, no se encontró el perfil del candidato');
                return redirect()->route('home');
            }

            // Cargar las relaciones del candidato usando las relaciones definidas
            $candidato->load([
                'sector.sector',     // Carga sectores preferidos con su información
                'areasPreferidas.area', // Carga áreas preferidas con su información
                'perfil',           // Carga el perfil del candidato
                'nuevoTrabajo' => function ($query) {
                    $query->with([
                        'rangoSalario' => function ($q) {
                            $q->where('id_estado', 1);
                        },
                        'tipoTrabajo' => function ($q) {
                            $q->where('id_estado', 1);
                        }
                    ]);
                }
            ]);

            // Obtener catálogos para los selects
            $rangos_salario = RangoSalario::where('id_estado', 1)
                ->orderBy('minimo', 'asc')
                ->get();
            $tipos_trabajo = TipoTrabajo::where('id_estado', 1)
                ->orderBy('descripcion')
                ->get();
            $areas = Area::where('id_estado', 1)->orderBy('descripcion')->get();
            $sectores = Sector::where('id_estado', 1)->orderBy('descripcion')->get();

            return view('candidate.profile.index', [
                'user' => $user,
                'perSectores' => $candidato->sector,
                'perAreas' => $candidato->areasPreferidas,
                'perfil' => $candidato->perfil,
                'newJob' => $candidato->nuevoTrabajo,
                'rangos_salario' => $rangos_salario,
                'tipos_trabajo' => $tipos_trabajo,
                'areas' => $areas,
                'sectores' => $sectores
            ]);
        } catch (\Exception $e) {
            Log::error('Error en ProfileController@index: ' . $e->getMessage());
            toastr()->error('Ha ocurrido un error al cargar el perfil');
            return redirect()->route('home');
        }
    }


    public function edit(Usuario $id)
    {
        $hoja_vida = HvHojaVida::where('id_hoja_vida', session('hoja_vida'))->first();
        $user = Auth::user();



        // Obtener la URL de la foto si existe
        $foto_url = null;
        if ($hoja_vida && $hoja_vida->foto) {
            try {
                $storage = new \Google\Cloud\Storage\StorageClient([
                    'keyFilePath' => base_path(env('GOOGLE_CLOUD_KEY_FILE'))
                ]);

                $bucket = $storage->bucket(env('GOOGLE_CLOUD_STORAGE_BUCKET'));
                $object = $bucket->object('profile_images/' . $hoja_vida->foto);

                if ($object->exists()) {
                    $foto_url = $object->signedUrl(
                        new \DateTime('+1 hour'),
                        [
                            'version' => 'v4',
                            'method' => 'GET',
                            'responseDisposition' => 'inline',
                            'responseType' => 'image/jpeg'
                        ]
                    );
                }
            } catch (\Exception $e) {
                Log::error('Error al obtener URL de la foto: ' . $e->getMessage());
            }
        }

        return view('candidate.profile.edit', compact('user', 'hoja_vida', 'foto_url'));
    }

    public function update(UpdateProfileRequest $request, string $id)
    {
        try {
            DB::beginTransaction();

            $user = Auth::user();
            if (!$user || $user->id != $id) {
                toastr()->error('No autorizado');
                return back();
            }

            $hojaVida = $user->hojaVida;
            if (!$hojaVida || !$hojaVida->candidato) {
                toastr()->error('Error, primero debe completar su hoja de vida');
                return back();
            }

            $candidato = $hojaVida->candidato;

            // 1. Actualizar o crear perfil
            $perfil = $candidato->perfil ?? new HvCanPerfil();
            $perfil->descripcion_perfil = $request->descripcion_perfil;
            $perfil->fecha_creacion = $perfil->fecha_creacion ?? now();
            $perfil->id_candidato = $candidato->id_candidato;
            $perfil->id_estado = 1;
            $perfil->save();

            // 2. Actualizar sectores preferidos
            $candidato->sector()->delete(); // Eliminar sectores existentes
            if ($request->sectores) {
                foreach ($request->sectores as $sectorId) {
                    HvCanPerSector::create([
                        'fecha_creacion' => now(),
                        'id_candidato' => $candidato->id_candidato,
                        'id_sector' => $sectorId,
                        'id_estado' => 1
                    ]);
                }
            }

            // 3. Actualizar áreas preferidas
            $candidato->areasPreferidas()->delete(); // Eliminar áreas existentes
            if ($request->areas) {
                foreach ($request->areas as $areaId) {
                    HvCanPerArea::create([
                        'fecha_creacion' => now(),
                        'id_candidato' => $candidato->id_candidato,
                        'id_area' => $areaId,
                        'id_estado' => 1
                    ]);
                }
            }

            // 4. Actualizar o crear nuevo trabajo
            $newJob = $candidato->nuevoTrabajo ?? new HvCanNewjob();
            $newJob->fecha_creacion = $newJob->fecha_creacion ?? now();
            $newJob->fecha_modificacion = now();
            $newJob->nombre_cargo = $request->nombre_cargo;
            $newJob->pregunta1 = filter_var($request->pregunta1, FILTER_VALIDATE_BOOLEAN);
            $newJob->pregunta2 = filter_var($request->pregunta2, FILTER_VALIDATE_BOOLEAN);
            $newJob->pregunta3 = filter_var($request->pregunta3, FILTER_VALIDATE_BOOLEAN);
            $newJob->texto1 = $request->texto1;
            $newJob->texto2 = $request->texto2;
            $newJob->texto3 = $request->texto3;
            $newJob->texto4 = $request->texto4;
            $newJob->texto5 = $request->texto5;
            $newJob->texto6 = $request->texto6;
            $newJob->texto7 = $request->texto7;
            $newJob->usuario_creacion = $user->email;
            $newJob->usuario_modificacion = $user->email;
            $newJob->id_candidato = $candidato->id_candidato;
            $newJob->id_estado = 1;
            $newJob->id_rango_salario = $request->id_rango_salario;
            $newJob->id_tipo_trabajo = $request->id_tipo_trabajo;
            $newJob->is_buscando_ofertas = filter_var($request->is_buscando_ofertas, FILTER_VALIDATE_BOOLEAN);
            $newJob->is_visible_reclutadores = filter_var($request->is_visible_reclutadores, FILTER_VALIDATE_BOOLEAN);
            $newJob->save();

            DB::commit();
            toastr()->success('Perfil actualizado correctamente');
            return back();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error en ProfileController@update: ' . $e->getMessage());
            toastr()->error('Error al actualizar el perfil');
            return back();
        }
    }


    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
            'new_password_confirmation' => 'required'
        ]);

        $user = Auth::user();

        if (!$this->checkPassword($request->current_password, $user->password)) {
            toastr()->error('La contraseña actual es incorrecta');
            return back();
        }

        try {

            $user->password = bcrypt($request->new_password);
            $user->save();

            toastr()->success('Contraseña actualizada correctamente');
            return back();
        } catch (\Exception $e) {
            Log::error('Error al actualizar contraseña: ' . $e->getMessage());
            toastr()->error('Error al actualizar la contraseña');
            return back();
        }
    }


    private function checkPassword($password, $hashedPassword)
    {
        return password_verify($password, $hashedPassword);
    }

    public function updatePhoto(Request $request)
    {
        $request->validate([
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        try {
            // Verificar configuración de Google Cloud
            $keyFile = base_path(env('GOOGLE_CLOUD_KEY_FILE'));
            $bucket = env('GOOGLE_CLOUD_STORAGE_BUCKET');

            if (!file_exists($keyFile)) {
                toastr()->error('Error de configuración del servicio de almacenamiento.');
                return back();
            }

            if (!$bucket) {
                toastr()->error('Error de configuración del servicio de almacenamiento.');
                return back();
            }

            $hoja_vida = HvHojaVida::where('id_usuario', Auth::id())->first();

            if (!$hoja_vida) {
                toastr()->error('Debe completar su perfil primero.');
                return back();
            }

            if ($request->hasFile('profile_photo')) {
                $image = $request->file('profile_photo');

                // Validar que el archivo sea válido
                if (!$image->isValid()) {
                    throw new \Exception('Archivo no válido: ' . $image->getErrorMessage());
                }

                // Eliminar foto anterior si existe
                if ($hoja_vida->foto) {
                    try {
                        $storage = new StorageClient([
                            'keyFilePath' => $keyFile
                        ]);
                        $bucketObj = $storage->bucket($bucket);
                        $oldObject = $bucketObj->object('profile_images/' . $hoja_vida->foto);
                        if ($oldObject->exists()) {
                            $oldObject->delete();
                        }
                    } catch (\Exception $e) {
                        // Continuar aunque no se pueda eliminar la foto anterior
                    }
                }

                // Generar nombre único para la imagen
                $extension = $image->getClientOriginalExtension();
                $imageName = Auth::id() . '_' . time() . '.' . $extension;
                $filePath = 'profile_images/' . $imageName;

                // Subir nueva imagen a Google Cloud Storage
                $storage = new StorageClient([
                    'keyFilePath' => $keyFile
                ]);

                $bucketObj = $storage->bucket($bucket);

                // Obtener contenido del archivo - Múltiples métodos para mayor compatibilidad
                $imageContent = null;

                // Método 1: Intentar getRealPath()
                $realPath = $image->getRealPath();
                if ($realPath && file_exists($realPath)) {
                    $imageContent = file_get_contents($realPath);
                }

                // Método 2: Si falla, usar getPathname()
                if (!$imageContent) {
                    $pathname = $image->getPathname();
                    if ($pathname && file_exists($pathname)) {
                        $imageContent = file_get_contents($pathname);
                    }
                }

                // Método 3: Si falla, usar directamente el stream
                if (!$imageContent) {
                    $stream = $image->openFile('r');
                    $imageContent = $stream->fread($image->getSize());
                }

                if (!$imageContent) {
                    throw new \Exception('No se pudo leer el contenido del archivo.');
                }

                // Subir archivo
                $object = $bucketObj->upload(
                    $imageContent,
                    [
                        'name' => $filePath,
                        'metadata' => [
                            'contentType' => $image->getMimeType(),
                            'cacheControl' => 'public, max-age=3600'
                        ]
                    ]
                );

                // Actualizar base de datos
                $hoja_vida->foto = $imageName;
                $hoja_vida->save();

                toastr()->success('Foto actualizada correctamente');
                return back();
            }

            toastr()->error('No se seleccionó ninguna imagen');
            return back();
        } catch (\Illuminate\Validation\ValidationException $e) {
            toastr()->error('Archivo no válido. Solo se permiten imágenes JPG, PNG menores a 2MB.');
            return back();
        } catch (\Exception $e) {
            toastr()->error('Ha ocurrido un error. Por favor intente nuevamente.');
            return back();
        }
    }

    public function verFotoPerfil()
    {
        try {
            $hoja_vida = \App\Models\HvHojaVida::where('id_usuario', Auth::id())->first();

            if (!$hoja_vida || !$hoja_vida->foto) {
                return response()->json(['error' => 'No se encontró la imagen.'], 404);
            }

            $storage = new \Google\Cloud\Storage\StorageClient([
                'keyFilePath' => base_path(env('GOOGLE_CLOUD_KEY_FILE'))
            ]);

            $bucket = $storage->bucket(env('GOOGLE_CLOUD_STORAGE_BUCKET'));
            $object = $bucket->object('profile_images/' . $hoja_vida->foto);

            if (!$object->exists()) {
                return response()->json(['error' => 'La imagen no existe en el bucket.'], 404);
            }

            // Generar URL firmada con duración más larga (1 hora)
            $url = $object->signedUrl(
                new \DateTime('+1 hour'),
                [
                    'version' => 'v4',
                    'method' => 'GET',
                    'responseDisposition' => 'inline',
                    'responseType' => 'image/jpeg'
                ]
            );

            // Devolver la URL directamente para que se pueda usar en el src de la imagen
            return response()->json(['url' => $url]);
        } catch (\Exception $e) {
            Log::error('Error al obtener foto firmada: ' . $e->getMessage());
            return response()->json(['error' => 'Error al generar la URL de la imagen.'], 500);
        }
    }
}
