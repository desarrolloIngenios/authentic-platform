<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class HandleSessionError
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            return $next($request);
        } catch (\Illuminate\Database\QueryException $e) {
            return $this->handleSessionError($e, $request);
        } catch (\PDOException $e) {
            return $this->handleSessionError($e, $request);
        } catch (\Exception $e) {
            // Verificar si el mensaje contiene el error de payload
            if (
                str_contains($e->getMessage(), 'Data too long for column \'payload\'') ||
                str_contains($e->getMessage(), 'String data, right truncated')
            ) {
                return $this->handleSessionError($e, $request);
            }

            // Si no es el error que esperamos, lanzar la excepción original
            throw $e;
        }
    }

    private function handleSessionError($exception, Request $request)
    {
        // Verificar si es el error específico de datos demasiado largos en la sesión
        $isSessionError =
            (isset($exception) && method_exists($exception, 'getCode') && $exception->getCode() === '22001') ||
            str_contains($exception->getMessage(), 'Data too long for column \'payload\'') ||
            str_contains($exception->getMessage(), 'String data, right truncated') ||
            str_contains($exception->getMessage(), 'sessions');

        if ($isSessionError) {
            Log::warning('Session payload too large, clearing session and redirecting to login', [
                'error' => $exception->getMessage(),
                'error_code' => method_exists($exception, 'getCode') ? $exception->getCode() : 'N/A',
                'user_id' => Auth::id(),
                'request_url' => $request->fullUrl(),
                'user_agent' => $request->userAgent()
            ]);

            try {
                // Limpiar la sesión completamente
                Session::flush();
                Session::regenerate();

                // Hacer logout si el usuario está autenticado
                if (Auth::check()) {
                    Auth::logout();
                }
            } catch (\Exception $cleanupException) {
                Log::error('Error during session cleanup', [
                    'error' => $cleanupException->getMessage()
                ]);
            }

            // Mensaje personalizado para el usuario
            return redirect()->route('login')->with('warning', 'Demasiado texto o datos en la sesión. Por favor, intenta de nuevo. Si el problema persiste, reduce la cantidad de información ingresada.');
        }

        // Si no es el error que esperamos, lanzar la excepción original
        throw $exception;
    }
}
