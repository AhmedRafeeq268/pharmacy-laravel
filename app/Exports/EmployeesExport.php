<?php
namespace App\Exports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class EmployeesExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $employees;

    public function __construct($employees)
    {
        $this->employees = $employees;
    }

    public function collection()
    {
        return $this->employees->map(function ($employee) {
            return [
                 $employee->id,
                 $employee->name,
                 $employee->phone,
                 $employee->email,
                 $employee->id_card,
                 $employee->created_at->format('Y-m-d'),
            ];
        });

    }

    public function headings(): array
    {
        return ['ID', 'Name', 'Phone', 'Email','Id_card', 'Created At'];
    }
}
