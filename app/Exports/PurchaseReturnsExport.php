<?php

namespace App\Exports;

use App\Models\PurchaseReturn;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PurchaseReturnsExport implements FromCollection , WithHeadings,ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $purchaseReturns;

    public function __construct($purchaseReturns)
    {
        $this->purchaseReturns = $purchaseReturns;
    }

    public function collection()
    {
        return $this->purchaseReturns->map(function ($purchaseReturn) {
            return [
                 $purchaseReturn->id,
                 $purchaseReturn->purchase_bill_id,
                 $purchaseReturn->supplier->name,
                 $purchaseReturn->product->name ?? 'غير محدد',
                 $purchaseReturn->quantity,
                 $purchaseReturn->return_amount,
                 $purchaseReturn->reason,
                 $purchaseReturn->refunded_in_cash? 'yes' :'no',
                 $purchaseReturn->creator ? $purchaseReturn->creator->name : '-',
                 $purchaseReturn->session_id,
                 $purchaseReturn->created_at->format('Y-m-d'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'id',
            'purchase_bill_id',
            'supplier_name',
            'product_name',
            'quantity',
            'return_amount',
            'reason',
            'refunded_in_cash',
            'created_by',
            'session_id',
            'created_at',
        ];
    }
}
