<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExperienceLaboralService
{
    protected $geminiService;

    public function __construct()
    {
        // Usar el factory para obtener el servicio configurado (Vertex AI o Studio)
        $this->geminiService = GeminiServiceFactory::make();
    }
    public function validarCargosDesdePDF(array $experiencias)
    {
        // Obtener listas desde la base de datos
        $cargosValidos = DB::table('tipo_cargo')->pluck('nombre')->toArray();
        $areasValidas = DB::table('area')->pluck('nombre')->toArray();
        $sectoresValidos = DB::table('sector')->pluck('nombre')->toArray();

        $experienciasActualizadas = [];

        foreach ($experiencias as $exp) {
            $puestoOriginal = $exp['puesto'];
            $descripcion = $exp['descripcion'] ?? '';

            try {
                // === IA para el cargo ===
                $promptCargo = "Tengo este cargo laboral: \"$puestoOriginal\".\n";
                $promptCargo .= "De la siguiente lista de cargos válidos, dime cuál es el más parecido o equivalente. ";
                $promptCargo .= "Responde solo con uno de los nombres exactos de la lista, sin explicación.\n\n";
                $promptCargo .= implode(", ", $cargosValidos);

                $cargoMatch = $this->geminiService->generateContent($promptCargo, [
                    'temperature' => 0.1,
                    'maxTokens' => 100
                ]);

                $cargoMatch = $this->cleanResponse($cargoMatch);
                $exp['puesto_normalizado'] = $cargoMatch ?: $puestoOriginal;

                // === IA para el área ===
                $promptArea = "Según esta descripción laboral: \"$descripcion\".\n";
                $promptArea .= "¿Cuál de estas áreas es la más relacionada? Responde solo con un nombre exacto de la lista. Si no encuentras ninguno escoge 'Otros'\n\n";
                $promptArea .= implode(", ", $areasValidas);

                $areaMatch = $this->geminiService->generateContent($promptArea, [
                    'temperature' => 0.1,
                    'maxTokens' => 100
                ]);

                $areaMatch = $this->cleanResponse($areaMatch);
                $exp['area_deducida'] = $areaMatch ?: 'Otros';

                // === IA para el sector ===
                $promptSector = "Según esta descripción laboral: \"$descripcion\".\n";
                $promptSector .= "¿Cuál de estos sectores es el más relacionado? Responde solo con un nombre exacto de la lista. Si no encuentras ninguno escoge 'Otras Industrias'\n\n";
                $promptSector .= implode(", ", $sectoresValidos);

                $sectorMatch = $this->geminiService->generateContent($promptSector, [
                    'temperature' => 0.1,
                    'maxTokens' => 100
                ]);

                $sectorMatch = $this->cleanResponse($sectorMatch);
                $exp['sector_deducido'] = $sectorMatch ?: 'Otras Industrias';

                // Agregar a la colección final
                $experienciasActualizadas[] = $exp;
            } catch (\Exception $e) {
                // En caso de error, usar valores por defecto
                $exp['puesto_normalizado'] = $puestoOriginal;
                $exp['area_deducida'] = 'Otros';
                $exp['sector_deducido'] = 'Otras Industrias';
                $experienciasActualizadas[] = $exp;

                // Log del error para debugging
                Log::error('Error en validación de cargo con IA: ' . $e->getMessage());
            }
        }

        return $experienciasActualizadas;
    }

    /**
     * Limpia la respuesta de Gemini removiendo marcadores de código
     */
    private function cleanResponse(?string $response): ?string
    {
        if (!$response) {
            return null;
        }

        $response = trim($response);
        $response = preg_replace('/^```json\s*/', '', $response);
        $response = preg_replace('/\s*```$/', '', $response);
        $response = trim($response);

        return $response;
    }
}
