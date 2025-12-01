<?php


namespace App\Services;

use Google\Cloud\AIPlatform\V1\Client\PredictionServiceClient;
use Illuminate\Support\Facades\Http;

use Illuminate\Support\Facades\Log;

class GeminiServiceFactory
{
    /**
     * Crea la instancia correcta según el provider configurado
     */
    public static function make(?string $provider = null): GeminiServiceInterface
    {
        $provider = $provider ?? config('gemini.provider', 'vertex');

        Log::info('Inicializando Gemini Service', ['provider' => $provider]);

        return match ($provider) {
            'vertex' => new GeminiVertexService(),
            'studio' => new GeminiStudioService(),
            default => throw new \Exception("Provider no válido: {$provider}")
        };
    }
}

/**
 * Interface común para ambos providers
 */
interface GeminiServiceInterface
{
    public function generateContent(string $prompt, array $options = []): string;
    public function setModel(string $model): self;
    public function getModelInfo(): array;
    public function getAvailableModels(): array;
}

/**
 * Implementación para Google AI Studio
 */
class GeminiStudioService implements GeminiServiceInterface
{
    protected $apiKey;
    protected $model;
    protected $baseUrl = 'https://generativelanguage.googleapis.com';

    public function __construct(?string $model = null)
    {
        $this->apiKey = config('gemini.api_key') ?? env('GEMINI_API_KEY');
        $this->model = $model ?? config('gemini.model', 'gemini-1.5-flash');

        if (empty($this->apiKey)) {
            throw new \Exception('GEMINI_API_KEY no configurada');
        }
    }

    public function generateContent(string $prompt, array $options = []): string
    {
        $url = "{$this->baseUrl}/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}";

        $payload = [
            'contents' => [
                ['parts' => [['text' => $prompt]]]
            ],
            'generationConfig' => [
                'temperature' => $options['temperature'] ?? 0.7,
                'maxOutputTokens' => $options['maxTokens'] ?? 2048,
            ]
        ];

        $response = \Illuminate\Support\Facades\Http::timeout(60)
            ->post($url, $payload);

        if ($response->failed()) {
            $this->handleError($response);
        }

        $data = $response->json();
        return $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
    }

    protected function handleError($response): void
    {
        $status = $response->status();
        $body = $response->json();
        $message = $body['error']['message'] ?? 'Error desconocido';

        if ($status === 400 && str_contains($message, 'leaked')) {
            Log::critical('🚨 API KEY COMPROMETIDA en AI Studio');
            throw new \Exception('API Key comprometida. Genera una nueva en https://aistudio.google.com/app/apikey', 403);
        }

        throw new \Exception("Error {$status}: {$message}", $status);
    }

    public function setModel(string $model): self
    {
        $this->model = $model;
        return $this;
    }

    public function getModelInfo(): array
    {
        return [
            'provider' => 'Google AI Studio',
            'model' => $this->model,
            'api_version' => 'v1beta',
        ];
    }

    public function getAvailableModels(): array
    {
        return ['gemini-1.5-pro', 'gemini-1.5-flash', 'gemini-1.0-pro'];
    }
}

/**
 * Implementación para Vertex AI
 */
class GeminiVertexService implements GeminiServiceInterface
{
    protected $projectId;
    protected $location;
    protected $model;

    public function __construct(?string $model = null)
    {
        $this->projectId = config('gemini.project_id') ?? env('GOOGLE_CLOUD_PROJECT');
        $this->location = config('gemini.location') ?? env('GOOGLE_CLOUD_LOCATION', 'us-central1');
        $this->model = $model ?? config('gemini.model', 'gemini-1.5-flash');

        $credentialsPath = config('gemini.credentials') ?? env('GOOGLE_APPLICATION_CREDENTIALS');

        // Convertir ruta relativa a absoluta si es necesario
        if ($credentialsPath && !file_exists($credentialsPath)) {
            $absolutePath = base_path($credentialsPath);
            if (file_exists($absolutePath)) {
                $credentialsPath = $absolutePath;
            }
        }

        // Con Workload Identity, no hay archivo de credenciales.
        // Si $credentialsPath es null o vacío, usamos ADC (metadata server) automáticamente.
        if (!empty($credentialsPath) && !file_exists($credentialsPath)) {
            throw new \Exception("Credenciales no encontradas: {$credentialsPath}");
        }

        // Ya no necesitamos crear el cliente gRPC aquí
        // $this->client = new PredictionServiceClient();
    }

    public function generateContent(string $prompt, array $options = []): string
    {
        try {
            // Para Gemini en Vertex AI, usamos la API REST de Generative AI
            $url = sprintf(
                'https://%s-aiplatform.googleapis.com/v1/projects/%s/locations/%s/publishers/google/models/%s:generateContent',
                $this->location,
                $this->projectId,
                $this->location,
                $this->model
            );

            $accessToken = $this->getAccessToken();

            $payload = [
                'contents' => [
                    ['role' => 'user', 'parts' => [['text' => $prompt]]]
                ],
                'generation_config' => [
                    'temperature' => $options['temperature'] ?? 0.7,
                    'maxOutputTokens' => $options['maxTokens'] ?? 2048,
                    'topP' => $options['topP'] ?? 0.95,
                    'topK' => $options['topK'] ?? 40,
                ]
            ];

            if (isset($options['systemInstruction'])) {
                $payload['system_instruction'] = [
                    'parts' => [['text' => $options['systemInstruction']]]
                ];
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->timeout(60)->post($url, $payload);

            if ($response->failed()) {
                throw new \Exception('Error HTTP: ' . $response->status() . ' - ' . $response->body());
            }

            $data = $response->json();
            return $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
        } catch (\Exception $e) {
            throw new \Exception('Error en Vertex AI: ' . $e->getMessage());
        }
    }

    private function getAccessToken(): string
    {
        $credentialsPath = config('gemini.credentials') ?? env('GOOGLE_APPLICATION_CREDENTIALS');

        // Convertir ruta relativa a absoluta si es necesario
        if ($credentialsPath && !file_exists($credentialsPath)) {
            $absolutePath = base_path($credentialsPath);
            if (file_exists($absolutePath)) {
                $credentialsPath = $absolutePath;
            }
        }

        if (!file_exists($credentialsPath)) {
            throw new \Exception("Credenciales no encontradas: {$credentialsPath}");
        }

        $credentials = json_decode(file_get_contents($credentialsPath), true);

        // Usar Google OAuth2 para obtener access token
        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $this->createJWT($credentials)
        ]);

        if ($response->failed()) {
            throw new \Exception('Error obteniendo access token: ' . $response->body());
        }

        return $response->json()['access_token'];
    }

    private function createJWT(array $credentials): string
    {
        $header = json_encode(['alg' => 'RS256', 'typ' => 'JWT']);
        $now = time();

        $payload = json_encode([
            'iss' => $credentials['client_email'],
            'scope' => 'https://www.googleapis.com/auth/cloud-platform',
            'aud' => 'https://oauth2.googleapis.com/token',
            'exp' => $now + 3600,
            'iat' => $now
        ]);

        $base64Header = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64Payload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

        $signature = '';
        openssl_sign($base64Header . '.' . $base64Payload, $signature, $credentials['private_key'], 'sha256WithRSAEncryption');
        $base64Signature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        return $base64Header . '.' . $base64Payload . '.' . $base64Signature;
    }

    protected function handleApiException(\Google\ApiCore\ApiException $e): void
    {
        $message = $e->getMessage();

        if (str_contains($message, 'leaked')) {
            Log::critical('🚨 CREDENCIALES COMPROMETIDAS en Vertex AI');
            throw new \Exception('Credenciales comprometidas. Revocar Service Account inmediatamente.', 403);
        }

        if ($e->getCode() === 429) {
            throw new \Exception('Cuota excedida. Intenta más tarde.', 429);
        }

        Log::error('Error Vertex AI: ' . $message);
        throw new \Exception('Error en Vertex AI: ' . $message, $e->getCode());
    }

    public function setModel(string $model): self
    {
        $this->model = $model;
        return $this;
    }

    public function getModelInfo(): array
    {
        return [
            'provider' => 'Vertex AI',
            'model' => $this->model,
            'project_id' => $this->projectId,
            'location' => $this->location,
        ];
    }

    public function getAvailableModels(): array
    {
        return ['gemini-1.5-pro', 'gemini-1.5-flash', 'gemini-2.0-flash-exp'];
    }
}
