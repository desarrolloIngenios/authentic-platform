<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'descripcion_perfil' => 'required|string|max:1000',
            'sectores' => 'required|array|max:3',
            'sectores.*' => 'exists:sector,id',
            'areas' => 'required|array|max:3',
            'areas.*' => 'exists:area,id',
            'nombre_cargo' => 'required|string|max:255',
            'id_rango_salario' => 'required|exists:rango_salario,id',
            'id_tipo_trabajo' => 'required|exists:tipo_trabajo,id',
            'pregunta1' => 'required|boolean',
            'pregunta2' => 'required|boolean',
            'pregunta3' => 'required|boolean',
            'texto1' => 'required|string|max:1000',
            'texto2' => 'required|string|max:1000',
            'texto3' => 'required|string|max:1000',
            'texto4' => 'nullable|string|max:1000',
            'texto5' => 'nullable|string|max:1000',
            'texto6' => 'nullable|string|max:1000',
            'texto7' => 'nullable|string|max:1000',
            'is_buscando_ofertas' => 'required|boolean',
            'is_visible_reclutadores' => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'descripcion_perfil.required' => 'La descripción del perfil es obligatoria',
            'descripcion_perfil.max' => 'La descripción del perfil no puede exceder los 1000 caracteres',
            'texto1.max' => 'Los logros/proyectos no deben tener más de 1000 caracteres',
            'texto2.max' => 'El campo "¿Qué buscas de una organización?" no debe tener más de 1000 caracteres',
            'texto3.max' => 'El campo "¿Qué buscas de un líder?" no debe tener más de 1000 caracteres',
            'texto4.max' => 'El plan de desarrollo no debe tener más de 1000 caracteres',
            'texto5.max' => 'Las competencias comportamentales no deben tener más de 1000 caracteres',
            'texto6.max' => 'Las competencias tecnológicas no deben tener más de 1000 caracteres',
            'texto7.max' => 'Los talentos no deben tener más de 1000 caracteres',
            'sectores.required' => 'Debe seleccionar al menos un sector',
            'sectores.max' => 'No puede seleccionar más de 3 sectores',
            'sectores.*.exists' => 'Uno de los sectores seleccionados no es válido',
            'areas.required' => 'Debe seleccionar al menos un área',
            'areas.max' => 'No puede seleccionar más de 3 áreas',
            'areas.*.exists' => 'Una de las áreas seleccionadas no es válida',
            'nombre_cargo.required' => 'El cargo es obligatorio',
            'nombre_cargo.max' => 'El cargo no puede exceder los 255 caracteres',
            'id_rango_salario.required' => 'El rango salarial es obligatorio',
            'id_rango_salario.exists' => 'El rango salarial seleccionado no es válido',
            'id_tipo_trabajo.required' => 'La modalidad de trabajo es obligatoria',
            'id_tipo_trabajo.exists' => 'La modalidad de trabajo seleccionada no es válida',
            'pregunta1.required' => 'Debe responder si tiene permiso para trabajar en Colombia',
            'pregunta2.required' => 'Debe responder si tiene posibilidades de trasladarse dentro de Colombia',
            'pregunta3.required' => 'Debe responder si le gustaría trasladarse a otros países',
            'texto1.required' => 'Los logros/proyectos son obligatorios',
            'texto2.required' => 'Debe indicar qué busca de una organización',
            'texto3.required' => 'Debe indicar qué busca de un líder',
            'is_buscando_ofertas.required' => 'Debe indicar si está buscando ofertas laborales',
            'is_visible_reclutadores.required' => 'Debe indicar si quiere ser visible para reclutadores',
        ];
    }
}
