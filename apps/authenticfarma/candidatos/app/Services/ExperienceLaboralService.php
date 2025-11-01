<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ExperienceLaboralService
{
    public function validarCargosDesdePDF(array $experiencias)
    {
        // Determinar el token según el ambiente
        if (app()->environment('production')) {
            $token = config('IA.gemini.prod_token', 'AIzaSyDXcJ1nZoEe2Ilkt_RAPAcgausCQjjS0To');
        } else {
            // $token = config('IA.gemini.dev_token', 'AIzaSyAumhK-8m_t_zqVwJz8UndYjesVd_mRyPo');
            $token = 'AIzaSyDXcJ1nZoEe2Ilkt_RAPAcgausCQjjS0To';
        }

        // $model = 'gemini-1.5-flash';
        $model = 'gemini-2.0-flash-lite';
        // Obtener listas desde la base de datos
        $cargosValidos = DB::table('tipo_cargo')->pluck('nombre')->toArray();
        $areasValidas = DB::table('area')->pluck('nombre')->toArray();
        $sectoresValidos = DB::table('sector')->pluck('nombre')->toArray();

        $experienciasActualizadas = [];

        foreach ($experiencias as $exp) {
            $puestoOriginal = $exp['puesto'];
            $descripcion = $exp['descripcion'] ?? '';

            // === IA para el cargo ===
            $promptCargo = "Tengo este cargo laboral: \"$puestoOriginal\".\n";
            $promptCargo .= "De la siguiente lista de cargos válidos, dime cuál es el más parecido o equivalente. ";
            $promptCargo .= "Responde solo con uno de los nombres exactos de la lista, sin explicación.\n\n";
            $promptCargo .= implode(", ", $cargosValidos);

            $responseCargo = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/$model:generateContent?key=$token", [
                'contents' => [
                    'parts' => [
                        ['text' => $promptCargo]
                    ]
                ]
            ]);

            $cargoMatch = trim($responseCargo->json()['candidates'][0]['content']['parts'][0]['text'] ?? $puestoOriginal);
            if ($cargoMatch) {
                $cargoMatch = preg_replace('/^```json\s*/', '', $cargoMatch);
                $cargoMatch = preg_replace('/\s*```$/', '', $cargoMatch);
                $cargoMatch = trim($cargoMatch);
            }
            $exp['puesto_normalizado'] = $cargoMatch;

            // === IA para el área ===
            $promptArea = "Según esta descripción laboral: \"$descripcion\".\n";
            $promptArea .= "¿Cuál de estas áreas es la más relacionada? Responde solo con un nombre exacto de la lista. Si no encentras ninguno escoge 'Otros'\n\n";
            $promptArea .= implode(", ", $areasValidas);

            $responseArea = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/$model:generateContent?key=$token", [
                'contents' => [
                    'parts' => [
                        ['text' => $promptArea]
                    ]
                ]
            ]);

            $areaMatch = trim($responseArea->json()['candidates'][0]['content']['parts'][0]['text'] ?? null);

            if ($areaMatch) {
                $areaMatch = preg_replace('/^```json\s*/', '', $areaMatch);
                $areaMatch = preg_replace('/\s*```$/', '', $areaMatch);
                $areaMatch = trim($areaMatch);
            }
            $exp['area_deducida'] = $areaMatch;

            // === IA para el sector ===
            $promptSector = "Según esta descripción laboral: \"$descripcion\".\n";
            $promptSector .= "¿Cuál de estos sectores es el más relacionado? Responde solo con un nombre exacto de la lista. Si no encentras ninguno escoge 'Otras Industrias'\n\n";
            $promptSector .= implode(", ", $sectoresValidos);

            $responseSector = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/$model:generateContent?key=$token", [
                'contents' => [
                    'parts' => [
                        ['text' => $promptSector]
                    ]
                ]
            ]);

            $sectorMatch = trim($responseSector->json()['candidates'][0]['content']['parts'][0]['text'] ?? null);

            if ($sectorMatch) {
                $sectorMatch = preg_replace('/^```json\s*/', '', $sectorMatch);
                $sectorMatch = preg_replace('/\s*```$/', '', $sectorMatch);
                $sectorMatch = trim($sectorMatch);
            }
            $exp['sector_deducido'] = $sectorMatch;

            // Agregar a la colección final
            $experienciasActualizadas[] = $exp;
        }

        return $experienciasActualizadas;
    }
}
