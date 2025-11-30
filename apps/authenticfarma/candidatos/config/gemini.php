<?php
return [
    // Proveedor por defecto: 'vertex' o 'studio'
    'provider' => env('GEMINI_PROVIDER', 'vertex'),

    // API Key para Google AI Studio
    'api_key' => env('GEMINI_API_KEY'),

    // Configuración para Vertex AI
    'project_id' => env('GOOGLE_CLOUD_PROJECT'),
    'location' => env('GOOGLE_CLOUD_LOCATION', 'us-central1'),
    'model' => env('GEMINI_MODEL', 'gemini-1.5-flash'),
        'credentials' => env('GOOGLE_APPLICATION_CREDENTIALS'),
];
