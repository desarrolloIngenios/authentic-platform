<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ExperienceLaboralService;

class TestExperienciaLaboral extends Command
{
    protected $signature = 'experiencia:test';
    protected $description = 'Prueba el ExperienceLaboralService con Vertex AI';

    public function handle()
    {
        try {
            $this->info('🧪 Probando ExperienceLaboralService con Vertex AI...');

            $experienceService = new ExperienceLaboralService();

            // Simulamos experiencias laborales de ejemplo
            $experiencias = [
                [
                    'puesto' => 'Desarrollador Full Stack',
                    'descripcion' => 'Desarrollo de aplicaciones web usando Laravel, React y bases de datos MySQL. Responsable del diseño y implementación de APIs REST.'
                ],
                [
                    'puesto' => 'Analista de Sistemas',
                    'descripcion' => 'Análisis de requerimientos empresariales y diseño de soluciones tecnológicas para el sector financiero.'
                ]
            ];

            $resultado = $experienceService->validarCargosDesdePDF($experiencias);

            $this->info('✅ Experiencias procesadas:');

            foreach ($resultado as $index => $exp) {
                $this->newLine();
                $this->line("Experiencia " . ($index + 1) . ":");
                $this->line("  Puesto original: " . $exp['puesto']);
                $this->line("  Puesto normalizado: " . $exp['puesto_normalizado']);
                $this->line("  Área deducida: " . $exp['area_deducida']);
                $this->line("  Sector deducido: " . $exp['sector_deducido']);
            }

            $this->newLine();
            $this->info('🎉 ¡ExperienceLaboralService funciona correctamente con Vertex AI!');

            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            return 1;
        }
    }
}
