<?php

namespace App\Exports;

use App\Models\Expense;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ExpensesExport implements FromCollection , WithHeadings,ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $expenses;


    public function __construct($expenses)
    {
        $this->expenses = $expenses;
    }

    public function collection()
    {
        return $this->expenses->map(function ($expense) {
            return [
                 $expense->id,
                 $expense->type,
                 $expense->description,
                 $expense->amount ?? 'غير محدد',
                 $expense->expense_date,
                 $expense->user->name,
                 $expense->created_at->format('Y-m-d'),
            ];
        });
    }


    public function headings(): array
    {
        return [
            'id',
            'type',
            'description',
            'amount',
            'expense_date',
            'created_by',
            'created_at',
        ];
    }
}
