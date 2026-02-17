<?php

namespace App\Policies;

use App\Models\LeaveRequest;
use App\Models\User;

class LeaveRequestPolicy
{
    /** Can the user assess (approve/reject at assessment stage)? */
    public function assess(User $user, LeaveRequest $leaveRequest): bool
    {
        return $user->canAssess($leaveRequest);
    }

    /** Can the user post a comment? */
    public function comment(User $user, LeaveRequest $leaveRequest): bool
    {
        if ($user->isAdmin()) return true;

        // Assessor can comment on their department's requests
        if ($user->isAssessor()) {
            return $leaveRequest->employee?->department === $user->heads_department;
        }

        // Managing partner can comment on HOD requests
        if ($user->isManagingPartner()) {
            return in_array($leaveRequest->employee?->user?->role, ['assessor', 'managing_partner']);
        }

        // Employee can comment on their own request
        return $leaveRequest->employee?->user_id === $user->id;
    }

    /** Can the user see this request? */
    public function view(User $user, LeaveRequest $leaveRequest): bool
    {
        if ($user->isAdmin() || $user->isManagingPartner()) return true;

        if ($user->isAssessor()) {
            return $leaveRequest->employee?->department === $user->heads_department;
        }

        return $leaveRequest->employee?->user_id === $user->id;
    }
}
