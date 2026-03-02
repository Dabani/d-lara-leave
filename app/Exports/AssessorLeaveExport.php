<?php

namespace App\Exports;

use App\Models\LeaveRequest;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AssessorLeaveExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $userRole;
    protected $department;
    protected $userId;

    public function __construct($userRole, $department = null, $userId = null)
    {
        $this->userRole = $userRole;
        $this->department = $department;
        $this->userId = $userId;
    }

    public function collection()
    {
        if ($this->userRole === 'managing_partner') {
            // MP sees ALL leave applications
            return LeaveRequest::with(['employee.user', 'assessor', 'mpReviewer'])
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            // HOD sees only their department
            return LeaveRequest::with(['employee.user', 'assessor'])
                ->whereHas('employee', function($q) {
                    $q->where('department', $this->department);
                })
                ->whereHas('employee.user', function($q) {
                    $q->where('role', 'user'); // Only regular employees
                })
                ->orderBy('created_at', 'desc')
                ->get();
        }
    }

    public function headings(): array
    {
        return [
            'ID',
            'Employee Name',
            'Department',
            'Leave Type',
            'From Date',
            'To Date',
            'Working Days',
            'Reason',
            'Status',
            'Assessment Status',
            'Assessed By',
            'Assessed Date',
            'MP Status',
            'MP Reviewed By',
            'MP Review Date',
            'Applied Date',
        ];
    }

    public function map($leave): array
    {
        return [
            $leave->id,
            $leave->employee->user->name ?? 'N/A',
            $leave->employee->department ?? 'N/A',
            $leave->leave_type,
            $leave->leave_from->format('Y-m-d'),
            $leave->leave_to->format('Y-m-d'),
            $leave->working_days_count ?? 0,
            $leave->reason ?? 'N/A',
            $leave->status,
            $leave->assessment_status ?? 'Not Assessed',
            $leave->assessor->name ?? 'N/A',
            $leave->assessed_at ? $leave->assessed_at->format('Y-m-d H:i') : 'N/A',
            $leave->mp_status ?? 'N/A',
            $leave->mpReviewer->name ?? 'N/A',
            $leave->mp_reviewed_at ? $leave->mp_reviewed_at->format('Y-m-d H:i') : 'N/A',
            $leave->created_at->format('Y-m-d H:i'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E2E8F0']
                ]
            ],
        ];
    }

    public function title(): string
    {
        return $this->userRole === 'managing_partner' 
            ? 'All Leave Applications' 
            : $this->department . ' Leave Applications';
    }
}
