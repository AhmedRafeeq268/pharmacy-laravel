<?php
namespace App\Exports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class EmployeesExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $search;

    public function __construct($search = null)
    {
        $this->search = $search;
    }

    public function collection()
    {
        $query = Employee::select('id','name','phone','email','id_card','created_at');

        if (!empty($this->search)) {
            $query->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('id_card', 'like','%' . $this->search . '%')
                  ->orWhere('phone', 'like','%' . $this->search . '%');
        }

        return $query->get()->map(function ($employee) {
            return [
                'Id'               => $employee->id,
                'Name'             => $employee->name,
                'Phone'            => $employee->phone,
                'Email'            => $employee->email,
                'Id_card'          => $employee->id_card,
                'Created At'       => $employee->created_at->format('Y-m-d'),
            ];
        });

    }

    public function headings(): array
    {
        return ['ID', 'Name', 'Phone', 'Email','Id_card', 'Created At'];
    }
}
