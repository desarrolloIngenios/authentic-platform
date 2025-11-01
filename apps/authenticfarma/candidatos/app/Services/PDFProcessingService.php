<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PDFProcessingService
{
    public function processPdf($path)
    {

        $pdfParser = new \Smalot\PdfParser\Parser();
        $pdfContent = $pdfParser->parseFile($path);
        $content_pdf = $pdfContent->getText();
        if ($content_pdf == null) {
            $res = $this->sendOCR($path);
            $content_pdf = $res['ParsedResults'][0]['ParsedText'];
        } else {
        }



        $response = $this->sendToOpenAI($content_pdf);


        // Validar si la respuesta contiene error de cuota (429)
        if (isset($response['error']) && isset($response['error']['code']) && $response['error']['code'] == 429) {
            throw new \Exception('Has excedido la cuota de uso de la API de Gemini. Por favor, revisa tu plan y detalles de facturación.');
            toastr()->error('Has excedido la cuota de uso de la API de Gemini. Por favor, revisa tu plan y detalles de facturación.');
            return redirect()->back();
        }

        // Manejo de error si el modelo está sobrecargado
        if (isset($response['code']) && $response['code'] == 503) {
            throw new \Exception('El modelo de IA está sobrecargado. Por favor, intenta nuevamente más tarde.');
            toastr()->error("El modelo de IA está sobrecargado. Por favor, intenta nuevamente más tarde.");
            return redirect()->back();
        }

        // $jsonContent = $response['choices'][0]['message']['content'];
        $jsonContent = $response['candidates'][0]['content']['parts'][0]['text'] ?? null;

        // Limpiar marcadores de código markdown si existen
        if ($jsonContent) {
            $jsonContent = preg_replace('/^```json\s*/', '', $jsonContent);
            $jsonContent = preg_replace('/\s*```$/', '', $jsonContent);
            $jsonContent = trim($jsonContent);
        }

        // dd($jsonContent);

        $cvData = json_decode($jsonContent, true);

        // ...existing code...
        $data['nombreCompleto'] = $cvData['Nombres'];
        $data['apellidoCompleto'] = $cvData['Apellidos'];
        $data['genero'] = $cvData['Genero'];
        $data['experienciaLaboral'] = $cvData['Experiencia laboral'];
        $data['habilidades'] = $cvData['Habilidades'];
        $data['educacion'] = $cvData['Educacion'];
        $data['educacionAdicional'] = $cvData['Educacion adicional'] ?? null;
        $data['contacto'] = $cvData['Contacto'];
        $data['idiomas'] = $cvData['Idiomas'];
        $data['nivelIdioma'] = $cvData['Nivel de idioma'];
        $data['certificaciones'] = $cvData['Certificaciones'];
        $data['tipoDocumento'] = $cvData['tipo documento de identidad'] ?? null;
        $data['documentoIdentidad'] = $cvData['documento de identidad'] ?? null;
        $data['direccion'] = $cvData['Dirección'] ?? null;
        $data['fechaNacimiento'] = $cvData['Fecha de nacimiento'] ?? null;

        return $data;
    }

    protected function sendToOpenAI($pdfText)
    {
        $apiKey = config('IA.openIA.token');

        // Determinar el token según el ambiente
        if (app()->environment('production')) {
            $token = config('IA.gemini.prod_token', 'AIzaSyDXcJ1nZoEe2Ilkt_RAPAcgausCQjjS0To');
        } else {
            // $token = config('IA.gemini.dev_token', 'AIzaSyAumhK-8m_t_zqVwJz8UndYjesVd_mRyPo');
            $token = 'AIzaSyDXcJ1nZoEe2Ilkt_RAPAcgausCQjjS0To';
        }

        $model = 'gemini-2.0-flash-lite';
        //$model = 'gemini-2.5-flash';

        $prompt = "
            Extrae la siguiente información del siguiente texto de un CV:
            1. **Nombres**: Nombres del candidato.
            2. **Apellidos**: Apellidos del candidato.
            3. **Genero** : Extrae el género del candidato. Masculino, Femenino o Otros.
            4. **Experiencia laboral**: Busca secciones que comiencen con 'Experiencia' o 'Historial laboral'. Extrae el nombre de la empresa, el puesto, la fecha de inicio(fecha_inicio) y fecha fin(fecha_fin) en este formarto  año-mes-dia ejemplo'2013-11-22' y una breve descripción.
            5. **Habilidades**: Busca una sección titulada 'Habilidades' o 'Competencias'. Extrae una lista de habilidades.
            6. **Educacion**: Busca una sección titulada 'Educación' , 'Formación académica', 'FORMACIÓN ACADEMICA'. Extrae el nombre de la institución, el título, el nivel de educación(Primaria, Secundaria, Bachiller, Tecnica, Tecnológica, Profesional, Especialización, Maestría, Doctorado)  y la fecha de inicio(fecha_inicio) y fecha fin(fecha_fin) en este formarto  año-mes-dia ejemplo'2013-11-22'. Si fecha_fin llega a ser 'actual' deja el campo como nulo.
            7. **Educacion adicional**: Busca una sección titulada 'Educación adicional' o 'Cursos adicionales' o 'Estudios complementarios'. Extrae el nombre del curso, la institución y la fecha de inicio(fecha_inicio) y fecha fin(fecha_fin) en este formarto  año-mes-dia ejemplo'2013-11-22'.
            8. **Contacto**: Busca un correo electrónico y un número de teléfono en el texto.
            9. **Idiomas**: Busca una sección titulada 'Idiomas' o 'Idiomas que hablo'. Extrae una lista de idiomas.
            10. **Nivel de idioma**: Busca una sección titulada 'Nivel de idioma' o 'Competencia lingüística'. Extrae el nivel de competencia para cada idioma y clasificalo como A1, A2, B1, B2, C1 o C2.
            11. **Certificaciones** : Busca una sección titulada 'Certificaciones' o 'Cursos'. Extrae una lista de certificaciones.
            12. **tipo documento de identidad** : Busca una sección titulada 'Tipo de documento' o 'DNI'. Extrae el tipo de documento de identidad.
            13. **documento de identidad** : Busca una sección titulada 'Documento de identidad' o 'DNI'. Extrae el número de documento de identidad.
            14. **Dirección** : Busca una sección titulada “Dirección” o “Domicilio”. Extrae los siguientes datos: dirección, ciudad de residencia, departamento de residencia, país de residencia, y la ciudad, departamento y país de nacimiento si están disponibles. Si solo se encuentra la ciudad, infiere automáticamente el departamento y el país correspondientes. Si se encuentra el departamento, infiere también el país. Si no hay datos de nacimiento, usa los mismos datos de residencia como respaldo . Si la dirección está vacía, usa el nombre del municipio como valor por defecto. Corrige errores ortográficos comunes en los nombres de ciudades, departamentos o países si es necesario antes de procesarlos.
            15. **Fecha de nacimiento** : Busca una sección titulada 'Fecha de nacimiento' o 'Nacimiento'. Extrae la fecha de nacimiento.        

            Formato de respuesta: JSON sin saltos de linea ni `.

            Texto del CV:" . $pdfText;
        // dd($prompt);
        // $response = Http::withHeaders([
        //     'Authorization' => 'Bearer '.$apiKey,
        //     'Content-Type' => 'application/json',
        // ])->post('https://api.openai.com/v1/chat/completions', [
        //     'model' => 'gpt-4o-mini', 
        //     'store' => true,
        //     'messages' => [
        //         [
        //             'role' => 'user',
        //             'content' => $prompt,
        //         ],
        //     ],
        // ]);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])
            ->timeout(60)
            ->post("https://generativelanguage.googleapis.com/v1beta/models/$model:generateContent?key=$token", [
                'contents' => [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ]);

        $json = $response->json();

        // dd($json);
        // Manejo robusto de error de API

        return $json;
    }

    public function sendOCR($path)
    {
        $file = new \CURLFile($path);

        $apiKey = "K86921164088957";
        $response = Http::withHeaders([
            'apikey' => $apiKey,
        ])
            ->timeout(120)
            ->connectTimeout(120)
            ->attach('file', fopen($path, 'r'), 'document.pdf')
            ->post('https://api.ocr.space/parse/image', [
                'language' => 'spa'
            ]);
        return $response->json();
    }
}
