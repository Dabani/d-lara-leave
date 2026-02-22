<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\Employee;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class LeaveCalendarController extends Controller
{
    /**
     * Get blocked dates for the CURRENT user only
     * Blocks dates where the current user already has pending/approved leaves
     * (Prevents double-booking themselves)
     */
    public function getBlockedDates(Request $request)
    {
        $currentUser = auth()->user();
        
        if (!$currentUser || !$currentUser->employee) {
            return response()->json([
                'blocked_dates' => [],
                'message' => 'No employee record found'
            ]);
        }

        // Get ONLY the current user's own pending and approved leaves
        // These dates should be blocked because user cannot have overlapping leaves
        $userLeaves = LeaveRequest::where('employee_id', $currentUser->employee->id)
            ->whereIn('status', ['pending', 'approved']) // Only pending/approved block the user
            ->where('leave_to', '>=', now()->subDays(7))
            ->select('id', 'leave_from', 'leave_to', 'leave_type', 'status')
            ->get();

        // Convert to date range format for Flatpickr
        $blockedDates = $userLeaves->map(function($leave) {
            return [
                'from' => $leave->leave_from->format('Y-m-d'),
                'to' => $leave->leave_to->format('Y-m-d'),
                'type' => $leave->leave_type,
                'status' => $leave->status,
                'leave_id' => $leave->id,
                'reason' => 'You already have a ' . $leave->status . ' ' . $leave->leaveType . ' request for this period'
            ];
        })->values()->toArray();

        return response()->json([
            'blocked_dates' => $blockedDates,
            'count' => count($blockedDates),
            'message' => 'Your personal blocked dates fetched successfully'
        ]);
    }

    /**
     * Check if a specific date range conflicts with:
     * 1. User's own existing leaves (blocked)
     * 2. Department congestion (warning only, not blocked)
     */
    public function checkDateAvailability(Request $request)
    {
        $request->validate([
            'leave_from' => 'required|date',
            'leave_to' => 'required|date|after_or_equal:leave_from',
            'exclude_id' => 'nullable|integer', // Exclude when editing own request
        ]);

        $currentUser = auth()->user();
        
        if (!$currentUser || !$currentUser->employee) {
            return response()->json([
                'available' => true,
                'conflicts' => [],
                'department_warnings' => [],
                'message' => 'No employee record found'
            ]);
        }

        $employee = $currentUser->employee;
        $leaveFrom = Carbon::parse($request->leave_from);
        $leaveTo = Carbon::parse($request->leave_to);

        // ===== 1. CHECK OWN CONFLICTS (BLOCKING) =====
        // These are actual conflicts - user cannot have overlapping leaves
        $ownConflicts = LeaveRequest::where('employee_id', $employee->id)
            ->whereIn('status', ['pending', 'approved'])
            ->where(function($query) use ($leaveFrom, $leaveTo) {
                // Check for ANY overlap
                $query->where(function($q) use ($leaveFrom, $leaveTo) {
                    $q->whereBetween('leave_from', [$leaveFrom, $leaveTo])
                      ->orWhereBetween('leave_to', [$leaveFrom, $leaveTo])
                      ->orWhere(function($sub) use ($leaveFrom, $leaveTo) {
                          $sub->where('leave_from', '<=', $leaveFrom)
                              ->where('leave_to', '>=', $leaveTo);
                      });
                });
            });

        // Exclude current request when editing
        if ($request->exclude_id) {
            $ownConflicts = $ownConflicts->where('id', '!=', $request->exclude_id);
        }

        $ownConflicts = $ownConflicts->get();

        if ($ownConflicts->count() > 0) {
            $conflictDetails = $ownConflicts->map(function($leave) {
                return [
                    'id' => $leave->id,
                    'from' => $leave->leave_from->format('M d, Y'),
                    'to' => $leave->leave_to->format('M d, Y'),
                    'type' => $leave->leave_type,
                    'status' => $leave->status,
                ];
            });

            return response()->json([
                'available' => false,
                'message' => 'You already have a leave request during this period',
                'conflicts' => $conflictDetails,
                'department_warnings' => []
            ]);
        }

        // ===== 2. CHECK DEPARTMENT CONGESTION (WARNING ONLY) =====
        // Count how many people from same department are on leave during this period
        $departmentEmployees = Employee::where('department', $employee->department)
            ->where('id', '!=', $employee->id) // Exclude current user
            ->pluck('id');

        $departmentLeaves = LeaveRequest::whereIn('employee_id', $departmentEmployees)
            ->whereIn('status', ['approved']) // Only count approved leaves for congestion
            ->where(function($query) use ($leaveFrom, $leaveTo) {
                $query->whereBetween('leave_from', [$leaveFrom, $leaveTo])
                      ->orWhereBetween('leave_to', [$leaveFrom, $leaveTo])
                      ->orWhere(function($q) use ($leaveFrom, $leaveTo) {
                          $q->where('leave_from', '<=', $leaveFrom)
                            ->where('leave_to', '>=', $leaveTo);
                      });
            })
            ->with('employee.user')
            ->get();

        $departmentWarnings = [];
        
        if ($departmentLeaves->count() >= 2) {
            // Warning if 2 or more department members are already on leave
            $employeeNames = $departmentLeaves->map(function($leave) {
                return $leave->employee->user->name;
            })->join(', ');
            
            $departmentWarnings[] = [
                'level' => $departmentLeaves->count() >= 3 ? 'high' : 'medium',
                'message' => "{$departmentLeaves->count()} member(s) of your department already have approved leave during this period: {$employeeNames}",
                'count' => $departmentLeaves->count(),
                'suggestion' => 'Consider discussing with your HOD if department coverage might be affected'
            ];
        }

        // ===== 3. CHECK FOR SIMILAR LEAVE TYPE CONCENTRATION =====
        // Additional warning for same leave type concentration
        $sameTypeLeaves = $departmentLeaves->filter(function($leave) use ($request) {
            return $leave->leave_type === $request->leave_type;
        });

        if ($sameTypeLeaves->count() >= 2 && in_array($request->leave_type, ['Annual Leave', 'Casual Leave'])) {
            $departmentWarnings[] = [
                'level' => 'info',
                'message' => "{$sameTypeLeaves->count()} other(s) in your department are taking {$request->leave_type} during this period",
                'count' => $sameTypeLeaves->count()
            ];
        }

        // Calculate working days
        $workingDays = $this->calculateWorkingDays($leaveFrom, $leaveTo);

        return response()->json([
            'available' => true,
            'message' => 'Date range is available',
            'working_days' => $workingDays,
            'department_warnings' => $departmentWarnings,
            'department_congestion' => $departmentLeaves->count()
        ]);
    }

    /**
     * Get department leave summary for a given date range
     * Useful for showing users how busy their department is
     */
    public function getDepartmentSummary(Request $request)
    {
        $request->validate([
            'date' => 'required|date'
        ]);

        $currentUser = auth()->user();
        
        if (!$currentUser || !$currentUser->employee) {
            return response()->json([
                'department' => null,
                'on_leave' => 0,
                'total' => 0,
                'employees' => []
            ]);
        }

        $employee = $currentUser->employee;
        $date = Carbon::parse($request->date);

        // Get all employees in same department
        $departmentEmployees = Employee::where('department', $employee->department)->get();
        $totalInDepartment = $departmentEmployees->count();

        // Find who's on leave on this date
        $onLeave = LeaveRequest::whereIn('employee_id', $departmentEmployees->pluck('id'))
            ->where('status', 'approved')
            ->where('leave_from', '<=', $date)
            ->where('leave_to', '>=', $date)
            ->with('employee.user')
            ->get();

        return response()->json([
            'department' => $employee->department,
            'on_leave' => $onLeave->count(),
            'total' => $totalInDepartment,
            'employees' => $onLeave->map(function($leave) {
                return [
                    'name' => $leave->employee->user->name,
                    'leave_type' => $leave->leave_type,
                    'from' => $leave->leave_from->format('M d'),
                    'to' => $leave->leave_to->format('M d')
                ];
            })
        ]);
    }

    private function calculateWorkingDays(Carbon $from, Carbon $to): int
    {
        $workingDays = 0;
        $period = CarbonPeriod::create($from, $to);

        foreach ($period as $date) {
            if (!in_array($date->dayOfWeek, [Carbon::SATURDAY, Carbon::SUNDAY])) {
                $workingDays++;
            }
        }

        return $workingDays;
    }
}
