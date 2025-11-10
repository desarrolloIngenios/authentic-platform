<?php

namespace App\Http\Controllers\AI;

use App\Http\Controllers\Controller;
use App\Services\GeminiService;
use App\Models\AIActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class GeminiController extends Controller
{
    private $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
        
        // Aplicar middleware de autenticación
        $this->middleware('auth:sanctum');
    }

    /**
     * Analizar CV de candidato con IA
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function analyzeCV(Request $request)
    {
        try {
            // Validar entrada
            $validator = Validator::make($request->all(), [
                'cv_text' => 'required|string|min:100',
                'job_description' => 'nullable|string',
                'candidate_id' => 'nullable|integer'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de entrada inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Registrar solicitud
            Log::info('Analizando CV con Gemini', [
                'user_id' => auth()->id(),
                'candidate_id' => $request->candidate_id,
                'cv_length' => strlen($request->cv_text)
            ]);

            // Analizar CV con Gemini
            $analysis = $this->geminiService->analyzeCV(
                $request->cv_text,
                $request->job_description
            );

            if (!$analysis['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error en el análisis del CV',
                    'error' => $analysis['error'] ?? 'Error desconocido'
                ], 500);
            }

            // Guardar resultado (opcional)
            if ($request->candidate_id) {
                $this->saveCVAnalysis($request->candidate_id, $analysis);
            }

            return response()->json([
                'success' => true,
                'message' => 'CV analizado exitosamente',
                'data' => [
                    'score' => $analysis['score'],
                    'analysis' => $analysis['analysis'],
                    'recommendations' => $analysis['recommendations'],
                    'processed_at' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error en análisis de CV:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => config('app.debug') ? $e->getMessage() : 'Error procesando CV'
            ], 500);
        }
    }

    /**
     * Generar preguntas de entrevista personalizadas
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateInterviewQuestions(Request $request)
    {
        try {
            // Validar entrada
            $validator = Validator::make($request->all(), [
                'candidate_data' => 'required|array',
                'position' => 'required|string',
                'difficulty_level' => 'nullable|string|in:basic,intermediate,advanced,mixed'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Generar preguntas con Gemini
            $questions = $this->geminiService->generateInterviewQuestions(
                $request->candidate_data,
                $request->position
            );

            if (!$questions['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error generando preguntas de entrevista'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Preguntas generadas exitosamente',
                'data' => [
                    'questions' => $questions['questions'],
                    'difficulty_level' => $questions['difficulty_level'],
                    'total_questions' => count($questions['questions']),
                    'generated_at' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error generando preguntas:', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error generando preguntas de entrevista'
            ], 500);
        }
    }

    /**
     * Matching candidato-puesto usando IA
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function matchCandidate(Request $request)
    {
        try {
            // Validar entrada
            $validator = Validator::make($request->all(), [
                'candidate_profile' => 'required|array',
                'job_requirements' => 'required|array',
                'candidate_id' => 'nullable|integer',
                'job_id' => 'nullable|integer'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Realizar matching con Gemini
            $matching = $this->geminiService->matchCandidateToJob(
                $request->candidate_profile,
                $request->job_requirements
            );

            if (!$matching['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error en el proceso de matching'
                ], 500);
            }

            // Guardar resultado de matching (opcional)
            if ($request->candidate_id && $request->job_id) {
                $this->saveMatchingResult($request->candidate_id, $request->job_id, $matching);
            }

            return response()->json([
                'success' => true,
                'message' => 'Matching completado exitosamente',
                'data' => [
                    'match_score' => $matching['match_score'],
                    'strengths' => $matching['strengths'],
                    'gaps' => $matching['gaps'],
                    'recommendation' => $matching['recommendation'],
                    'processed_at' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error en matching:', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error en el proceso de matching'
            ], 500);
        }
    }

    /**
     * Obtener estadísticas de uso de IA
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAIStats()
    {
        try {
            $userId = auth()->id();
            
            // Estadísticas reales desde la base de datos
            $stats = [
                'total_cv_analyzed' => AIActivity::byUser($userId)->cvAnalysis()->successful()->count(),
                'total_questions_generated' => AIActivity::byUser($userId)->interviewQuestions()->successful()->count(),
                'total_matchings' => AIActivity::byUser($userId)->matching()->successful()->count(),
                'average_match_score' => AIActivity::byUser($userId)->matching()->successful()->avg('score') ?? 0,
                'last_30_days_usage' => AIActivity::byUser($userId)->successful()
                    ->where('created_at', '>=', now()->subDays(30))->count(),
                'this_month_usage' => AIActivity::byUser($userId)->thisMonth()->successful()->count(),
                'error_rate' => $this->calculateErrorRate($userId),
                'most_used_feature' => $this->getMostUsedFeature($userId),
                'recent_activities' => AIActivity::byUser($userId)
                    ->with('candidate')
                    ->latest()
                    ->limit(5)
                    ->get()
                    ->map(function ($activity) {
                        return [
                            'type' => $activity->getActivityTypeLabel(),
                            'created_at' => $activity->created_at->diffForHumans(),
                            'status' => $activity->status,
                            'score' => $activity->score
                        ];
                    })
            ];

            return response()->json([
                'success' => true,
                'message' => 'Estadísticas obtenidas',
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Error obteniendo estadísticas AI:', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error obteniendo estadísticas'
            ], 500);
        }
    }

    /**
     * Calcular tasa de error
     */
    private function calculateErrorRate($userId)
    {
        $total = AIActivity::byUser($userId)->count();
        $errors = AIActivity::byUser($userId)->where('status', 'error')->count();
        
        return $total > 0 ? round(($errors / $total) * 100, 2) : 0;
    }

    /**
     * Obtener la funcionalidad más usada
     */
    private function getMostUsedFeature($userId)
    {
        $mostUsed = AIActivity::byUser($userId)
            ->selectRaw('activity_type, COUNT(*) as count')
            ->groupBy('activity_type')
            ->orderBy('count', 'desc')
            ->first();

        return $mostUsed ? $mostUsed->activity_type : null;
    }

    /**
     * Test de conectividad con Gemini
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function testConnection()
    {
        try {
            // Test simple con Gemini
            $testResult = $this->geminiService->generateContent("Responde solo 'OK' si recibes este mensaje");

            return response()->json([
                'success' => true,
                'message' => 'Conexión con Gemini exitosa',
                'data' => [
                    'status' => 'connected',
                    'response' => $testResult,
                    'tested_at' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de conexión con Gemini',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Guardar análisis de CV (método auxiliar)
     */
    private function saveCVAnalysis($candidateId, $analysis)
    {
        try {
            AIActivity::create([
                'user_id' => auth()->id(),
                'activity_type' => 'cv_analysis',
                'candidate_id' => $candidateId,
                'output_data' => $analysis,
                'score' => $analysis['score'] ?? null,
                'model_used' => env('VERTEX_AI_MODEL', 'gemini-1.5-flash'),
                'status' => $analysis['success'] ? 'completed' : 'error',
                'error_message' => $analysis['success'] ? null : ($analysis['error'] ?? 'Unknown error')
            ]);
            
            Log::info("Análisis de CV guardado para candidato: $candidateId");
        } catch (\Exception $e) {
            Log::warning("No se pudo guardar análisis de CV: " . $e->getMessage());
        }
    }

    /**
     * Guardar resultado de matching (método auxiliar)
     */
    private function saveMatchingResult($candidateId, $jobId, $matching)
    {
        try {
            AIActivity::create([
                'user_id' => auth()->id(),
                'activity_type' => 'matching',
                'candidate_id' => $candidateId,
                'job_id' => $jobId,
                'output_data' => $matching,
                'score' => $matching['match_score'] ?? null,
                'model_used' => env('VERTEX_AI_MODEL', 'gemini-1.5-flash'),
                'status' => $matching['success'] ? 'completed' : 'error',
                'error_message' => $matching['success'] ? null : ($matching['error'] ?? 'Unknown error')
            ]);
            
            Log::info("Resultado de matching guardado: candidato $candidateId, trabajo $jobId");
        } catch (\Exception $e) {
            Log::warning("No se pudo guardar resultado de matching: " . $e->getMessage());
        }
    }
}