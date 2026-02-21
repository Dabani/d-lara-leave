<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard') }}
            </h2>
            
            {{-- Search Box in Header --}}
            <div class="w-96">
                <form method="GET" action="{{ route('dashboard') }}" class="flex gap-2">
                    <div class="flex-1 relative">
                        <input type="text" 
                            name="search" 
                            value="{{ request('search') }}"
                            placeholder="Search leave history..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition">
                        Search
                    </button>
                    @if(request('search'))
                        <a href="{{ route('dashboard') }}" class="px-3 py-2 bg-gray-200 text-gray-700 text-sm rounded-lg hover:bg-gray-300 transition">
                            Clear
                        </a>
                    @endif
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Display Success/Error Messages --}}
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Success!</strong>
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            @if(session('warning'))
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Warning!</strong>
                    <span class="block sm:inline">{{ session('warning') }}</span>
                </div>
            @endif

            {{-- PENDING APPROVAL NOTICE --}}
            @if(auth()->user()->isPendingApproval())
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-6 mb-6 rounded-r-lg shadow-sm">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-4 flex-1">
                            <h3 class="text-lg font-semibold text-yellow-800 mb-2">
                                ⏳ Account Pending Approval
                            </h3>
                            <div class="text-sm text-yellow-700">
                                <p class="mb-3">
                                    Welcome to the Leave Management System! Your account is currently pending approval from the administrator. 
                                    Once approved, you will gain access to the full leave management features.
                                </p>
                                
                                <div class="bg-yellow-100 rounded-lg p-4 mb-3">
                                    <h4 class="font-semibold text-yellow-900 mb-2">What you can do now:</h4>
                                    <ul class="list-disc list-inside space-y-1 ml-2">
                                        <li>Update your profile information</li>
                                        <li>Upload a profile picture</li>
                                        <li>View this dashboard</li>
                                    </ul>
                                </div>

                                <div class="bg-white rounded-lg p-4 border border-yellow-200">
                                    <h4 class="font-semibold text-yellow-900 mb-2">After approval, you'll be able to:</h4>
                                    <ul class="list-disc list-inside space-y-1 ml-2">
                                        <li>Apply for various types of leave (Annual, Sick, Casual, etc.)</li>
                                        <li>View your leave history and statistics</li>
                                        <li>Track your leave balance and entitlements</li>
                                        <li>Receive email notifications about your requests</li>
                                    </ul>
                                </div>

                                <p class="mt-4 font-semibold text-yellow-900">
                                    <svg class="inline w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                    Please be patient while your account is being reviewed. You'll receive a notification once approved.
                                </p>

                                <div class="mt-4 flex gap-3">
                                    <a href="{{ route('user.profile') }}" 
                                       class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-md transition">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                        </svg>
                                        Update My Profile
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- EMPLOYEE PROFILE CARD --}}
            @if($employee)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="flex flex-col md:flex-row items-center md:items-start gap-6">
                            {{-- Profile Image --}}
                            <div class="flex-shrink-0">
                                @if($employee->profile_image)
                                    <img src="{{ asset('storage/' . $employee->profile_image) }}" 
                                         alt="Profile" 
                                         class="w-32 h-32 rounded-full object-cover border-4 border-indigo-100 shadow-lg"
                                         onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'128\' height=\'128\'%3E%3Crect width=\'128\' height=\'128\' fill=\'%23ddd\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' font-size=\'48\' text-anchor=\'middle\' alignment-baseline=\'middle\' fill=\'%23999\'%3E{{ substr(auth()->user()->name, 0, 1) }}%3C/text%3E%3C/svg%3E';">
                                @else
                                    <div class="w-32 h-32 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center border-4 border-indigo-100 shadow-lg">
                                        <span class="text-white text-5xl font-bold">{{ substr(auth()->user()->name, 0, 1) }}</span>
                                    </div>
                                @endif
                            </div>

                            {{-- Employee Information --}}
                            <div class="flex-1 text-center md:text-left">
                                <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ auth()->user()->name }}</h3>
                                <p class="text-gray-600 mb-4">{{ auth()->user()->email }}</p>
                                
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                                    <div class="bg-gray-50 rounded-lg p-3">
                                        <div class="text-xs text-gray-500 mb-1">Department</div>
                                        <div class="font-semibold text-gray-900">{{ $employee->department ?? 'N/A' }}</div>
                                    </div>
                                    
                                    <div class="bg-gray-50 rounded-lg p-3">
                                        <div class="text-xs text-gray-500 mb-1">Gender</div>
                                        <div class="font-semibold text-gray-900">{{ ucfirst(auth()->user()->gender ?? 'N/A') }}</div>
                                    </div>
                                    
                                    @if($employee->hire_date)
                                        <div class="bg-gray-50 rounded-lg p-3">
                                            <div class="text-xs text-gray-500 mb-1">Hire Date</div>
                                            <div class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($employee->hire_date)->format('M d, Y') }}</div>
                                        </div>
                                        
                                        <div class="bg-indigo-50 rounded-lg p-3">
                                            <div class="text-xs text-indigo-600 mb-1">Years of Service</div>
                                            <div class="font-semibold text-indigo-900">{{ round($employee->getYearsOfService(), 1) }} years</div>
                                        </div>
                                    @endif
                                </div>

                                {{-- Status Badge --}}
                                <div class="mt-4">
                                    @if($employee->status === 'active')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            Active Employee
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                            </svg>
                                            {{ ucfirst($employee->status ?? 'Pending') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- LEAVE STATISTICS - ONLY FOR APPROVED EMPLOYEES --}}
            @if(auth()->user()->isApprovedEmployee() && $employee)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    {{-- Total Leave Taken --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Total Leave</dt>
                                        <dd class="text-2xl font-semibold text-gray-900">{{ $employee->total_leave ?? 0 }} days</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Annual Leave - With Eligibility Check --}}
                    @php
                        $isEligible = $employee->isEligibleForAnnualLeave();
                        $stats = $employee->getAnnualLeaveStats();
                    @endphp
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 {{ $isEligible ? 'bg-blue-500' : 'bg-gray-400' }} rounded-md p-3">
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">
                                            Annual Leave
                                            @if(!$isEligible)
                                                <span class="ml-1 text-xs text-red-600">(Not Eligible)</span>
                                            @endif
                                        </dt>
                                        @if($isEligible)
                                            <dd class="text-2xl font-semibold text-gray-900">
                                                {{ $stats['remaining_days'] }}/{{ $stats['entitlement'] }}
                                            </dd>
                                            <dd class="text-xs text-gray-500">days remaining</dd>
                                        @else
                                            <dd class="text-sm font-semibold text-red-600 mt-1">
                                                Need {{ 12 - $employee->getMonthsOfService() }} more months
                                            </dd>
                                        @endif
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Sick Leave --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-red-500 rounded-md p-3">
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Sick Leave</dt>
                                        <dd class="text-2xl font-semibold text-gray-900">{{ $employee->sick_leave ?? 0 }} days</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Casual Leave --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Casual Leave</dt>
                                        <dd class="text-2xl font-semibold text-gray-900">{{ $employee->casual_leave ?? 0 }} days</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- RECENT LEAVE REQUESTS --}}
                @if($leaveRequests && $leaveRequests->count() > 0)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">Recent Leave Requests</h3>
                                <a href="{{ route('leave-history') }}" class="text-sm text-indigo-600 hover:text-indigo-900 font-medium">
                                    View All →
                                </a>
                            </div>

                            {{-- Year Filter --}}
                            @if(isset($years) && $years->count() > 1)
                                <form method="GET" action="{{ route('dashboard') }}" class="mb-4">
                                    <label for="year" class="text-sm font-medium text-gray-700 mr-2">Filter by Year:</label>
                                    <select name="year" id="year" onchange="this.form.submit()" 
                                            class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        @foreach($years as $year)
                                            <option value="{{ $year }}" {{ $yearFilter == $year ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>
                            @endif

                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Leave Type</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">From</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">To</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($leaveRequests as $request)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="text-sm font-medium text-gray-900">{{ $request->leave_type }}</span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ \Carbon\Carbon::parse($request->leave_from)->format('M d, Y') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ \Carbon\Carbon::parse($request->leave_to)->format('M d, Y') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    @php
                                                        $totalDays = \Carbon\Carbon::parse($request->leave_from)->diffInDays(\Carbon\Carbon::parse($request->leave_to)) + 1;
                                                    @endphp
                                                    {{ $totalDays }} day{{ $totalDays > 1 ? 's' : '' }}
                                                    @if($request->working_days_count && in_array($request->leave_type, ['Annual Leave', 'Paternity Leave', 'Casual Leave', 'Emergency Leave']))
                                                        <br><span class="text-xs">({{ $request->working_days_count }} working)</span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @if(strtolower($request->status) === 'pending')
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                            Pending
                                                        </span>
                                                    @elseif(strtolower($request->status) === 'approved')
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                            Approved
                                                        </span>
                                                    @elseif(strtolower($request->status) === 'rejected')
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                            Rejected
                                                        </span>
                                                    @else
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                            {{ ucfirst($request->status) }}
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                    @if(strtolower($request->status) === 'pending')
                                                        {{-- Pending requests are editable --}}
                                                        <a href="{{ route('leave-request.edit', $request->id) }}" 
                                                        class="text-indigo-600 hover:text-indigo-900 mr-3 inline-flex items-center">
                                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                                            </svg>
                                                            Edit
                                                        </a>
                                                        <form action="{{ route('leave-request.destroy', $request->id) }}" 
                                                            method="POST" 
                                                            class="inline"
                                                            onsubmit="return confirm('Are you sure you want to delete this request?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-red-600 hover:text-red-900 inline-flex items-center">
                                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                                </svg>
                                                                Delete
                                                            </button>
                                                        </form>
                                                    @elseif(strtolower($request->status) === 'rejected')
                                                        {{-- Rejected requests: Show view details --}}
                                                        <button onclick="showRejectDetails{{ $request->id }}()" 
                                                                class="text-gray-600 hover:text-gray-900 inline-flex items-center">
                                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                                            </svg>
                                                            View Details
                                                        </button>
                                                        
                                                        {{-- Hidden details modal --}}
                                                        <div id="rejectModal{{ $request->id }}" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                                                            <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-md shadow-lg rounded-md bg-white">
                                                                <div class="mt-3">
                                                                    <div class="flex items-center justify-between mb-4">
                                                                        <h3 class="text-lg font-semibold text-red-800">Rejected Leave Request</h3>
                                                                        <button onclick="closeRejectDetails{{ $request->id }}()" class="text-gray-400 hover:text-gray-600">
                                                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                                            </svg>
                                                                        </button>
                                                                    </div>
                                                                    <div class="text-sm text-gray-700">
                                                                        <p class="mb-2"><strong>Leave Type:</strong> {{ $request->leave_type }}</p>
                                                                        <p class="mb-2"><strong>Period:</strong> {{ \Carbon\Carbon::parse($request->leave_from)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($request->leave_to)->format('M d, Y') }}</p>
                                                                        @if($request->comment)
                                                                            <div class="mt-3 p-3 bg-red-50 border-l-4 border-red-400 rounded">
                                                                                <p class="font-semibold text-red-800 mb-1">Admin Comments:</p>
                                                                                <p class="text-red-700">{{ $request->comment }}</p>
                                                                            </div>
                                                                        @endif
                                                                        @if($request->admin_notes)
                                                                            <div class="mt-3 p-3 bg-gray-50 border-l-4 border-gray-400 rounded">
                                                                                <p class="font-semibold text-gray-800 mb-1">Notes:</p>
                                                                                <p class="text-gray-700 text-xs">{{ $request->admin_notes }}</p>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                    <div class="mt-4 text-center">
                                                                        <p class="text-sm text-gray-600 mb-3">Contact your administrator for more information about this rejection.</p>
                                                                        <button onclick="closeRejectDetails{{ $request->id }}()" 
                                                                                class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                                                                            Close
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <script>
                                                            function showRejectDetails{{ $request->id }}() {
                                                                document.getElementById('rejectModal{{ $request->id }}').classList.remove('hidden');
                                                                document.body.style.overflow = 'hidden';
                                                            }
                                                            function closeRejectDetails{{ $request->id }}() {
                                                                document.getElementById('rejectModal{{ $request->id }}').classList.add('hidden');
                                                                document.body.style.overflow = 'auto';
                                                            }
                                                        </script>
                                                    @else
                                                        {{-- Approved requests: View only --}}
                                                        <span class="text-green-600 inline-flex items-center">
                                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                            </svg>
                                                            Approved
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            {{-- Pagination --}}
                            @if($leaveRequests->hasPages())
                                <div class="mt-4">
                                    {{ $leaveRequests->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    {{-- No Leave Requests Yet --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No leave requests yet</h3>
                            <p class="mt-1 text-sm text-gray-500">Get started by applying for your first leave.</p>
                            <div class="mt-6">
                                <a href="{{ route('leave-request.create') }}" 
                                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <svg class="-ml-1 mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                                    </svg>
                                    Apply for Leave
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- QUICK ACTIONS --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            <a href="{{ route('leave-request.create') }}" 
                               class="flex items-center p-4 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition">
                                <svg class="h-8 w-8 text-indigo-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                <div>
                                    <div class="text-sm font-medium text-indigo-900">Apply for Leave</div>
                                    <div class="text-xs text-indigo-600">Submit new request</div>
                                </div>
                            </a>
                            
                            <a href="{{ route('leave-history') }}" 
                               class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
                                <svg class="h-8 w-8 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <div>
                                    <div class="text-sm font-medium text-blue-900">Leave History</div>
                                    <div class="text-xs text-blue-600">View all requests</div>
                                </div>
                            </a>

                            <a href="{{ route('user.profile') }}" 
                               class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition">
                                <svg class="h-8 w-8 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <div>
                                    <div class="text-sm font-medium text-green-900">My Profile</div>
                                    <div class="text-xs text-green-600">Update information</div>
                                </div>
                            </a>

                            @if($isEligible)
                                <div class="flex items-center p-4 bg-purple-50 rounded-lg">
                                    <svg class="h-8 w-8 text-purple-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                    </svg>
                                    <div>
                                        <div class="text-sm font-medium text-purple-900">Annual Leave</div>
                                        <div class="text-xs text-purple-600">{{ $stats['remaining_days'] }} days left</div>
                                    </div>
                                </div>
                            @else
                                <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                                    <svg class="h-8 w-8 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                    <div>
                                        <div class="text-sm font-medium text-gray-700">Annual Leave</div>
                                        <div class="text-xs text-gray-500">Not eligible yet</div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>