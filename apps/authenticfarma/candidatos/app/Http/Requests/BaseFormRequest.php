<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class BaseFormRequest extends FormRequest
{
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();

        foreach ($errors->all() as $message) {
            toastr()->error($message); 
        }

        throw new HttpResponseException(
            redirect()->back()->withInput() 
        );
    }
}
