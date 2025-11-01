<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateLanguageRequest extends  BaseFormRequest
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
            'certificado' => 'nullable|string|max:255',
            'detalle' => 'nullable|string|max:500',
            'id_idioma' => 'required|integer|exists:idioma,id',
            'id_nivel_idioma' => 'required|integer|exists:nivel_idioma,id',
        ];
    }

    public function messages(): array
    {
        return [
            'certificado.string' => 'El certificado debe ser un texto válido.',
            'certificado.max' => 'El certificado no debe superar los 255 caracteres.',

            'detalle.string' => 'El detalle debe ser un texto válido.',
            'detalle.max' => 'El detalle no debe superar los 500 caracteres.',

            'id_idioma.required' => 'El idioma es obligatorio.',
            'id_idioma.integer' => 'El idioma debe ser un número válido.',
            'id_idioma.exists' => 'El idioma seleccionado no es válido.',

            'id_nivel_idioma.required' => 'El nivel del idioma es obligatorio.',
            'id_nivel_idioma.integer' => 'El nivel del idioma debe ser un número válido.',
            'id_nivel_idioma.exists' => 'El nivel del idioma seleccionado no es válido.',
        ];
    }

}
