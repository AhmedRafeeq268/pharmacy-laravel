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
    protected $search;


    public function __construct($search = null)
    {
        $this->search = $search;
    }

    public function collection()
    {
        $query = Expense::with(['user']);

        if (!empty($this->search)) {
            $query->where('type', 'like', '%' . $this->search . '%')
                ->orWhere('description', 'like', '%' . $this->search . '%')
                ->orWhere('amount', 'like', '%' . $this->search . '%')
                ->orWhereHas('user', fn($q) => $q->where('name', 'like', '%' . $this->search . '%'));
        }

        return $query->get()->map(function ($expense) {
            return [
                'Id'                    => $expense->id,
                'type'                  => $expense->type,
                'description'           => $expense->description,
                'amount'                => $expense->amount ?? 'غير محدد',
                'expense_date'          => $expense->expense_date,
                'created_by'            => $expense->user->name,
                'Created At'            => $expense->created_at->format('Y-m-d'),
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
