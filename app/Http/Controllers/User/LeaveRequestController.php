<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLeaveRequestRequest;
use App\Http\Requests\UpdateLeaveRequestRequest;
use App\Mail\LeaveRequestSubmission;
use App\Services\LeaveValidationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class LeaveRequestController extends Controller
{
    protected $validationService;

    public function __construct(LeaveValidationService $validationService)
    {
        $this->validationService = $validationService;
    }

    public function create()
    {
        $employee = auth()->user()->employee;
        
        if (!$employee) {
            return redirect()->route('dashboard')
                ->with('error', 'Employee record not found. Please contact administrator.');
        }

        // Get annual leave statistics
        $annualLeaveStats = $this->validationService->getAnnualLeaveStats($employee);

        return view('user.leave-request.create', compact('employee', 'annualLeaveStats'));
    }

    public function store(StoreLeaveRequestRequest $request)
    {
        $employee = $request->user()->employee;

        if (!$employee) {
            return redirect()->route('dashboard')
                ->with('error', 'Employee record not found.');
        }

        $data = $request->validated();

        // Handle medical certificate upload
        if ($request->hasFile('medical_certificate')) {
            $path = $request->file('medical_certificate')->store('medical_certificates', 'public');
            $data['medical_certificate'] = $path;
        }

        // Handle supporting document upload for study leave
        if ($request->hasFile('supporting_document')) {
            $path = $request->file('supporting_document')->store('supporting_documents', 'public');
            $data['supporting_document'] = $path;
        }

        // Calculate working days
        $leaveFrom = \Carbon\Carbon::parse($data['leave_from']);
        $leaveTo = \Carbon\Carbon::parse($data['leave_to']);
        
        $data['working_days_count'] = $this->validationService->calculateWorkingDays($leaveFrom, $leaveTo);
        
        // Check if annual leave is outside recommended period
        if ($data['leave_type'] === 'Annual Leave') {
            $data['is_out_of_recommended_period'] = !$this->validationService->isWithinRecommendedPeriod($leaveFrom, $leaveTo);
        }

        // Auto-convert to unpaid if necessary (except maternity)
        $totalDays = $leaveFrom->diffInDays($leaveTo) + 1;
        $originalLeaveType = $data['leave_type'];
        $data['leave_type'] = $this->validationService->autoConvertLeaveType($data['leave_type'], $totalDays);

        if ($originalLeaveType !== $data['leave_type']) {
            session()->flash('warning', "Your leave request exceeds 30 days and has been automatically converted to 'Leave Without Pay'.");
        }

        // Ensure status is set to pending
        $data['status'] = 'pending';

        // Create leave request
        $leaveRequest = $employee->leaveRequests()->create($data);

        // Send email notification
        try {
            $mailData = [
                'employee_name' => $employee->user->name,
                'leave_from' => $data['leave_from'],
                'leave_to' => $data['leave_to'],
                'leave_type' => $data['leave_type'],
                'reason' => $data['reason'] ?? '',
                'working_days' => $data['working_days_count'],
            ];

            Mail::to($employee->user->email)->send(new LeaveRequestSubmission($mailData));
        } catch (\Exception $e) {
            \Log::warning('Failed to send leave submission email: ' . $e->getMessage());
        }

        $successMessage = 'Leave request submitted successfully.';
        
        if (session()->has('leave_warnings')) {
            $warnings = session()->get('leave_warnings');
            $successMessage .= ' ' . implode(' ', $warnings);
        }

        return redirect()->route('dashboard')->with('success', $successMessage);
    }
    
    public function edit(string $id)
    {
        $employee = auth()->user()->employee;
        $leaveRequest = $employee->leaveRequests()->findOrFail($id);
        
        // Only allow editing if status is pending
        // (This includes rejected requests that were restored to pending)
        if (strtolower($leaveRequest->status) !== 'pending') {
            return redirect()->route('dashboard')
                ->with('error', 'Only pending leave requests can be edited. This request is ' . $leaveRequest->status . '.');
        }
        
        // Get annual leave statistics
        $annualLeaveStats = $this->validationService->getAnnualLeaveStats($employee);

        return view('user.leave-request.edit', compact('leaveRequest', 'employee', 'annualLeaveStats'));
    }

    public function update(UpdateLeaveRequestRequest $request, string $id)
    {
        $employee = auth()->user()->employee;
        $leaveRequest = $employee->leaveRequests()->findOrFail($id);

        $data = $request->validated();

        // Handle medical certificate upload
        if ($request->hasFile('medical_certificate')) {
            if ($leaveRequest->medical_certificate && Storage::disk('public')->exists($leaveRequest->medical_certificate)) {
                Storage::disk('public')->delete($leaveRequest->medical_certificate);
            }
            
            $path = $request->file('medical_certificate')->store('medical_certificates', 'public');
            $data['medical_certificate'] = $path;
        }

        // Handle supporting document upload
        if ($request->hasFile('supporting_document')) {
            if ($leaveRequest->supporting_document && Storage::disk('public')->exists($leaveRequest->supporting_document)) {
                Storage::disk('public')->delete($leaveRequest->supporting_document);
            }
            
            $path = $request->file('supporting_document')->store('supporting_documents', 'public');
            $data['supporting_document'] = $path;
        }

        // Recalculate working days
        $leaveFrom = \Carbon\Carbon::parse($data['leave_from']);
        $leaveTo = \Carbon\Carbon::parse($data['leave_to']);
        
        $data['working_days_count'] = $this->validationService->calculateWorkingDays($leaveFrom, $leaveTo);
        
        if ($data['leave_type'] === 'Annual Leave') {
            $data['is_out_of_recommended_period'] = !$this->validationService->isWithinRecommendedPeriod($leaveFrom, $leaveTo);
        }

        // Auto-convert to unpaid if necessary
        $totalDays = $leaveFrom->diffInDays($leaveTo) + 1;
        $originalLeaveType = $data['leave_type'];
        $data['leave_type'] = $this->validationService->autoConvertLeaveType($data['leave_type'], $totalDays);

        // Reset status to pending if dates or type changed
        if ($leaveRequest->isDirty(['leave_from', 'leave_to', 'leave_type'])) {
            $data['status'] = 'pending';
        }

        $leaveRequest->update($data);

        $successMessage = 'Leave request updated successfully.';
        
        if ($originalLeaveType !== $data['leave_type']) {
            $successMessage .= " Your leave has been converted to 'Leave Without Pay' due to exceeding 30 days.";
        }

        return redirect()->route('dashboard')->with('success', $successMessage);
    }

    public function destroy(string $id): \Illuminate\Http\RedirectResponse
    {
        $employee = auth()->user()->employee;
        $leaveRequest = $employee->leaveRequests()->findOrFail($id);

        // Delete medical certificate if exists
        if ($leaveRequest->medical_certificate && Storage::disk('public')->exists($leaveRequest->medical_certificate)) {
            Storage::disk('public')->delete($leaveRequest->medical_certificate);
        }

        // Delete supporting document if exists
        if ($leaveRequest->supporting_document && Storage::disk('public')->exists($leaveRequest->supporting_document)) {
            Storage::disk('public')->delete($leaveRequest->supporting_document);
        }

        $leaveRequest->delete();

        return redirect()->route('dashboard')->with('success', 'Leave request deleted successfully.');
    }
}
