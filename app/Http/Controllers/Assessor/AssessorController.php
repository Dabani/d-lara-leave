<?php

namespace App\Http\Controllers\Assessor;

use App\Http\Controllers\Controller;
use App\Mail\LeaveAssessmentRejected;
use App\Models\LeaveComment;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AssessorController extends Controller
{
    /**
     * Dashboard with two-column layout:
     * Left: Pending assessment | Right: Assessed history
     * 
     * MANAGING PARTNER sees:
     *  - Pending: HOD leave applications awaiting MP approval
     *  - For Info: All other leave applications (read-only overview)
     * 
     * HOD/ASSESSOR sees:
     *  - Pending: Regular employees in their department
     *  - Assessed: Their assessment history
     */
    public function dashboard(Request $request)
    {
        $user = auth()->user();

        if ($user->isManagingPartner()) {
            // ═══════════════════════════════════════════════════════════════
            // MANAGING PARTNER DASHBOARD
            // ═══════════════════════════════════════════════════════════════
            
            // PRIMARY: HOD leave applications awaiting MP approval
            // These are HOD applications that are pending and have no MP decision yet
            $pendingRequests = LeaveRequest::whereHas('employee.user', function($q) {
                    $q->where('role', 'assessor'); // Only HODs
                })
                ->where('status', 'pending')
                ->whereNull('mp_status') // No MP decision yet
                ->with(['employee.user', 'comments.user', 'assessor'])
                ->latest()
                ->paginate(10, ['*'], 'pending_page');
            
            // HISTORY: All HOD applications MP has reviewed
            $assessedRequests = LeaveRequest::whereHas('employee.user', function($q) {
                    $q->where('role', 'assessor');
                })
                ->where('mp_reviewed_by', $user->id) // Reviewed by this MP
                ->with(['employee.user', 'comments.user'])
                ->latest()
                ->paginate(10, ['*'], 'assessed_page');

            // FOR INFO: All other leave applications (regular employees)
            // This gives MP visibility into the entire organization
            $infoRequests = LeaveRequest::whereHas('employee.user', function($q) {
                    $q->where('role', 'user'); // Regular employees only
                })
                ->with(['employee.user', 'assessor', 'comments.user'])
                ->latest()
                ->paginate(15, ['*'], 'info_page');

        } elseif ($user->isAssessor()) {
            // ═══════════════════════════════════════════════════════════════
            // HOD/ASSESSOR DASHBOARD
            // ═══════════════════════════════════════════════════════════════
            
            // PENDING: Regular employees in this HOD's department awaiting assessment
            $pendingRequests = LeaveRequest::whereHas('employee', function($q) use ($user) {
                    $q->where('department', $user->heads_department);
                })
                ->whereHas('employee.user', function($q) {
                    $q->where('role', 'user'); // Only regular employees
                })
                ->where('status', 'pending')
                ->whereNull('assessment_status') // Not yet assessed
                ->with(['employee.user', 'comments.user'])
                ->latest()
                ->paginate(10, ['*'], 'pending_page');
            
            // ASSESSED: Requests this HOD has reviewed
            $assessedRequests = LeaveRequest::whereHas('employee', function($q) use ($user) {
                    $q->where('department', $user->heads_department);
                })
                ->whereHas('employee.user', function($q) {
                    $q->where('role', 'user');
                })
                ->where('assessed_by', $user->id)
                ->with(['employee.user', 'comments.user'])
                ->latest()
                ->paginate(10, ['*'], 'assessed_page');

            // No info requests for HODs
            $infoRequests = collect();

        } else {
            // ═══════════════════════════════════════════════════════════════
            // ADMIN FALLBACK (if they access this route)
            // ═══════════════════════════════════════════════════════════════
            
            $pendingRequests = LeaveRequest::where('status', 'pending')
                ->with(['employee.user', 'comments.user'])
                ->latest()
                ->paginate(10, ['*'], 'pending_page');
                
            $assessedRequests = LeaveRequest::whereIn('status', ['Approved', 'Rejected'])
                ->with(['employee.user', 'comments.user'])
                ->latest()
                ->paginate(10, ['*'], 'assessed_page');

            $infoRequests = collect();
        }

        return view('assessor.dashboard', compact(
            'pendingRequests',
            'assessedRequests',
            'infoRequests',
            'user'
        ));
    }

    /**
     * HOD / Assessor approves a regular employee's leave request
     */
    public function approve(Request $request, string $id)
    {
        $leaveRequest = LeaveRequest::with('employee.user')->findOrFail($id);
        $user = auth()->user();

        // Verify this HOD can assess this request
        abort_unless(
            $user->isAssessor() && 
            $leaveRequest->employee->department === $user->heads_department &&
            $leaveRequest->employee->user->role === 'user',
            403,
            'You are not authorized to assess this leave request.'
        );

        $leaveRequest->assessment_status = 'assessed_approved';
        $leaveRequest->assessed_by = $user->id;
        $leaveRequest->assessed_at = now();
        $leaveRequest->save();

        // Add optional comment
        if ($request->filled('comment')) {
            LeaveComment::create([
                'leave_request_id' => $leaveRequest->id,
                'user_id' => $user->id,
                'body' => $request->comment,
                'type' => 'comment',
                'visibility' => 'all',
            ]);
        }

        return redirect()->back()
            ->with('success', 'Leave request approved. Awaiting Admin confirmation.');
    }

    /**
     * HOD / Assessor rejects a regular employee's leave request
     */
    public function reject(Request $request, string $id)
    {
        $request->validate([
            'comment' => 'required|string|min:10|max:1000',
            'suggestion' => 'nullable|string|max:1000',
        ]);

        $leaveRequest = LeaveRequest::with('employee.user')->findOrFail($id);
        $user = auth()->user();

        // Verify this HOD can assess this request
        abort_unless(
            $user->isAssessor() && 
            $leaveRequest->employee->department === $user->heads_department &&
            $leaveRequest->employee->user->role === 'user',
            403,
            'You are not authorized to assess this leave request.'
        );

        $leaveRequest->assessment_status = 'assessed_rejected';
        $leaveRequest->status = 'Rejected';
        $leaveRequest->assessed_by = $user->id;
        $leaveRequest->assessed_at = now();
        $leaveRequest->save();

        // Build rejection notice
        $suggestion = $request->filled('suggestion')
            ? "\n\n**Suggested way forward:** " . $request->suggestion
            : '';

        $noticeBody = "Your leave request ({$leaveRequest->leave_type}, "
            . $leaveRequest->leave_from->format('M d') . ' – '
            . $leaveRequest->leave_to->format('M d, Y')
            . ") has been **rejected by your HOD**.\n\n"
            . "**Reason:** " . $request->comment
            . $suggestion;

        LeaveComment::create([
            'leave_request_id' => $leaveRequest->id,
            'user_id' => $user->id,
            'body' => $noticeBody,
            'type' => 'rejection_notice',
            'visibility' => 'all',
        ]);

        // Email the applicant
        try {
            $employee = $leaveRequest->employee;
            Mail::to($employee->user->email)->send(new LeaveAssessmentRejected([
                'employee_name' => $employee->user->name,
                'leave_type' => $leaveRequest->leave_type,
                'leave_from' => $leaveRequest->leave_from,
                'leave_to' => $leaveRequest->leave_to,
                'reason' => $request->comment,
                'suggestion' => $request->suggestion ?? '',
                'assessor_name' => $user->name . ' (HOD)',
            ]));
        } catch (\Exception $e) {
            \Log::warning('Assessment rejection email failed: ' . $e->getMessage());
        }

        return redirect()->back()
            ->with('success', 'Leave request rejected. The applicant has been notified.');
    }

    /**
     * Managing Partner approves an ASSESSOR's leave request
     */
    public function mpApprove(Request $request, string $id)
    {
        $user = auth()->user();
        abort_unless($user->isManagingPartner(), 403, 'Only Managing Partner can perform this action.');

        $leaveRequest = LeaveRequest::with('employee.user')->findOrFail($id);

        // Verify this is an assessor's leave request
        abort_unless(
            $leaveRequest->employee->user->role === 'assessor',
            403,
            'This is not an assessor leave request.'
        );

        $leaveRequest->mp_status = 'mp_approved';
        $leaveRequest->mp_reviewed_by = $user->id;
        $leaveRequest->mp_reviewed_at = now();
        // NOTE: MP approval is NOT final - still needs Admin confirmation
        $leaveRequest->assessment_status = 'assessed_approved'; // Mark as assessed for admin queue
        $leaveRequest->save();

        if ($request->filled('comment')) {
            LeaveComment::create([
                'leave_request_id' => $leaveRequest->id,
                'user_id' => $user->id,
                'body' => $request->comment,
                'type' => 'comment',
                'visibility' => 'all',
            ]);
        }

        return redirect()->back()
            ->with('success', 'HOD leave request approved. Awaiting Admin confirmation.');
    }

    /**
     * Managing Partner rejects an ASSESSOR's leave request
     */
    public function mpReject(Request $request, string $id)
    {
        $request->validate([
            'comment' => 'required|string|min:10',
            'suggestion' => 'nullable|string|max:1000',
        ]);

        $user = auth()->user();
        abort_unless($user->isManagingPartner(), 403, 'Only Managing Partner can perform this action.');

        $leaveRequest = LeaveRequest::with('employee.user')->findOrFail($id);

        // Verify this is an assessor's leave request
        abort_unless(
            $leaveRequest->employee->user->role === 'assessor',
            403,
            'This is not an assessor leave request.'
        );

        $leaveRequest->mp_status = 'mp_rejected';
        $leaveRequest->status = 'Rejected';
        $leaveRequest->mp_reviewed_by = $user->id;
        $leaveRequest->mp_reviewed_at = now();
        $leaveRequest->save();

        $suggestion = $request->filled('suggestion')
            ? "\n\n**Suggested way forward:** " . $request->suggestion
            : '';

        LeaveComment::create([
            'leave_request_id' => $leaveRequest->id,
            'user_id' => $user->id,
            'body' => "Your leave request was **rejected by the Managing Partner**.\n\n"
                . "**Reason:** " . $request->comment . $suggestion,
            'type' => 'rejection_notice',
            'visibility' => 'all',
        ]);

        try {
            Mail::to($leaveRequest->employee->user->email)->send(new LeaveAssessmentRejected([
                'employee_name' => $leaveRequest->employee->user->name,
                'leave_type' => $leaveRequest->leave_type,
                'leave_from' => $leaveRequest->leave_from,
                'leave_to' => $leaveRequest->leave_to,
                'reason' => $request->comment,
                'suggestion' => $request->suggestion ?? '',
                'assessor_name' => $user->name . ' (Managing Partner)',
            ]));
        } catch (\Exception $e) {
            \Log::warning('MP rejection email failed: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'HOD leave rejected. Applicant notified.');
    }
}
