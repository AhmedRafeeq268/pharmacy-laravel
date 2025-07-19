<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ProductsExport implements FromCollection, WithHeadings,ShouldAutoSize
{
    protected $search;

    public function __construct($search = null)
    {
        $this->search = $search;
    }

    public function collection()
    {
        $query = Product::with(['productCategory']);
        if (!empty($this->search)) {
            $query->where('name', 'like', '%' . $this->search . '%')
                  ->orWhereHas('productCategory', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                });
        }

        return $query->get()->map(function ($product) {
            return [
                'Id'                   => $product->id,
                'Name'                 => $product->name,
                'manufacture_company'  => $product->manufacture_company,
                'productCategory'      => $product->productCategory->name ?? 'غير محدد',
                'unit_price'           => $product->unit_price,
                'barcode'              => $product->barcode,
                'Created At'           => $product->created_at->format('Y-m-d'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'id',
            'name',
            'manufacture_company',
            'productCategory',
            'unit_price',
            'barcode',
            'created_at',
        ];
    }
}
