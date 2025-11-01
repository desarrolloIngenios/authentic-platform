<?php
return [

    /*
    |--------------------------------------------------------------------------
    | API OpenIA
    |--------------------------------------------------------------------------
    |
    | Esta es la opción para configurar la API de OpenIA.
    |
    */
    'openIA' => [
        'url' => env('OPENIA_URL', 'https://api.openai.com/v1/chat/completions'),
        'token' => env('OPENIA_TOKEN', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | API Gemini
    |--------------------------------------------------------------------------
    |
    | Esta es la opción para configurar la API de Gemini en diferentes ambientes.
    |
    */
    'gemini' => [
        'prod_token' => env('GEMINI_PROD_TOKEN', ''),
        'dev_token' => env('GEMINI_DEV_TOKEN', ''),
    ],
];
