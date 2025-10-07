<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;

class StoreParchaseReturnRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('store-purchase-return');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'purchase_bill_id' => 'required|exists:purchases_bills,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:0',
            'items.*.return_amount' => 'required|numeric|min:0',
            'items.*.reason' => 'nullable|string|max:255',
            'items.*.refunded_in_cash' => 'required|boolean',
        ];
    }

    public function attributes()
    {
        return [
            'purchase_bill_id' => __('messages.purchaseReturns.purchase_bill_id'),
            'supplier_id' => __('messages.purchaseReturns.supplier_name'),
            'product_id' => __('messages.purchaseReturns.product_name'),
            'quantity' => __('messages.purchaseReturns.quantity'),
            'return_amount' => __('messages.purchaseReturns.return_amount'),
            'reason' => __('messages.purchaseReturns.reason'),
            'refunded_in_cash' => __('messages.purchaseReturns.refunded_in_cash'),
        ];
    }
}
