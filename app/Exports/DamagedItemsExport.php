<?php

namespace App\Exports;

use App\Models\DamagedItem;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class DamagedItemsExport implements FromCollection ,WithHeadings, ShouldAutoSize
{
    protected $damagedItems;

    public function __construct($damagedItems)
    {
        $this->damagedItems = $damagedItems;
    }

    public function collection()
    {
        return $this->damagedItems->map(function ($damagedItem) {
            return [
                 $damagedItem->id,
                 $damagedItem->product->name,
                 $damagedItem->quantity,
                 $damagedItem->reason,
                 $damagedItem->user->name,
                 $damagedItem->created_at->format('Y-m-d'),
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

