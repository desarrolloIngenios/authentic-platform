<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfSessionExpired
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

       
        if ($response->getStatusCode() === 419) {
            toastr()->error('Tu sesión ha expirado. Por favor, inicia sesión nuevamente.'); 
            return redirect()->route('login');
        }

        return $response;
    }
}
