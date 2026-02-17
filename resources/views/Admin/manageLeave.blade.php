<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manage Leave Requests') }}
        </h2>
    </x-slot>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mx-4 mt-4 sm:mx-auto sm:max-w-7xl" style="background-color: #68D391" role="alert">
            <strong class="font-bold">Success!</strong>
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Filters and Export -->
    <div class="py-4 no-print">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-sm p-4">
                <form method="GET" action="{{ route('admin.manage-leave') }}" class="flex flex-col sm:flex-row gap-3">
                    <select name="department" class="p-2 border border-gray-300 rounded-md text-sm flex-1">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->name }}" {{ $departmentFilter == $dept->name ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                    
                    <select name="year" class="p-2 border border-gray-300 rounded-md text-sm flex-1">
                        @foreach($years as $year)
                            <option value="{{ $year }}" {{ $yearFilter == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                    
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-semibold hover:bg-blue-700">
                        Filter
                    </button>
                    
                    <a href="{{ route('admin.export-leave-requests', ['department' => $departmentFilter, 'year' => $yearFilter]) }}" 
                       class="bg-green-600 text-white px-4 py-2 rounded-md text-sm font-semibold hover:bg-green-700 inline-flex items-center justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Export
                    </a>

                    <a href="{{ route('admin.print-leave-report', ['department' => $departmentFilter, 'year' => $yearFilter]) }}" 
                       target="_blank"
                       class="bg-gray-600 text-white px-4 py-2 rounded-md text-sm font-semibold hover:bg-gray-700 inline-flex items-center justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Print PDF
                    </a>
                </form>
            </div>
        </div>
    </div>

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
                <div class="bg-white rounded-lg shadow-sm">
                    <div class="bg-yellow-500 text-white p-4 rounded-t-lg">
                        <h3 class="font-semibold text-lg">Pending ({{ $pendingLeaveRequests->total() }})</h3>
                    </div>
                    <div class="p-4 space-y-4">
                        @forelse($pendingLeaveRequests as $request)
                            @php $card = getLeaveRequestCard($request); @endphp
                            <div class="border rounded-lg p-4 hover:shadow-md transition">
                                <x-leave-comments :leave-request="$request" :show-form="true" />
                                {{-- Employee Info --}}
                                <div class="flex items-center justify-between mb-3">
                                    <div>
                                        <div class="font-semibold text-gray-900">{{ $card['employee_name'] }}</div>
                                        <div class="text-xs text-gray-500">
                                            Gender: {{ ucfirst($card['gender']) }}
                                        </div>
                                    </div>
                                    
                                    {{-- Leave Type Badge --}}
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                                        @if($request->leave_type === 'Annual Leave') bg-blue-100 text-blue-800
                                        @elseif($request->leave_type === 'Sick Leave') bg-red-100 text-red-800
                                        @elseif($request->leave_type === 'Maternity Leave') bg-pink-100 text-pink-800
                                        @elseif($request->leave_type === 'Paternity Leave') bg-indigo-100 text-indigo-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ $request->leave_type }}
                                    </span>
                                </div>

                                {{-- Leave Details --}}
                                <div class="text-sm text-gray-600 space-y-2">
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <strong class="text-gray-700">From:</strong> 
                                            <span>{{ \Carbon\Carbon::parse($card['leave_from'])->format('M d, Y') }}</span>
                                        </div>
                                        <div>
                                            <strong class="text-gray-700">To:</strong> 
                                            <span>{{ \Carbon\Carbon::parse($card['leave_to'])->format('M d, Y') }}</span>
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-2 pt-2 border-t border-gray-200">
                                        <div>
                                            <strong class="text-gray-700">Total Days:</strong> 
                                            <span class="font-semibold">{{ $card['total_days'] }}</span>
                                        </div>
                                        @if($request->leave_type === 'Annual Leave' || $request->leave_type === 'Paternity Leave')
                                            <div>
                                                <strong class="text-gray-700">Working Days:</strong> 
                                                <span class="font-semibold text-blue-600">{{ $card['working_days'] }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Special Indicators --}}
                                    <div class="space-y-1 pt-2">
                                        {{-- Medical Certificate --}}
                                        @if($card['has_certificate'])
                                            <div class="flex items-center text-green-700 text-xs">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                                Medical Certificate Uploaded
                                                <a href="{{ asset('storage/' . $card['certificate_path']) }}" 
                                                target="_blank" 
                                                class="ml-2 underline hover:text-green-900">View</a>
                                            </div>
                                        @endif

                                        {{-- Study Leave Attempt --}}
                                        @if($request->leave_type === 'Study Leave')
                                            <div class="flex items-center text-xs text-gray-600">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                                                </svg>
                                                {{ $card['is_first_attempt'] ? 'First Attempt (Max 5 days)' : 'Repeat Attempt (Max 2 days)' }}
                                            </div>
                                        @endif

                                        {{-- Out of Recommended Period Warning --}}
                                        @if($card['is_out_of_period'] && $request->leave_type === 'Annual Leave')
                                            <div class="flex items-start text-xs text-red-700 bg-red-50 p-2 rounded">
                                                <svg class="w-4 h-4 mr-1 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                </svg>
                                                <span>Outside recommended period (July-Sept)</span>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Reason --}}
                                    @if($card['reason'])
                                        <div class="pt-2 border-t border-gray-200">
                                            <strong class="text-gray-700">Reason:</strong>
                                            <p class="text-gray-600 mt-1">{{ Str::limit($card['reason'], 100) }}</p>
                                        </div>
                                    @endif
                                </div>

                                {{-- Action Buttons --}}
                                <div class="flex gap-2 mt-4 pt-3 border-t border-gray-200 no-print">
                                    <a href="{{ route('admin.approve-leave', $request->id) }}" 
                                    onclick="return confirm('Are you sure you want to approve this leave request?')"
                                    style="background-color:#128019" 
                                    class="flex-1 text-center text-white font-bold py-2 px-3 rounded text-sm hover:opacity-90 transition">
                                        <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                        Approve
                                    </a>
                                    <a href="{{ route('admin.reject-leave', $request->id) }}" 
                                    onclick="return confirm('Are you sure you want to reject this leave request?')"
                                    style="background-color:#cd3952" 
                                    class="flex-1 text-center text-white font-bold py-2 px-3 rounded text-sm hover:opacity-90 transition">
                                        <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                        </svg>
                                        Reject
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-gray-500 py-8">No pending requests</div>
                        @endforelse
                    </div>
                    @if($pendingLeaveRequests->hasPages())
                        <div class="p-4 border-t no-print">
                            {{ $pendingLeaveRequests->links() }}
                        </div>
                    @endif
                </div>

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