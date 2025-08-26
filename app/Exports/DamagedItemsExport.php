<?php

namespace App\Exports;

use App\Models\DamagedItem;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class DamagedItemsExport implements FromCollection ,WithHeadings, ShouldAutoSize
{
    protected $search;

    public function __construct($search = null)
    {
        $this->search = $search;
    }

    public function collection()
    {
        $query = DamagedItem::with(['product']);
        if (!empty($this->search)) {
            $query->where('quantity', 'like', '%' . $this->search . '%')
                  ->orWhereHas('product', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                });
        }

        return $query->get()->map(function ($damagedItem) {
            return [
                'Id'               => $damagedItem->id,
                'Product Name'     => $damagedItem->product->name,
                'Quantity'         => $damagedItem->quantity,
                'Reason'           => $damagedItem->reason,
                'Reported By'      => $damagedItem->user->name,
                'Created At'       => $damagedItem->created_at->format('Y-m-d'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Id',
            'Product Name',
            'Quantity',
            'Reason',
            'Reported By',
            'Created At',
        ];
    }
}

