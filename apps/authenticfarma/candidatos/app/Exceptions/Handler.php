<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */    public function register(): void
    {
        $this->renderable(function (Throwable $e, Request $request) {
            // Handle HTTP exceptions
            if ($e instanceof HttpException) {
                $status = $e->getStatusCode();
            } else {
                // For other exceptions, use 500 as default
                $status = 500;
            }

            // For specific error codes that have dedicated views
            if (view()->exists("errors.{$status}")) {
                return response()->view("errors.{$status}", [
                    'errorCode' => $status,
                    'errorMessage' => $this->getErrorMessage($status),
                    'errorDescription' => $e->getMessage() ?: $this->getErrorDescription($status),
                ], $status);
            }
            
            // For other errors, use the generic error template
            return response()->view('errors.error', [
                'errorCode' => $status,
                'errorMessage' => $this->getErrorMessage($status),
                'errorDescription' => $this->isHttpException($e) ? ($e->getMessage() ?: $this->getErrorDescription($status)) : $this->getErrorDescription($status),
            ], $status);
        });

        $this->reportable(function (Throwable $e) {
            if (app()->bound('sentry')) {
                app('sentry')->captureException($e);
            }
        });
    }    /**
     * Get a user-friendly error message based on the HTTP status code.
     */
    private function getErrorMessage(int $status): string
    {
        return match ($status) {
            400 => '¡Solicitud incorrecta!',
            401 => '¡No autorizado!',
            403 => '¡Acceso denegado!',
            404 => '¡Página no encontrada!',
            408 => '¡Tiempo de espera agotado!',
            419 => '¡La página ha expirado!',
            422 => '¡Datos inválidos!',
            429 => '¡Demasiadas solicitudes!',
            500 => '¡Error del servidor!',
            502 => '¡Puerta de enlace incorrecta!',
            503 => '¡Servicio no disponible!',
            504 => '¡Tiempo de espera del servidor!',
            default => '¡Ha ocurrido un error!',
        };
    }    /**
     * Get a user-friendly error description based on the HTTP status code.
     */
    private function getErrorDescription(int $status): string
    {
        return match ($status) {
            400 => 'La solicitud enviada no es válida o está mal formada. Por favor, verifica los datos e inténtalo de nuevo.',
            401 => 'Debes iniciar sesión para acceder a esta página. Si ya has iniciado sesión, es posible que tu sesión haya expirado.',
            403 => 'No tienes los permisos necesarios para acceder a esta sección. Si crees que esto es un error, contacta al administrador.',
            404 => 'La página que estás buscando no existe o ha sido movida a otra ubicación. Verifica la URL o navega usando los enlaces del sitio.',
            408 => 'El servidor tardó demasiado en responder a tu solicitud. Por favor, verifica tu conexión a internet e inténtalo de nuevo.',
            419 => 'La página ha expirado por inactividad. Esto suele ocurrir por razones de seguridad. Por favor, recarga la página.',
            422 => 'Los datos proporcionados no son válidos. Por favor, revisa la información ingresada y corrige los errores señalados.',
            429 => 'Has realizado demasiadas solicitudes en poco tiempo. Por favor, espera un momento antes de intentar nuevamente.',
            500 => 'Ha ocurrido un error interno en el servidor. Nuestro equipo técnico ha sido notificado y estamos trabajando para solucionarlo.',
            502 => 'El servidor no pudo procesar tu solicitud en este momento. Por favor, intenta nuevamente en unos minutos.',
            503 => 'El servicio no está disponible temporalmente debido a mantenimiento o sobrecarga. Por favor, intenta más tarde.',
            504 => 'El servidor tardó demasiado en responder. Esto puede deberse a problemas de red o sobrecarga. Por favor, intenta más tarde.',
            default => 'Ha ocurrido un error inesperado. Por favor, intenta nuevamente o contacta con soporte técnico si el problema persiste.',
        };
    }
}
