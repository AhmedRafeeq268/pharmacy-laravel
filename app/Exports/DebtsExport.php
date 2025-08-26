<?php

namespace App\Exports;

use App\Models\Debt;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class DebtsExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $search;

    public function __construct($search = null)
    {
        $this->search = $search;
    }

    public function collection()
    {
        // جلب مجموع الديون لكل زبون
        $debts = Debt::select('customer_id',
                                DB::raw('SUM(total_amount) as total_debt'),
                                DB::raw('SUM(remaining_amount) as total_remaining')
            )
            ->with('customer')
            ->where('status', 'open')
            ->groupBy('customer_id');

        // إذا فيه كلمة بحث
        if (!empty($this->search)) {
            $debts->whereHas('customer', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            });
        }

        $debts = $debts->get();

        // حساب المجموع النهائي لكل الديون
        $totalDebt = $debts->sum('total_debt');
        $totalRemaining = $debts->sum('total_remaining');

        // تحويل البيانات إلى Collection جاهزة للتصدير
        $data = $debts->map(function ($debt) {
            return [
                $debt->id,
                $debt->customer->name,
                $debt->customer->phone,
                $debt->customer->address,
                number_format($debt->total_debt, 2),
                number_format($debt->total_remaining, 2),
            ];
        });

        // إضافة صف الإجمالي
        // $data->push([
        //     'الإجمالي', '', '', number_format($totalDebt, 2), number_format($totalRemaining, 2)
        // ]);

        return $data;
    }

    public function headings(): array
    {
        return [
            'Id',
            'Customer Name',
            'Phone',
            'Address',
            'Total Debts',
            'Total Remaining',
        ];
    }
}
