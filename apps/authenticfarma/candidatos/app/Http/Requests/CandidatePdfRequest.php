<?php

namespace App\Http\Requests;

use App\Http\Requests\BaseFormRequest;

class CandidatePdfRequest extends  BaseFormRequest
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
            'pdf' => 'required|mimes:pdf|max:10240', 
        ];
    }
    public function messages()
    {
        return [
            'pdf.required' => 'El archivo es requerido.',
            'pdf.mimes' => 'El formato del archivo no es valido.',
            'pdf.max' => 'El archivo supera el peso maximo.',

        ];
    }
}
