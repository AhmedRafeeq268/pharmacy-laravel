<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreCustomerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('store-customer');
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
            'address' => ['required'],
            'id_card' => ['required'],
            'address_details' => ['required'],
            'status_cd' => ['required']
        ];
    }
    public function attributes()
    {
        return [
            'name' => __('messages.customer.name'),
            'phone' => __('messages.customer.phone'),
            'email' => __('messages.customer.email'),
            'address' => __('messages.customer.address'),
            'id_card' => __('messages.customer.id_card'),
            'address_details' => __('messages.customer.address_details'),
            'status_cd' => __('messages.customer.status'),
        ];
    }

}
