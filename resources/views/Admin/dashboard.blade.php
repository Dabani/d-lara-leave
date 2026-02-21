<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Admin Dashboard') }}
            </h2>
            {{-- FIXED: Profile link for admin --}}
            <a href="{{ route('profile.edit') }}"
            class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-semibold rounded-md hover:bg-gray-700 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                My Profile
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- Quick Stats Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <a href="{{ route('admin.manage-employee') }}" 
                   class="bg-white shadow-md rounded-lg overflow-hidden hover:shadow-lg transition-all hover:-translate-y-1">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900">Manage Employees</h2>
                                <p class="text-gray-600 mt-1">
                                    <span class="font-bold text-yellow-600">{{ $pendingReq }}</span> pending approval(s)
                                </p>
                            </div>
                            <div class="bg-blue-100 p-3 rounded-full">
                                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.manage-leave') }}" 
                   class="bg-white shadow-md rounded-lg overflow-hidden hover:shadow-lg transition-all hover:-translate-y-1">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900">Manage Leave</h2>
                                <p class="text-gray-600 mt-1">
                                    <span class="font-bold text-yellow-600">{{ $leaveRequests }}</span> pending request(s)
                                </p>
                            </div>
                            <div class="bg-green-100 p-3 rounded-full">
                                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            {{-- Leave Applications Overview (Tabbed) --}}
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="bg-indigo-600 text-white px-6 py-4 rounded-t-lg">
                    <h3 class="font-semibold text-lg">📋 Leave Applications Overview</h3>
                </div>

                {{-- Tab Navigation --}}
                <div class="border-b border-gray-200 bg-gray-50">
                    <nav class="flex px-6" aria-label="Tabs">
                        <button onclick="showLeaveTab('approved')" 
                                id="tab-approved"
                                class="leave-tab border-b-2 border-indigo-500 text-indigo-600 py-4 px-6 text-sm font-medium">
                            ✅ Approved
                            <span class="ml-2 bg-green-100 text-green-700 px-2 py-0.5 rounded-full text-xs font-bold">
                                {{ $approvedCount ?? 0 }}
                            </span>
                        </button>
                        <button onclick="showLeaveTab('pending')" 
                                id="tab-pending"
                                class="leave-tab border-b-2 border-transparent text-gray-500 hover:text-gray-700 py-4 px-6 text-sm font-medium">
                            ⏳ Pending
                            <span class="ml-2 bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full text-xs font-bold">
                                {{ $pendingCount ?? 0 }}
                            </span>
                        </button>
                        <button onclick="showLeaveTab('rejected')" 
                                id="tab-rejected"
                                class="leave-tab border-b-2 border-transparent text-gray-500 hover:text-gray-700 py-4 px-6 text-sm font-medium">
                            ❌ Rejected
                            <span class="ml-2 bg-red-100 text-red-700 px-2 py-0.5 rounded-full text-xs font-bold">
                                {{ $rejectedCount ?? 0 }}
                            </span>
                        </button>
                    </nav>
                </div>

                {{-- Tab Content: Approved --}}
                <div id="content-approved" class="leave-tab-content p-6">
                    @if($approvedLeaves && $approvedLeaves->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($approvedLeaves->take(5) as $leave)
                                <div class="border border-green-200 rounded-lg p-4 bg-green-50 hover:bg-green-100 transition">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <span class="font-semibold text-gray-900">{{ $leave->employee->user->name }}</span>
                                            <span class="mx-2 text-gray-400">·</span>
                                            <span class="text-sm text-gray-600">{{ $leave->leave_type }}</span>
                                        </div>
                                        <span class="px-2 py-0.5 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
                                            Approved
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600 mt-2">
                                        {{ $leave->leave_from->format('M d') }} – {{ $leave->leave_to->format('M d, Y') }}
                                        ({{ $leave->working_days_count ?? 0 }} days)
                                    </p>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4 text-center">
                            <a href="{{ route('admin.manage-leave') }}" 
                               class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                View all approved leaves →
                            </a>
                        </div>
                    @else
                        <p class="text-center text-gray-500 py-8">No approved leave applications yet.</p>
                    @endif
                </div>

                {{-- Tab Content: Pending --}}
                <div id="content-pending" class="leave-tab-content hidden p-6">
                    @if($pendingLeaves && $pendingLeaves->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($pendingLeaves->take(5) as $leave)
                                <div class="border border-yellow-200 rounded-lg p-4 bg-yellow-50 hover:bg-yellow-100 transition">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <span class="font-semibold text-gray-900">{{ $leave->employee->user->name }}</span>
                                            <span class="mx-2 text-gray-400">·</span>
                                            <span class="text-sm text-gray-600">{{ $leave->leave_type }}</span>
                                        </div>
                                        <span class="px-2 py-0.5 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">
                                            Pending
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600 mt-2">
                                        {{ $leave->leave_from->format('M d') }} – {{ $leave->leave_to->format('M d, Y') }}
                                        ({{ $leave->working_days_count ?? 0 }} days)
                                    </p>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4 text-center">
                            <a href="{{ route('admin.manage-leave') }}" 
                               class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                View all pending leaves →
                            </a>
                        </div>
                    @else
                        <p class="text-center text-gray-500 py-8">No pending leave applications.</p>
                    @endif
                </div>

                {{-- Tab Content: Rejected --}}
                <div id="content-rejected" class="leave-tab-content hidden p-6">
                    @if($rejectedLeaves && $rejectedLeaves->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($rejectedLeaves->take(5) as $leave)
                                <div class="border border-red-200 rounded-lg p-4 bg-red-50 hover:bg-red-100 transition">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <span class="font-semibold text-gray-900">{{ $leave->employee->user->name }}</span>
                                            <span class="mx-2 text-gray-400">·</span>
                                            <span class="text-sm text-gray-600">{{ $leave->leave_type }}</span>
                                        </div>
                                        <span class="px-2 py-0.5 bg-red-100 text-red-800 text-xs font-semibold rounded-full">
                                            Rejected
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600 mt-2">
                                        {{ $leave->leave_from->format('M d') }} – {{ $leave->leave_to->format('M d, Y') }}
                                        ({{ $leave->working_days_count ?? 0 }} days)
                                    </p>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4 text-center">
                            <a href="{{ route('admin.manage-leave') }}" 
                               class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                View all rejected leaves →
                            </a>
                        </div>
                    @else
                        <p class="text-center text-gray-500 py-8">No rejected leave applications.</p>
                    @endif
                </div>

            </div>

        </div>
    </div>

    @push('scripts')
    <script>
        function showLeaveTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.leave-tab-content').forEach(content => {
                content.classList.add('hidden');
            });
            
            // Remove active state from all tabs
            document.querySelectorAll('.leave-tab').forEach(button => {
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
    </script>
    @endpush
</x-app-layout>
