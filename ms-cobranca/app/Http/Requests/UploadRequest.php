<?php

namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
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
            'listDebt' => 'required|file|max:4194304',
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
            'listDebt.mimes' => 'The list debt must be a file of type: csv.',
            'listDebt.required' => 'Required send the file .csv for process.',
            'csv.required' => 'Required a file .csv for process.',
            'listDebt.max' => 'The maximum file size is 4GB.'
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (!$this->isValidCsvMimeType()) {
                $validator->errors()->add('listDebt', 'The list debt must be a file of type text/csv.');
            }
        });
    }

    protected function isValidCsvMimeType()
    {
        if(!$this->hasFile('listDebt')){
            return false;
        }

        $mimeType = $this->file('listDebt')->getClientMimeType();

        return $mimeType === 'text/csv';
    }
}
