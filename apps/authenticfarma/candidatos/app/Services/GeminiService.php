<?php

namespace App\Services;

use Google\Cloud\AIPlatform\V1\EndpointServiceClient;
use Google\Cloud\AIPlatform\V1\PredictRequest;
use Google\Cloud\AIPlatform\V1\PredictResponse;
use Google\Protobuf\Value;
use Google\Protobuf\Struct;
use Google\Protobuf\ListValue;
use Illuminate\Support\Facades\Log;
use Exception;

class GeminiService
{
    private $projectId;
    private $location;
    private $model;
    private $client;

    public function __construct()
    {
        $this->projectId = env('VERTEX_AI_PROJECT_ID', 'authentic-prod-464216');
        $this->location = env('VERTEX_AI_LOCATION', 'us-central1');
        $this->model = env('VERTEX_AI_MODEL', 'gemini-1.5-flash');
        
        // Service Account: laravel-gemini-prod@authentic-prod-464216.iam.gserviceaccount.com
        Log::info('🤖 Initializing GeminiService for AuthenticFarma', [
            'project_id' => $this->projectId,
            'location' => $this->location,
            'model' => $this->model,
            'service_account' => env('VERTEX_AI_SERVICE_ACCOUNT', 'laravel-gemini-prod@authentic-prod-464216.iam.gserviceaccount.com')
        ]);
        
        // Verificar credenciales
        $credentialsPath = env('GOOGLE_APPLICATION_CREDENTIALS');
        if ($credentialsPath && file_exists($credentialsPath)) {
            Log::info('✅ Vertex AI credentials found', ['path' => $credentialsPath]);
        } else {
            Log::warning('⚠️ Vertex AI credentials not found', ['path' => $credentialsPath]);
        }
        
        // Configurar autenticación desde variable de entorno
        $this->initializeClient();
    }

    /**
     * Inicializar cliente de Vertex AI
     */
    private function initializeClient()
    {
        try {
            // La autenticación se maneja automáticamente con GOOGLE_APPLICATION_CREDENTIALS
            $this->client = new EndpointServiceClient();
        } catch (Exception $e) {
            Log::error('Error inicializando cliente Gemini: ' . $e->getMessage());
            throw new Exception('No se pudo inicializar el servicio Gemini');
        }
    }

    /**
     * Analizar CV de candidato
     * 
     * @param string $cvText Texto del CV
     * @param string $jobDescription Descripción del puesto
     * @return array
     */
    public function analyzeCV($cvText, $jobDescription = null)
    {
        try {
            $prompt = $this->buildCVAnalysisPrompt($cvText, $jobDescription);
            
            $response = $this->generateContent($prompt);
            
            return [
                'success' => true,
                'analysis' => $response,
                'score' => $this->extractScore($response),
                'recommendations' => $this->extractRecommendations($response)
            ];
        } catch (Exception $e) {
            Log::error('Error analizando CV con Gemini: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Error en análisis de CV',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Generar preguntas de entrevista personalizadas
     * 
     * @param array $candidateData Datos del candidato
     * @param string $position Puesto de trabajo
     * @return array
     */
    public function generateInterviewQuestions($candidateData, $position)
    {
        try {
            $prompt = $this->buildInterviewPrompt($candidateData, $position);
            
            $response = $this->generateContent($prompt);
            
            return [
                'success' => true,
                'questions' => $this->parseQuestions($response),
                'difficulty_level' => $this->extractDifficulty($response)
            ];
        } catch (Exception $e) {
            Log::error('Error generando preguntas con Gemini: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Error generando preguntas de entrevista'
            ];
        }
    }

    /**
     * Matching candidato-puesto usando IA
     * 
     * @param array $candidateProfile Perfil del candidato
     * @param array $jobRequirements Requisitos del puesto
     * @return array
     */
    public function matchCandidateToJob($candidateProfile, $jobRequirements)
    {
        try {
            $prompt = $this->buildMatchingPrompt($candidateProfile, $jobRequirements);
            
            $response = $this->generateContent($prompt);
            
            return [
                'success' => true,
                'match_score' => $this->extractMatchScore($response),
                'strengths' => $this->extractStrengths($response),
                'gaps' => $this->extractGaps($response),
                'recommendation' => $this->extractRecommendation($response)
            ];
        } catch (Exception $e) {
            Log::error('Error en matching con Gemini: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Error en matching candidato-puesto'
            ];
        }
    }

    /**
     * Generar contenido usando Gemini
     * 
     * @param string $prompt
     * @return string
     */
    private function generateContent($prompt)
    {
        try {
            // Construir endpoint
            $endpoint = sprintf(
                'projects/%s/locations/%s/publishers/google/models/%s',
                $this->projectId,
                $this->location,
                $this->model
            );

            // Preparar parámetros
            $parameters = new Struct([
                'temperature' => new Value(['number_value' => 0.3]),
                'maxOutputTokens' => new Value(['number_value' => 2048]),
                'topP' => new Value(['number_value' => 0.8]),
                'topK' => new Value(['number_value' => 40])
            ]);

            // Preparar instancia
            $instance = new Struct([
                'prompt' => new Value(['string_value' => $prompt])
            ]);

            // Crear request
            $request = new PredictRequest([
                'endpoint' => $endpoint,
                'instances' => [new Value(['struct_value' => $instance])],
                'parameters' => new Value(['struct_value' => $parameters])
            ]);

            // Ejecutar predicción
            $response = $this->client->predict($request);
            
            // Extraer respuesta
            $predictions = $response->getPredictions();
            if (!empty($predictions)) {
                $prediction = $predictions[0];
                return $prediction->getStructValue()->getFields()['content']->getStringValue();
            }
            
            throw new Exception('No se recibió respuesta válida de Gemini');
            
        } catch (Exception $e) {
            Log::error('Error llamando a Gemini API: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Construir prompt para análisis de CV
     */
    private function buildCVAnalysisPrompt($cvText, $jobDescription)
    {
        $prompt = "Actúa como un experto reclutador especializado en análisis de CVs. ";
        $prompt .= "Analiza el siguiente CV y proporciona un análisis detallado:\n\n";
        $prompt .= "CV DEL CANDIDATO:\n" . $cvText . "\n\n";
        
        if ($jobDescription) {
            $prompt .= "DESCRIPCIÓN DEL PUESTO:\n" . $jobDescription . "\n\n";
            $prompt .= "Evalúa la compatibilidad del candidato con el puesto específico.\n\n";
        }
        
        $prompt .= "Proporciona el análisis en el siguiente formato JSON:\n";
        $prompt .= "{\n";
        $prompt .= "  \"score\": \"puntuación del 1-100\",\n";
        $prompt .= "  \"strengths\": [\"fortaleza 1\", \"fortaleza 2\"],\n";
        $prompt .= "  \"weaknesses\": [\"debilidad 1\", \"debilidad 2\"],\n";
        $prompt .= "  \"experience_years\": \"años de experiencia\",\n";
        $prompt .= "  \"key_skills\": [\"habilidad 1\", \"habilidad 2\"],\n";
        $prompt .= "  \"recommendations\": [\"recomendación 1\", \"recomendación 2\"]\n";
        $prompt .= "}";
        
        return $prompt;
    }

    /**
     * Construir prompt para preguntas de entrevista
     */
    private function buildInterviewPrompt($candidateData, $position)
    {
        $prompt = "Genera 5 preguntas de entrevista personalizadas para el siguiente candidato:\n\n";
        $prompt .= "CANDIDATO:\n" . json_encode($candidateData, JSON_PRETTY_PRINT) . "\n\n";
        $prompt .= "PUESTO: " . $position . "\n\n";
        $prompt .= "Las preguntas deben ser específicas, relevantes y de diferentes niveles de dificultad.\n";
        $prompt .= "Formato JSON requerido:\n";
        $prompt .= "{\n";
        $prompt .= "  \"questions\": [\n";
        $prompt .= "    {\"question\": \"pregunta\", \"level\": \"básico/intermedio/avanzado\", \"category\": \"técnica/comportamental/situacional\"}\n";
        $prompt .= "  ]\n";
        $prompt .= "}";
        
        return $prompt;
    }

    /**
     * Construir prompt para matching
     */
    private function buildMatchingPrompt($candidateProfile, $jobRequirements)
    {
        $prompt = "Analiza la compatibilidad entre el siguiente candidato y los requisitos del puesto:\n\n";
        $prompt .= "PERFIL DEL CANDIDATO:\n" . json_encode($candidateProfile, JSON_PRETTY_PRINT) . "\n\n";
        $prompt .= "REQUISITOS DEL PUESTO:\n" . json_encode($jobRequirements, JSON_PRETTY_PRINT) . "\n\n";
        $prompt .= "Proporciona un análisis detallado en formato JSON:\n";
        $prompt .= "{\n";
        $prompt .= "  \"match_score\": \"puntuación 1-100\",\n";
        $prompt .= "  \"compatibility_level\": \"alta/media/baja\",\n";
        $prompt .= "  \"strengths\": [\"fortalezas que coinciden\"],\n";
        $prompt .= "  \"gaps\": [\"requisitos no cumplidos\"],\n";
        $prompt .= "  \"recommendation\": \"recomendación final\"\n";
        $prompt .= "}";
        
        return $prompt;
    }

    /**
     * Métodos auxiliares para extraer información de las respuestas
     */
    private function extractScore($response)
    {
        try {
            $data = json_decode($response, true);
            return isset($data['score']) ? intval($data['score']) : null;
        } catch (Exception $e) {
            return null;
        }
    }

    private function extractRecommendations($response)
    {
        try {
            $data = json_decode($response, true);
            return isset($data['recommendations']) ? $data['recommendations'] : [];
        } catch (Exception $e) {
            return [];
        }
    }

    private function parseQuestions($response)
    {
        try {
            $data = json_decode($response, true);
            return isset($data['questions']) ? $data['questions'] : [];
        } catch (Exception $e) {
            return [];
        }
    }

    private function extractDifficulty($response)
    {
        try {
            $data = json_decode($response, true);
            return isset($data['difficulty_level']) ? $data['difficulty_level'] : 'mixed';
        } catch (Exception $e) {
            return 'mixed';
        }
    }

    private function extractMatchScore($response)
    {
        try {
            $data = json_decode($response, true);
            return isset($data['match_score']) ? intval($data['match_score']) : 0;
        } catch (Exception $e) {
            return 0;
        }
    }

    private function extractStrengths($response)
    {
        try {
            $data = json_decode($response, true);
            return isset($data['strengths']) ? $data['strengths'] : [];
        } catch (Exception $e) {
            return [];
        }
    }

    private function extractGaps($response)
    {
        try {
            $data = json_decode($response, true);
            return isset($data['gaps']) ? $data['gaps'] : [];
        } catch (Exception $e) {
            return [];
        }
    }

    private function extractRecommendation($response)
    {
        try {
            $data = json_decode($response, true);
            return isset($data['recommendation']) ? $data['recommendation'] : '';
        } catch (Exception $e) {
            return '';
        }
    }
}