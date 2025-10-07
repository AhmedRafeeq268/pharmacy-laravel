<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePurchaseBillRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('edit-purchase-bill');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'supplier_id' => 'required|exists:suppliers,id',
            'total_amount' => 'required|numeric|min:0',
            'currancy_type' => 'required|string|max:10',
            'bill_number' => [
                        'required',
                        'integer',
                        Rule::unique('purchases_bills', 'bill_number')
                            ->ignore($this->route('bill')),
            ],
            'bill_date' => 'required|date',
            'employee_receipt' => 'required|string|max:191',
            'manufacturer' => 'required|string|max:191',
            'paid' => 'nullable|numeric|min:0',
            'remaining' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:paid,partial,unpaid',
        ];
    }

    public function attributes()
    {
        return [
            'supplier_id' => __('messages.suppliers.name'),
            'total_amount' => __('messages.bill.total_amount'),
            'currancy_type' => __('messages.bill.currency_type'),
            'bill_number' => __('messages.bill.bill_number'),
            'bill_date' => __('messages.bill.bill_date'),
            'employee_receipt' => __('messages.bill.receiving_employee'),
            'manufacturer' => __('messages.bill.manufacturer'),
            'paid' => __('messages.bill.status_paid'),
            'remaining' => __('messages.bill.remaining'),
            'status' => __('messages.bill.status'),

        ];
    }
}
