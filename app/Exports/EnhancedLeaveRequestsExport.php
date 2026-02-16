<?php

namespace App\Exports;

use App\Models\LeaveRequest;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Carbon\Carbon;

class EnhancedLeaveRequestsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $department;
    protected $year;

    public function __construct($department = null, $year = null)
    {
        $this->department = $department;
        $this->year = $year ?? date('Y');
    }

    public function collection()
    {
        $query = LeaveRequest::with(['employee.user']);

        if ($this->department) {
            $query->whereHas('employee', function($q) {
                $q->where('department', $this->department);
            });
        }

        if ($this->year) {
            $query->whereYear('leave_from', $this->year);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Request ID',
            'Employee Name',
            'Email',
            'Gender',
            'Department',
            'Hire Date',
            'Years of Service',
            'Employment Status',
            
            // Leave Request Details
            'Leave Type',
            'Leave From',
            'Leave To',
            'Total Days',
            'Working Days',
            'Status',
            'Applied Date',
            'Reason',
            
            // Annual Leave Statistics
            'Annual Leave Entitlement',
            'Annual Leave Taken',
            'Casual Leave Taken',
            'Emergency Leave Taken',
            'Total Leave Used',
            'Annual Leave Remaining',
            'Annual Runs Taken',
            'Max Days Per Run',
            'Eligible for Annual',
            
            // Other Leave Types
            'Sick Leave Total',
            'Maternity Leave Total',
            'Paternity Leave Total',
            'Study Leave Total',
            
            // Additional Info
            'Is First Attempt',
            'Out of Recommended Period',
            'Has Medical Certificate',
            'Has Supporting Document',
            'Admin Comments',
        ];
    }

    public function map($leaveRequest): array
    {
        $employee = $leaveRequest->employee;
        $user = $employee ? $employee->user : null;
        
        // Calculate statistics
        $annualStats = $employee ? $employee->getAnnualLeaveStats() : null;
        $yearsOfService = $employee && $employee->hire_date 
            ? $employee->getYearsOfService() 
            : 0;
        
        $totalDays = Carbon::parse($leaveRequest->leave_from)
            ->diffInDays(Carbon::parse($leaveRequest->leave_to)) + 1;

        return [
            // Basic Info
            $leaveRequest->id,
            $user ? $user->name : 'N/A',
            $user ? $user->email : 'N/A',
            $user ? ucfirst($user->gender ?? 'N/A') : 'N/A',
            $employee ? $employee->department : 'N/A',
            $employee && $employee->hire_date 
                ? Carbon::parse($employee->hire_date)->format('Y-m-d') 
                : 'N/A',
            $yearsOfService > 0 ? round($yearsOfService, 1) . ' years' : 'N/A',
            $employee ? ucfirst($employee->status) : 'N/A',
            
            // Leave Request Details
            $leaveRequest->leave_type,
            Carbon::parse($leaveRequest->leave_from)->format('Y-m-d'),
            Carbon::parse($leaveRequest->leave_to)->format('Y-m-d'),
            $totalDays,
            $leaveRequest->working_days_count ?? $totalDays,
            ucfirst($leaveRequest->status),
            $leaveRequest->created_at->format('Y-m-d H:i'),
            $leaveRequest->reason ?? 'N/A',
            
            // Annual Leave Statistics
            $annualStats ? $annualStats['entitlement'] : 'N/A',
            $annualStats ? $annualStats['annual_days_taken'] : 'N/A',
            $annualStats ? $annualStats['casual_days_taken'] : 'N/A',
            $annualStats ? $annualStats['emergency_days_taken'] : 'N/A',
            $annualStats ? $annualStats['total_days_taken'] : 'N/A',
            $annualStats ? $annualStats['remaining_days'] : 'N/A',
            $annualStats ? $annualStats['annual_runs_count'] : 'N/A',
            $annualStats ? $annualStats['max_days_per_run'] : 'N/A',
            $annualStats && $annualStats['is_eligible'] ? 'Yes' : 'No',
            
            // Other Leave Totals
            $employee ? $employee->sick_leave : 0,
            $employee ? $employee->maternity_leave : 0,
            $employee ? $employee->paternity_leave : 0,
            $employee ? $employee->study_leave : 0,
            
            // Additional Info
            $leaveRequest->is_first_attempt ? 'First Attempt' : 'Repeat Attempt',
            $leaveRequest->is_out_of_recommended_period ? 'Yes' : 'No',
            $leaveRequest->medical_certificate ? 'Yes' : 'No',
            $leaveRequest->supporting_document ? 'Yes' : 'No',
            $leaveRequest->comment ?? 'N/A',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Header row styling
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F46E5'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }
}
