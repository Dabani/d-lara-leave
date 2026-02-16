<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    // -------------------------------------------------------------------------
    // DASHBOARD
    // Route: GET /dashboard  →  name: dashboard
    // -------------------------------------------------------------------------
    public function index(Request $request)
    {
        $userId     = Auth::id();
        $user       = User::findOrFail($userId);
        $employee   = $user->employee;
        $yearFilter = $request->get('year', date('Y'));

        $leaveRequests = null;
        $years         = collect();

        if ($employee) {
            $leaveRequests = $employee->leaveRequests()
                ->whereYear('leave_from', $yearFilter)
                ->orderBy('created_at', 'desc')
                ->paginate(5);

            $years = $employee->leaveRequests()
                ->selectRaw('YEAR(leave_from) as year')
                ->distinct()
                ->orderBy('year', 'desc')
                ->pluck('year');
        }

        return view('user.dashboard', compact(
            'employee',
            'leaveRequests',
            'years',
            'yearFilter'
        ));
    }

    // -------------------------------------------------------------------------
    // PROFILE  (replaces both UserController::profile and
    //           UserProfileController::index — now always passes all leave data)
    // Route: GET /user/profile  →  name: user.profile
    // Route: GET /my-profile    →  name: my-profile   (legacy alias kept working)
    // Both routes should point here.
    // -------------------------------------------------------------------------
    public function profile()
    {
        $user     = Auth::user();
        $employee = $user->employee;

        [$approvedLeaves, $pendingLeaves, $rejectedLeaves] =
            $this->groupedLeaveRequests($employee);

        return view('user.profile', compact(
            'user',
            'employee',
            'approvedLeaves',
            'pendingLeaves',
            'rejectedLeaves'
        ));
    }

    // -------------------------------------------------------------------------
    // PRINT PROFILE
    // Route: GET /user/profile/print  →  name: user.profile.print
    // -------------------------------------------------------------------------
    public function printProfile()
    {
        $user     = Auth::user();
        $employee = $user->employee;

        [$approvedLeaves, $pendingLeaves, $rejectedLeaves] =
            $this->groupedLeaveRequests($employee);

        return view('user.printProfile', compact(
            'user',
            'employee',
            'approvedLeaves',
            'pendingLeaves',
            'rejectedLeaves'
        ));
    }

    // -------------------------------------------------------------------------
    // UPDATE PROFILE IMAGE
    // Route: POST /user/update-profile  →  name: user.update-profile
    // -------------------------------------------------------------------------
    public function updateProfile(Request $request)
    {
        $userId   = Auth::id();
        $employee = Employee::where('user_id', $userId)->first();

        if (!$employee) {
            return redirect()->back()->with('error', 'Employee record not found.');
        }

        $request->validate([
            'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($request->hasFile('profile_image')) {
            // Delete old image from disk if one exists
            if (!empty($employee->profile_image) &&
                Storage::disk('public')->exists($employee->profile_image)) {
                Storage::disk('public')->delete($employee->profile_image);
            }

            // store() returns the relative path e.g. "profile_images/abc123.jpg"
            // — store ONLY this relative path in the database, never prefix it
            // with "storage/" so that asset('storage/'.$path) always works correctly.
            $employee->profile_image = $request
                ->file('profile_image')
                ->store('profile_images', 'public');

            $employee->save();

            return redirect()->back()->with('success', 'Profile picture updated successfully.');
        }

        return redirect()->back()->with('error', 'No image was selected.');
    }

    // -------------------------------------------------------------------------
    // LEAVE HISTORY
    // Route: GET /user/leave-history  →  name: leave-history
    // -------------------------------------------------------------------------
    public function leaveHistory(Request $request)
    {
        $userId   = Auth::id();
        $user     = User::findOrFail($userId);
        $employee = $user->employee;

        if (!$employee) {
            return redirect()->route('dashboard')
                ->with('error', 'Employee record not found.');
        }

        $yearFilter   = $request->get('year');
        $statusFilter = $request->get('status');

        $query = $employee->leaveRequests();

        if ($yearFilter) {
            $query->whereYear('leave_from', $yearFilter);
        }

        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }

        $leaveHistory = $query->orderBy('created_at', 'desc')->paginate(10);

        $years = $employee->leaveRequests()
            ->selectRaw('YEAR(leave_from) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        $stats = [
            'total_requests' => $employee->leaveRequests()->count(),
            'approved'       => $employee->leaveRequests()->where('status', 'Approved')->count(),
            'rejected'       => $employee->leaveRequests()->where('status', 'Rejected')->count(),
            'pending'        => $employee->leaveRequests()->where('status', 'pending')->count(),
            'total_days_taken' => $employee->total_leave,
        ];

        return view('user.leave-history', compact(
            'leaveHistory',
            'employee',
            'years',
            'yearFilter',
            'statusFilter',
            'stats'
        ));
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    /**
     * Return the three leave collections needed by the profile and print views.
     * Centralised here so profile() and printProfile() stay DRY and any future
     * change to query logic only needs to be made in one place.
     *
     * @param  \App\Models\Employee|null  $employee
     * @return array  [ $approvedLeaves, $pendingLeaves, $rejectedLeaves ]
     */
    private function groupedLeaveRequests(?Employee $employee): array
    {
        if (!$employee) {
            return [collect(), collect(), collect()];
        }

        $approved = $employee->leaveRequests()
            ->where('status', 'Approved')
            ->orderBy('leave_from', 'desc')
            ->get();

        $pending = $employee->leaveRequests()
            ->where('status', 'pending')
            ->orderBy('leave_from', 'desc')
            ->get();

        $rejected = $employee->leaveRequests()
            ->where('status', 'Rejected')
            ->orderBy('leave_from', 'desc')
            ->get();

        return [$approved, $pending, $rejected];
    }
}
