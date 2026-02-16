<?php

namespace App\Exports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Carbon\Carbon;

class EnhancedEmployeesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $type;

    public function __construct($type = 'all')
    {
        $this->type = $type;
    }

    public function collection()
    {
        $query = Employee::with('user');

        switch ($this->type) {
            case 'active':
                $query->where('status', 'active');
                break;
            case 'blocked':
                $query->where('status', 'blocked');
                break;
            case 'all':
            default:
                // All employees
                break;
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Employee ID',
            'Name',
            'Email',
            'Gender',
            'Department',
            'Status',
            
            // Employment Details
            'Hire Date',
            'Years of Service',
            'Months of Service',
            'Employment Duration',
            
            // Annual Leave Details
            'Eligible for Annual Leave',
            'Annual Leave Entitlement',
            'Max Days Per Run',
            'Annual Leave Taken',
            'Casual Leave Taken',
            'Emergency Leave Taken',
            'Total Against Annual',
            'Annual Leave Remaining',
            'Annual Runs Taken',
            
            // Other Leave Types
            'Sick Leave',
            'Maternity Leave',
            'Paternity Leave',
            'Study Leave',
            'Without Pay Leave',
            'Total Leave',
            
            // Additional Info
            'Leave Year',
            'Has Profile Image',
            'Registered Date',
        ];
    }

    public function map($employee): array
    {
        $user = $employee->user;
        $stats = $employee->getAnnualLeaveStats();
        $yearsOfService = $employee->hire_date ? $employee->getYearsOfService() : 0;
        $monthsOfService = $employee->hire_date ? $employee->getMonthsOfService() : 0;
        
        // Calculate employment duration string
        $employmentDuration = 'N/A';
        if ($employee->hire_date) {
            $years = floor($yearsOfService);
            $months = $monthsOfService % 12;
            $employmentDuration = '';
            if ($years > 0) {
                $employmentDuration .= $years . ' year' . ($years > 1 ? 's' : '');
            }
            if ($months > 0) {
                if ($years > 0) $employmentDuration .= ', ';
                $employmentDuration .= $months . ' month' . ($months > 1 ? 's' : '');
            }
            if ($employmentDuration === '') {
                $employmentDuration = '< 1 month';
            }
        }

        return [
            $employee->id,
            $user ? $user->name : 'N/A',
            $user ? $user->email : 'N/A',
            $user ? ucfirst($user->gender ?? 'N/A') : 'N/A',
            $employee->department ?? 'N/A',
            ucfirst($employee->status),
            
            // Employment Details
            $employee->hire_date ? Carbon::parse($employee->hire_date)->format('Y-m-d') : 'N/A',
            $yearsOfService > 0 ? round($yearsOfService, 1) : 0,
            $monthsOfService,
            $employmentDuration,
            
            // Annual Leave Details
            $stats['is_eligible'] ? 'Yes' : 'No',
            $stats['entitlement'],
            $stats['max_days_per_run'],
            $stats['annual_days_taken'],
            $stats['casual_days_taken'],
            $stats['emergency_days_taken'],
            $stats['total_days_taken'],
            $stats['remaining_days'],
            $stats['annual_runs_count'],
            
            // Other Leave Types
            $employee->sick_leave ?? 0,
            $employee->maternity_leave ?? 0,
            $employee->paternity_leave ?? 0,
            $employee->study_leave ?? 0,
            $employee->without_pay_leave ?? 0,
            $employee->total_leave ?? 0,
            
            // Additional Info
            $employee->leave_year ?? date('Y'),
            $employee->profile_image ? 'Yes' : 'No',
            $employee->created_at->format('Y-m-d H:i'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '059669'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }
}
