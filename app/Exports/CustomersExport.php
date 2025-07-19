<?php
namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class CustomersExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $search;

    public function __construct($search = null)
    {
        $this->search = $search;
    }

    public function collection()
    {
        $query = Customer::select('id','name','phone','email','address','id_card','address_details','created_at');

        if (!empty($this->search)) {
            $query->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('id_card', 'like','%' . $this->search . '%')
                  ->orWhere('phone', 'like','%' . $this->search . '%');
        }

        return $query->get()->map(function ($customer) {
            return [
                'Id'               => $customer->id,
                'Name'             => $customer->name,
                'Phone'            => $customer->phone,
                'Email'            => $customer->email,
                'Address'          => $customer->address,
                'Id_card'          => $customer->id_card,
                'Address_details'  => $customer->address_details,
                'Created At'       => $customer->created_at->format('Y-m-d'),
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
            'Address',
            'Id_card',
            'Address_details',
            'Created At',
        ];
    }
}
