<?php

namespace App\Http\Controllers\Assessor;

use App\Http\Controllers\Controller;
use App\Mail\LeaveAssessmentRejected;
use App\Mail\LeaveAdminNotification;
use App\Models\LeaveComment;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AssessorController extends Controller
{
    // ── Dashboard ─────────────────────────────────────────────────────────────

    public function dashboard(Request $request)
    {
        $user = auth()->user();

        // Build base query depending on role
        $query = LeaveRequest::with(['employee.user', 'comments.user']);

        if ($user->isAssessor()) {
            // HOD sees their department only, excluding other HODs/MP
            $query->whereHas('employee', fn($q) =>
                $q->where('department', $user->heads_department)
            )->whereHas('employee.user', fn($q) =>
                $q->whereNotIn('role', ['assessor', 'managing_partner', 'admin'])
            );

        } elseif ($user->isManagingPartner()) {
            // Managing Partner sees HOD applications only
            $query->whereHas('employee.user', fn($q) =>
                $q->where('role', 'assessor')
            );
        }
        // Admin sees everything (no additional filter)

        // Filter by assessment stage relevant to this role
        if ($user->isAssessor()) {
            $pendingRequests  = (clone $query)->where('status', 'pending')
                                              ->whereNull('assessment_status')
                                              ->latest()->paginate(10, ['*'], 'pending_page');
            $assessedRequests = (clone $query)->whereIn('assessment_status', ['assessed_approved','assessed_rejected'])
                                              ->latest()->paginate(10, ['*'], 'assessed_page');
        } elseif ($user->isManagingPartner()) {
            // Show HOD apps that have been assessed_approved but not yet MP-reviewed
            $pendingRequests  = (clone $query)->where('assessment_status', 'assessed_approved')
                                              ->whereNull('mp_status')
                                              ->latest()->paginate(10, ['*'], 'pending_page');
            $assessedRequests = (clone $query)->whereNotNull('mp_status')
                                              ->latest()->paginate(10, ['*'], 'assessed_page');
        } else {
            $pendingRequests  = (clone $query)->where('status', 'pending')->latest()->paginate(10, ['*'], 'pending_page');
            $assessedRequests = (clone $query)->whereIn('status', ['Approved','Rejected'])->latest()->paginate(10, ['*'], 'assessed_page');
        }

        return view('assessor.dashboard', compact(
            'pendingRequests',
            'assessedRequests',
            'user'
        ));
    }

    // ── HOD / Assessor: approve ────────────────────────────────────────────────

    public function approve(Request $request, string $id)
    {
        $leaveRequest = LeaveRequest::with('employee.user')->findOrFail($id);
        $user         = auth()->user();

        abort_unless($user->canAssess($leaveRequest), 403);

        $leaveRequest->assessment_status = 'assessed_approved';
        $leaveRequest->assessed_by       = $user->id;
        $leaveRequest->assessed_at       = now();
        $leaveRequest->save();

        // Add optional comment
        if ($request->filled('comment')) {
            LeaveComment::create([
                'leave_request_id' => $leaveRequest->id,
                'user_id'          => $user->id,
                'body'             => $request->comment,
                'type'             => 'comment',
                'visibility'       => 'all',
            ]);
        }

        return redirect()->back()
            ->with('success', 'Leave request approved at assessment stage. Awaiting Admin confirmation.');
    }

    // ── HOD / Assessor: reject ─────────────────────────────────────────────────

    public function reject(Request $request, string $id)
    {
        $request->validate([
            'comment'    => 'required|string|min:10|max:1000',
            'suggestion' => 'nullable|string|max:1000',
        ]);

        $leaveRequest = LeaveRequest::with('employee.user')->findOrFail($id);
        $user         = auth()->user();

        abort_unless($user->canAssess($leaveRequest), 403);

        $leaveRequest->assessment_status = 'assessed_rejected';
        $leaveRequest->status            = 'Rejected';
        $leaveRequest->assessed_by       = $user->id;
        $leaveRequest->assessed_at       = now();
        $leaveRequest->save();

        // Build rejection notice body
        $suggestion = $request->filled('suggestion')
            ? "\n\n**Suggested way forward:** " . $request->suggestion
            : '';

        $noticeBody = "Your leave request ({$leaveRequest->leave_type}, "
            . $leaveRequest->leave_from->format('M d') . ' – '
            . $leaveRequest->leave_to->format('M d, Y')
            . ") has been **rejected at assessment stage**.\n\n"
            . "**Reason:** " . $request->comment
            . $suggestion;

        LeaveComment::create([
            'leave_request_id' => $leaveRequest->id,
            'user_id'          => $user->id,
            'body'             => $noticeBody,
            'type'             => 'rejection_notice',
            'visibility'       => 'all',
        ]);

        // Email the applicant
        try {
            $employee = $leaveRequest->employee;
            Mail::to($employee->user->email)->send(new LeaveAssessmentRejected([
                'employee_name' => $employee->user->name,
                'leave_type'    => $leaveRequest->leave_type,
                'leave_from'    => $leaveRequest->leave_from,
                'leave_to'      => $leaveRequest->leave_to,
                'reason'        => $request->comment,
                'suggestion'    => $request->suggestion ?? '',
                'assessor_name' => $user->name,
            ]));
        } catch (\Exception $e) {
            \Log::warning('Assessment rejection email failed: ' . $e->getMessage());
        }

        return redirect()->back()
            ->with('success', 'Leave request rejected. The applicant has been notified.');
    }

    // ── Managing Partner: approve HOD application ─────────────────────────────

    public function mpApprove(Request $request, string $id)
    {
        abort_unless(auth()->user()->isManagingPartner() || auth()->user()->isAdmin(), 403);

        $leaveRequest = LeaveRequest::findOrFail($id);
        $leaveRequest->mp_status       = 'mp_approved';
        $leaveRequest->mp_reviewed_by  = auth()->id();
        $leaveRequest->mp_reviewed_at  = now();
        $leaveRequest->save();

        if ($request->filled('comment')) {
            LeaveComment::create([
                'leave_request_id' => $leaveRequest->id,
                'user_id'          => auth()->id(),
                'body'             => $request->comment,
                'type'             => 'comment',
                'visibility'       => 'all',
            ]);
        }

        return redirect()->back()
            ->with('success', 'HOD leave request approved by Managing Partner. Awaiting Admin confirmation.');
    }

    // ── Managing Partner: reject HOD application ──────────────────────────────

    public function mpReject(Request $request, string $id)
    {
        $request->validate([
            'comment'    => 'required|string|min:10',
            'suggestion' => 'nullable|string|max:1000',
        ]);

        abort_unless(auth()->user()->isManagingPartner() || auth()->user()->isAdmin(), 403);

        $leaveRequest = LeaveRequest::with('employee.user')->findOrFail($id);
        $leaveRequest->mp_status      = 'mp_rejected';
        $leaveRequest->status         = 'Rejected';
        $leaveRequest->mp_reviewed_by = auth()->id();
        $leaveRequest->mp_reviewed_at = now();
        $leaveRequest->save();

        $suggestion = $request->filled('suggestion')
            ? "\n\n**Suggested way forward:** " . $request->suggestion
            : '';

        LeaveComment::create([
            'leave_request_id' => $leaveRequest->id,
            'user_id'          => auth()->id(),
            'body'             => "Your leave request was **rejected by the Managing Partner**.\n\n"
                . "**Reason:** " . $request->comment . $suggestion,
            'type'             => 'rejection_notice',
            'visibility'       => 'all',
        ]);

        try {
            Mail::to($leaveRequest->employee->user->email)->send(new LeaveAssessmentRejected([
                'employee_name' => $leaveRequest->employee->user->name,
                'leave_type'    => $leaveRequest->leave_type,
                'leave_from'    => $leaveRequest->leave_from,
                'leave_to'      => $leaveRequest->leave_to,
                'reason'        => $request->comment,
                'suggestion'    => $request->suggestion ?? '',
                'assessor_name' => auth()->user()->name . ' (Managing Partner)',
            ]));
        } catch (\Exception $e) {
            \Log::warning('MP rejection email failed: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'HOD leave rejected. Applicant notified.');
    }
}
