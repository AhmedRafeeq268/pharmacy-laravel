<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreDamagedItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows("store-damaged-item");
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string|max:255',
        ];
    }

    public function attributes()
    {
        return [
            'product_id' => __('messages.damaged.prodact_name'),
            'quantity' => __('messages.damaged.damaged_quantity'),
            'reason' => __('messages.damaged.damage_reason'),
        ];
    }
}
