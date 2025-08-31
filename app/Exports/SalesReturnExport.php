<?php

namespace App\Exports;

use App\Models\SalesReturn;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SalesReturnExport implements FromCollection ,WithHeadings, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
        protected $salesReturns;


    public function __construct($salesReturns)
    {
        $this->salesReturns = $salesReturns;
    }

    public function collection()
    {
        return $this->salesReturns->flatMap(function ($salesReturn) {
            return $salesReturn->details->map(function ($detail) use ($salesReturn) {
                return [
                     $salesReturn->id,
                     $salesReturn->pos_bill_id,
                     optional($salesReturn->customer)->name,
                     $salesReturn->total,
                     $salesReturn->refund_method,
                     optional($detail->product)->name,
                     $detail->price,
                     $detail->quantity,
                     $detail->subtotal,
                     $salesReturn->created_at->format('Y-m-d'),
                ];
            });
        });
    }


    public function headings(): array
    {
        return [
            'id',
            'pos_bill_number',
            'customer_name',
            'total',
            'refund_method',
            'product',
            'price',
            'quantity',
            'subtotal',
            'created_at',
        ];
    }
}
