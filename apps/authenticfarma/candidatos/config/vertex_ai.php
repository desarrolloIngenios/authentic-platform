<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Vertex AI Configuration for AuthenticFarma
    |--------------------------------------------------------------------------
    |
    | Configuration for Google Cloud Vertex AI integration with Gemini models
    | Service Account: laravel-gemini-prod@authentic-prod-464216.iam.gserviceaccount.com
    |
    */

    'project_id' => env('VERTEX_AI_PROJECT_ID', 'authentic-prod-464216'),
    
    'location' => env('VERTEX_AI_LOCATION', 'us-central1'),
    
    'model' => env('VERTEX_AI_MODEL', 'gemini-1.5-flash'),
    
    'service_account' => env('VERTEX_AI_SERVICE_ACCOUNT', 'laravel-gemini-prod@authentic-prod-464216.iam.gserviceaccount.com'),
    
    'credentials_path' => env('GOOGLE_APPLICATION_CREDENTIALS'),
    
    /*
    |--------------------------------------------------------------------------
    | Model Configuration
    |--------------------------------------------------------------------------
    */
    
    'models' => [
        'gemini-1.5-flash' => [
            'name' => 'gemini-1.5-flash',
            'description' => 'Fast and efficient model for text generation',
            'max_tokens' => 8192,
            'temperature' => 0.7,
        ],
        'gemini-1.5-pro' => [
            'name' => 'gemini-1.5-pro', 
            'description' => 'Advanced model for complex reasoning tasks',
            'max_tokens' => 32768,
            'temperature' => 0.7,
        ],
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Generation Parameters
    |--------------------------------------------------------------------------
    */
    
    'generation_config' => [
        'temperature' => env('VERTEX_AI_TEMPERATURE', 0.7),
        'max_output_tokens' => env('VERTEX_AI_MAX_TOKENS', 2048),
        'top_p' => env('VERTEX_AI_TOP_P', 0.95),
        'top_k' => env('VERTEX_AI_TOP_K', 40),
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Safety Settings
    |--------------------------------------------------------------------------
    */
    
    'safety_settings' => [
        'HARM_CATEGORY_HARASSMENT' => 'BLOCK_MEDIUM_AND_ABOVE',
        'HARM_CATEGORY_HATE_SPEECH' => 'BLOCK_MEDIUM_AND_ABOVE',
        'HARM_CATEGORY_SEXUALLY_EXPLICIT' => 'BLOCK_MEDIUM_AND_ABOVE',
        'HARM_CATEGORY_DANGEROUS_CONTENT' => 'BLOCK_MEDIUM_AND_ABOVE',
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Prompt Templates
    |--------------------------------------------------------------------------
    */
    
    'prompts' => [
        'cv_analysis' => [
            'system' => 'Eres un experto en recursos humanos especializado en análisis de CVs para el sector farmacéutico y salud.',
            'template' => 'Analiza el siguiente CV y proporciona una evaluación detallada para el puesto: {job_title}

CV del Candidato:
{cv_text}

Descripción del Puesto:
{job_description}

Proporciona un análisis estructurado incluyendo:
1. Puntuación de compatibilidad (0-100)
2. Fortalezas del candidato
3. Áreas de mejora
4. Recomendaciones específicas
5. Experiencia relevante en el sector farmacéutico/salud

Responde en formato JSON estructurado.',
        ],
        
        'interview_questions' => [
            'system' => 'Eres un especialista en entrevistas de trabajo para el sector farmacéutico y salud.',
            'template' => 'Genera preguntas de entrevista personalizadas basadas en:

Puesto: {job_title}
Nivel: {job_level}
Descripción: {job_description}
Perfil del candidato: {candidate_profile}

Genera 10 preguntas divididas en:
- 3 preguntas técnicas específicas del sector farmacéutico
- 3 preguntas sobre experiencia y competencias
- 2 preguntas situacionales
- 2 preguntas sobre motivación y cultura empresarial

Responde en formato JSON con la estructura: {"questions": [{"category": "", "question": "", "expected_focus": ""}]}',
        ],
        
        'candidate_matching' => [
            'system' => 'Eres un algoritmo avanzado de matching entre candidatos y posiciones en el sector salud.',
            'template' => 'Evalúa la compatibilidad entre el candidato y el puesto:

CANDIDATO:
Nombre: {candidate_name}
Experiencia: {candidate_experience}
Habilidades: {candidate_skills}
Educación: {candidate_education}

PUESTO:
Título: {job_title}
Requisitos: {job_requirements}
Habilidades necesarias: {required_skills}
Experiencia mínima: {min_experience}

Calcula:
1. Porcentaje de compatibilidad (0-100%)
2. Match por categorías (técnico, experiencia, educación, soft skills)
3. Factores diferenciadores del candidato
4. Posibles brechas a considerar
5. Recomendación de entrevista (sí/no con justificación)

Responde en formato JSON estructurado.',
        ],
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Rate Limiting & Quotas
    |--------------------------------------------------------------------------
    */
    
    'rate_limits' => [
        'requests_per_minute' => env('VERTEX_AI_RPM', 60),
        'requests_per_day' => env('VERTEX_AI_RPD', 1000),
        'tokens_per_minute' => env('VERTEX_AI_TPM', 40000),
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    */
    
    'logging' => [
        'enabled' => env('VERTEX_AI_LOGGING', true),
        'level' => env('VERTEX_AI_LOG_LEVEL', 'info'),
        'log_requests' => env('VERTEX_AI_LOG_REQUESTS', true),
        'log_responses' => env('VERTEX_AI_LOG_RESPONSES', false), // Por privacidad
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    */
    
    'cache' => [
        'enabled' => env('VERTEX_AI_CACHE_ENABLED', true),
        'ttl' => env('VERTEX_AI_CACHE_TTL', 3600), // 1 hora
        'prefix' => 'vertex_ai:',
    ],
];