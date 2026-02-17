<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @if($user->isManagingPartner())
                Managing Partner — Assessment Dashboard
            @elseif($user->isAssessor())
                Assessment Dashboard — {{ $user->heads_department }} Department
            @else
                Admin Assessment Overview
            @endif
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- Flash messages --}}
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            {{-- ── PENDING ASSESSMENT ── --}}
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="bg-yellow-500 text-white px-6 py-4 rounded-t-lg flex justify-between items-center">
                    <h3 class="font-semibold text-lg">Awaiting Your Assessment</h3>
                    <span class="bg-white text-yellow-600 rounded-full px-3 py-1 text-sm font-bold">
                        {{ $pendingRequests->total() }}
                    </span>
                </div>

                <div class="divide-y divide-gray-100">
                    @forelse($pendingRequests as $request)
                        <div class="p-6">
                            <x-leave-comments :leave-request="$request" :show-form="true" />
                            {{-- Header --}}
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-3">
                                <div>
                                    <span class="font-semibold text-gray-900">
                                        {{ $request->employee->user->name }}
                                    </span>
                                    <span class="ml-2 text-sm text-gray-500">
                                        {{ $request->employee->department }}
                                    </span>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $request->leave_type }}
                                </span>
                            </div>

                            {{-- Details --}}
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm text-gray-600 mb-4">
                                <div>
                                    <span class="font-medium block">From</span>
                                    {{ \Carbon\Carbon::parse($request->leave_from)->format('M d, Y') }}
                                </div>
                                <div>
                                    <span class="font-medium block">To</span>
                                    {{ \Carbon\Carbon::parse($request->leave_to)->format('M d, Y') }}
                                </div>
                                <div>
                                    <span class="font-medium block">Working Days</span>
                                    {{ $request->working_days_count ?? '—' }}
                                </div>
                                <div>
                                    <span class="font-medium block">Applied</span>
                                    {{ $request->created_at->format('M d, Y') }}
                                </div>
                            </div>

                            @if($request->reason)
                                <p class="text-sm text-gray-600 mb-4 bg-gray-50 rounded p-3">
                                    <span class="font-medium">Reason:</span> {{ $request->reason }}
                                </p>
                            @endif

                            {{-- Early emergency notice --}}
                            @if($request->is_pre_annual_emergency)
                                <div class="mb-4 p-3 bg-amber-50 border-l-4 border-amber-400 text-amber-800 text-sm rounded">
                                    ⚠ This is an <strong>early emergency leave</strong> (employee not yet 12-month eligible).
                                    Days will be deducted from annual leave once eligible.
                                </div>
                            @endif

                            {{-- Approve form --}}
                            <div class="flex flex-col sm:flex-row gap-3 mt-4">
                                <form action="{{ auth()->user()->isManagingPartner()
                                        ? route('assessor.mp-approve', $request->id)
                                        : route('assessor.approve', $request->id) }}"
                                      method="POST" class="flex-1">
                                    @csrf
                                    <div class="flex gap-2">
                                        <input type="text" name="comment"
                                               placeholder="Optional comment..."
                                               class="flex-1 text-sm border border-gray-300 rounded-md px-3 py-2">
                                        <button type="submit"
                                                class="px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-md hover:bg-green-700">
                                            ✓ Approve
                                        </button>
                                    </div>
                                </form>

                                {{-- Reject modal trigger --}}
                                <button onclick="openRejectModal('{{ $request->id }}')"
                                        class="px-4 py-2 bg-red-600 text-white text-sm font-semibold rounded-md hover:bg-red-700">
                                    ✗ Reject
                                </button>
                            </div>

                            {{-- Comments thread --}}
                            @if($request->comments->count())
                                <div class="mt-4 border-t pt-3 space-y-2">
                                    @foreach($request->comments as $comment)
                                        <div class="text-xs text-gray-600 bg-gray-50 rounded p-2">
                                            <span class="font-semibold">{{ $comment->user->name }}</span>
                                            <span class="text-gray-400 ml-1">{{ $comment->created_at->diffForHumans() }}</span>
                                            <p class="mt-1">{{ $comment->body }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        {{-- Reject Modal --}}
                        <div id="reject-modal-{{ $request->id }}"
                             class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 z-50 flex items-center justify-center p-4">
                            <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6">
                                <h4 class="font-semibold text-gray-900 mb-4">Reject Leave Request</h4>
                                <form action="{{ auth()->user()->isManagingPartner()
                                        ? route('assessor.mp-reject', $request->id)
                                        : route('assessor.reject', $request->id) }}"
                                      method="POST">
                                    @csrf
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Reason for Rejection <span class="text-red-500">*</span>
                                        </label>
                                        <textarea name="comment" rows="4" required minlength="10"
                                                  placeholder="Please explain clearly why this request is rejected..."
                                                  class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-red-300"></textarea>
                                    </div>
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Suggested Way Forward <span class="text-gray-400">(optional)</span>
                                        </label>
                                        <textarea name="suggestion" rows="3"
                                                  placeholder="e.g. Please re-apply for dates in July–September..."
                                                  class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300"></textarea>
                                    </div>
                                    <div class="flex gap-3 justify-end">
                                        <button type="button"
                                                onclick="closeRejectModal('{{ $request->id }}')"
                                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md text-sm hover:bg-gray-400">
                                            Cancel
                                        </button>
                                        <button type="submit"
                                                class="px-4 py-2 bg-red-600 text-white rounded-md text-sm font-semibold hover:bg-red-700">
                                            Confirm Rejection
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="p-10 text-center text-gray-500">
                            <svg class="w-16 h-16 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p>No leave requests awaiting assessment.</p>
                        </div>
                    @endforelse
                </div>

                @if($pendingRequests->hasPages())
                    <div class="px-6 pb-4">{{ $pendingRequests->links() }}</div>
                @endif
            </div>

            {{-- ── ASSESSED / HISTORY ── --}}
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="bg-gray-600 text-white px-6 py-4 rounded-t-lg flex justify-between items-center">
                    <h3 class="font-semibold text-lg">Previously Assessed</h3>
                    <span class="bg-white text-gray-600 rounded-full px-3 py-1 text-sm font-bold">
                        {{ $assessedRequests->total() }}
                    </span>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($assessedRequests as $request)
                        <div class="p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                            <div>
                                <span class="font-medium text-gray-900">{{ $request->employee->user->name }}</span>
                                <span class="mx-2 text-gray-400">·</span>
                                <span class="text-sm text-gray-600">{{ $request->leave_type }}</span>
                                <span class="mx-2 text-gray-400">·</span>
                                <span class="text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($request->leave_from)->format('M d') }} –
                                    {{ \Carbon\Carbon::parse($request->leave_to)->format('M d, Y') }}
                                </span>
                            </div>
                            <div class="flex items-center gap-2">
                                @if(strtolower($request->status) === 'approved')
                                    <span class="px-2 py-0.5 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Approved</span>
                                @elseif(strtolower($request->status) === 'rejected')
                                    <span class="px-2 py-0.5 bg-red-100 text-red-800 text-xs font-semibold rounded-full">Rejected</span>
                                @else
                                    <span class="px-2 py-0.5 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">{{ ucfirst($request->assessment_status ?? $request->status) }}</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-400 text-sm">No assessed requests yet.</div>
                    @endforelse
                </div>
                @if($assessedRequests->hasPages())
                    <div class="px-6 pb-4">{{ $assessedRequests->links() }}</div>
                @endif
            </div>

        </div>
    </div>

    @push('scripts')
    <script>
        function openRejectModal(id) {
            document.getElementById('reject-modal-' + id).classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        function closeRejectModal(id) {
            document.getElementById('reject-modal-' + id).classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('[id^="reject-modal-"]').forEach(m => {
                    m.classList.add('hidden');
                });
                document.body.style.overflow = 'auto';
            }
        });
    </script>
    @endpush
</x-app-layout>
