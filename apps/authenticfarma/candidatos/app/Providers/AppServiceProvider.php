<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Carbon::setLocale('es');
        
        // Forzar HTTPS en producción, excepto para el health check endpoint
        if ($this->app->environment('production')) {
            $request = $this->app['request'];
            
            // No forzar HTTPS para /healthz (usado por Kubernetes probes)
            if ($request && $request->path() !== 'healthz') {
                URL::forceScheme('https');
            }
        }
    }
}
