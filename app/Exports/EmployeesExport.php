<?php

namespace App\Exports;

use App\Models\Employee;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EmployeesExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $type;

    public function __construct($type = 'all')
    {
        $this->type = $type;
    }

    public function collection()
    {
        $query = User::with('employee');

        switch ($this->type) {
            case 'pending':
                $query->whereDoesntHave('employee')->where('role', '!=', 'admin');
                break;
            case 'active':
                $query->whereHas('employee', function($q) {
                    $q->where('status', 'active');
                });
                break;
            case 'blocked':
                $query->whereHas('employee', function($q) {
                    $q->where('status', 'blocked');
                });
                break;
            default:
                $query->whereHas('employee');
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email',
            'Department',
            'Status',
            'Casual Leave',
            'Sick Leave',
            'Emergency Leave',
            'Study Leave',
            'Maternity Leave',
            'Paternity Leave',
            'Annual Leave',
            'Without Pay Leave',
            'Total Leave',
            'Leave Year',
        ];
    }

    public function map($user): array
    {
        $employee = $user->employee;
        
        return [
            $user->id,
            $user->name,
            $user->email,
            $employee->department ?? 'N/A',
            $employee->status ?? 'Pending',
            $employee->casual_leave ?? 0,
            $employee->sick_leave ?? 0,
            $employee->emergency_leave ?? 0,
            $employee->study_leave ?? 0,
            $employee->maternity_leave ?? 0,
            $employee->paternity_leave ?? 0,
            $employee->annual_leave ?? 0,
            $employee->without_pay_leave ?? 0,
            $employee->total_leave ?? 0,
            $employee->leave_year ?? date('Y'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
