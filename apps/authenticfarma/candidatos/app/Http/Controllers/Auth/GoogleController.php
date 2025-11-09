<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\HvHojaVida;
use App\Models\Usuario;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeUser;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GoogleController extends Controller
{
    /**
     * Optimización 7: Método para limpiar y optimizar sesión
     */
    private function optimizeSession()
    {
        // Limpiar datos temporales innecesarios y optimizar almacenamiento
        session()->forget([
            '_token',
            '_previous',
            '_flash',
            'oauth_state',
            'oauth_verifier',
            'intended_url'
        ]);
    }
    public function redirect()
    {

        return Socialite::driver('google')->redirect();
    }
    public function callback()
    {
        try {
            // Optimización 4: Usar stateless() para evitar almacenar estado en sesión
            $googleUser = Socialite::driver('google')->stateless()->user();

            // Optimización 5: Limpiar y optimizar sesión
            $this->optimizeSession();

            $user = Usuario::where('email', $googleUser->email)->first();

            if ($user && !$user->enabled) {
                // Refuerzo: limpiar completamente la sesión para evitar bucles
                session()->flush();
                session()->invalidate();
                session()->regenerateToken();

                toastr()->error('Tu cuenta está deshabilitada o requiere aprobación. Por favor, contacta a soporte. Si el problema persiste, intenta cerrar el navegador.');
                return redirect()->route('login')->with('error', 'Cuenta deshabilitada. Si el problema persiste, limpia cookies o intenta en modo incógnito.');
            }

            if (!$user) {
                $lastId = DB::table('usuarios')->max('id');
                $newId = $lastId ? $lastId + 1 : 1;

                $user = new Usuario();
                $user->id = $newId;
                $user->google_id = $googleUser->id;
                $user->nombre = $googleUser['given_name'];
                $user->apellido = $googleUser['family_name'];
                $user->email = $googleUser->email;
                $user->username = $googleUser->email;
                $user->enabled = 1;
                $user->save();

                $hoja_vida = new HvHojaVida();
                $hoja_vida->id_usuario = $user->id;
                $hoja_vida->id_estado = 1;
                $hoja_vida->fecha_creacion = now();
                $hoja_vida->usuario_creacion = $googleUser->email;
                $hoja_vida->save();

                Mail::to($user->email)->send(new WelcomeUser($user));

                $user->roles()->sync([2]);
            } else {
                if (!$user->google_id) {
                    $user->google_id = $googleUser->id;
                    $user->save();
                }
            }

            Auth::login($user);

            // Optimización 1: Usar solo IDs esenciales en sesión
            $hoja_vida = $user->hojaVida()->first();
            $candidato = $hoja_vida->candidato()->first();

            // Optimización 2: Almacenar solo datos mínimos en sesión
            session()->put([
                'user_role' => $user->roles->first()->nombre,
                'user_id' => Auth::id(),
                'hoja_vida' => $hoja_vida->id_hoja_vida,
                'candidato' => !is_null($candidato) ? $candidato->id_candidato : 0
            ]);

            // Optimización 3: Limpiar datos innecesarios de la sesión
            session()->forget(['_previous', '_flash']);

            // Optimización 6: Regenerar ID de sesión para sesión limpia
            session()->regenerate();

            if (session('candidato') != 0) {
                toastr()->success('Inicio de sesión exitoso');
                return redirect()->route('vacant.index');
            } else {
                toastr()->success('Inicio de sesión exitoso');
                return redirect()->route('account.index');
            }
        } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
            // Refuerzo: limpiar completamente la sesión antes de redirigir
            session()->flush();
            session()->invalidate();
            session()->regenerateToken();
            toastr()->error('Error con el estado de la autenticación. Por favor, intenta nuevamente. Si el problema persiste, limpia cookies o usa modo incógnito.');
            return redirect()->route('login')->with('error', 'Error de autenticación. Si el problema persiste, limpia cookies o intenta en modo incógnito.');
        } catch (\Illuminate\Database\QueryException $e) {
            // Capturar específicamente errores de base de datos relacionados con sesiones
            if (str_contains($e->getMessage(), 'Data too long for column') && str_contains($e->getMessage(), 'payload')) {
                // Limpiar sesión y reintentar una vez
                session()->flush();
                session()->regenerate(true);

                toastr()->info('Reintentando inicio de sesión...');
                // Reintentar con configuración mínima
                return $this->handleSessionError();
            }

            // Otros errores de BD
            session()->flush();
            session()->invalidate();
            session()->regenerateToken();
            Log::error('Error de base de datos en Google login: ' . $e->getMessage());
            toastr()->error('Problema temporal en el servidor. Por favor, intenta más tarde.');
            return redirect()->route('login')->with('error', 'Problema temporal. Si el problema persiste, limpia cookies o intenta en modo incógnito.');
        } catch (\Exception $e) {
            // Log del error para debugging pero mensaje amigable al usuario
            session()->flush();
            session()->invalidate();
            session()->regenerateToken();
            Log::error('Error en Google login: ' . $e->getMessage(), [
                'user_email' => isset($googleUser) ? $googleUser->email : 'desconocido',
                'error_line' => $e->getLine(),
                'error_file' => $e->getFile()
            ]);

            toastr()->error('Error al iniciar sesión con Google. Por favor, intenta nuevamente. Si el problema persiste, limpia cookies o usa modo incógnito.');
            return redirect()->route('login')->with('error', 'Error inesperado. Si el problema persiste, limpia cookies o intenta en modo incógnito.');
        }
    }

    /**
     * Maneja errores específicos de sesión y reintenta con configuración mínima
     */
    private function handleSessionError()
    {
        try {
            // Limpiar completamente la sesión
            session()->flush();
            session()->invalidate();
            session()->regenerateToken();

            // Configurar sesión con parámetros mínimos
            config([
                'session.cookie_lifetime' => 120,
                'session.gc_maxlifetime' => 7200,
            ]);

            toastr()->warning('Sesión reiniciada. Por favor, intenta el login nuevamente.');
            return redirect()->route('login')->with('info', 'Por favor, intenta iniciar sesión con Google nuevamente.');
        } catch (\Exception $e) {
            Log::error('Error al manejar sesión: ' . $e->getMessage());
            toastr()->error('Error del sistema. Por favor, contacta al administrador.');
            return redirect()->route('login');
        }
    }

    /**
     * Método alternativo para login cuando hay problemas de sesión
     */
    public function loginMinimal()
    {
        try {
            // Configurar sesión ultra-mínima
            session()->flush();

            $googleUser = Socialite::driver('google')->stateless()->user();
            $user = Usuario::where('email', $googleUser->email)->first();

            if ($user) {
                Auth::login($user);

                // Almacenar solo lo esencial
                session()->put('user_id', Auth::id());

                toastr()->success('Inicio de sesión exitoso (modo mínimo)');
                return redirect()->route('account.index');
            } else {
                toastr()->error('Usuario no encontrado. Por favor, registrate primero.');
                return redirect()->route('login');
            }
        } catch (\Exception $e) {
            Log::error('Error en login mínimo: ' . $e->getMessage());
            toastr()->error('Error al iniciar sesión. Intenta con email y contraseña.');
            return redirect()->route('login');
        }
    }
}
