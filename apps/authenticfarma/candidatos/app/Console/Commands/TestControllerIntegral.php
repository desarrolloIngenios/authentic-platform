<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PDFProcessingService;
use App\Services\ExperienceLaboralService;

class TestControllerIntegral extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'integral:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prueba integral: PDF → Vertex AI → Experiencias normalizadas';

    protected $pdfService;
    protected $experienceService;

    public function __construct(PDFProcessingService $pdfService, ExperienceLaboralService $experienceService)
    {
        parent::__construct();
        $this->pdfService = $pdfService;
        $this->experienceService = $experienceService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 INICIANDO PRUEBA INTEGRAL DEL CONTROLADOR');
        $this->info('════════════════════════════════════════════');

        // Ruta del PDF de prueba
        $pdfPath = public_path('CV_Julian_Garnica.pdf');

        if (!file_exists($pdfPath)) {
            $this->error('❌ No se encontró el archivo PDF: ' . $pdfPath);
            return;
        }

        $this->info('📄 Archivo encontrado: ' . $pdfPath);
        $this->info('📊 Tamaño: ' . number_format(filesize($pdfPath) / 1024, 2) . ' KB');
        $this->newLine();

        try {
            // PASO 1: Procesar PDF con Vertex AI
            $this->info('🔄 PASO 1: Procesando PDF con Vertex AI...');
            $this->info('────────────────────────────────────────────');

            $startTime = microtime(true);
            $cv = $this->pdfService->processPdf($pdfPath);
            $pdfProcessTime = microtime(true) - $startTime;

            $this->info('✅ PDF procesado exitosamente en ' . number_format($pdfProcessTime, 2) . ' segundos');
            $this->newLine();

            // Mostrar datos extraídos
            $this->info('📋 DATOS EXTRAÍDOS DEL PDF:');
            $this->info('────────────────────────────');
            $this->info('👤 Nombre: ' . ($cv['nombreCompleto'] ?? 'No extraído'));
            $this->info('👤 Apellido: ' . ($cv['apellidoCompleto'] ?? 'No extraído'));
            $this->info('📧 Email: ' . ($cv['contacto']['correo'] ?? 'No extraído'));
            $this->info('📞 Teléfono: ' . ($cv['contacto']['telefono'] ?? 'No extraído'));
            $this->info('🎓 Educación: ' . count($cv['educacion'] ?? []) . ' registros');
            $this->info('🌍 Idiomas: ' . count($cv['nivelIdioma'] ?? []) . ' idiomas');
            $this->info('💼 Experiencias: ' . count($cv['experienciaLaboral'] ?? []) . ' trabajos');

            if (!empty($cv['experienciaLaboral'])) {
                $this->newLine();
                $this->info('📝 EXPERIENCIAS EXTRAÍDAS:');
                foreach ($cv['experienciaLaboral'] as $index => $exp) {
                    $this->info('  ' . ($index + 1) . '. ' . ($exp['puesto'] ?? 'Sin puesto') . ' en ' . ($exp['empresa'] ?? 'Sin empresa'));
                }
            }

            $this->newLine();

            // PASO 2: Procesar experiencias con Vertex AI
            $this->info('🔄 PASO 2: Normalizando experiencias con Vertex AI...');
            $this->info('───────────────────────────────────────────────────');

            if (empty($cv['experienciaLaboral'])) {
                $this->warn('⚠️  No hay experiencias laborales para procesar');
                return;
            }

            $startTime = microtime(true);
            $experiences = $this->experienceService->validarCargosDesdePDF($cv['experienciaLaboral']);
            $expProcessTime = microtime(true) - $startTime;

            $this->info('✅ Experiencias procesadas exitosamente en ' . number_format($expProcessTime, 2) . ' segundos');
            $this->newLine();

            // PASO 3: Mostrar resultados procesados
            $this->info('🎯 PASO 3: RESULTADOS NORMALIZADOS');
            $this->info('═══════════════════════════════════');

            foreach ($experiences as $index => $exp) {
                $this->info('📍 EXPERIENCIA ' . ($index + 1) . ':');
                $this->info('  🏢 Empresa: ' . ($exp['empresa'] ?? 'N/A'));
                $this->info('  💼 Puesto Original: ' . ($exp['puesto'] ?? 'N/A'));
                $this->info('  ✨ Puesto Normalizado: ' . ($exp['puesto_normalizado'] ?? 'N/A'));
                $this->info('  🎯 Área: ' . ($exp['area_deducida'] ?? 'N/A'));
                $this->info('  🏭 Sector: ' . ($exp['sector_deducido'] ?? 'N/A'));
                $this->info('  📅 Período: ' . ($exp['fecha_inicio'] ?? 'N/A') . ' - ' . ($exp['fecha_fin'] ?? 'Actual'));
                $this->newLine();
            }

            // RESUMEN FINAL
            $this->info('📊 RESUMEN DE LA PRUEBA INTEGRAL');
            $this->info('═══════════════════════════════════');
            $this->info('✅ PDFProcessingService: FUNCIONANDO con Vertex AI');
            $this->info('✅ ExperienceLaboralService: FUNCIONANDO con Vertex AI');
            $this->info('⚡ Tiempo PDF: ' . number_format($pdfProcessTime, 2) . 's');
            $this->info('⚡ Tiempo Experiencias: ' . number_format($expProcessTime, 2) . 's');
            $this->info('⚡ Tiempo Total: ' . number_format($pdfProcessTime + $expProcessTime, 2) . 's');
            $this->info('🎯 Experiencias Procesadas: ' . count($experiences));
            $this->newLine();
            $this->info('🎉 INTEGRACIÓN COMPLETA VALIDADA EXITOSAMENTE');
        } catch (\Exception $e) {
            $this->error('❌ Error durante la prueba: ' . $e->getMessage());
            $this->error('📁 Archivo: ' . $e->getFile());
            $this->error('📍 Línea: ' . $e->getLine());
        }
    }
}
