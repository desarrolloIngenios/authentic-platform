<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login.index')->with('error', 'Debes iniciar sesión para acceder a esta página.');
        }

        if (Auth::user()->roles->first()->nombre !== 'ROLE_USER') {
            toastr()->error('No tienes permisos para acceder a esta página.');
            return back();
        }

        return $next($request);
    }
}
