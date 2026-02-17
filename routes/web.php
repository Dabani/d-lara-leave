<?php

use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\LeaveManageController;
use App\Http\Controllers\Api\LeaveCalendarController;
use App\Http\Controllers\Assessor\AssessorController;
use App\Http\Controllers\User\LeaveCommentController;
use App\Http\Controllers\User\LeaveRequestController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (!auth()->check()) {
        return redirect()->route('login');
    }

    return match(auth()->user()->role) {
        'admin'            => redirect()->route('admin.dashboard'),
        'assessor'         => redirect()->route('assessor.dashboard'),
        'managing_partner' => redirect()->route('assessor.dashboard'),
        default            => redirect()->route('dashboard'),  // 'user'
    };
});

// Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// User routes
Route::middleware(['auth', 'verified', 'user'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [UserController::class, 'index'])->name('dashboard');
    
    // Leave History
    Route::get('/leave-history', [UserController::class, 'leaveHistory'])->name('leave-history');
    
    // Profile Update
    Route::post('/update-profile', [UserController::class, 'updateProfile'])->name('update-profile');

    // Leave Request routes
    Route::get('/leave-request/create', [LeaveRequestController::class, 'create'])->name('leave-request.create');
    Route::post('/leave-request', [LeaveRequestController::class, 'store'])->name('leave-request.store');
    Route::get('/leave-request/{id}', [LeaveRequestController::class, 'show'])->name('leave-request.show');
    Route::get('/leave-request/{id}/edit', [LeaveRequestController::class, 'edit'])->name('leave-request.edit');
    Route::put('/leave-request/{id}', [LeaveRequestController::class, 'update'])->name('leave-request.update');
    Route::delete('/leave-request/{id}', [LeaveRequestController::class, 'destroy'])->name('leave-request.destroy');

    // ── Comments on own leave requests ────────────────────
    Route::post('/leave-request/{id}/comment', [LeaveCommentController::class, 'store'])->name('leave-comment.store');

    // ── Profile ──────────────────────────────────────────────────────────────
    // Primary route
    Route::get('/user/profile', [UserController::class, 'profile'])
        ->name('user.profile');

    // Legacy alias (was handled by UserProfileController::index before merge).
    // Keep so any existing links to route('my-profile') still resolve.
    Route::get('/my-profile', [UserController::class, 'profile'])
        ->name('my-profile');

    // Print profile
    Route::get('/user/profile/print', [UserController::class, 'printProfile'])
        ->name('user.profile.print');

    // Update profile image
    Route::post('/user/update-profile', [UserController::class, 'updateProfile'])
        ->name('user.update-profile');

});

// ── Assessor routes (HOD + Managing Partner — guarded by 'assessor' middleware) ──
// ADD entire group —
Route::middleware(['auth', 'verified', 'assessor'])->group(function () {

    Route::get('/assessor/dashboard',
        [AssessorController::class, 'dashboard'])->name('assessor.dashboard');

    // HOD assessment actions
    Route::post('/assessor/assess/{id}/approve',
        [AssessorController::class, 'approve'])->name('assessor.approve');
    Route::post('/assessor/assess/{id}/reject',
        [AssessorController::class, 'reject'])->name('assessor.reject');

    // Comments posted from the assessor dashboard
    Route::post('/assessor/assess/{id}/comment',
        [LeaveCommentController::class, 'store'])->name('assessor.comment');

    // Managing Partner review of HOD applications
    Route::post('/assessor/mp-review/{id}/approve',
        [AssessorController::class, 'mpApprove'])->name('assessor.mp-approve');
    Route::post('/assessor/mp-review/{id}/reject',
        [AssessorController::class, 'mpReject'])->name('assessor.mp-reject');
});

// ── Admin routes ──────────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'admin'])->group(function () {
    // Dashboard
    Route::get('/admin/dashboard', [HomeController::class, 'index'])->name('admin.dashboard');

    // Employee Management
    Route::get('/admin/manage-employee', [EmployeeController::class, 'index'])->name('admin.manage-employee');
    Route::post('/admin/approve-employee/{id}', [EmployeeController::class, 'approveEmployee'])->name('admin.approve-employee');
    Route::get('/admin/block-employee/{id}', [EmployeeController::class, 'blockEmployee'])->name('admin.block-employee');
    Route::get('/admin/unblock-employee/{id}', [EmployeeController::class, 'unblockEmployee'])->name('admin.unblock-employee');
    Route::post('/admin/update-employee-profile/{id}', [EmployeeController::class, 'updateProfile'])->name('admin.update-employee-profile');
    Route::get('/admin/export-employees', [EmployeeController::class, 'exportToExcel'])->name('admin.export-employees');

    // Leave Management
    Route::get('/admin/manage-leave', [LeaveManageController::class, 'index'])->name('admin.manage-leave');

    // CHANGED GET → POST (forms must use POST for state-changing actions)
    Route::post('/admin/approve-leave/{id}', [LeaveManageController::class, 'approveLeave'])->name('admin.approve-leave');
    Route::post('/admin/reject-leave/{id}',  [LeaveManageController::class, 'rejectLeave'])->name('admin.reject-leave');
    Route::post('/admin/restore-leave/{id}', [LeaveManageController::class, 'restoreLeave'])->name('admin.restore-leave');

    // ADD — admin comments on any leave request
    Route::post('/admin/leave/{id}/comment', [LeaveCommentController::class, 'store'])->name('admin.comment');

    Route::get('/admin/export-leave-requests', [LeaveManageController::class, 'exportToExcel'])->name('admin.export-leave-requests');
    Route::get('/admin/print-leave-report', [LeaveManageController::class, 'printReport'])->name('admin.print-leave-report');
    Route::get('/admin/department-report/{department}', [LeaveManageController::class, 'departmentReport'])->name('admin.department-report');

    // ADD — role management
    Route::get('/admin/manage-employee/role/{id}', [EmployeeController::class, 'updateRole'])->name('admin.update-role');
});

Route::get('/test-email', function () {
    $mailData = [
        'employee_name' => 'John Doe',
        'leave_type' => 'Annual Leave',
        'leave_from' => '2026-07-15',
        'leave_to' => '2026-07-25',
        'working_days' => 8,
        'reason' => 'Family vacation',
    ];
    
    return new App\Mail\LeaveRequestSubmission($mailData);
});

// API routes for calendar (authenticated users)
Route::middleware(['auth'])->group(function () {
    Route::get('/api/leave-calendar/blocked-dates', [LeaveCalendarController::class, 'getBlockedDates'])
        ->name('api.leave-calendar.blocked-dates');
    
    Route::post('/api/leave-calendar/check-availability', [LeaveCalendarController::class, 'checkDateAvailability'])
        ->name('api.leave-calendar.check-availability');
});

require __DIR__.'/auth.php';
