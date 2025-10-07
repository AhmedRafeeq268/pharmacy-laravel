<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows("store-product");
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required'],
            'barcode' => ['required'],
            'manufacture_company' => ['required'],
            'unit_price' => ['required'],
            'price_sell' => ['required'],
            'category_id' => ['required'],
        ];
    }

    public function attributes()
    {
        return [
            'name' => __('messages.product.product_name'),
            'barcode' => __('messages.product.barcode'),
            'manufacture_company' => __('messages.product.manufacturer'),
            'unit_price' => __('messages.product.unit_price'),
            'price_sell' => __('messages.product.price_sell'),
            'category_id' => __('messages.product.product_category'),

        ];
    }
}
