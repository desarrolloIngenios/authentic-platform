<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateJobRequest extends BaseFormRequest
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
            'nombre_cargo' => 'required|string|max:200',
            'empresa' => 'required|string|max:200',
            'descripcion_cargo' => 'required|string|max:1000',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after:fecha_inicio',
            'id_area' => 'required|integer|exists:area,id',
            'id_tipo_cargo' => 'required|integer|exists:tipo_cargo,id',
            'id_sector' => 'required|integer|exists:sector,id',
            'id_pais' => 'required|integer|exists:pais,id',
        ];
    }

    public function messages()
    {
        return [
            'nombre_cargo.required' => 'El nombre del cargo es obligatorio',
            'nombre_cargo.max' => 'El nombre del cargo no puede exceder los 200 caracteres',
            'empresa.required' => 'El nombre de la empresa es obligatorio',
            'empresa.max' => 'El nombre de la empresa no puede exceder los 200 caracteres',
            'descripcion_cargo.required' => 'La descripción del cargo es obligatoria',
            'descripcion_cargo.max' => 'La descripción del cargo no puede exceder los 1000 caracteres',
            'fecha_inicio.required' => 'La fecha de inicio es obligatoria',
            'fecha_inicio.date' => 'La fecha de inicio debe ser una fecha válida',
            'fecha_fin.date' => 'La fecha de fin debe ser una fecha válida',
            'fecha_fin.after' => 'La fecha de fin debe ser posterior a la fecha de inicio',
            'id_area.required' => 'El área es obligatoria',
            'id_area.exists' => 'El área seleccionada no es válida',
            'id_tipo_cargo.required' => 'El tipo de cargo es obligatorio',
            'id_tipo_cargo.exists' => 'El tipo de cargo seleccionado no es válido',
            'id_sector.required' => 'El sector es obligatorio',
            'id_sector.exists' => 'El sector seleccionado no es válido',
            'id_pais.required' => 'El país es obligatorio',
            'id_pais.exists' => 'El país seleccionado no es válido',
        ];
    }
}
