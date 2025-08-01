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
    protected $search;

    public function __construct($search = null)
    {
        $this->search = $search;
    }

    public function collection()
    {
        $query = PurchaseReturn::with(['purchaseBill','supplier','product','creator']);
        if (!empty($this->search)) {
            $query->where('purchase_bill_id', 'like', '%' . $this->search . '%')
                    ->orWhereHas('supplier', fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
                    ->orWhereHas('product', fn($q) => $q->where('name', 'like', '%' . $this->search . '%'));
        }

        return $query->get()->map(function ($purchaseReturn) {
            return [
                'Id'                    => $purchaseReturn->id,
                'purchase_bill_id'      => $purchaseReturn->purchase_bill_id,
                'supplier_name'         => $purchaseReturn->supplier->name,
                'product_name'          => $purchaseReturn->product->name ?? 'غير محدد',
                'quantity'              => $purchaseReturn->quantity,
                'return_amount'         => $purchaseReturn->return_amount,
                'reason'                => $purchaseReturn->reason,
                'refunded_in_cash'      => $purchaseReturn->refunded_in_cash? 'yes' :'no',
                'created_by'            => $purchaseReturn->created_by,
                'session_id'            => $purchaseReturn->session_id,
                'Created At'            => $purchaseReturn->created_at->format('Y-m-d'),
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
