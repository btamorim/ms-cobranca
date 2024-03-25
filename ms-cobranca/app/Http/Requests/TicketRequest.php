<?php

namespace App\Http\Requests;


use Carbon\Carbon;
use App\DTO\TicketCheckoutDTO;
use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\FormRequestFailedValidation;

class TicketRequest extends FormRequest
{

    use FormRequestFailedValidation;

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'debtId' => 'required',
            'paidAt' => 'required',
            'paidAmount' => 'required|numeric',
            'paidBy' => 'required',
        ];
    }

    public function parseToDTO()
    {
        $data = $this->validate($this->rules());

        return new TicketCheckoutDTO(
            debtId: $data['debtId'],
            paidAmount: $data['paidAmount'],
            paidAt: Carbon::make($data['paidAt']),
            paidBy: $data['paidBy'],
        );
    }
    public function messages()
    {
        return [
            'debtId.required' => 'This :attribute field is required. Enter the paid billing ID.',
            'paidAt.required' => 'This :attribute field is required. Enter the billing payment date.',
            'paidAmount.required' => 'This :attribute field is required. Enter the payment amount.',
            'paidBy.required' => 'This :attribute field is required. Enter the name of the customer who made the payment.',
        ];
    }
}
