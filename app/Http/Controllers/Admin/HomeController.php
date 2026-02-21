<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display the admin dashboard with leave overview tabs
     */
    public function index()
    {
        // Pending employee approvals count
        $pendingReq = User::whereDoesntHave('employee')
            ->where('role', '!=', 'admin')
            ->count();

        // Total pending leave requests count
        $leaveRequests = LeaveRequest::where('status', 'pending')->count();

        // Fetch leave applications for the tabbed overview
        // Get latest 10 of each status (showing 5 in view, but having more for variety)
        $approvedLeaves = LeaveRequest::with(['employee.user'])
            ->where('status', 'Approved')
            ->latest('updated_at')
            ->limit(10)
            ->get();

        $pendingLeaves = LeaveRequest::with(['employee.user'])
            ->where('status', 'pending')
            ->latest('created_at')
            ->limit(10)
            ->get();

        $rejectedLeaves = LeaveRequest::with(['employee.user'])
            ->where('status', 'Rejected')
            ->latest('updated_at')
            ->limit(10)
            ->get();

        // Counts for tab badges
        $approvedCount = LeaveRequest::where('status', 'Approved')->count();
        $pendingCount  = LeaveRequest::where('status', 'pending')->count();
        $rejectedCount = LeaveRequest::where('status', 'Rejected')->count();

        return view('admin.dashboard', compact(
            'pendingReq',
            'leaveRequests',
            'approvedLeaves',
            'pendingLeaves',
            'rejectedLeaves',
            'approvedCount',
            'pendingCount',
            'rejectedCount'
        ));
    }
}
