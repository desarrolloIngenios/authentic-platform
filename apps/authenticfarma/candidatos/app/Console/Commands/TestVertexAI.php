<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GeminiServiceFactory;

class TestVertexAI extends Command
{
    protected $signature = 'vertex:test';
    protected $description = 'Prueba conexión con Vertex AI Gemini';

    public function handle()
    {
        try {
            $this->info('🧪 Probando Vertex AI...');

            $gemini = GeminiServiceFactory::make();
            $response = $gemini->generateContent(
                'Responde solo: "Vertex AI funcionando correctamente"',
                ['temperature' => 0.1, 'maxTokens' => 50]
            );

            $this->info('✅ Respuesta recibida:');
            $this->line($response);
            $this->newLine();
            $this->info('🎉 ¡Vertex AI funciona correctamente!');

            // Mostrar info
            $info = $gemini->getModelInfo();
            $this->table(
                ['Key', 'Value'],
                collect($info)->map(fn($v, $k) => [$k, $v])
            );

            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            return 1;
        }
    }
}
