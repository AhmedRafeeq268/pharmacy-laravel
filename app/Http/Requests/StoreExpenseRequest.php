<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;

class StoreExpenseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('store-expense');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => 'required|in:salary,rent,bills,other',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
        ];
    }

    public function attributes()
    {
        return [
            'type' => __('messages.expenses.expense_type'),
            'description' => __('messages.expenses.description'),
            'amount' => __('messages.expenses.amount'),
            'expense_date' => __('messages.expenses.expense_date'),
        ];
    }
}
