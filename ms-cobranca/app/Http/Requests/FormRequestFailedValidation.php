<?php

namespace App\Http\Requests;

use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

trait FormRequestFailedValidation
{
    public function failedValidation(Validator $validator)
    {
        $response = new JsonResponse([
            'statusCode' => 'INVALID_FIELDS',
            'msg' => 'Invalid fields.',
            'errors' => $validator->errors()
        ], 422);

        throw new ValidationException($validator, $response);
    }
}
