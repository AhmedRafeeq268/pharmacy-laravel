<?php

namespace App\Exports;

use App\Models\Debt;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DebtsExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $debts;

    public function __construct($debts)
    {
        $this->debts = $debts;
    }

    public function collection()
    {
        $totalDebt = $this->debts->sum('total_debt');
        $totalRemaining = $this->debts->sum('total_remaining');

        $data = $this->debts->map(function ($debt, $index) {
            return [
                $index + 1,
                $debt->customer->name ?? '-',
                $debt->customer->phone ?? '-',
                $debt->customer->address ?? '-',
                number_format($debt->total_debt, 2),
                number_format($debt->total_remaining, 2),
            ];
        });

        // إضافة صف الإجمالي
        $data->push([
            'الإجمالي', '', '', '',
            number_format($totalDebt, 2),
            number_format($totalRemaining, 2),
        ]);

        return $data;
    }

    public function headings(): array
    {
        return [
            'رقم',
            'اسم الزبون',
            'الهاتف',
            'العنوان',
            'إجمالي الديون',
            'المتبقي',
        ];
    }

    // تظليل صف الإجمالي
    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow(); // صف الإجمالي
        return [
            $lastRow => [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => [
                        'rgb' => 'FFFF99', // لون أصفر فاتح
                    ],
                ],
                'font' => [
                    'bold' => true,
                ],
            ],
        ];
    }
}
