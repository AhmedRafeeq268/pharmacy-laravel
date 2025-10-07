<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreCodesTbRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('store-codesTb');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => ['nullable'],
            'father' => [$this->has('status') ? 'nullable' : 'required', 'numeric'],
            'desc_ar' => ['required'],
            'desc_en' => ['required'],
            'is_active' => ['required'],
            'is_editables' => ['required'],
        ];
    }
    public function attributes()
    {
        return [
            'status' => __('messages.codesTb.status'),
            'father' => __('messages.codesTb.parent_category'),
            'desc_ar' => __('messages.codesTb.description_ar'),
            'desc_en' => __('messages.codesTb.description_en'),
        ];
    }
}
