<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Profile') }}
        </h2>
    </x-slot>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mx-4 mt-4 sm:mx-auto sm:max-w-7xl" style="background-color: #68D391" role="alert">
            <strong class="font-bold">Success!</strong>
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Print Button -->
    <div class="py-4 no-print">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-end">
                <a href="{{ route('user.profile.print') }}" 
                   target="_blank"
                   class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Print Profile
                </a>
            </div>
        </div>
    </div>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Employee Information Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6">
                        <!-- Profile Image -->
                        <div class="flex-shrink-0">
                            @if($employee && $employee->profile_image)
                                <img src="{{ asset('storage/' . $employee->profile_image) }}" 
                                     alt="Profile" 
                                     class="w-32 h-32 rounded-full object-cover border-4 border-blue-100">
                            @else
                                <div class="w-32 h-32 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center border-4 border-blue-100">
                                    <span class="text-white text-4xl font-bold">{{ substr(auth()->user()->name, 0, 1) }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Employee Details -->
                        <div class="flex-grow text-center sm:text-left">
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ auth()->user()->name }}</h3>
                            <div class="space-y-2 text-gray-600">
                                <p class="flex items-center justify-center sm:justify-start">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    {{ auth()->user()->email }}
                                </p>
                                @if($employee)
                                    <p class="flex items-center justify-center sm:justify-start">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                        <span class="font-semibold">Department:</span>&nbsp;{{ $employee->department ?? 'N/A' }}
                                    </p>
                                    <p class="flex items-center justify-center sm:justify-start">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <span class="font-semibold">Leave Year:</span>&nbsp;{{ $employee->leave_year ?? date('Y') }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        <!-- Leave Summary Stats -->
                        @if($employee)
                            <div class="flex-shrink-0 bg-blue-50 rounded-lg p-4 border-2 border-blue-200">
                                <div class="text-center">
                                    <div class="text-3xl font-bold text-blue-600">{{ $employee->total_leave ?? 0 }}</div>
                                    <div class="text-sm text-gray-600 font-semibold">Total Leaves Taken</div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Leave Records - Three Column Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Approved Leaves -->
                <div class="bg-white rounded-lg shadow-sm">
                    <div class="bg-green-600 text-white p-4 rounded-t-lg">
                        <h3 class="font-semibold text-lg flex items-center justify-between">
                            <span>Approved Leaves</span>
                            <span class="bg-white text-green-600 rounded-full px-3 py-1 text-sm font-bold">{{ $approvedLeaves->count() }}</span>
                        </h3>
                    </div>
                    <div class="p-4 space-y-3 max-h-96 overflow-y-auto">
                        @forelse($approvedLeaves as $leave)
                            <div class="border border-green-200 rounded-lg p-3 bg-green-50 hover:shadow-md transition">
                                <div class="font-semibold text-gray-900 text-sm mb-2">{{ $leave->leave_type }}</div>
                                <div class="text-xs text-gray-600 space-y-1">
                                    <div class="flex justify-between">
                                        <span class="font-semibold">From:</span>
                                        <span>{{ \Carbon\Carbon::parse($leave->leave_from)->format('M d, Y') }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="font-semibold">To:</span>
                                        <span>{{ \Carbon\Carbon::parse($leave->leave_to)->format('M d, Y') }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="font-semibold">Duration:</span>
                                        <span class="font-bold text-green-600">{{ \Carbon\Carbon::parse($leave->leave_from)->diffInDays(\Carbon\Carbon::parse($leave->leave_to)) + 1 }} days</span>
                                    </div>
                                    @if($leave->reason)
                                        <div class="mt-2 pt-2 border-t border-green-200">
                                            <span class="font-semibold">Reason:</span>
                                            <p class="text-gray-700 mt-1">{{ $leave->reason }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-gray-500">
                                <svg class="w-16 h-16 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p>No approved leaves yet</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Pending Leaves -->
                <div class="bg-white rounded-lg shadow-sm">
                    <div class="bg-yellow-500 text-white p-4 rounded-t-lg">
                        <h3 class="font-semibold text-lg flex items-center justify-between">
                            <span>Pending Leaves</span>
                            <span class="bg-white text-yellow-600 rounded-full px-3 py-1 text-sm font-bold">{{ $pendingLeaves->count() }}</span>
                        </h3>
                    </div>
                    <div class="p-4 space-y-3 max-h-96 overflow-y-auto">
                        @forelse($pendingLeaves as $leave)
                            <div class="border border-yellow-200 rounded-lg p-3 bg-yellow-50 hover:shadow-md transition">
                                <div class="font-semibold text-gray-900 text-sm mb-2">{{ $leave->leave_type }}</div>
                                <div class="text-xs text-gray-600 space-y-1">
                                    <div class="flex justify-between">
                                        <span class="font-semibold">From:</span>
                                        <span>{{ \Carbon\Carbon::parse($leave->leave_from)->format('M d, Y') }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="font-semibold">To:</span>
                                        <span>{{ \Carbon\Carbon::parse($leave->leave_to)->format('M d, Y') }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="font-semibold">Duration:</span>
                                        <span class="font-bold text-yellow-600">{{ \Carbon\Carbon::parse($leave->leave_from)->diffInDays(\Carbon\Carbon::parse($leave->leave_to)) + 1 }} days</span>
                                    </div>
                                    @if($leave->reason)
                                        <div class="mt-2 pt-2 border-t border-yellow-200">
                                            <span class="font-semibold">Reason:</span>
                                            <p class="text-gray-700 mt-1">{{ $leave->reason }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-gray-500">
                                <svg class="w-16 h-16 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p>No pending leaves</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Rejected Leaves -->
                <div class="bg-white rounded-lg shadow-sm">
                    <div class="bg-red-600 text-white p-4 rounded-t-lg">
                        <h3 class="font-semibold text-lg flex items-center justify-between">
                            <span>Rejected Leaves</span>
                            <span class="bg-white text-red-600 rounded-full px-3 py-1 text-sm font-bold">{{ $rejectedLeaves->count() }}</span>
                        </h3>
                    </div>
                    <div class="p-4 space-y-3 max-h-96 overflow-y-auto">
                        @forelse($rejectedLeaves as $leave)
                            <div class="border border-red-200 rounded-lg p-3 bg-red-50 hover:shadow-md transition">
                                <div class="font-semibold text-gray-900 text-sm mb-2">{{ $leave->leave_type }}</div>
                                <div class="text-xs text-gray-600 space-y-1">
                                    <div class="flex justify-between">
                                        <span class="font-semibold">From:</span>
                                        <span>{{ \Carbon\Carbon::parse($leave->leave_from)->format('M d, Y') }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="font-semibold">To:</span>
                                        <span>{{ \Carbon\Carbon::parse($leave->leave_to)->format('M d, Y') }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="font-semibold">Duration:</span>
                                        <span class="font-bold text-red-600">{{ \Carbon\Carbon::parse($leave->leave_from)->diffInDays(\Carbon\Carbon::parse($leave->leave_to)) + 1 }} days</span>
                                    </div>
                                    @if($leave->reason)
                                        <div class="mt-2 pt-2 border-t border-red-200">
                                            <span class="font-semibold">Reason:</span>
                                            <p class="text-gray-700 mt-1">{{ $leave->reason }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-gray-500">
                                <svg class="w-16 h-16 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p>No rejected leaves</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            .no-print { display: none; }
            body { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
        }
    </style>
</x-app-layout>