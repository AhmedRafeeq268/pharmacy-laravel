<?php

namespace App\Exports;

use App\Models\PosBill;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class PosBillsExport implements FromCollection, WithHeadings, WithMapping, WithColumnFormatting, ShouldAutoSize
{
    protected $posBills;

    public function __construct($posBills)
    {
        $this->posBills = $posBills;
    }

    public function collection()
    {
        return $this->posBills;

    }

    public function map($posBill): array
    {
        return [
            $posBill->id,
            $posBill->customer?->name ?? 'not found',
            $posBill->employee?->name ?? 'not found',
            $posBill->total_amount,
            $posBill->discount,
            $posBill->net_amount,
            $posBill->payment_status,
            $posBill->created_at->format('Y-m-d'),
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Customer Name',
            'Employee Name',
            'Total Amount',
            'Discount',
            'Net Amount',
            'Payment Status',
            'Created At',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_NUMBER, // Total Amount
            'E' => NumberFormat::FORMAT_NUMBER, // Discount
            'F' => NumberFormat::FORMAT_NUMBER, // Net Amount
        ];
    }
}
