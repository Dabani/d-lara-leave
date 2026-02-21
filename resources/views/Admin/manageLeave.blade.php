<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center py-1"> <!-- Reduced to py-1 -->
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Manage Leave Requests') }}
            </h2>
            
            <!-- Filters and Export Container - aligned to middle -->
            <div class="flex items-center gap-1"> <!-- Reduced gap to gap-1 -->
                <x-search-box 
                    route="{{ route('admin.manage-leave') }}" 
                    placeholder="Search by employee name or leave type..."
                    :value="request('search')" />
                <form method="GET" action="{{ route('admin.manage-leave') }}" class="flex items-center gap-1"> <!-- Reduced gap -->
                    <select name="department" class="p-1.5 border border-gray-300 rounded-lg text-sm h-[38px] w-40"> <!-- Reduced padding to p-1.5, height to 38px -->
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->name }}" {{ $departmentFilter == $dept->name ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                    
                    <select name="year" class="p-1.5 border border-gray-300 rounded-lg text-sm h-[38px] w-28"> <!-- Reduced padding to p-1.5, height to 38px -->
                        @foreach($years as $year)
                            <option value="{{ $year }}" {{ $yearFilter == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                    
                    <button type="submit" class="px-3 py-1.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 inline-flex items-center justify-center h-[38px] whitespace-nowrap"> <!-- Reduced padding, height to 38px -->
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        Filter
                    </button>
                </form>

                <!-- Export Button -->
                <a href="{{ route('admin.export-leave-requests', ['department' => $departmentFilter, 'year' => $yearFilter]) }}" 
                   class="inline-flex items-center justify-center px-3 py-1.5 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 h-[38px] whitespace-nowrap"> <!-- Reduced padding, height to 38px -->
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export
                </a>

                <!-- Print Button -->
                <a href="{{ route('admin.print-leave-report', ['department' => $departmentFilter, 'year' => $yearFilter]) }}" 
                   target="_blank"
                   class="inline-flex items-center justify-center px-3 py-1.5 bg-gray-600 text-white text-sm font-semibold rounded-lg hover:bg-gray-700 h-[38px] whitespace-nowrap"> <!-- Reduced padding, height to 38px -->
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Print PDF
                </a>
            </div>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mx-4 mt-4 sm:mx-auto sm:max-w-7xl" style="background-color: #68D391" role="alert">
            <strong class="font-bold">Success!</strong>
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Statistics Summary - FIXED VERSION -->
    <div class="py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] }}</div>
                            <div class="text-sm text-gray-600">Pending</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">{{ $stats['approved'] }}</div>
                            <div class="text-sm text-gray-600">Approved</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-red-600">{{ $stats['rejected'] }}</div>
                            <div class="text-sm text-gray-600">Rejected</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">{{ $stats['total'] }}</div>
                            <div class="text-sm text-gray-600">Total</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Enhanced Leave Request Card Component --}}
        @php
        function getLeaveRequestCard($request) {
            $totalDays = \Carbon\Carbon::parse($request->leave_from)->diffInDays(\Carbon\Carbon::parse($request->leave_to)) + 1;
            $workingDays = $request->working_days_count ?: $totalDays;
            $employeeName = $request->employee->user->name ?? 'Unknown';
            $employeeGender = $request->employee->user->gender ?? 'N/A';
            
            return [
                'employee_name' => $employeeName,
                'gender' => $employeeGender,
                'leave_type' => $request->leave_type,
                'leave_from' => $request->leave_from,
                'leave_to' => $request->leave_to,
                'total_days' => $totalDays,
                'working_days' => $workingDays,
                'reason' => $request->reason,
                'has_certificate' => !empty($request->medical_certificate),
                'certificate_path' => $request->medical_certificate,
                'is_first_attempt' => $request->is_first_attempt,
                'is_out_of_period' => $request->is_out_of_recommended_period,
            ];
        }
        @endphp    

    <!-- Three Column Layout -->
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Pending Column -->
{{-- This snippet shows the key changes needed in admin/manageLeave.blade.php
     Replace the existing leave request cards with this structure --}}

{{-- ══════════════════════════════════════════════════════════════════════════
     PENDING SECTION — Split into "Not Yet Assessed" and "Assessed (Awaiting Admin)"
     ══════════════════════════════════════════════════════════════════════════ --}}

<div class="bg-white shadow-sm sm:rounded-lg mb-6">
    <div class="bg-yellow-500 text-white px-6 py-4 rounded-t-lg">
        <h3 class="font-semibold text-lg">🟡 Pending Leave Requests ({{ $pendingLeaveRequests->total() }})</h3>
    </div>

    {{-- SUB-TABS: Not Assessed vs Assessed --}}
    <div class="border-b border-gray-200 bg-gray-50">
        <nav class="flex px-6" aria-label="Tabs">
            <button onclick="showTab('not-assessed')" id="tab-not-assessed"
                    class="tab-button border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-4 px-6 text-sm font-medium">
                🔴 Not Yet Assessed
                <span class="ml-2 bg-red-100 text-red-600 px-2 py-0.5 rounded-full text-xs font-bold">
                    {{ $pendingLeaveRequests->where('assessment_status', null)->count() }}
                </span>
            </button>
            <button onclick="showTab('assessed')" id="tab-assessed"
                    class="tab-button border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-4 px-6 text-sm font-medium">
                🟢 Assessed (Awaiting Final Approval)
                <span class="ml-2 bg-green-100 text-green-600 px-2 py-0.5 rounded-full text-xs font-bold">
                    {{ $pendingLeaveRequests->where('assessment_status', 'assessed_approved')->count() }}
                </span>
            </button>
        </nav>
    </div>

    {{-- TAB CONTENT: Not Yet Assessed --}}
    <div id="content-not-assessed" class="tab-content">
        <div class="p-6">
            <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-400 text-red-700">
                <p class="font-semibold">⚠ These requests have not been assessed by the Head of Department yet.</p>
                <p class="text-sm mt-1">You can approve them directly (bypassing assessment) if urgent, or wait for HOD assessment.</p>
            </div>

            @php
                $notAssessed = $pendingLeaveRequests->where('assessment_status', null);
            @endphp

            @forelse($notAssessed as $request)
                <div class="mb-4 border border-red-200 rounded-lg p-4 bg-red-50">
                    {{-- Header with warning badge --}}
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <span class="font-semibold text-gray-900">{{ $request->employee->user->name }}</span>
                            <span class="ml-2 text-sm text-gray-500">{{ $request->employee->department }}</span>
                            <span class="ml-2 px-2 py-0.5 bg-red-100 text-red-700 text-xs font-bold rounded">
                                NOT ASSESSED
                            </span>
                        </div>
                        <span class="px-2 py-0.5 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">
                            {{ $request->leave_type }}
                        </span>
                    </div>

                    {{-- Details --}}
                    <div class="grid grid-cols-4 gap-3 text-sm mb-3">
                        <div>
                            <span class="font-medium block text-gray-700">From</span>
                            {{ $request->leave_from->format('M d, Y') }}
                        </div>
                        <div>
                            <span class="font-medium block text-gray-700">To</span>
                            {{ $request->leave_to->format('M d, Y') }}
                        </div>
                        <div>
                            <span class="font-medium block text-gray-700">Days</span>
                            {{ $request->working_days_count ?? '—' }}
                        </div>
                        <div>
                            <span class="font-medium block text-gray-700">Applied</span>
                            {{ $request->created_at->diffForHumans() }}
                        </div>
                    </div>

                    @if($request->reason)
                        <p class="text-sm text-gray-600 mb-3 bg-white rounded p-2 border">
                            <strong>Reason:</strong> {{ $request->reason }}
                        </p>
                    @endif

                    {{-- Comments --}}
                    @if($request->comments->count() > 0)
                        <div class="mb-3 border-t pt-3">
                            <p class="text-xs font-semibold text-gray-700 mb-2">💬 Comments</p>
                            @foreach($request->comments as $comment)
                                <div class="bg-white rounded p-2 mb-2 text-xs border">
                                    <span class="font-semibold">{{ $comment->user->name }}</span>
                                    <span class="text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                                    <p class="text-gray-700 mt-1">{{ $comment->body }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Admin Actions --}}
                    <div class="flex gap-2 mt-3">
                        <form action="{{ route('admin.approve-leave', $request->id) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded hover:bg-green-700">
                                ✓ Approve (Direct)
                            </button>
                        </form>
                        <button onclick="openAdminRejectModal({{ $request->id }})"
                                class="flex-1 px-4 py-2 bg-red-600 text-white text-sm font-semibold rounded hover:bg-red-700">
                            ✗ Reject
                        </button>
                    </div>
                </div>
            @empty
                <p class="text-center text-gray-500 py-8">All pending requests have been assessed by HOD.</p>
            @endforelse
        </div>
    </div>

    {{-- TAB CONTENT: Assessed (Awaiting Admin) --}}
    <div id="content-assessed" class="tab-content hidden">
        <div class="p-6">
            <div class="mb-4 p-4 bg-green-50 border-l-4 border-green-400 text-green-700">
                <p class="font-semibold">✅ These requests have been approved by the Head of Department.</p>
                <p class="text-sm mt-1">You can now give final approval or reject with admin override.</p>
            </div>

            @php
                $assessed = $pendingLeaveRequests->where('assessment_status', 'assessed_approved');
            @endphp

            @forelse($assessed as $request)
                <div class="mb-4 border border-green-200 rounded-lg p-4 bg-green-50">
                    {{-- Header with HOD approval badge --}}
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <span class="font-semibold text-gray-900">{{ $request->employee->user->name }}</span>
                            <span class="ml-2 text-sm text-gray-500">{{ $request->employee->department }}</span>
                            <span class="ml-2 px-2 py-0.5 bg-green-100 text-green-700 text-xs font-bold rounded">
                                ✓ ASSESSED BY {{ $request->assessor?->name ?? 'HOD' }}
                            </span>
                        </div>
                        <span class="px-2 py-0.5 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">
                            {{ $request->leave_type }}
                        </span>
                    </div>

                    {{-- Details --}}
                    <div class="grid grid-cols-4 gap-3 text-sm mb-3">
                        <div>
                            <span class="font-medium block text-gray-700">From</span>
                            {{ $request->leave_from->format('M d, Y') }}
                        </div>
                        <div>
                            <span class="font-medium block text-gray-700">To</span>
                            {{ $request->leave_to->format('M d, Y') }}
                        </div>
                        <div>
                            <span class="font-medium block text-gray-700">Days</span>
                            {{ $request->working_days_count ?? '—' }}
                        </div>
                        <div>
                            <span class="font-medium block text-gray-700">Assessed</span>
                            {{ $request->assessed_at?->diffForHumans() ?? 'Recently' }}
                        </div>
                    </div>

                    @if($request->reason)
                        <p class="text-sm text-gray-600 mb-3 bg-white rounded p-2 border">
                            <strong>Reason:</strong> {{ $request->reason }}
                        </p>
                    @endif

                    {{-- Comments --}}
                    @if($request->comments->count() > 0)
                        <div class="mb-3 border-t pt-3">
                            <p class="text-xs font-semibold text-gray-700 mb-2">💬 Comments</p>
                            @foreach($request->comments as $comment)
                                <div class="bg-white rounded p-2 mb-2 text-xs border">
                                    <span class="font-semibold">{{ $comment->user->name }}</span>
                                    <span class="text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                                    <p class="text-gray-700 mt-1">{{ $comment->body }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Add Comment Form --}}
                    <form action="{{ route('admin.comment', $request->id) }}" method="POST" class="mb-3">
                        @csrf
                        <div class="flex gap-2">
                            <input type="text" name="comment" placeholder="Add a comment..."
                                   class="flex-1 text-sm border rounded px-3 py-2">
                            <button type="submit" class="px-3 py-2 bg-indigo-600 text-white text-xs rounded hover:bg-indigo-700">
                                Send
                            </button>
                        </div>
                    </form>

                    {{-- Admin Actions --}}
                    <div class="flex gap-2">
                        <form action="{{ route('admin.approve-leave', $request->id) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded hover:bg-green-700">
                                ✓ Final Approval
                            </button>
                        </form>
                        <button onclick="openAdminRejectModal({{ $request->id }})"
                                class="flex-1 px-4 py-2 bg-red-600 text-white text-sm font-semibold rounded hover:bg-red-700">
                            ✗ Admin Reject
                        </button>
                    </div>
                </div>
            @empty
                <p class="text-center text-gray-500 py-8">No assessed requests awaiting your approval.</p>
            @endforelse
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════════
     TAB SWITCHING JAVASCRIPT
     ══════════════════════════════════════════════════════════════════════════ --}}
@push('scripts')
<script>
function showTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Remove active state from all tabs
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('border-indigo-500', 'text-indigo-600');
        button.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Show selected tab content
    document.getElementById('content-' + tabName).classList.remove('hidden');
    
    // Set active state on selected tab
    const activeTab = document.getElementById('tab-' + tabName);
    activeTab.classList.remove('border-transparent', 'text-gray-500');
    activeTab.classList.add('border-indigo-500', 'text-indigo-600');
}

// Show "not-assessed" tab by default on page load
document.addEventListener('DOMContentLoaded', function() {
    showTab('not-assessed');
});

function openAdminRejectModal(id) {
    // Your existing reject modal code
}
</script>
@endpush
                <!-- Approved Column -->
                <div class="bg-white rounded-lg shadow-sm">
                    <div class="bg-green-600 text-white p-4 rounded-t-lg">
                        <h3 class="font-semibold text-lg">Approved ({{ $approvedLeaveRequests->total() }})</h3>
                    </div>
                    <div class="p-4 space-y-4">
                        @forelse($approvedLeaveRequests as $request)
                            @php $card = getLeaveRequestCard($request); @endphp
                            <div class="border border-green-200 rounded-lg p-4 bg-green-50">
                                {{-- Employee Info --}}
                                <div class="flex items-center justify-between mb-3">
                                    <div>
                                        <div class="font-semibold text-gray-900">{{ $card['employee_name'] }}</div>
                                        <div class="text-xs text-gray-600">Gender: {{ ucfirst($card['gender']) }}</div>
                                    </div>
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-200 text-green-900">
                                        {{ $request->leave_type }}
                                    </span>
                                </div>

                                {{-- Leave Details --}}
                                <div class="text-sm text-gray-600 space-y-2">
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <strong>From:</strong> {{ \Carbon\Carbon::parse($card['leave_from'])->format('M d, Y') }}
                                        </div>
                                        <div>
                                            <strong>To:</strong> {{ \Carbon\Carbon::parse($card['leave_to'])->format('M d, Y') }}
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-2 pt-2 border-t border-green-200">
                                        <div>
                                            <strong>Total Days:</strong> <span class="font-semibold">{{ $card['total_days'] }}</span>
                                        </div>
                                        @if($request->leave_type === 'Annual Leave' || $request->leave_type === 'Paternity Leave')
                                            <div>
                                                <strong>Working Days:</strong> <span class="font-semibold">{{ $card['working_days'] }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Special Indicators --}}
                                    @if($card['has_certificate'])
                                        <div class="flex items-center text-green-700 text-xs pt-2">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            Certificate Verified
                                        </div>
                                    @endif

                                    @if($card['reason'])
                                        <div class="pt-2 border-t border-green-200">
                                            <strong>Reason:</strong>
                                            <p class="text-gray-600 mt-1 text-xs">{{ Str::limit($card['reason'], 80) }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-gray-500 py-8">No approved requests</div>
                        @endforelse
                    </div>
                    @if($approvedLeaveRequests->hasPages())
                        <div class="p-4 border-t no-print">
                            {{ $approvedLeaveRequests->links() }}
                        </div>
                    @endif
                </div>

                <!-- Rejected Column -->
                <div class="bg-white rounded-lg shadow-sm">
                    <div class="bg-red-600 text-white p-4 rounded-t-lg">
                        <h3 class="font-semibold text-lg">Rejected ({{ $rejectedLeaveRequests->total() }})</h3>
                    </div>
                    <div class="p-4 space-y-4">
                        @forelse($rejectedLeaveRequests as $request)
                            @php $card = getLeaveRequestCard($request); @endphp
                            <div class="border border-red-200 rounded-lg p-4 bg-red-50">
                                {{-- Employee Info --}}
                                <div class="flex items-center justify-between mb-3">
                                    <div>
                                        <div class="font-semibold text-gray-900">{{ $card['employee_name'] }}</div>
                                        <div class="text-xs text-gray-600">Gender: {{ ucfirst($card['gender']) }}</div>
                                    </div>
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-200 text-red-900">
                                        {{ $request->leave_type }}
                                    </span>
                                </div>

                                {{-- Leave Details --}}
                                <div class="text-sm text-gray-600 space-y-2">
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <strong>From:</strong> {{ \Carbon\Carbon::parse($card['leave_from'])->format('M d, Y') }}
                                        </div>
                                        <div>
                                            <strong>To:</strong> {{ \Carbon\Carbon::parse($card['leave_to'])->format('M d, Y') }}
                                        </div>
                                    </div>
                                    
                                    <div class="pt-2 border-t border-red-200">
                                        <strong>Duration:</strong> <span class="font-semibold">{{ $card['total_days'] }} days</span>
                                        @if($request->leave_type === 'Annual Leave' || $request->leave_type === 'Paternity Leave')
                                            ({{ $card['working_days'] }} working days)
                                        @endif
                                    </div>

                                    @if($card['reason'])
                                        <div class="pt-2 border-t border-red-200">
                                            <strong>Reason:</strong>
                                            <p class="text-gray-600 mt-1 text-xs">{{ Str::limit($card['reason'], 80) }}</p>
                                        </div>
                                    @endif
                                </div>

                                {{-- ADD THIS: Restore Button --}}
                                <div class="mt-4 pt-3 border-t border-red-200 no-print">
                                    <a href="{{ route('admin.restore-leave', $request->id) }}" 
                                    onclick="return confirm('Are you sure you want to restore this rejected request to pending status?')"
                                    class="inline-flex items-center justify-center w-full bg-blue-600 text-white font-bold py-2 px-4 rounded text-sm hover:bg-blue-700 transition">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"/>
                                        </svg>
                                        Restore to Pending
                                    </a>
                                    <p class="text-xs text-gray-500 mt-2 text-center">
                                        This will move the request back to pending status for reconsideration
                                    </p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-gray-500 py-8">No rejected requests</div>
                        @endforelse
                    </div>
                    @if($rejectedLeaveRequests->hasPages())
                        <div class="p-4 border-t no-print">
                            {{ $rejectedLeaveRequests->links() }}
                        </div>
                    @endif
                </div>                
            </div>
        </div>
    </div>

    <style>
        @media print {
            .no-print { display: none !important; }
            body { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
        }
    </style>
</x-app-layout>