<?php

namespace App\Exports;

use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SuppliersExport implements FromCollection,WithHeadings,ShouldAutoSize
{

    protected $search;

    public function __construct($search = null)
    {
        $this->search = $search;
    }

    public function collection()
    {
        $query = Supplier::select('id','name','phone','email','created_at');

        if (!empty($this->search)) {
            $query->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('phone', 'like','%' . $this->search . '%');;
        }

        return $query->get()->map(function ($supplier) {
            return [
                'Id'               => $supplier->id,
                'Name'             => $supplier->name,
                'Phone'            => $supplier->phone,
                'Email'            => $supplier->email,
                'Created At'       => $supplier->created_at->format('Y-m-d'),
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
