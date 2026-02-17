<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    public function index()
    {
        $pendingUsers = User::whereDoesntHave('employee')
            ->where('role', '!=', 'admin')
            ->select('id', 'name', 'email')
            ->latest()
            ->paginate(5, ['*'], 'pending_page');

        $activeEmployees = User::whereHas('employee', function($query) {
            $query->where('status', 'active');
        })
        ->with('employee')
        ->paginate(5, ['*'], 'active_page');

        $blockedEmployees = User::whereHas('employee', function($query) {
            $query->where('status', 'blocked');
        })
        ->with('employee')
        ->paginate(5, ['*'], 'blocked_page');

        $departments = Department::all();

        return view('admin.manageEmployee', compact(
            'pendingUsers',
            'activeEmployees',
            'blockedEmployees',
            'departments'
        ));
    }

    public function approveEmployee(Request $request, $id)
    {
        try {
            $request->validate([
                'department' => 'required|string',
                'gender' => 'required|in:male,female,other',
                'hire_date' => 'required|date|before_or_equal:today',
            ]);
    
            $user = User::findOrFail($id);
            
            // Check if already has employee record
            if ($user->employee) {
                return redirect()->back()->with('error', 'Employee already approved');
            }
            
            // Update user gender
            $user->gender = $request->gender;
            $user->save();
            
            // Create employee record
            $user->employee()->create([
                'status' => 'active',
                'department' => $request->department,
                'leave_year' => date('Y'),
                'hire_date' => $request->hire_date,
            ]);
    
            return redirect()->back()->with('success', 'Employee approved successfully');
            
        } catch (\Exception $e) {
            \Log::error('Employee approval error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to approve employee: ' . $e->getMessage());
        }
    }

    public function blockEmployee($id)
    {
        $employee = Employee::where('user_id', $id)->first();

        if ($employee) {
            $employee->status = 'blocked';
            $employee->save();
            return redirect()->back()->with('success', 'Employee blocked successfully');
        }

        return redirect()->back()->with('error', 'Employee not found');
    }

    public function unblockEmployee($id)
    {
        $employee = Employee::where('user_id', $id)->first();

        if ($employee) {
            $employee->status = 'active';
            $employee->save();
            return redirect()->back()->with('success', 'Employee unblocked successfully');
        }

        return redirect()->back()->with('error', 'Employee not found');
    }

    public function updateProfile(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);
        
        $request->validate([
            'department' => 'nullable|string',
            'hire_date' => 'nullable|date|before_or_equal:today',  // ADD THIS
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',  // ADD webp
        ]);

        // Update hire date if provided
        if ($request->has('hire_date')) {
            $employee->hire_date = $request->hire_date;
        }

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            // Delete old image if exists
            if ($employee->profile_image && Storage::disk('public')->exists($employee->profile_image)) {
                Storage::disk('public')->delete($employee->profile_image);
            }

            // Store new image in public disk
            $imagePath = $request->file('profile_image')->store('profile_images', 'public');
            $employee->profile_image = $imagePath;
        }

        if ($request->has('department')) {
            $employee->department = $request->department;
        }

        $employee->save();

        return redirect()->back()->with('success', 'Profile updated successfully');
    }

    public function updateRole(Request $request, $id)
    {
        $request->validate([
            'role'             => 'required|in:user,assessor,managing_partner,admin',
            'heads_department' => 'required_if:role,assessor|nullable|string',
        ]);

        $user = \App\Models\User::findOrFail($id);

        $user->role             = $request->role;
        $user->heads_department = $request->role === 'assessor'
            ? $request->heads_department
            : null;
        $user->save();

        return redirect()->back()
            ->with('success', "Role updated to '{$request->role}' for {$user->name}.");
    }

    public function exportToExcel(Request $request)
    {
        $type = $request->get('type', 'all');
        
        $filename = 'employees_' . $type . '_' . date('Ymd_His') . '.xlsx';
        
        return Excel::download(
            new \App\Exports\EnhancedEmployeesExport($type),
            $filename
        );
    }
}
