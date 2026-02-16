<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\LeaveRequestApproved;
use App\Mail\LeaveRequestRejected;
use App\Models\Department;
use App\Models\LeaveRequest;
use App\Services\LeaveValidationService;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class LeaveManageController extends Controller
{
    protected $validationService;

    public function __construct(LeaveValidationService $validationService)
    {
        $this->validationService = $validationService;
    }

    public function index(Request $request): View|Application|Factory|\Illuminate\Contracts\Foundation\Application
    {
        $departmentFilter = $request->get('department');
        $yearFilter = $request->get('year', date('Y'));

        $query = LeaveRequest::with('employee.user');

        if ($departmentFilter) {
            $query->whereHas('employee', function($q) use ($departmentFilter) {
                $q->where('department', $departmentFilter);
            });
        }

        if ($yearFilter) {
            $query->whereYear('leave_from', $yearFilter);
        }

        $pendingLeaveRequests = (clone $query)
            ->where('status', 'pending')
            ->latest()
            ->paginate(5, ['*'], 'pending_page');

        $approvedLeaveRequests = (clone $query)
            ->where('status', 'Approved')
            ->latest()
            ->paginate(5, ['*'], 'approved_page');

        $rejectedLeaveRequests = (clone $query)
            ->where('status', 'Rejected')
            ->latest()
            ->paginate(5, ['*'], 'rejected_page');

        $leaveRequestsQuery = LeaveRequest::with('employee.user');
        
        if ($departmentFilter) {
            $leaveRequestsQuery->whereHas('employee', function($q) use ($departmentFilter) {
                $q->where('department', $departmentFilter);
            });
        }

        if ($yearFilter) {
            $leaveRequestsQuery->whereYear('leave_from', $yearFilter);
        }

        $leaveRequests = $leaveRequestsQuery->get();

        $stats = [
            'pending' => $leaveRequests->filter(fn($r) => strtolower($r->status) === 'pending')->count(),
            'approved' => $leaveRequests->filter(fn($r) => strtolower($r->status) === 'approved')->count(),
            'rejected' => $leaveRequests->filter(fn($r) => strtolower($r->status) === 'rejected')->count(),
            'total' => $leaveRequests->count(),
        ];

        try {
            $departments = Department::all();
        } catch (\Exception $e) {
            $departments = collect([]);
        }

        $years = LeaveRequest::selectRaw('YEAR(leave_from) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');
        
        if ($years->isEmpty()) {
            $years = collect([date('Y')]);
        }

        return view('admin.manageLeave', compact(
            'pendingLeaveRequests',
            'approvedLeaveRequests',
            'rejectedLeaveRequests',
            'leaveRequests',
            'departments',
            'years',
            'departmentFilter',
            'yearFilter',
            'stats'
        ));
    }

    public function approveLeave(Request $request): RedirectResponse
    {
        $leaveRequest = LeaveRequest::find($request->id);
        
        if (!$leaveRequest) {
            return redirect()->back()->with('error', 'Leave request not found');
        }

        // Re-validate before approval
        $employee = $leaveRequest->employee;
        $validationResult = $this->validationService->validateLeaveRequest($employee, [
            'leave_type' => $leaveRequest->leave_type,
            'leave_from' => $leaveRequest->leave_from,
            'leave_to' => $leaveRequest->leave_to,
            'is_first_attempt' => $leaveRequest->is_first_attempt,
            'medical_certificate' => $leaveRequest->medical_certificate,
            'supporting_document' => $leaveRequest->supporting_document,
        ]);

        if (!$validationResult['valid']) {
            $errors = implode(', ', $validationResult['errors']);
            return redirect()->back()->with('error', "Cannot approve: {$errors}");
        }

        $leaveRequest->status = 'Approved';
        $leaveRequest->save();

        $duration = $leaveRequest->working_days_count ?: 
                    Carbon::parse($leaveRequest->leave_from)->diffInDays(Carbon::parse($leaveRequest->leave_to)) + 1;
        
        if ($employee) {
            $leaveTypeMap = [
                'Casual Leave' => 'casual_leave',
                'Sick Leave' => 'sick_leave',
                'Emergency Leave' => 'emergency_leave',
                'Study Leave' => 'study_leave',
                'Maternity Leave' => 'maternity_leave',
                'Paternity Leave' => 'paternity_leave',
                'Annual Leave' => 'annual_leave',
                'Without Pay' => 'without_pay_leave',
            ];

            $field = $leaveTypeMap[$leaveRequest->leave_type] ?? null;
            
            if ($field && DB::getSchemaBuilder()->hasColumn('employees', $field)) {
                $employee->$field += $duration;
            }

            $employee->total_leave += $duration;
            
            if ($leaveRequest->leave_type === 'Annual Leave') {
                $employee->recordAnnualLeave($leaveRequest->id, $leaveRequest->working_days_count);
            }
            
            $employee->save();

            // Send email notification
            try {
                $mailData = [
                    'employee_name' => $employee->user->name,
                    'leave_from' => $leaveRequest->leave_from,
                    'leave_to' => $leaveRequest->leave_to,
                    'leave_type' => $leaveRequest->leave_type,
                    'reason' => $leaveRequest->reason,
                    'duration' => $duration,
                ];

                Mail::to($employee->user->email)->send(new LeaveRequestApproved($mailData));
            } catch (\Exception $e) {
                \Log::warning('Failed to send approval email: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('success', 'Leave request approved successfully');
    }

    public function rejectLeave(Request $request): RedirectResponse
    {
        $leaveRequest = LeaveRequest::find($request->id);
        
        if (!$leaveRequest) {
            return redirect()->back()->with('error', 'Leave request not found');
        }

        $leaveRequest->status = 'Rejected';
        $leaveRequest->save();

        $employee = $leaveRequest->employee;
        
        if ($employee) {
            try {
                $mailData = [
                    'employee_name' => $employee->user->name,
                    'leave_from' => $leaveRequest->leave_from,
                    'leave_to' => $leaveRequest->leave_to,
                    'leave_type' => $leaveRequest->leave_type,
                    'reason' => $leaveRequest->reason,
                ];

                Mail::to($employee->user->email)->send(new LeaveRequestRejected($mailData));
            } catch (\Exception $e) {
                \Log::warning('Failed to send rejection email: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('success', 'Leave request rejected successfully');
    }

    /**
     * NEW: Restore a rejected leave request back to pending
     */
    public function restoreLeave(Request $request): RedirectResponse
    {
        $leaveRequest = LeaveRequest::find($request->id);
        
        if (!$leaveRequest) {
            return redirect()->back()->with('error', 'Leave request not found');
        }

        if ($leaveRequest->status !== 'Rejected') {
            return redirect()->back()->with('error', 'Only rejected requests can be restored');
        }

        $leaveRequest->status = 'pending';
        $leaveRequest->admin_notes = ($leaveRequest->admin_notes ?? '') . "\nRestored from rejected status on " . now()->format('Y-m-d H:i:s');
        $leaveRequest->save();

        return redirect()->back()->with('success', 'Leave request restored to pending status');
    }

    public function exportToExcel(Request $request)
    {
        $department = $request->get('department');
        $year = $request->get('year', date('Y'));
        
        $filename = 'leave_requests_' . ($department ?? 'all') . '_' . $year . '_' . date('Ymd_His') . '.xlsx';
        
        return Excel::download(
            new \App\Exports\EnhancedLeaveRequestsExport($department, $year),
            $filename
        );
    }

    public function departmentReport(Request $request, $department)
    {
        $year = $request->get('year', date('Y'));
        
        $leaveRequests = LeaveRequest::with('employee.user')
            ->whereHas('employee', function($q) use ($department) {
                $q->where('department', $department);
            })
            ->whereYear('leave_from', $year)
            ->get();

        try {
            $departmentModel = Department::where('name', $department)->first();
        } catch (\Exception $e) {
            $departmentModel = null;
        }

        return view('admin.departmentReport', compact(
            'leaveRequests',
            'department',
            'departmentModel',
            'year'
        ));
    }

    public function printReport(Request $request): View|Application|Factory|\Illuminate\Contracts\Foundation\Application
    {
        $departmentFilter = $request->get('department');
        $yearFilter = $request->get('year', date('Y'));

        $query = LeaveRequest::with('employee.user');

        if ($departmentFilter) {
            $query->whereHas('employee', function($q) use ($departmentFilter) {
                $q->where('department', $departmentFilter);
            });
        }

        if ($yearFilter) {
            $query->whereYear('leave_from', $yearFilter);
        }

        $leaveRequests = $query->get();

        $stats = [
            'pending' => $leaveRequests->filter(fn($r) => strtolower($r->status) === 'pending')->count(),
            'approved' => $leaveRequests->filter(fn($r) => strtolower($r->status) === 'approved')->count(),
            'rejected' => $leaveRequests->filter(fn($r) => strtolower($r->status) === 'rejected')->count(),
            'total' => $leaveRequests->count(),
        ];

        return view('admin.printLeaveReport', compact(
            'leaveRequests',
            'departmentFilter',
            'yearFilter',
            'stats'
        ));
    }
}
