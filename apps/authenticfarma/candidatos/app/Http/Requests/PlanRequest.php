<?php

namespace App\Http\Requests;

use App\Http\Requests\BaseFormRequest;

class PlanRequest extends BaseFormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:200',
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0',
            'image_url' => 'nullable|url',
            'status' => 'required|in:active,inactive',
            'details' => 'array',
            'details.*.description' => 'required|string|max:1000',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'El nombre del plan es obligatorio.',
            'name.max' => 'El nombre del plan no debe exceder los 200 caracteres.',
            'description.max' => 'La descripción no debe exceder los 1000 caracteres.',
            'price.required' => 'El precio es obligatorio.',
            'price.numeric' => 'El precio debe ser un número.',
            'price.min' => 'El precio no puede ser negativo.',
            'image_url.url' => 'La URL de la imagen no es válida.',
            'status.required' => 'El estado es obligatorio.',
            'status.in' => 'El estado debe ser activo o inactivo.',
            'details.array' => 'Los detalles deben ser un arreglo.',
            'details.*.description.required' => 'Cada detalle debe tener una descripción.',
            'details.*.description.max' => 'Cada detalle no debe exceder los 1000 caracteres.',
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->has('price')) {
            $this->merge([
                'price' => str_replace('.', '', $this->input('price'))
            ]);
        }
    }
} 