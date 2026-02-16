<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $employee = $user->employee;

        // Get leave requests grouped by status
        if ($employee) {
            $approvedLeaves = $employee->leaveRequests()
                ->where('status', 'Approved')
                ->orderBy('leave_from', 'desc')
                ->get();

            $pendingLeaves = $employee->leaveRequests()
                ->where('status', 'pending')
                ->orderBy('leave_from', 'desc')
                ->get();

            $rejectedLeaves = $employee->leaveRequests()
                ->where('status', 'Rejected')
                ->orderBy('leave_from', 'desc')
                ->get();
        } else {
            $approvedLeaves = collect([]);
            $pendingLeaves = collect([]);
            $rejectedLeaves = collect([]);
        }

        return view('user.profile', compact(
            'user',
            'employee',
            'approvedLeaves',
            'pendingLeaves',
            'rejectedLeaves'
        ));
    }

    public function print()
    {
        $user = Auth::user();
        $employee = $user->employee;

        // Get leave requests grouped by status
        if ($employee) {
            $approvedLeaves = $employee->leaveRequests()
                ->where('status', 'Approved')
                ->orderBy('leave_from', 'desc')
                ->get();

            $pendingLeaves = $employee->leaveRequests()
                ->where('status', 'pending')
                ->orderBy('leave_from', 'desc')
                ->get();

            $rejectedLeaves = $employee->leaveRequests()
                ->where('status', 'Rejected')
                ->orderBy('leave_from', 'desc')
                ->get();
        } else {
            $approvedLeaves = collect([]);
            $pendingLeaves = collect([]);
            $rejectedLeaves = collect([]);
        }

        return view('user.printProfile', compact(
            'user',
            'employee',
            'approvedLeaves',
            'pendingLeaves',
            'rejectedLeaves'
        ));
    }
}