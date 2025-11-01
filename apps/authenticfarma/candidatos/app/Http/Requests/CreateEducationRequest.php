<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateEducationRequest extends  BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
           'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'institucion' => 'nullable|string|max:255',
            'titulo' => 'nullable|string|max:255',
            'nivel_educacion' => 'required|integer|exists:nivel_educacion,id',
            'pais' => 'required|integer|exists:pais,id',
        ];
    }
    public function messages()
    {
        return [
            'fecha_inicio.date' => 'La fecha de inicio no es válida.',
            'fecha_fin.date' => 'La fecha de finalización no es válida.',
            'fecha_fin.after_or_equal' => 'La fecha de finalización debe ser igual o posterior a la fecha de inicio.',
            'institucion.max' => 'La institución no debe superar los 255 caracteres.',
            'titulo.max' => 'El título no debe superar los 255 caracteres.',
            'nivel_educacion.required' => 'El nivel de educación es obligatorio.',
            'nivel_educacion.exists' => 'El nivel de educación seleccionado no es válido.',
            'pais.required' => 'El país es obligatorio.',
            'pais.exists' => 'El país seleccionado no es válido.',
        ];
    }
}
