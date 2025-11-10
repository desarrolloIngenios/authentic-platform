<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PDFProcessingService;

class TestPDFProcessing extends Command
{
    protected $signature = 'pdf:test';
    protected $description = 'Prueba el PDFProcessingService con Vertex AI';

    public function handle()
    {
        try {
            $this->info('🧪 Probando PDFProcessingService con Vertex AI...');

            $pdfService = new PDFProcessingService();

            // Simulamos el texto de un CV simple
            $cvText = "
            CURRICULUM VITAE
            
            Nombres: Juan Carlos
            Apellidos: Pérez González
            Género: Masculino
            Documento: Cédula de ciudadanía 1234567890
            
            EXPERIENCIA LABORAL
            - Desarrollador Senior en TechCorp (2020-01-15 a 2023-12-31)
            - Desarrollador Junior en StartupXYZ (2018-06-01 a 2019-12-31)
            
            EDUCACIÓN
            - Ingeniería de Sistemas - Universidad Nacional (2014-02-01 a 2018-12-15)
            
            HABILIDADES
            PHP, Laravel, JavaScript, React
            
            CONTACTO
            Email: juan.perez@email.com
            Teléfono: +57 300 123 4567
            
            IDIOMAS
            Español (Nativo), Inglés (B2)
            ";

            // Usar el método processWithGemini directamente
            $reflection = new \ReflectionClass($pdfService);
            $method = $reflection->getMethod('processWithGemini');
            $method->setAccessible(true);

            $response = $method->invoke($pdfService, $cvText);

            $this->info('✅ Respuesta recibida:');
            $this->line(substr($response, 0, 500) . '...');
            $this->newLine();
            $this->info('🎉 ¡PDFProcessingService funciona correctamente con Vertex AI!');

            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            return 1;
        }
    }
}
