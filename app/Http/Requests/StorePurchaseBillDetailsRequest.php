<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseBillDetailsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('store-purchase-bill-details');
    }

    public function rules(): array
    {
        return [
            "billId" => 'required|integer|exists:purchases_bills,id',

            "product_id"       => 'required|array',
            "product_id.*"     => 'required|integer|exists:products,id',

            "quantity"         => 'required|array',
            "quantity.*"       => 'required|integer|min:1',

            "cost"             => 'required|array',
            "cost.*"           => 'required|numeric|min:0',

            "discount"         => 'nullable|array',
            "discount.*"       => 'nullable|numeric|min:0',

            // "product_category" => 'required|array',
            // "product_category.*" => 'required|string|max:255',

            "production_date"        => 'required|array',
            "production_date.*"      => 'required|date',

            "exp_date"         => 'required|array',
            "exp_date.*"       => 'required|date', // لاحقاً يمكن التحقق أنها بعد أو مساوية للـ prod_date

            "manufacture"      => 'required|array',
            "manufacture.*"    => 'required|string|max:255',
        ];
    }


    public function attributes()
    {
        return [
            'product_id'       => __('messages.billDetails.product_id'),
            'quantity'         => __('messages.billDetails.quantity'),
            'cost'             => __('messages.billDetails.cost'),
            'total'            => __('messages.billDetails.total'),
            'discount'         => __('messages.billDetails.discount'),
            // 'product_category' => __('messages.billDetails.product_category'),
            'billId'           => __('messages.billDetails.bill_id'),
            'production_date'        => __('messages.billDetails.product_data'),
            'exp_date'         => 'تاريخ الانتهاء',
            'manufacture'      => 'المصنع',
        ];
    }


}
