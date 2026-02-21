<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                @if(auth()->user()->isManagingPartner())
                    Managing Partner — Assessment Dashboard
                @elseif(auth()->user()->isAssessor())
                    Assessment Dashboard — {{ auth()->user()->heads_department }} Department
                @else
                    Admin Assessment Overview
                @endif
            </h2>

            {{-- Quick action buttons for assessors --}}
            <div class="flex gap-2">
                {{-- Apply for Leave button - Available to all employees --}}
                <a href="{{ route('leave-request.create') }}"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-md hover:bg-indigo-700 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Apply for Leave
                </a>

                {{-- View Profile (read-only) --}}
                <a href="{{ route('user.profile') }}"
                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-md hover:bg-blue-700 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    View Profile
                </a>

                {{-- Edit Profile (password change) --}}
                <a href="{{ route('profile.edit') }}"
                class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-semibold rounded-md hover:bg-gray-700 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                    </svg>
                    Edit Profile
                </a>

                {{-- Leave History --}}
                <a href="{{ route('leave-history') }}"
                class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-md hover:bg-green-700 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Leave History
                </a>
            </div>
        </div>
    </x-slot>    

    {{-- Rest of your existing dashboard code remains exactly the same --}}
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Flash messages --}}
            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Two-column layout for Pending and Assessed --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- LEFT COLUMN: PENDING ASSESSMENT --}}
                <div class="bg-white shadow-sm sm:rounded-lg">
                    <div class="bg-yellow-500 text-white px-6 py-4 rounded-t-lg flex justify-between items-center">
                        <h3 class="font-semibold text-lg">⏳ Awaiting Your Assessment</h3>
                        <span class="bg-white text-yellow-600 rounded-full px-3 py-1 text-sm font-bold">
                            {{ $pendingRequests->total() }}
                        </span>
                        <x-search-box 
                            route="{{ route('assessor.dashboard') }}" 
                            placeholder="Search applications..."
                            :value="request('search')" />
                    </div>

                    <div class="divide-y divide-gray-100 max-h-[800px] overflow-y-auto">
                        @forelse($pendingRequests as $request)
                            <div class="p-5 hover:bg-gray-50 transition">
                                {{-- Header --}}
                                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2 mb-3">
                                    <div>
                                        <span class="font-semibold text-gray-900 text-base">
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

                                {{-- Details Grid --}}
                                <div class="grid grid-cols-2 gap-3 text-sm text-gray-600 mb-3">
                                    <div>
                                        <span class="font-medium block text-gray-700">From</span>
                                        {{ $request->leave_from->format('M d, Y') }}
                                    </div>
                                    <div>
                                        <span class="font-medium block text-gray-700">To</span>
                                        {{ $request->leave_to->format('M d, Y') }}
                                    </div>
                                    <div>
                                        <span class="font-medium block text-gray-700">Working Days</span>
                                        {{ $request->working_days_count ?? '—' }}
                                    </div>
                                    <div>
                                        <span class="font-medium block text-gray-700">Applied</span>
                                        {{ $request->created_at->format('M d, Y') }}
                                    </div>
                                </div>

                                {{-- Reason --}}
                                @if($request->reason)
                                    <p class="text-sm text-gray-600 mb-3 bg-gray-50 rounded p-3">
                                        <span class="font-medium">Reason:</span> {{ $request->reason }}
                                    </p>
                                @endif

                                {{-- Early emergency notice --}}
                                @if($request->is_pre_annual_emergency)
                                    <div class="mb-3 p-3 bg-amber-50 border-l-4 border-amber-400 text-amber-800 text-sm rounded">
                                        ⚠ This is an <strong>early emergency leave</strong> (employee not yet 12-month eligible).
                                        Days will be deducted from annual leave once eligible.
                                    </div>
                                @endif

                                {{-- Comments Thread (VISIBLE) --}}
                                @if($request->comments->count() > 0)
                                    <div class="mb-3 border-t border-gray-200 pt-3">
                                        <p class="text-xs font-semibold text-gray-700 mb-2">💬 Comments ({{ $request->comments->count() }})</p>
                                        <div class="space-y-2 max-h-32 overflow-y-auto">
                                            @foreach($request->comments as $comment)
                                                <div class="bg-gray-50 rounded p-2 text-xs">
                                                    <span class="font-semibold text-gray-800">{{ $comment->user->name }}</span>
                                                    <span class="text-gray-400 ml-1">{{ $comment->created_at->diffForHumans() }}</span>
                                                    <p class="text-gray-700 mt-1">{{ $comment->body }}</p>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                {{-- Action Buttons --}}
                                <div class="flex flex-col gap-2">
                                    {{-- Approve Form --}}
                                    <form action="{{ auth()->user()->isManagingPartner()
                                            ? route('assessor.mp-approve', $request->id)
                                            : route('assessor.approve', $request->id) }}"
                                          method="POST" class="w-full">
                                        @csrf
                                        <div class="flex gap-2">
                                            <input type="text" name="comment"
                                                   placeholder="Optional comment..."
                                                   class="flex-1 text-sm border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-green-300">
                                            <button type="submit"
                                                    class="px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-md hover:bg-green-700 whitespace-nowrap">
                                                ✓ Approve
                                            </button>
                                        </div>
                                    </form>

                                    {{-- Reject Button (opens modal) --}}
                                    <button onclick="openRejectModal('{{ $request->id }}')"
                                            class="w-full px-4 py-2 bg-red-600 text-white text-sm font-semibold rounded-md hover:bg-red-700">
                                        ✗ Reject with Reason
                                    </button>
                                </div>

                                {{-- Reject Modal (one per request) --}}
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
                        <div class="px-6 py-4 bg-gray-50 rounded-b-lg">
                            {{ $pendingRequests->links() }}
                        </div>
                    @endif
                </div>

                {{-- RIGHT COLUMN: ASSESSED (HISTORY) --}}
                <div class="bg-white shadow-sm sm:rounded-lg">
                    <div class="bg-gray-600 text-white px-6 py-4 rounded-t-lg flex justify-between items-center">
                        <h3 class="font-semibold text-lg">✅ Previously Assessed</h3>
                        <span class="bg-white text-gray-600 rounded-full px-3 py-1 text-sm font-bold">
                            {{ $assessedRequests->total() }}
                        </span>
                        <x-search-box 
                            route="{{ route('assessor.dashboard') }}" 
                            placeholder="Search applications..."
                            :value="request('search')" />
                    </div>

                    <div class="divide-y divide-gray-100 max-h-[800px] overflow-y-auto">
                        @forelse($assessedRequests as $request)
                            <div class="p-4 hover:bg-gray-50 transition">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                    <div>
                                        <span class="font-medium text-gray-900">{{ $request->employee->user->name }}</span>
                                        <span class="mx-2 text-gray-400">·</span>
                                        <span class="text-sm text-gray-600">{{ $request->leave_type }}</span>
                                        <span class="mx-2 text-gray-400">·</span>
                                        <span class="text-sm text-gray-500">
                                            {{ $request->leave_from->format('M d') }} – {{ $request->leave_to->format('M d, Y') }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        @if(strtolower($request->status) === 'approved')
                                            <span class="px-2 py-0.5 bg-green-100 text-green-800 text-xs font-semibold rounded-full">✓ Approved</span>
                                        @elseif(strtolower($request->status) === 'rejected')
                                            <span class="px-2 py-0.5 bg-red-100 text-red-800 text-xs font-semibold rounded-full">✗ Rejected</span>
                                        @elseif($request->assessment_status === 'assessed_approved')
                                            <span class="px-2 py-0.5 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">⏳ Awaiting Admin</span>
                                        @else
                                            <span class="px-2 py-0.5 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">{{ ucfirst($request->status) }}</span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Show comments if any exist --}}
                                @if($request->comments->count() > 0)
                                    <div class="mt-2 pt-2 border-t border-gray-100">
                                        <details class="text-xs">
                                            <summary class="cursor-pointer text-gray-600 hover:text-gray-900">
                                                💬 {{ $request->comments->count() }} comment(s)
                                            </summary>
                                            <div class="mt-2 space-y-1 ml-4">
                                                @foreach($request->comments as $comment)
                                                    <div class="bg-gray-50 rounded p-2">
                                                        <span class="font-semibold text-gray-800">{{ $comment->user->name }}</span>
                                                        <span class="text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                                                        <p class="text-gray-700 mt-1">{{ $comment->body }}</p>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </details>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="p-8 text-center text-gray-400 text-sm">No assessed requests yet.</div>
                        @endforelse
                    </div>

                    @if($assessedRequests->hasPages())
                        <div class="px-6 py-4 bg-gray-50 rounded-b-lg">
                            {{ $assessedRequests->links() }}
                        </div>
                    @endif
                </div>

            </div>{{-- End two-column grid --}}
            {{-- ══════════════════════════════════════════════════════════════════════════
                MANAGING PARTNER ONLY: ORGANIZATIONAL OVERVIEW (FOR INFORMATION)
                Place this AFTER the two-column grid but BEFORE the closing </div>
                ══════════════════════════════════════════════════════════════════════════ --}}

            @if(auth()->user()->isManagingPartner() && isset($infoRequests) && $infoRequests->count() > 0)
                <div class="mt-6 bg-white shadow-sm sm:rounded-lg">
                    <div class="bg-blue-600 text-white px-6 py-4 rounded-t-lg">
                        <h3 class="font-semibold text-lg">📊 Organizational Overview (For Information)</h3>
                        <p class="text-sm text-blue-100 mt-1">All regular employee leave applications across departments</p>
                    </div>

                    {{-- Tab Navigation --}}
                    <div class="border-b border-gray-200 bg-gray-50">
                        <nav class="flex px-6" aria-label="Tabs">
                            <button onclick="showMPTab('approved')" 
                                    id="mp-tab-approved"
                                    class="mp-tab border-b-2 border-blue-500 text-blue-600 py-4 px-6 text-sm font-medium">
                                ✅ Approved
                                <span class="ml-2 bg-green-100 text-green-700 px-2 py-0.5 rounded-full text-xs font-bold">
                                    {{ $infoRequests->where('status', 'Approved')->count() }}
                                </span>
                            </button>
                            <button onclick="showMPTab('pending')" 
                                    id="mp-tab-pending"
                                    class="mp-tab border-b-2 border-transparent text-gray-500 hover:text-gray-700 py-4 px-6 text-sm font-medium">
                                ⏳ Pending
                                <span class="ml-2 bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full text-xs font-bold">
                                    {{ $infoRequests->where('status', 'pending')->count() }}
                                </span>
                            </button>
                            <button onclick="showMPTab('rejected')" 
                                    id="mp-tab-rejected"
                                    class="mp-tab border-b-2 border-transparent text-gray-500 hover:text-gray-700 py-4 px-6 text-sm font-medium">
                                ❌ Rejected
                                <span class="ml-2 bg-red-100 text-red-700 px-2 py-0.5 rounded-full text-xs font-bold">
                                    {{ $infoRequests->where('status', 'Rejected')->count() }}
                                </span>
                            </button>
                        </nav>
                    </div>

                    {{-- ══════════════════════════════════════════════════════════════════
                        TAB: APPROVED
                        ══════════════════════════════════════════════════════════════════ --}}
                    <div id="mp-content-approved" class="mp-tab-content p-6">
                        <p class="text-sm text-gray-600 mb-4 bg-green-50 border-l-4 border-green-400 p-3 rounded">
                            ℹ️ <strong>Read-only overview</strong> — These applications have been approved by HODs and Admin.
                        </p>

                        @php
                            $approvedInfo = $infoRequests->where('status', 'Approved');
                        @endphp

                        @if($approvedInfo->count() > 0)
                            {{-- 4-card grid --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                @foreach($approvedInfo as $request)
                                    <div class="border border-green-200 bg-green-50 rounded-lg p-4 hover:shadow-md transition">
                                        {{-- Employee info --}}
                                        <div class="mb-3">
                                            <h4 class="font-semibold text-gray-900 text-sm truncate" title="{{ $request->employee->user->name }}">
                                                {{ $request->employee->user->name }}
                                            </h4>
                                            <p class="text-xs text-gray-600 truncate">{{ $request->employee->department }}</p>
                                        </div>

                                        {{-- Leave type badge --}}
                                        <div class="mb-3">
                                            <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">
                                                {{ $request->leave_type }}
                                            </span>
                                        </div>

                                        {{-- Dates --}}
                                        <div class="text-xs text-gray-700 mb-2">
                                            <div class="flex items-center gap-1 mb-1">
                                                <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                <span>{{ $request->leave_from->format('M d') }} – {{ $request->leave_to->format('M d, Y') }}</span>
                                            </div>
                                            <div class="flex items-center gap-1">
                                                <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                <span class="font-semibold">{{ $request->working_days_count ?? 0 }} working days</span>
                                            </div>
                                        </div>

                                        {{-- Assessed by --}}
                                        @if($request->assessed_by)
                                            <div class="text-xs text-gray-600 bg-white rounded p-2 border border-gray-200">
                                                <span class="font-medium">Assessed by:</span><br>
                                                {{ $request->assessor->name ?? 'Unknown HOD' }}
                                            </div>
                                        @endif

                                        {{-- Status badge --}}
                                        <div class="mt-3 pt-3 border-t border-green-200">
                                            <span class="inline-flex items-center px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                                Approved
                                            </span>
                                        </div>

                                        {{-- Comments indicator --}}
                                        @if($request->comments->count() > 0)
                                            <div class="mt-2 text-xs text-gray-500">
                                                💬 {{ $request->comments->count() }} comment(s)
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-center text-gray-500 py-8">No approved applications.</p>
                        @endif
                    </div>

                    {{-- ══════════════════════════════════════════════════════════════════
                        TAB: PENDING
                        ══════════════════════════════════════════════════════════════════ --}}
                    <div id="mp-content-pending" class="mp-tab-content hidden p-6">
                        <p class="text-sm text-gray-600 mb-4 bg-yellow-50 border-l-4 border-yellow-400 p-3 rounded">
                            ℹ️ <strong>Read-only overview</strong> — These applications are pending assessment by their HODs or awaiting Admin approval.
                        </p>

                        @php
                            $pendingInfo = $infoRequests->where('status', 'pending');
                        @endphp

                        @if($pendingInfo->count() > 0)
                            {{-- 4-card grid --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                @foreach($pendingInfo as $request)
                                    <div class="border border-yellow-200 bg-yellow-50 rounded-lg p-4 hover:shadow-md transition">
                                        {{-- Employee info --}}
                                        <div class="mb-3">
                                            <h4 class="font-semibold text-gray-900 text-sm truncate" title="{{ $request->employee->user->name }}">
                                                {{ $request->employee->user->name }}
                                            </h4>
                                            <p class="text-xs text-gray-600 truncate">{{ $request->employee->department }}</p>
                                        </div>

                                        {{-- Leave type badge --}}
                                        <div class="mb-3">
                                            <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">
                                                {{ $request->leave_type }}
                                            </span>
                                        </div>

                                        {{-- Dates --}}
                                        <div class="text-xs text-gray-700 mb-2">
                                            <div class="flex items-center gap-1 mb-1">
                                                <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                <span>{{ $request->leave_from->format('M d') }} – {{ $request->leave_to->format('M d, Y') }}</span>
                                            </div>
                                            <div class="flex items-center gap-1">
                                                <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                <span class="font-semibold">{{ $request->working_days_count ?? 0 }} working days</span>
                                            </div>
                                        </div>

                                        {{-- Current stage --}}
                                        <div class="text-xs text-gray-600 bg-white rounded p-2 border border-gray-200 mb-2">
                                            @if($request->assessment_status === 'assessed_approved')
                                                <span class="font-medium text-teal-700">⏳ Awaiting Admin</span><br>
                                                <span class="text-gray-500">Assessed by {{ $request->assessor->name ?? 'HOD' }}</span>
                                            @else
                                                <span class="font-medium text-yellow-700">⏳ Pending HOD Review</span><br>
                                                <span class="text-gray-500">Applied {{ $request->created_at->diffForHumans() }}</span>
                                            @endif
                                        </div>

                                        {{-- Status badge --}}
                                        <div class="mt-3 pt-3 border-t border-yellow-200">
                                            <span class="inline-flex items-center px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                                </svg>
                                                Pending
                                            </span>
                                        </div>

                                        {{-- Comments indicator --}}
                                        @if($request->comments->count() > 0)
                                            <div class="mt-2 text-xs text-gray-500">
                                                💬 {{ $request->comments->count() }} comment(s)
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-center text-gray-500 py-8">No pending applications.</p>
                        @endif
                    </div>

                    {{-- ══════════════════════════════════════════════════════════════════
                        TAB: REJECTED
                        ══════════════════════════════════════════════════════════════════ --}}
                    <div id="mp-content-rejected" class="mp-tab-content hidden p-6">
                        <p class="text-sm text-gray-600 mb-4 bg-red-50 border-l-4 border-red-400 p-3 rounded">
                            ℹ️ <strong>Read-only overview</strong> — These applications were rejected by HODs or Admin.
                        </p>

                        @php
                            $rejectedInfo = $infoRequests->where('status', 'Rejected');
                        @endphp

                        @if($rejectedInfo->count() > 0)
                            {{-- 4-card grid --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                @foreach($rejectedInfo as $request)
                                    <div class="border border-red-200 bg-red-50 rounded-lg p-4 hover:shadow-md transition">
                                        {{-- Employee info --}}
                                        <div class="mb-3">
                                            <h4 class="font-semibold text-gray-900 text-sm truncate" title="{{ $request->employee->user->name }}">
                                                {{ $request->employee->user->name }}
                                            </h4>
                                            <p class="text-xs text-gray-600 truncate">{{ $request->employee->department }}</p>
                                        </div>

                                        {{-- Leave type badge --}}
                                        <div class="mb-3">
                                            <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">
                                                {{ $request->leave_type }}
                                            </span>
                                        </div>

                                        {{-- Dates --}}
                                        <div class="text-xs text-gray-700 mb-2">
                                            <div class="flex items-center gap-1 mb-1">
                                                <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                <span>{{ $request->leave_from->format('M d') }} – {{ $request->leave_to->format('M d, Y') }}</span>
                                            </div>
                                            <div class="flex items-center gap-1">
                                                <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                <span class="font-semibold">{{ $request->working_days_count ?? 0 }} working days</span>
                                            </div>
                                        </div>

                                        {{-- Rejection info --}}
                                        @if($request->assessment_status === 'assessed_rejected')
                                            <div class="text-xs text-gray-600 bg-white rounded p-2 border border-gray-200">
                                                <span class="font-medium">Rejected by:</span><br>
                                                {{ $request->assessor->name ?? 'HOD' }}
                                            </div>
                                        @endif

                                        {{-- Status badge --}}
                                        <div class="mt-3 pt-3 border-t border-red-200">
                                            <span class="inline-flex items-center px-2 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded-full">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                                </svg>
                                                Rejected
                                            </span>
                                        </div>

                                        {{-- Comments indicator --}}
                                        @if($request->comments->count() > 0)
                                            <div class="mt-2 text-xs text-gray-500">
                                                💬 {{ $request->comments->count() }} comment(s)
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-center text-gray-500 py-8">No rejected applications.</p>
                        @endif
                    </div>

                </div>

                {{-- Tab switching JavaScript --}}
                @push('scripts')
                <script>
                    function showMPTab(tabName) {
                        // Hide all MP tab contents
                        document.querySelectorAll('.mp-tab-content').forEach(content => {
                            content.classList.add('hidden');
                        });
                        
                        // Remove active state from all MP tabs
                        document.querySelectorAll('.mp-tab').forEach(button => {
                            button.classList.remove('border-blue-500', 'text-blue-600');
                            button.classList.add('border-transparent', 'text-gray-500');
                        });
                        
                        // Show selected tab content
                        document.getElementById('mp-content-' + tabName).classList.remove('hidden');
                        
                        // Set active state on selected tab
                        const activeTab = document.getElementById('mp-tab-' + tabName);
                        activeTab.classList.remove('border-transparent', 'text-gray-500');
                        activeTab.classList.add('border-blue-500', 'text-blue-600');
                    }

                    // Show approved tab by default on page load
                    document.addEventListener('DOMContentLoaded', function() {
                        if (document.getElementById('mp-tab-approved')) {
                            showMPTab('approved');
                        }
                    });
                </script>
                @endpush
            @endif
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
