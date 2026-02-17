<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\LeaveComment;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;

class LeaveCommentController extends Controller
{
    public function store(Request $request, string $id)
    {
        $request->validate([
            'comment' => 'required|string|min:2|max:1000',
        ]);

        $leaveRequest = LeaveRequest::findOrFail($id);
        $user         = auth()->user();

        // Authorise using policy
        $this->authorize('comment', $leaveRequest);

        LeaveComment::create([
            'leave_request_id' => $leaveRequest->id,
            'user_id'          => $user->id,
            'body'             => $request->comment,
            'type'             => 'comment',
            'visibility'       => $user->isAdmin() ? 'all' : 'all',
        ]);

        return redirect()->back()->with('success', 'Comment added.');
    }
}
