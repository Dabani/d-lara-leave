<?php

namespace App\Exports;

use App\Models\LeaveRequest;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LeaveRequestsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
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
        $query = LeaveRequest::with('employee.user');

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
            'ID',
            'Employee Name',
            'Department',
            'Leave Type',
            'Leave From',
            'Leave To',
            'Duration (Days)',
            'Reason',
            'Status',
            'Submitted Date',
        ];
    }

    public function map($leaveRequest): array
    {
        $duration = Carbon::parse($leaveRequest->leave_from)
            ->diffInDays(Carbon::parse($leaveRequest->leave_to)) + 1;

        return [
            $leaveRequest->id,
            $leaveRequest->employee->user->name,
            $leaveRequest->employee->department ?? 'N/A',
            $leaveRequest->leave_type,
            $leaveRequest->leave_from,
            $leaveRequest->leave_to,
            $duration,
            $leaveRequest->reason ?? 'N/A',
            $leaveRequest->status,
            $leaveRequest->created_at->format('Y-m-d H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
