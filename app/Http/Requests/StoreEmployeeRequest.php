<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('store-employee');
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
            'id_card' => ['required'],
            'bank_account' => ['nullable', 'numeric'],
            'bank_name' => ['nullable', 'integer'],
            'wallet_phone' => ['nullable', 'numeric'],
            'wallet_type' => ['nullable', 'integer'],
        ];
    }

    public function attributes()
    {
        return [
            'name' => __('messages.employee.employee_name'),
            'phone' => __('messages.employee.phone_number'),
            'email' => __('messages.employee.email'),
            'id_card' => __('messages.employee.id_card'),
            'bank_account' => __('messages.employee.bank_account_number'),
            'bank_name' => __('messages.employee.bank_name'),
            'wallet_phone' => __('messages.employee.wallet_phone_number'),
            'wallet_type' => __('messages.employee.wallet_type'),
        ];
    }
}
