<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;

class UpdateParchaseReturnRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('edit-purchase-return');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'quantity' => 'required|integer|min:1',
            'return_amount' => 'required|numeric|min:0',
            'reason' => 'nullable|string|max:255',
            'refunded_in_cash' => 'required|boolean',
        ];
    }

    public function attributes()
    {
        return [
            'quantity' => __('messages.purchaseReturns.quantity'),
            'return_amount' => __('messages.purchaseReturns.return_amount'),
            'reason' => __('messages.purchaseReturns.reason'),
            'refunded_in_cash' => __('messages.purchaseReturns.refunded_in_cash'),
        ];
    }
}
