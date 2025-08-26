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
        protected $search;


    public function __construct($search = null)
    {
        $this->search = $search;
    }

    public function collection()
    {
        $query = SalesReturn::with(['customer','details.product','bill']);

        if (!empty($this->search)) {
            $query->where('pos_bill_id', 'like', '%' . $this->search . '%')
                ->orWhere('refund_method', 'like', '%' . $this->search . '%')
                ->orWhereHas('customer', fn($q) => $q->where('name', 'like', '%' . $this->search . '%'));
        }

        return $query->get()->flatMap(function ($salesReturn) {
            return $salesReturn->details->map(function ($detail) use ($salesReturn) {
                return [
                    'id'              => $salesReturn->id,
                    'pos_bill_number' => $salesReturn->pos_bill_id,
                    'customer_name'   => optional($salesReturn->customer)->name,
                    'total'           => $salesReturn->total,
                    'refund_method'   => $salesReturn->refund_method,
                    'product'         => optional($detail->product)->name,
                    'price'           => $detail->price,
                    'quantity'        => $detail->quantity,
                    'subtotal'        => $detail->subtotal,
                    'Created At'      => $salesReturn->created_at->format('Y-m-d'),
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
