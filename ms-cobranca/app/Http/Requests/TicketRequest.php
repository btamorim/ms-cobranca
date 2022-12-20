<?php

namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\FormRequestFailedValidation;

class TicketRequest extends FormRequest
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
            'debtId' => 'required',
            'paidAt' => 'required',
            'paidAmount' => 'required|numeric',
            'paidBy' => 'required',
        ];
    }

    public function createTicketRules()
    {
        return [
            'debtId' => 'required|numeric',
            'costumerId' => 'required|numeric',
            'governmentId' => 'required|string',
            'amount' => 'required|numeric',
            'debtDueDate' => 'required|date'

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
            'debtId.required' => 'This :attribute ield is required. Enter the paid billing ID.',
            'paidAt.required' => 'This :attribute ield is required. Enter the billing payment date.',
            'paidAmount.required' => 'This :attribute ield is required. Enter the payment amount.',
            'paidBy.required' => 'This :attribute ield is required. Enter the name of the customer who made the payment.',
            'governmentId' => 'This :attribute ield is required. Enter the customers CPF.',
            'amount' => 'This :attribute ield is required. Enter the ticket amount correctly.',
            'debtDueDate' => 'This :attribute ield is required. Enter the correct expiration date.',
            'costumerId' => 'This :attribute ield is required. Enter the customer code.'
        ];
    }
}
