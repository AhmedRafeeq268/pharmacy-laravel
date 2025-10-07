<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePurchaseBillDetailsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('edit-purchase-bill-details');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "product_id" => 'required|exists:products,id',
            "product_data" => 'required|string|max:255',
            "quantity" => 'required|integer|min:1',
            "cost" => 'required|numeric|min:0',
            "total" => 'required|numeric|min:0',
            "discount" => 'nullable|numeric|min:0',
            "product_category" => 'required|string|max:255',
            "billId" => 'required|exists:purchases_bills,id',
        ];
    }

    public function attributes()
    {
        return [
            'product_id' => __('messages.billDetails.product_id'),
            'product_data' => __('messages.billDetails.product_data'),
            'quantity' => __('messages.billDetails.quantity'),
            'cost' => __('messages.billDetails.cost'),
            'total' => __('messages.billDetails.total'),
            'discount' => __('messages.billDetails.discount'),
            'product_category' => __('messages.billDetails.product_category'),
            'billId' => __('messages.billDetails.bill_id'),

        ];
    }
}
