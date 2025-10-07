<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;

class StorePosBillRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('store-pos-bill');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'barcode'  => ['required'],
            'quantity' => ['required', 'numeric', 'min:1'],
            'discount' => ['nullable', 'numeric', 'min:0'],

        ];
    }

    public function attributes()
    {
        return [
            'barcode' => __('messages.pos.barcode'),
            'quantity' => __('messages.pos.quantity'),
            'discount' => __('messages.pos.discount'),

        ];
    }
}
