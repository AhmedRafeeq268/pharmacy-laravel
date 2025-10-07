<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSupplierRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('edit-supplier');
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
            'phone' => ['required'],
            'email' => ['required'],
            'bank_account' => ['nullable', 'numeric'],
            'bank_name' => ['nullable', 'integer'],
            'wallet_phone' => ['nullable', 'numeric'],
            'wallet_type' => ['nullable', 'integer'],
        ];
    }

    public function attributes()
    {
        return [
            'name' => __('messages.suppliers.name'),
            'phone' => __('messages.suppliers.phone'),
            'email' => __('messages.suppliers.email'),
            'bank_account' => __('messages.suppliers.bank_account'),
            'bank_name' => __('messages.suppliers.bank_name'),
            'wallet_phone' => __('messages.suppliers.wallet_phone'),
            'wallet_type' => __('messages.suppliers.wallet_type'),
        ];
    }
}
