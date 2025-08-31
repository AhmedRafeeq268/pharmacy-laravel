<?php

namespace App\Exports;

use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SuppliersExport implements FromCollection,WithHeadings,ShouldAutoSize
{

    protected $suppliers;

    public function __construct($suppliers)
    {
        $this->suppliers = $suppliers;
    }

    public function collection()
    {
        return $this->suppliers->map(function ($supplier) {
            return [
                 $supplier->id,
                 $supplier->name,
                 $supplier->phone,
                 $supplier->email,
                 $supplier->created_at->format('Y-m-d'),
            ];
        });

    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Phone',
            'Email',
            'Created At',
        ];
    }
}
