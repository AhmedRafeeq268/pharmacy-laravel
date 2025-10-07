<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;

class StoreSalesReturnRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('store-customer-return');
    }

    public function rules(): array
    {
        return [
            'refund_method'       => 'required|in:cash,debt',
            'items'               => 'required|array|min:1',
            'items.*.product_id'  => 'required|exists:products,id',
            'items.*.price'       => 'required|numeric|min:0',
            'items.*.quantity'    => 'required|integer|min:1',
            'bill_id'             => 'nullable|exists:pos_bills,id',
        ];
    }

    public function attributes()
    {
        return [
            'refund_method'  => __('messages.salesReturn.refund_method'),
            'items.*.product_id' => __('messages.salesReturn.product_name'),
            'items.*.price'      => __('messages.salesReturn.price'),
            'items.*.quantity'   => __('messages.salesReturn.quantity'),
            'bill_id'        => __('messages.salesReturn.pos_bill_number'),
        ];
    }
}
