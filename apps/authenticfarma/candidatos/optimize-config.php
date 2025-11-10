<?php
// Configuraciones de optimización para Laravel

return [
    // Cache de configuración
    'config_cache' => [
        'command' => 'php artisan config:cache',
        'description' => 'Cachear toda la configuración en un solo archivo'
    ],
    
    // Cache de rutas
    'route_cache' => [
        'command' => 'php artisan route:cache',
        'description' => 'Cachear todas las rutas registradas'
    ],
    
    // Cache de vistas
    'view_cache' => [
        'command' => 'php artisan view:cache',
        'description' => 'Pre-compilar todas las vistas Blade'
    ],
    
    // Optimización de eventos
    'event_cache' => [
        'command' => 'php artisan event:cache',
        'description' => 'Cachear eventos y listeners'
    ]
];
