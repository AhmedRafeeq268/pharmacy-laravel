<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCodesTbRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('edit-codesTb');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'main_cd' => ['required'],
            'sub_cd' => ['required'],
            'desc_ar' => ['required'],
            'desc_en' => ['required'],
            'details' => ['required'],
            'is_active' => ['required'],
            'is_editables' => ['required'],
        ];
    }
    public function attributes()
    {
        return [
            'main_cd' => __('messages.codesTb.main_cd'),
            'sub_cd' => __('messages.codesTb.sub_cd'),
            'desc_ar' => __('messages.codesTb.description_ar'),
            'desc_en' => __('messages.codesTb.description_en'),
            'details' => __('messages.codesTb.details'),
            'is_active' => __('messages.codesTb.is_active'),
            'is_editables' => __('messages.codesTb.editable'),
        ];
    }
}
