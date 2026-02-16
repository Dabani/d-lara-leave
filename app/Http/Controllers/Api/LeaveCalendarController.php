<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class LeaveCalendarController extends Controller
{
    /**
     * Get blocked dates for the current user
     */
    public function getBlockedDates(Request $request)
    {
        $employee = auth()->user()->employee;
        
        if (!$employee) {
            return response()->json([
                'blocked_dates' => [],
                'message' => 'No employee record found'
            ]);
        }

        // Get all approved and pending leave requests for this employee
        $leaveRequests = $employee->leaveRequests()
            ->whereIn('status', ['Approved', 'pending'])
            ->get();

        $blockedDates = [];

        foreach ($leaveRequests as $request) {
            $period = CarbonPeriod::create(
                Carbon::parse($request->leave_from),
                Carbon::parse($request->leave_to)
            );

            foreach ($period as $date) {
                $blockedDates[] = [
                    'date' => $date->format('Y-m-d'),
                    'leave_type' => $request->leave_type,
                    'status' => $request->status,
                    'leave_id' => $request->id,
                ];
            }
        }

        return response()->json([
            'blocked_dates' => $blockedDates,
            'message' => 'Success'
        ]);
    }

    /**
     * Check if dates overlap with existing leave
     */
    public function checkDateAvailability(Request $request)
    {
        $request->validate([
            'leave_from' => 'required|date',
            'leave_to' => 'required|date|after_or_equal:leave_from',
            'exclude_id' => 'nullable|integer', // Exclude current leave when editing
        ]);

        $employee = auth()->user()->employee;
        
        if (!$employee) {
            return response()->json([
                'available' => true,
                'conflicts' => []
            ]);
        }

        $leaveFrom = Carbon::parse($request->leave_from);
        $leaveTo = Carbon::parse($request->leave_to);

        // Query for overlapping leaves
        $query = $employee->leaveRequests()
            ->whereIn('status', ['Approved', 'pending'])
            ->where(function($q) use ($leaveFrom, $leaveTo) {
                // Check for any overlap
                $q->where(function($subQ) use ($leaveFrom, $leaveTo) {
                    $subQ->whereBetween('leave_from', [$leaveFrom, $leaveTo])
                         ->orWhereBetween('leave_to', [$leaveFrom, $leaveTo]);
                })->orWhere(function($subQ) use ($leaveFrom, $leaveTo) {
                    $subQ->where('leave_from', '<=', $leaveFrom)
                         ->where('leave_to', '>=', $leaveTo);
                });
            });

        // Exclude current leave when editing
        if ($request->exclude_id) {
            $query->where('id', '!=', $request->exclude_id);
        }

        $conflicts = $query->get();

        return response()->json([
            'available' => $conflicts->isEmpty(),
            'conflicts' => $conflicts->map(function($conflict) {
                return [
                    'id' => $conflict->id,
                    'leave_type' => $conflict->leave_type,
                    'leave_from' => $conflict->leave_from,
                    'leave_to' => $conflict->leave_to,
                    'status' => $conflict->status,
                ];
            })
        ]);
    }
}