<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Count pending employee approval requests
        $pendingReq = User::whereDoesntHave('employee')
            ->where('role', '!=', 'admin')
            ->count();

        // Count pending leave requests
        $leaveRequests = LeaveRequest::where('status', 'pending')->count();

        return view('admin.dashboard', compact('pendingReq', 'leaveRequests'));
    }
}
