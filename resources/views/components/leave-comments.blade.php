@props(['leaveRequest', 'showForm' => true])

<div class="mt-4 border-t border-gray-200 pt-4">
    <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"/>
        </svg>
        Comments & Notices
        @if($leaveRequest->comments->count())
            <span class="bg-gray-100 text-gray-600 text-xs px-2 py-0.5 rounded-full">
                {{ $leaveRequest->comments->count() }}
            </span>
        @endif
    </h4>

    {{-- Comment Thread --}}
    @forelse($leaveRequest->comments as $comment)
        <div class="mb-3 @if($comment->isRejectionNotice()) bg-red-50 border border-red-200 @else bg-gray-50 border border-gray-200 @endif rounded-lg p-3">
            <div class="flex items-center justify-between mb-1">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold text-gray-800">
                        {{ $comment->user->name }}
                    </span>
                    <span class="text-xs px-1.5 py-0.5 rounded text-gray-500
                        @if($comment->user->isAdmin()) bg-purple-100 text-purple-700
                        @elseif($comment->user->isAssessor()) bg-blue-100 text-blue-700
                        @elseif($comment->user->isManagingPartner()) bg-indigo-100 text-indigo-700
                        @else bg-gray-100 @endif">
                        {{ ucfirst(str_replace('_',' ', $comment->user->role)) }}
                    </span>
                    @if($comment->isRejectionNotice())
                        <span class="text-xs px-1.5 py-0.5 bg-red-100 text-red-700 rounded font-semibold">
                            Rejection Notice
                        </span>
                    @endif
                </div>
                <span class="text-xs text-gray-400">
                    {{ $comment->created_at->diffForHumans() }}
                </span>
            </div>
            <div class="text-sm text-gray-700 whitespace-pre-line">{{ $comment->body }}</div>
        </div>
    @empty
        <p class="text-xs text-gray-400 italic mb-3">No comments yet.</p>
    @endforelse

    {{-- Add comment form --}}
    @if($showForm && auth()->user()->can('comment', $leaveRequest))
        <form action="{{ route('leave-comment.store', $leaveRequest->id) }}" method="POST" class="mt-3">
            @csrf
            <div class="flex gap-2">
                <input type="text" name="comment"
                       placeholder="Add a comment..."
                       class="flex-1 text-sm border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-indigo-300">
                <button type="submit"
                        class="px-3 py-2 bg-indigo-600 text-white text-xs font-semibold rounded-md hover:bg-indigo-700">
                    Send
                </button>
            </div>
        </form>
    @endif
</div>