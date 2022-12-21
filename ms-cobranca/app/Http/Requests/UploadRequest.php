<?php

namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\FormRequestFailedValidation;

class UploadRequest extends FormRequest
{

    use FormRequestFailedValidation;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'listDebt' => 'required|mimes:csv',
        ];

    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function extensionRules()
    {
        return [
            'csv' => 'required',
        ];

    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'listDebt.required' => 'Required send the file .csv for process.',
            'csv.required' => 'Required a file .csv for process.',
        ];
    }
}
