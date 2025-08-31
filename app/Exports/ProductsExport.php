<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ProductsExport implements FromCollection, WithHeadings,ShouldAutoSize
{
    protected $products;

    public function __construct($products)
    {
        $this->products = $products;
    }

    public function collection()
    {
        return $this->products->map(function ($product) {
            return [
                 $product->id,
                 $product->name,
                 $product->manufacture_company,
                 $product->productCategory->name ?? 'غير محدد',
                 $product->unit_price,
                 $product->barcode,
                 $product->created_at->format('Y-m-d'),
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
