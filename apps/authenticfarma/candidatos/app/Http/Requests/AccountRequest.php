<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AccountRequest extends  BaseFormRequest
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
            'nombres' => 'required|string|min:2|max:100',
            'apellidos' => 'required|string|min:2|max:100',
            'genero_id' => 'required|exists:genero,id',
            'tipo_documento_id' => 'required|exists:tipo_documento,id',
            'numero_documento' => 'required|string|min:5|max:20',
            'fecha_nacimiento' => 'required|date|before:today',
            'telefono' => 'required|string|min:7|max:15',
            'otro_telefono' => 'nullable|string|min:7|max:15',
            'email' => 'required|email|max:255',
            'pais_nacimiento_id' => 'required|exists:pais,id',
            'departamento_nacimiento_id' => 'required|exists:departamento,id',
            'ciudad_nacimiento_id' => 'required|exists:ciudad,id',
            'pais_residencia_id' => 'required|exists:pais,id',
            'departamento_residencia_id' => 'required|exists:departamento,id',
            'ciudad_residencia_id' => 'required|exists:ciudad,id'
        ];
    }

    public function messages()
    {
        return [
            'required' => 'El campo :attribute es obligatorio.',
            'string' => 'El campo :attribute debe ser texto.',
            'email' => 'El campo :attribute debe ser una dirección de correo válida.',
            'min' => 'El campo :attribute debe tener al menos :min caracteres.',
            'max' => 'El campo :attribute no debe exceder :max caracteres.',
            'date' => 'El campo :attribute debe ser una fecha válida.',
            'before' => 'La :attribute debe ser una fecha anterior a hoy.',
            'exists' => 'El valor seleccionado para :attribute no es válido.',
            
            'nombres.required' => 'Por favor ingrese sus nombres.',
            'apellidos.required' => 'Por favor ingrese sus apellidos.',
            'genero_id.required' => 'Por favor seleccione su género.',
            'tipo_documento_id.required' => 'Por favor seleccione su tipo de documento.',
            'numero_documento.required' => 'Por favor ingrese su número de documento.',
            'fecha_nacimiento.required' => 'Por favor ingrese su fecha de nacimiento.',
            'telefono.required' => 'Por favor ingrese su número de teléfono.',
            'email.required' => 'Por favor ingrese su correo electrónico.',
            'email.email' => 'Por favor ingrese un correo electrónico válido.',
            
            'pais_nacimiento_id.required' => 'Por favor seleccione su país de nacimiento.',
            'departamento_nacimiento_id.required' => 'Por favor seleccione su departamento de nacimiento.',
            'ciudad_nacimiento_id.required' => 'Por favor seleccione su ciudad de nacimiento.',
            
            'pais_residencia_id.required' => 'Por favor seleccione su país de residencia.',
            'departamento_residencia_id.required' => 'Por favor seleccione su departamento de residencia.',
            'ciudad_residencia_id.required' => 'Por favor seleccione su ciudad de residencia.',
        ];
    }

    public function attributes()
    {
        return [
            'nombres' => 'nombres',
            'apellidos' => 'apellidos',
            'genero_id' => 'género',
            'tipo_documento_id' => 'tipo de documento',
            'numero_documento' => 'número de documento',
            'fecha_nacimiento' => 'fecha de nacimiento',
            'telefono' => 'teléfono',
            'otro_telefono' => 'teléfono alternativo',
            'email' => 'correo electrónico',
            'pais_nacimiento_id' => 'país de nacimiento',
            'departamento_nacimiento_id' => 'departamento de nacimiento',
            'ciudad_nacimiento_id' => 'ciudad de nacimiento',
            'pais_residencia_id' => 'país de residencia',
            'departamento_residencia_id' => 'departamento de residencia',
            'ciudad_residencia_id' => 'ciudad de residencia'
        ];
    }
}
