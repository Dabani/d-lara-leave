<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Profile') }}
        </h2>
    </x-slot>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="mx-4 mt-4 sm:mx-auto sm:max-w-7xl px-4 sm:px-6 lg:px-8 no-print">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Success! </strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="mx-4 mt-4 sm:mx-auto sm:max-w-7xl px-4 sm:px-6 lg:px-8 no-print">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Error! </strong>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    {{-- Print Button --}}
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

            {{-- ===================================================== --}}
            {{-- EMPLOYEE INFORMATION CARD (updated with upload form)   --}}
            {{-- ===================================================== --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6">

                        {{-- Profile Image + Upload --}}
                        <div class="flex-shrink-0 text-center">
                            {{-- Current Image --}}
                            @if($employee && $employee->profile_image)
                                <img src="{{ asset('storage/' . $employee->profile_image) }}"
                                     alt="Profile"
                                     id="current-profile-img"
                                     class="w-32 h-32 rounded-full object-cover border-4 border-blue-100 shadow-md mx-auto"
                                     onerror="this.onerror=null;this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'128\' height=\'128\'%3E%3Crect width=\'128\' height=\'128\' rx=\'64\' fill=\'%23dbeafe\'/%3E%3Ctext x=\'50%25\' y=\'54%25\' font-size=\'52\' font-family=\'Arial\' text-anchor=\'middle\' dominant-baseline=\'middle\' fill=\'%233b82f6\'%3E{{ substr(auth()->user()->name,0,1) }}%3C/text%3E%3C/svg%3E';">
                            @else
                                <div id="current-profile-img"
                                     class="w-32 h-32 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center border-4 border-blue-100 shadow-md mx-auto">
                                    <span class="text-white text-4xl font-bold">{{ substr(auth()->user()->name, 0, 1) }}</span>
                                </div>
                            @endif

                            {{-- Preview (hidden until file chosen) --}}
                            <img id="img-preview"
                                 src=""
                                 alt="Preview"
                                 class="w-32 h-32 rounded-full object-cover border-4 border-indigo-300 shadow-md mx-auto mt-2 hidden">

                            {{-- Upload Form --}}
                            <form action="{{ route('user.update-profile') }}"
                                  method="POST"
                                  enctype="multipart/form-data"
                                  class="mt-3 no-print">
                                @csrf

                                <label for="profile_image"
                                       class="cursor-pointer inline-flex items-center px-3 py-1.5 bg-indigo-600 text-white text-xs font-semibold rounded-md hover:bg-indigo-700 transition">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M5.5 13a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1.977A4.5 4.5 0 1113.5 13H11V9.413l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13H5.5z"/>
                                        <path d="M9 13h2v5a1 1 0 11-2 0v-5z"/>
                                    </svg>
                                    Change Photo
                                </label>

                                <input type="file"
                                       name="profile_image"
                                       id="profile_image"
                                       accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                                       class="hidden"
                                       onchange="previewProfileImage(this)">

                                <p class="text-xs text-gray-400 mt-1">JPG, PNG, GIF, WebP · Max 2MB</p>

                                @error('profile_image')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror

                                {{-- Submit button appears only after file is selected --}}
                                <button type="submit"
                                        id="upload-btn"
                                        class="hidden mt-2 inline-flex items-center px-3 py-1.5 bg-green-600 text-white text-xs font-semibold rounded-md hover:bg-green-700 transition">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Upload
                                </button>
                            </form>
                        </div>

                        {{-- Employee Details --}}
                        <div class="flex-grow text-center sm:text-left">
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ auth()->user()->name }}</h3>

                            <div class="space-y-2 text-gray-600">
                                {{-- Email --}}
                                <p class="flex items-center justify-center sm:justify-start">
                                    <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    {{ auth()->user()->email }}
                                </p>

                                @if($employee)
                                    {{-- Department --}}
                                    <p class="flex items-center justify-center sm:justify-start">
                                        <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                        <span class="font-semibold">Department:</span>&nbsp;{{ $employee->department ?? 'N/A' }}
                                    </p>

                                    {{-- Gender --}}
                                    <p class="flex items-center justify-center sm:justify-start">
                                        <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        <span class="font-semibold">Gender:</span>&nbsp;{{ ucfirst(auth()->user()->gender ?? 'N/A') }}
                                    </p>

                                    {{-- Hire Date --}}
                                    @if($employee->hire_date)
                                        <p class="flex items-center justify-center sm:justify-start">
                                            <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <span class="font-semibold">Hire Date:</span>&nbsp;{{ \Carbon\Carbon::parse($employee->hire_date)->format('F d, Y') }}
                                        </p>

                                        {{-- Years of Service --}}
                                        <p class="flex items-center justify-center sm:justify-start">
                                            <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <span class="font-semibold">Service:</span>&nbsp;
                                            @php
                                                $years  = floor($employee->getYearsOfService());
                                                $months = $employee->getMonthsOfService() % 12;
                                            @endphp
                                            {{ $years > 0 ? $years . ' yr' . ($years > 1 ? 's' : '') : '' }}
                                            {{ $months > 0 ? $months . ' mo' . ($months > 1 ? 's' : '') : '' }}
                                            {{ ($years == 0 && $months == 0) ? '< 1 month' : '' }}
                                        </p>
                                    @endif

                                    {{-- Leave Year --}}
                                    <p class="flex items-center justify-center sm:justify-start">
                                        <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <span class="font-semibold">Leave Year:</span>&nbsp;{{ $employee->leave_year ?? date('Y') }}
                                    </p>

                                    {{-- Status Badge --}}
                                    <p class="flex items-center justify-center sm:justify-start">
                                        @if($employee->status === 'active')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                                Active Employee
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                ⏳ {{ ucfirst($employee->status) }}
                                            </span>
                                        @endif
                                    </p>
                                @endif
                            </div>
                        </div>

                        {{-- Leave Summary Stats (right side) --}}
                        @if($employee)
                            <div class="flex-shrink-0 space-y-3">
                                {{-- Total Leaves --}}
                                <div class="bg-blue-50 rounded-lg p-4 border-2 border-blue-200 text-center min-w-[110px]">
                                    <div class="text-3xl font-bold text-blue-600">{{ $employee->total_leave ?? 0 }}</div>
                                    <div class="text-xs text-gray-600 font-semibold mt-1">Total Taken</div>
                                </div>

                                {{-- Annual Leave Remaining --}}
                                @php $stats = $employee->getAnnualLeaveStats(); @endphp
                                @if($stats['is_eligible'])
                                    <div class="bg-indigo-50 rounded-lg p-4 border-2 border-indigo-200 text-center min-w-[110px]">
                                        <div class="text-3xl font-bold text-indigo-600">{{ $stats['remaining_days'] }}</div>
                                        <div class="text-xs text-gray-600 font-semibold mt-1">Annual Left</div>
                                        <div class="text-xs text-gray-400">of {{ $stats['entitlement'] }}</div>
                                    </div>
                                @else
                                    <div class="bg-gray-50 rounded-lg p-4 border-2 border-gray-200 text-center min-w-[110px]">
                                        <div class="text-sm font-bold text-gray-400">Not Yet</div>
                                        <div class="text-xs text-gray-500 font-semibold mt-1">Annual Leave</div>
                                        <div class="text-xs text-gray-400">{{ 12 - $employee->getMonthsOfService() }} mo. left</div>
                                    </div>
                                @endif
                            </div>
                        @endif

                    </div>
                </div>
            </div>

            {{-- ===================================================== --}}
            {{-- ANNUAL LEAVE STATISTICS (only for eligible employees)  --}}
            {{-- ===================================================== --}}
            @if($employee && $stats['is_eligible'])
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-5">
                        <h3 class="text-base font-semibold text-gray-800 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-indigo-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zm6-4a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zm6-3a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                            </svg>
                            Annual Leave Statistics ({{ date('Y') }})
                        </h3>

                        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-6 gap-3">
                            <div class="bg-indigo-50 rounded-lg p-3 text-center">
                                <div class="text-xl font-bold text-indigo-700">{{ $stats['entitlement'] }}</div>
                                <div class="text-xs text-gray-500 mt-1">Entitlement</div>
                            </div>
                            <div class="bg-orange-50 rounded-lg p-3 text-center">
                                <div class="text-xl font-bold text-orange-600">{{ $stats['annual_days_taken'] }}</div>
                                <div class="text-xs text-gray-500 mt-1">Annual Used</div>
                            </div>
                            <div class="bg-yellow-50 rounded-lg p-3 text-center">
                                <div class="text-xl font-bold text-yellow-600">{{ $stats['casual_days_taken'] }}</div>
                                <div class="text-xs text-gray-500 mt-1">Casual Used</div>
                            </div>
                            <div class="bg-red-50 rounded-lg p-3 text-center">
                                <div class="text-xl font-bold text-red-500">{{ $stats['emergency_days_taken'] }}</div>
                                <div class="text-xs text-gray-500 mt-1">Emergency Used</div>
                            </div>
                            <div class="bg-green-50 rounded-lg p-3 text-center">
                                <div class="text-xl font-bold text-green-600">{{ $stats['remaining_days'] }}</div>
                                <div class="text-xs text-gray-500 mt-1">Remaining</div>
                            </div>
                            <div class="bg-purple-50 rounded-lg p-3 text-center">
                                <div class="text-xl font-bold text-purple-600">{{ $stats['annual_runs_count'] }}</div>
                                <div class="text-xs text-gray-500 mt-1">Runs Taken</div>
                            </div>
                        </div>

                        <p class="text-xs text-gray-500 mt-3">
                            <svg class="inline w-3.5 h-3.5 mr-1 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            Service: <strong>{{ round($employee->getYearsOfService(), 1) }} years</strong> ·
                            Max per run: <strong>{{ $stats['max_days_per_run'] }} working days</strong> ·
                            Casual and Emergency leaves are deducted from your annual allowance.
                        </p>
                    </div>
                </div>
            @endif

            {{-- ===================================================== --}}
            {{-- LEAVE RECORDS — three-column layout (unchanged style)  --}}
            {{-- ===================================================== --}}
            @php
                // Safety fallback: if a controller other than UserProfileController
                // served this view it may not have passed these variables.
                $approvedLeaves = $approvedLeaves ?? collect([]);
                $pendingLeaves  = $pendingLeaves  ?? collect([]);
                $rejectedLeaves = $rejectedLeaves ?? collect([]);
            @endphp
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Approved Leaves --}}
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
                                        <span class="font-bold text-green-600">
                                            {{ \Carbon\Carbon::parse($leave->leave_from)->diffInDays(\Carbon\Carbon::parse($leave->leave_to)) + 1 }} days
                                            @if($leave->working_days_count && in_array($leave->leave_type, ['Annual Leave','Paternity Leave','Casual Leave','Emergency Leave']))
                                                <span class="text-gray-400 font-normal">({{ $leave->working_days_count }} working)</span>
                                            @endif
                                        </span>
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p>No approved leaves yet</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Pending Leaves --}}
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
                                <div class="flex items-start justify-between mb-2">
                                    <span class="font-semibold text-gray-900 text-sm">{{ $leave->leave_type }}</span>
                                    {{-- Edit button for pending leaves --}}
                                    <a href="{{ route('leave-request.edit', $leave->id) }}"
                                       class="inline-flex items-center text-xs text-indigo-600 hover:text-indigo-800 font-medium no-print">
                                        <svg class="w-3.5 h-3.5 mr-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                        </svg>
                                        Edit
                                    </a>
                                </div>
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
                                        <span class="font-bold text-yellow-600">
                                            {{ \Carbon\Carbon::parse($leave->leave_from)->diffInDays(\Carbon\Carbon::parse($leave->leave_to)) + 1 }} days
                                        </span>
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p>No pending leaves</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Rejected Leaves --}}
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
                                        <span class="font-bold text-red-600">
                                            {{ \Carbon\Carbon::parse($leave->leave_from)->diffInDays(\Carbon\Carbon::parse($leave->leave_to)) + 1 }} days
                                        </span>
                                    </div>
                                    {{-- Admin comment if available --}}
                                    @if($leave->comment)
                                        <div class="mt-2 pt-2 border-t border-red-200">
                                            <span class="font-semibold text-red-700">Admin Comment:</span>
                                            <p class="text-red-600 mt-1">{{ $leave->comment }}</p>
                                        </div>
                                    @elseif($leave->reason)
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p>No rejected leaves</p>
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>{{-- end grid --}}
        </div>
    </div>

    <style>
        @media print {
            .no-print { display: none !important; }
            body { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
        }
    </style>

    @push('scripts')
    <script>
        function previewProfileImage(input) {
            const preview    = document.getElementById('img-preview');
            const currentImg = document.getElementById('current-profile-img');
            const uploadBtn  = document.getElementById('upload-btn');

            if (input.files && input.files[0]) {
                const file = input.files[0];

                // Basic size check (2 MB)
                if (file.size > 2 * 1024 * 1024) {
                    alert('File size exceeds 2MB. Please choose a smaller image.');
                    input.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function (e) {
                    // Show preview, hide current
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    if (currentImg.tagName === 'IMG') {
                        currentImg.classList.add('hidden');
                    } else {
                        currentImg.classList.add('hidden');
                    }
                    // Show upload button
                    uploadBtn.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        }
    </script>
    @endpush

</x-app-layout>