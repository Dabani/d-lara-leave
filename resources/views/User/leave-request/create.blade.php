<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Leave Request') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Display Validation Errors --}}
            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Validation Errors:</strong>
                    <ul class="mt-2 ml-4 list-disc">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Display Warnings --}}
            @if(session('leave_warnings'))
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Warnings:</strong>
                    <ul class="mt-2 ml-4 list-disc">
                        @foreach(session('leave_warnings') as $warning)
                            <li>{{ $warning }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Display Info --}}
            @if(session('leave_info'))
                <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Information:</strong>
                    <ul class="mt-2 ml-4 list-disc">
                        @foreach(session('leave_info') as $info)
                            <li>{{ $info }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Annual Leave Statistics --}}
            @if(isset($annualLeaveStats))
                <div class="bg-gradient-to-r from-indigo-50 to-blue-50 border border-indigo-200 rounded-lg p-5 mb-4 shadow-sm">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-semibold text-indigo-900 text-lg flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
                            </svg>
                            Your Annual Leave Summary
                        </h3>
                        @if($annualLeaveStats['is_eligible'])
                            <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
                                ✓ Eligible
                            </span>
                        @else
                            <span class="px-3 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded-full">
                                ✗ Not Eligible
                            </span>
                        @endif
                    </div>

                    @if($annualLeaveStats['is_eligible'])
                        {{-- Eligible - Show Statistics --}}
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                            <div class="bg-white rounded-lg p-3 shadow-sm">
                                <div class="text-xs text-gray-600 mb-1">Entitlement</div>
                                <div class="text-2xl font-bold text-indigo-600">{{ $annualLeaveStats['entitlement'] }}</div>
                                <div class="text-xs text-gray-500">working days</div>
                            </div>
                            <div class="bg-white rounded-lg p-3 shadow-sm">
                                <div class="text-xs text-gray-600 mb-1">Used</div>
                                <div class="text-2xl font-bold text-orange-600">{{ $annualLeaveStats['total_days_taken'] }}</div>
                                <div class="text-xs text-gray-500">working days</div>
                            </div>
                            <div class="bg-white rounded-lg p-3 shadow-sm">
                                <div class="text-xs text-gray-600 mb-1">Remaining</div>
                                <div class="text-2xl font-bold text-green-600">{{ $annualLeaveStats['remaining_days'] }}</div>
                                <div class="text-xs text-gray-500">working days</div>
                            </div>
                            <div class="bg-white rounded-lg p-3 shadow-sm">
                                <div class="text-xs text-gray-600 mb-1">Runs Taken</div>
                                <div class="text-2xl font-bold text-blue-600">{{ $annualLeaveStats['annual_runs_count'] }}</div>
                                <div class="text-xs text-gray-500">of min. 2</div>
                            </div>
                        </div>

                        {{-- Detailed Breakdown --}}
                        <div class="bg-white rounded-lg p-4 mb-3">
                            <h4 class="font-semibold text-gray-700 mb-2 text-sm">Breakdown:</h4>
                            <div class="space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Annual Leave:</span>
                                    <span class="font-semibold">{{ $annualLeaveStats['annual_days_taken'] }} days</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Casual Leave:</span>
                                    <span class="font-semibold">{{ $annualLeaveStats['casual_days_taken'] }} days</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Emergency Leave:</span>
                                    <span class="font-semibold">{{ $annualLeaveStats['emergency_days_taken'] }} days</span>
                                </div>
                                <div class="border-t pt-2 flex justify-between text-sm font-bold">
                                    <span>Total Used:</span>
                                    <span class="text-orange-600">{{ $annualLeaveStats['total_days_taken'] }} of {{ $annualLeaveStats['entitlement'] }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Service Info --}}
                        <div class="flex items-start gap-2 p-3 bg-blue-50 rounded-lg">
                            <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            <div class="text-xs text-blue-900">
                                <strong>Your service:</strong> {{ $annualLeaveStats['years_of_service'] }} years
                                <br><strong>Max per run:</strong> {{ $annualLeaveStats['max_days_per_run'] }} working days
                                <br><strong>Important:</strong> Annual leave must be split into at least 2 separate runs.
                                <br><strong>Note:</strong> Casual and Emergency leaves are deducted from your annual leave allowance.
                            </div>
                        </div>
                    @else
                        {{-- Not Eligible - Show Message --}}
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="flex items-start gap-3">
                                <svg class="w-6 h-6 text-yellow-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                <div>
                                    <h4 class="font-semibold text-yellow-900 mb-1">Annual Leave Not Available Yet</h4>
                                    <p class="text-sm text-yellow-800">
                                        You need at least <strong>12 months of service</strong> to be eligible for annual leave.
                                    </p>
                                    <p class="text-sm text-yellow-800 mt-2">
                                        Your service: <strong>{{ round($annualLeaveStats['years_of_service'] * 12) }} months</strong>
                                        <br>Months remaining: <strong>{{ 12 - round($annualLeaveStats['years_of_service'] * 12) }} months</strong>
                                    </p>
                                    <p class="text-xs text-yellow-700 mt-3 italic">
                                        You can still apply for other leave types (Sick, Casual, Emergency, etc.)
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Public Holiday Info Banner --}}
            <div id="public-holiday-info" class="hidden mb-4 p-4 bg-purple-50 border-l-4 border-purple-400 rounded">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-purple-600 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                    </svg>
                    <div class="flex-1">
                        <h4 class="font-semibold text-purple-800 mb-1">Public Holiday Notice</h4>
                        <div id="public-holiday-message" class="text-sm text-purple-700"></div>
                        <p class="text-xs text-purple-600 mt-2 italic">
                            ⓘ Public holidays are automatically blocked from leave selection.
                        </p>
                    </div>
                    <button onclick="this.parentElement.parentElement.classList.add('hidden')" class="text-purple-700 hover:text-purple-900">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Pre-submission assessment banner --}}
            <div id="assessment-banner" class="hidden mb-4 rounded-lg border-l-4 p-4 transition-all duration-300">
                <div class="flex items-start gap-3">
                    <span id="assessment-icon" class="text-2xl flex-shrink-0 mt-0.5"></span>
                    <div class="flex-1">
                        <p id="assessment-title" class="font-bold text-sm mb-1"></p>
                        <ul id="assessment-list" class="text-sm space-y-1 list-none"></ul>
                    </div>
                </div>
            </div>

            {{-- Department Warning Banner (will be dynamically shown) --}}
            <div id="department-warning-banner" class="hidden mb-4 p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <div class="flex-1">
                        <h4 class="font-semibold text-yellow-800 mb-2">Department Availability Notice</h4>
                        <div id="department-warnings-list" class="space-y-2"></div>
                        <p class="text-xs text-yellow-700 mt-2 italic">
                            ⓘ These are warnings only - you can still submit. Your HOD will consider department coverage when reviewing.
                        </p>
                    </div>
                    <button onclick="this.parentElement.parentElement.classList.add('hidden')" class="text-yellow-700 hover:text-yellow-900">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('leave-request.store') }}" method="POST" enctype="multipart/form-data" id="leaveRequestForm">
                        @csrf
                        
                        <div class="mb-6">
                            <label for="leave_type" class="block text-sm font-medium text-gray-700 mb-2">
                                Leave Type <span class="text-red-500">*</span>
                            </label>                            
                            <select id="leave_type" name="leave_type" required
                                    class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Select Leave Type</option>
                                
                                @if($employee->user->isFemale())
                                    <option value="Maternity Leave">
                                        Maternity Leave (Max 98 days)
                                    </option>
                                @endif
                                
                                @if($employee->user->isMale())
                                    <option value="Paternity Leave">
                                        Paternity Leave (Max 7 working days)
                                    </option>
                                @endif
                                
                                {{-- Dynamic Annual Leave based on service years --}}
                                <option value="Annual Leave">
                                    Annual Leave 
                                    @if(isset($annualLeaveStats) && $annualLeaveStats['is_eligible'])
                                        ({{ $annualLeaveStats['entitlement'] }} days/year, max {{ $annualLeaveStats['max_days_per_run'] }} per run)
                                    @else
                                        (Not eligible - need 12+ months service)
                                    @endif
                                </option>
                                
                                <option value="Casual Leave">Casual Leave</option>
                                <option value="Sick Leave">Sick Leave (Medical certificate required)</option>
                                <option value="Emergency Leave">Emergency Leave</option>
                                <option value="Study Leave">Study Leave (Supporting document required)</option>
                                <option value="Without Pay">Without Pay</option>
                            </select>
                            @error('leave_type')
                                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                            @enderror
                            
                            <!-- Leave Type Info -->
                            <div class="mt-2 text-sm text-gray-600">
                                <p id="leave-type-info" class="italic"></p>
                            </div>
                        </div>

                        {{-- Study Leave Attempt Selection --}}
                        <div id="study-leave-attempt" class="mb-6 hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Is this your first attempt? <span class="text-red-500">*</span>
                            </label>
                            <div class="flex gap-4">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="is_first_attempt" value="1" class="form-radio" checked>
                                    <span class="ml-2">First Attempt (Max 5 days)</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="is_first_attempt" value="0" class="form-radio">
                                    <span class="ml-2">Repeat Attempt (Max 2 days)</span>
                                </label>
                            </div>
                        </div>

                        {{-- Medical Certificate Upload Section --}}
                        <div id="medical-certificate-section" class="mb-6 hidden">
                            <label for="medical_certificate" class="block text-sm font-medium text-gray-700 mb-2">
                                Medical Certificate <span class="text-red-500">*</span>
                            </label>
                            <input type="file" 
                                name="medical_certificate" 
                                id="medical_certificate" 
                                accept=".pdf,.jpg,.jpeg,.png,.webp"
                                class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            <p class="text-xs text-gray-500 mt-1">
                                Required for sick leave. Accepted formats: PDF, JPG, PNG, WebP (Max 2MB)
                            </p>
                            <div class="mt-2 p-3 bg-blue-50 border-l-4 border-blue-400 text-blue-700 text-xs">
                                <strong>Important:</strong> Please upload a clear, readable medical certificate from a licensed healthcare provider.
                            </div>
                            @error('medical_certificate')
                                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Supporting Document Upload Section --}}
                        <div id="supporting-document-section" class="mb-6 hidden">
                            <label for="supporting_document" class="block text-sm font-medium text-gray-700 mb-2">
                                Supporting Document <span class="text-red-500">*</span>
                            </label>
                            <input type="file" 
                                name="supporting_document" 
                                id="supporting_document" 
                                accept=".pdf,.jpg,.jpeg,.png,.webp"
                                class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            <p class="text-xs text-gray-500 mt-1">
                                Required for study leave. Upload exam registration, professional exam notice, or similar documentation.
                                <br>Accepted formats: PDF, JPG, PNG, WebP (Max 2MB)
                            </p>
                            <div class="mt-2 p-3 bg-blue-50 border-l-4 border-blue-400 text-blue-700 text-xs">
                                <strong>Note:</strong> Study leave is reserved for professional exams only. 
                                Please upload official documentation from the exam body or educational institution.
                                <br><strong>Examples:</strong> Exam registration confirmation, exam timetable, admission letter, professional certification notice.
                            </div>
                            @error('supporting_document')
                                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Annual Leave Recommended Period Warning --}}
                        <div id="annual-leave-warning" class="mb-6 p-4 bg-red-50 border border-red-300 rounded-md hidden">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-red-600 mt-0.5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                <div>
                                    <h4 class="font-semibold text-red-800">Outside Recommended Period</h4>
                                    <p class="text-sm text-red-700 mt-1">
                                        Annual leave is recommended to be taken between <strong>July and September</strong>. 
                                        Your selected dates fall outside this recommended period.
                                    </p>
                                </div>
                            </div>
                        </div>  

                        {{-- Date Selection with Calendar --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                            <div>
                                <label for="leave_from" class="block text-sm font-medium text-gray-700 mb-2">
                                    Leave From <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                    name="leave_from" 
                                    id="leave_from" 
                                    required
                                    readonly
                                    placeholder="Select start date"
                                    class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white cursor-pointer">
                                @error('leave_from')
                                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                                @enderror
                                <p class="text-xs text-gray-500 mt-1">Click to open calendar</p>
                            </div>

                            <div>
                                <label for="leave_to" class="block text-sm font-medium text-gray-700 mb-2">
                                    Leave To <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                    name="leave_to" 
                                    id="leave_to" 
                                    required
                                    readonly
                                    placeholder="Select end date"
                                    class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white cursor-pointer">
                                @error('leave_to')
                                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                                @enderror
                                <p class="text-xs text-gray-500 mt-1">Click to open calendar</p>
                            </div>
                        </div>

                        {{-- Conflict Warning --}}
                        <div id="conflict-warning" class="mb-6 p-4 bg-red-50 border-l-4 border-red-400 rounded hidden">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">Date Conflict Detected</h3>
                                    <div class="mt-2 text-sm text-red-700" id="conflict-details">
                                        <!-- Conflict details will be inserted here -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Duration Display -->
                        <div class="mb-6 p-4 bg-blue-50 rounded-md hidden" id="duration-display">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-medium text-blue-900">
                                        Total Days: <span id="total-days-count" class="font-bold">0</span>
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-blue-900">
                                        Working Days: <span id="working-days-count" class="font-bold">0</span>
                                        <span class="text-xs text-gray-600">(excluding weekends & public holidays)</span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="mb-6">
                            <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                                Reason <span class="text-gray-400">(Optional)</span>
                            </label>
                            <textarea id="reason" name="reason" rows="4" 
                                      placeholder="Please provide a reason for your leave request..."
                                      class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ old('reason') }}</textarea>
                            @error('reason')
                                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex flex-col sm:flex-row gap-3 justify-center">
                            <button type="submit" 
                                    id="submit-button"
                                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-md transition duration-150">
                                Submit Request
                            </button>
                            <a href="{{ route('dashboard') }}" 
                               class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-3 px-6 rounded-md text-center transition duration-150">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // ─────────────────────────────────────────────────────────────────────────────
            // DATA FROM BLADE
            // ─────────────────────────────────────────────────────────────────────────────
            const annualLeaveInfo = @json($annualLeaveStats ?? null);

            const leaveTypeLimits = {
                'Maternity Leave': { type: 'calendar', max: 98 },
                'Paternity Leave': { type: 'working',  max: 7  },
                'Annual Leave':    { type: 'working',  max: annualLeaveInfo ? annualLeaveInfo.max_days_per_run : 9 },
                'Study Leave':     { type: 'calendar', max: 5  },
            };

            const leaveTypeInfo = {
                'Casual Leave':    'For personal matters (deducted from annual allowance)',
                'Sick Leave':      'Medical reasons — medical certificate required',
                'Emergency Leave': 'Urgent unforeseen circumstances (deducted from annual allowance)',
                'Study Leave':     'Professional exams only — max 5 days (first attempt) / 2 days (repeat). Supporting document required',
                'Maternity Leave': 'Before and after childbirth — max 98 calendar days',
                'Paternity Leave': 'New fathers — max 7 working days',
                'Annual Leave':    annualLeaveInfo && annualLeaveInfo.is_eligible
                    ? `Regular vacation (${annualLeaveInfo.entitlement} days/year · max ${annualLeaveInfo.max_days_per_run} per run · min 2 runs)`
                    : 'Requires 12+ months of service',
                'Without Pay':     'Leave without salary',
            };

            // ─────────────────────────────────────────────────────────────────────────────
            // DYNAMIC RWANDA PUBLIC HOLIDAY CALCULATOR
            // Calculates public holidays for any year based on official rules
            // ─────────────────────────────────────────────────────────────────────────────

            /**
             * Calculate Easter date for a given year (Anonymous Gregorian algorithm)
             * Returns Date object set to Easter Sunday
             */
            function getEasterDate(year) {
                const a = year % 19;
                const b = Math.floor(year / 100);
                const c = year % 100;
                const d = Math.floor(b / 4);
                const e = b % 4;
                const f = Math.floor((b + 8) / 25);
                const g = Math.floor((b - f + 1) / 3);
                const h = (19 * a + b - d - g + 15) % 30;
                const i = Math.floor(c / 4);
                const k = c % 4;
                const l = (32 + 2 * e + 2 * i - h - k) % 7;
                const m = Math.floor((a + 11 * h + 22 * l) / 451);
                const month = Math.floor((h + l - 7 * m + 114) / 31);
                const day = ((h + l - 7 * m + 114) % 31) + 1;
                
                return new Date(year, month - 1, day);
            }

            /**
             * Calculate Islamic holidays (Eid al-Fitr and Eid al-Adha)
             * These are approximations based on astronomical calculations
             * Note: Actual dates may vary by 1-2 days based on moon sighting
             */
            function getIslamicHolidays(year) {
                // Simplified calculations based on Umm al-Qura calendar approximation
                // For production, you might want to fetch from an API or maintain a lookup table
                
                // Eid al-Fitr: 1 Shawwal (approximate: 1st day after Ramadan)
                // Rough approximation: ~10-11 months after previous Eid
                // For 2025-2027 approximations:
                const eidFitriDates = {
                    2025: '2025-03-30',
                    2026: '2026-03-20',
                    2027: '2027-03-10'
                };
                
                // Eid al-Adha: 10 Dhu al-Hijjah (~70 days after Eid al-Fitr)
                const eidAdhaDates = {
                    2025: '2025-06-06',
                    2026: '2026-05-27',
                    2027: '2027-05-16'
                };
                
                return {
                    fitri: eidFitriDates[year] || null,
                    adha: eidAdhaDates[year] || null
                };
            }

            /**
             * Apply Rwanda's observance rule:
             * If holiday falls on weekend → observe on following Monday
             * Exception: April 7 (Genocide Memorial Day) is always observed on actual date
             */
            function applyObservanceRule(date, isGenocideMemorial = false) {
                if (isGenocideMemorial) {
                    return new Date(date); // Always observed on actual date
                }
                
                const dayOfWeek = date.getDay(); // 0 = Sunday, 6 = Saturday
                const observedDate = new Date(date);
                
                if (dayOfWeek === 0) { // Sunday
                    observedDate.setDate(observedDate.getDate() + 1); // Move to Monday
                } else if (dayOfWeek === 6) { // Saturday
                    observedDate.setDate(observedDate.getDate() + 2); // Move to Monday
                }
                
                return observedDate;
            }

            /**
             * Generate all public holidays for a given year
             */
            function getPublicHolidaysForYear(year) {
                const holidays = [];
                
                // Fixed date holidays
                const fixedHolidays = [
                    { name: "New Year's Day", month: 0, day: 1 }, // Jan 1
                    { name: "Day After New Year's Day", month: 0, day: 2 }, // Jan 2
                    { name: "National Heroes' Day", month: 1, day: 1 }, // Feb 1
                    { name: "Genocide Against the Tutsi Memorial Day", month: 3, day: 7, isGenocideMemorial: true }, // April 7
                    { name: "Labour Day", month: 4, day: 1 }, // May 1
                    { name: "Independence Day", month: 6, day: 1 }, // July 1
                    { name: "Liberation Day", month: 6, day: 4 }, // July 4
                    { name: "Umuganura Day", month: 7, day: 1 }, // First Friday in August (approximated as Aug 1 for calculation)
                    { name: "Assumption Day", month: 7, day: 15 }, // Aug 15
                    { name: "Christmas Day", month: 11, day: 25 }, // Dec 25
                    { name: "Boxing Day", month: 11, day: 26 } // Dec 26
                ];
                
                fixedHolidays.forEach(holiday => {
                    const date = new Date(year, holiday.month, holiday.day);
                    const observedDate = applyObservanceRule(date, holiday.isGenocideMemorial || false);
                    
                    holidays.push({
                        name: holiday.name,
                        date: date,
                        observed: observedDate,
                        originalDate: date.toISOString().split('T')[0],
                        observedDate: observedDate.toISOString().split('T')[0]
                    });
                });
                
                // Moveable Christian holidays (based on Easter)
                const easterSunday = getEasterDate(year);
                
                // Good Friday (Friday before Easter)
                const goodFriday = new Date(easterSunday);
                goodFriday.setDate(easterSunday.getDate() - 2);
                const observedGoodFriday = applyObservanceRule(goodFriday);
                holidays.push({
                    name: "Good Friday",
                    date: goodFriday,
                    observed: observedGoodFriday,
                    originalDate: goodFriday.toISOString().split('T')[0],
                    observedDate: observedGoodFriday.toISOString().split('T')[0]
                });
                
                // Easter Monday (Monday after Easter)
                const easterMonday = new Date(easterSunday);
                easterMonday.setDate(easterSunday.getDate() + 1);
                // Easter Monday is always Monday, so no observance rule needed
                holidays.push({
                    name: "Easter Monday",
                    date: easterMonday,
                    observed: easterMonday,
                    originalDate: easterMonday.toISOString().split('T')[0],
                    observedDate: easterMonday.toISOString().split('T')[0]
                });
                
                // Islamic holidays (tentative, need confirmation each year)
                const islamicHolidays = getIslamicHolidays(year);
                
                if (islamicHolidays.fitri) {
                    const eidFitri = new Date(islamicHolidays.fitri);
                    const observedEidFitri = applyObservanceRule(eidFitri);
                    holidays.push({
                        name: "Eid al-Fitr",
                        date: eidFitri,
                        observed: observedEidFitri,
                        originalDate: eidFitri.toISOString().split('T')[0],
                        observedDate: observedEidFitri.toISOString().split('T')[0],
                        tentative: true
                    });
                }
                
                if (islamicHolidays.adha) {
                    const eidAdha = new Date(islamicHolidays.adha);
                    const observedEidAdha = applyObservanceRule(eidAdha);
                    holidays.push({
                        name: "Eid al-Adha",
                        date: eidAdha,
                        observed: observedEidAdha,
                        originalDate: eidAdha.toISOString().split('T')[0],
                        observedDate: observedEidAdha.toISOString().split('T')[0],
                        tentative: true
                    });
                }
                
                // Handle Umuganura Day (First Friday in August)
                // Find the first Friday in August
                const firstAug = new Date(year, 7, 1);
                const dayOfWeek = firstAug.getDay();
                const daysUntilFriday = (5 - dayOfWeek + 7) % 7;
                const umuganura = new Date(firstAug);
                umuganura.setDate(firstAug.getDate() + daysUntilFriday);
                const observedUmuganura = applyObservanceRule(umuganura);
                
                // Update the placeholder Umuganura entry
                const umuganuraIndex = holidays.findIndex(h => h.name === "Umuganura Day");
                if (umuganuraIndex !== -1) {
                    holidays[umuganuraIndex] = {
                        name: "Umuganura Day",
                        date: umuganura,
                        observed: observedUmuganura,
                        originalDate: umuganura.toISOString().split('T')[0],
                        observedDate: observedUmuganura.toISOString().split('T')[0]
                    };
                }
                
                return holidays;
            }

            /**
             * Get observed holiday dates for a range of years (current year and next year)
             */
            function getAllObservedHolidayDates() {
                const currentYear = new Date().getFullYear();
                const years = [currentYear - 1, currentYear, currentYear + 1, currentYear + 2]; // Include adjacent years for coverage
                
                let allHolidays = [];
                years.forEach(year => {
                    const holidays = getPublicHolidaysForYear(year);
                    allHolidays = allHolidays.concat(holidays);
                });
                
                // Remove duplicates (e.g., holidays that might be observed on same date across years)
                const uniqueHolidays = {};
                allHolidays.forEach(holiday => {
                    uniqueHolidays[holiday.observedDate] = holiday;
                });
                
                return Object.values(uniqueHolidays);
            }

            // Get all holidays for current and future years
            const allPublicHolidays = getAllObservedHolidayDates();
            
            // Create a quick lookup for holiday checking
            const holidayLookup = {};
            allPublicHolidays.forEach(holiday => {
                holidayLookup[holiday.observedDate] = holiday.name;
            });

            // Generate array of observed holiday dates for Flatpickr
            const observedHolidayDates = allPublicHolidays.map(h => h.observedDate);

            // Check if a date is a public holiday
            function isPublicHoliday(date) {
                const dateStr = typeof date === 'string' ? date : date.toISOString().split('T')[0];
                return !!holidayLookup[dateStr];
            }

            // Get public holiday name for a date
            function getPublicHolidayName(date) {
                const dateStr = typeof date === 'string' ? date : date.toISOString().split('T')[0];
                return holidayLookup[dateStr] || null;
            }

            // Calculate working days excluding weekends AND public holidays
            function calculateWorkingDays(from, to) {
                let count = 0;
                const cur = new Date(from);
                const end = new Date(to);
                
                while (cur <= end) {
                    const d = cur.getDay();
                    const dateStr = cur.toISOString().split('T')[0];
                    
                    // Skip weekends (Saturday = 6, Sunday = 0) and public holidays
                    if (d !== 0 && d !== 6 && !isPublicHoliday(dateStr)) {
                        count++;
                    }
                    cur.setDate(cur.getDate() + 1);
                }
                return count;
            }

            // Check if a date range contains any public holidays
            function getPublicHolidaysInRange(from, to) {
                const holidays = [];
                const cur = new Date(from);
                const end = new Date(to);
                
                while (cur <= end) {
                    const dateStr = cur.toISOString().split('T')[0];
                    const holidayName = getPublicHolidayName(dateStr);
                    if (holidayName) {
                        holidays.push({
                            date: dateStr,
                            name: holidayName
                        });
                    }
                    cur.setDate(cur.getDate() + 1);
                }
                return holidays;
            }

            function isInRecommendedPeriod(from, to) {
                for (let d = new Date(from); d <= to; d.setDate(d.getDate() + 1)) {
                    if (d.getMonth() + 1 >= 7 && d.getMonth() + 1 <= 9) return true;
                }
                return false;
            }

            // ═════════════════════════════════════════════════════════════════════════════
            // FETCH USER'S OWN BLOCKED DATES FROM API
            // ═════════════════════════════════════════════════════════════════════════════
            let blockedDates = [];
            let flatpickrInstances = { from: null, to: null };

            // Fetch user's own blocked dates when page loads
            async function fetchBlockedDates() {
                try {
                    const response = await fetch('/api/leave-calendar/blocked-dates', {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) {
                        console.warn('Failed to fetch blocked dates:', response.status);
                        return;
                    }

                    const data = await response.json();
                    blockedDates = data.blocked_dates || [];
                    
                    console.log(`Loaded ${blockedDates.length} of your existing leave requests`);
                    console.log(`Loaded ${observedHolidayDates.length} public holidays dynamically`);
                    
                    // Reinitialize Flatpickr with your personal blocked dates and public holidays
                    initializeFlatpickrWithBlockedDates();
                    
                } catch (error) {
                    console.error('Error fetching blocked dates:', error);
                }
            }

            // Initialize or reinitialize Flatpickr with user's personal blocked dates and public holidays
            function initializeFlatpickrWithBlockedDates() {
                // Destroy existing instances if they exist
                if (flatpickrInstances.from) flatpickrInstances.from.destroy();
                if (flatpickrInstances.to) flatpickrInstances.to.destroy();

                // FROM date picker with user's personal blocked dates and public holidays
                flatpickrInstances.from = flatpickr("#leave_from", {
                    dateFormat: "Y-m-d",
                    minDate: "today",
                    disable: [
                        // Disable user's own blocked date ranges (their existing leaves)
                        ...blockedDates.map(range => ({
                            from: range.from,
                            to: range.to
                        })),
                        // Disable public holidays (individual days)
                        ...observedHolidayDates,
                        // Disable weekends (Saturday = 6, Sunday = 0)
                        function(date) {
                            return (date.getDay() === 0 || date.getDay() === 6);
                        }
                    ],
                    onChange: function(selectedDates, dateStr, instance) {
                        // Update TO picker's minDate
                        if (flatpickrInstances.to) {
                            flatpickrInstances.to.set('minDate', dateStr);
                        }
                        
                        // Trigger assessment and duration calculation
                        calculateDuration();
                        runAssessment();
                        checkDepartmentCongestion();
                        checkPublicHolidays();
                    },
                    onDayCreate: function(dObj, dStr, fp, dayElem) {
                        const dateStr = dayElem.dateObj.toISOString().split('T')[0];
                        
                        // Check if date is user's own blocked leave
                        const isUserBlocked = blockedDates.some(range => {
                            return dateStr >= range.from && dateStr <= range.to;
                        });
                        
                        // Check if date is a public holiday
                        const isHoliday = isPublicHoliday(dateStr);
                        const holidayName = getPublicHolidayName(dateStr);
                        
                        if (isUserBlocked) {
                            dayElem.classList.add('blocked-date');
                            dayElem.title = 'You already have a leave request for this period';
                            dayElem.style.background = '#fee2e2';
                            dayElem.style.color = '#991b1b';
                            dayElem.style.textDecoration = 'line-through';
                        } else if (isHoliday) {
                            dayElem.classList.add('public-holiday');
                            dayElem.title = `Public Holiday: ${holidayName}`;
                            dayElem.style.background = '#f3e8ff'; // Light purple
                            dayElem.style.color = '#6b21a8';
                            dayElem.style.fontWeight = 'bold';
                        }
                    }
                });

                // TO date picker with user's personal blocked dates and public holidays
                flatpickrInstances.to = flatpickr("#leave_to", {
                    dateFormat: "Y-m-d",
                    minDate: "today",
                    disable: [
                        ...blockedDates.map(range => ({
                            from: range.from,
                            to: range.to
                        })),
                        ...observedHolidayDates,
                        function(date) {
                            return (date.getDay() === 0 || date.getDay() === 6);
                        }
                    ],
                    onChange: function(selectedDates, dateStr, instance) {
                        calculateDuration();
                        runAssessment();
                        checkDepartmentCongestion();
                        checkPublicHolidays();
                    },
                    onDayCreate: function(dObj, dStr, fp, dayElem) {
                        const dateStr = dayElem.dateObj.toISOString().split('T')[0];
                        
                        const isUserBlocked = blockedDates.some(range => {
                            return dateStr >= range.from && dateStr <= range.to;
                        });
                        
                        const isHoliday = isPublicHoliday(dateStr);
                        const holidayName = getPublicHolidayName(dateStr);
                        
                        if (isUserBlocked) {
                            dayElem.classList.add('blocked-date');
                            dayElem.title = 'You already have a leave request for this period';
                            dayElem.style.background = '#fee2e2';
                            dayElem.style.color = '#991b1b';
                            dayElem.style.textDecoration = 'line-through';
                        } else if (isHoliday) {
                            dayElem.classList.add('public-holiday');
                            dayElem.title = `Public Holiday: ${holidayName}`;
                            dayElem.style.background = '#f3e8ff';
                            dayElem.style.color = '#6b21a8';
                            dayElem.style.fontWeight = 'bold';
                        }
                    }
                });
            }

            // Check for public holidays in selected range and show info
            function checkPublicHolidays() {
                const from = document.getElementById('leave_from').value;
                const to = document.getElementById('leave_to').value;
                
                if (!from || !to) return;
                
                const holidays = getPublicHolidaysInRange(from, to);
                const holidayBanner = document.getElementById('public-holiday-info');
                const holidayMessage = document.getElementById('public-holiday-message');
                
                if (holidays.length > 0) {
                    const holidayList = holidays.map(h => `${h.date}: ${h.name}`).join('<br>');
                    holidayMessage.innerHTML = `
                        <strong>Public holidays in your selected range:</strong><br>
                        ${holidayList}<br>
                        <span class="text-xs">These days are automatically excluded from working days calculation.</span>
                    `;
                    holidayBanner.classList.remove('hidden');
                } else {
                    holidayBanner.classList.add('hidden');
                }
            }

            // Check department congestion (warning only)
            async function checkDepartmentCongestion() {
                const leaveType = document.getElementById('leave_type').value;
                const fromVal = document.getElementById('leave_from').value;
                const toVal = document.getElementById('leave_to').value;
                
                if (!leaveType || !fromVal || !toVal) return;
                
                try {
                    const response = await fetch('/api/leave-calendar/check-availability', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            leave_from: fromVal,
                            leave_to: toVal,
                            leave_type: leaveType
                        })
                    });
                    
                    const data = await response.json();
                    
                    if (data.department_warnings && data.department_warnings.length > 0) {
                        showDepartmentWarnings(data.department_warnings);
                    } else {
                        hideDepartmentWarnings();
                    }
                    
                } catch (error) {
                    console.error('Error checking department congestion:', error);
                }
            }

            // Show department warnings in a non-blocking banner
            function showDepartmentWarnings(warnings) {
                const warningBanner = document.getElementById('department-warning-banner');
                const listContainer = document.getElementById('department-warnings-list');
                
                // Populate warnings
                listContainer.innerHTML = warnings.map(warning => {
                    const colorClass = warning.level === 'high' ? 'text-red-700' : 
                                      (warning.level === 'medium' ? 'text-yellow-700' : 'text-blue-700');
                    return `<p class="text-sm ${colorClass}">• ${warning.message}</p>`;
                }).join('');
                
                warningBanner.classList.remove('hidden');
            }

            function hideDepartmentWarnings() {
                const warningBanner = document.getElementById('department-warning-banner');
                if (warningBanner) {
                    warningBanner.classList.add('hidden');
                }
            }

            // ─────────────────────────────────────────────────────────────────────────────
            // ASSESSMENT ENGINE
            // ─────────────────────────────────────────────────────────────────────────────
            function runAssessment() {
                const leaveType = document.getElementById('leave_type').value;
                const fromVal   = document.getElementById('leave_from').value;
                const toVal     = document.getElementById('leave_to').value;
                const isFirst   = document.querySelector('input[name="is_first_attempt"]:checked')?.value === '1';
                const hasMedDoc = document.getElementById('medical_certificate')?.files?.length > 0;
                const hasSuppDoc= document.getElementById('supporting_document')?.files?.length > 0;

                const errors   = [];   // red  — will block submit
                const warnings = [];   // amber — allowed but flagged
                const oks      = [];   // green
                const submitBtn = document.getElementById('submit-button');

                if (!leaveType) {
                    hideBanner();
                    return;
                }

                // ── Date checks ──────────────────────────────────────────────────────────
                if (!fromVal || !toVal) { hideBanner(); return; }

                const from = new Date(fromVal);
                const to   = new Date(toVal);
                const today = new Date(); today.setHours(0,0,0,0);

                if (from < today) errors.push('Start date cannot be in the past.');
                if (to < from)    errors.push('End date must be on or after the start date.');

                // ── Check if dates overlap with user's existing leaves ──────────────────
                const isDateBlocked = blockedDates.some(range => {
                    const rangeFrom = new Date(range.from);
                    const rangeTo = new Date(range.to);
                    return (from <= rangeTo && to >= rangeFrom); // Any overlap
                });

                if (isDateBlocked) {
                    errors.push('These dates overlap with one of your existing leave requests.');
                }

                const totalDays   = Math.round((to - from) / 86400000) + 1;
                const workingDays = calculateWorkingDays(from, to);

                // ── Check for public holidays in range (info only) ─────────────────────
                const holidaysInRange = getPublicHolidaysInRange(fromVal, toVal);
                if (holidaysInRange.length > 0) {
                    oks.push(`${holidaysInRange.length} public holiday(s) in range (excluded from working days) ✓`);
                }

                // ── Type-specific checks ─────────────────────────────────────────────────
                if (leaveType === 'Sick Leave') {
                    if (!hasMedDoc)
                        errors.push('Medical certificate is required for Sick Leave. Please upload before submitting.');
                    else
                        oks.push('Medical certificate uploaded ✓');
                }

                if (leaveType === 'Study Leave') {
                    if (!hasSuppDoc)
                        errors.push('Supporting document is required for Study Leave.');
                    else
                        oks.push('Supporting document uploaded ✓');

                    const maxDays = isFirst ? 5 : 2;
                    if (totalDays > maxDays)
                        errors.push(`Study Leave cannot exceed ${maxDays} days (${isFirst ? 'first' : 'repeat'} attempt). You requested ${totalDays}.`);
                    else
                        oks.push(`Duration within limit (${totalDays} of ${maxDays} days) ✓`);
                }

                if (leaveType === 'Maternity Leave') {
                    if (totalDays > 98)
                        errors.push(`Maternity Leave cannot exceed 98 days. You requested ${totalDays}.`);
                    else
                        oks.push(`Duration within limit (${totalDays} of 98 days) ✓`);
                }

                if (leaveType === 'Paternity Leave') {
                    if (workingDays > 7)
                        errors.push(`Paternity Leave cannot exceed 7 working days. You requested ${workingDays}.`);
                    else
                        oks.push(`Duration within limit (${workingDays} of 7 working days) ✓`);
                }

                if (leaveType === 'Annual Leave') {
                    if (!annualLeaveInfo || !annualLeaveInfo.is_eligible) {
                        errors.push('You are not yet eligible for Annual Leave. You need at least 12 months of service.');
                    } else {
                        const maxRun     = annualLeaveInfo.max_days_per_run;
                        const remaining  = annualLeaveInfo.remaining_days;
                        const runsCount  = annualLeaveInfo.annual_runs_count;

                        if (workingDays > maxRun)
                            errors.push(`Annual Leave cannot exceed ${maxRun} working days per run. You requested ${workingDays}.`);
                        else
                            oks.push(`Duration within per-run limit (${workingDays} of ${maxRun}) ✓`);

                        if (workingDays > remaining)
                            errors.push(`Insufficient Annual Leave balance. Requested: ${workingDays} days · Remaining: ${remaining} days.`);
                        else
                            oks.push(`Sufficient balance (${remaining} days remaining) ✓`);

                        if (!isInRecommendedPeriod(from, to))
                            warnings.push('Annual Leave is recommended during July–September. Out-of-period requests require additional justification.');
                        else
                            oks.push('Dates fall within recommended period (Jul–Sep) ✓');

                        if (runsCount === 0)
                            warnings.push('This will be your first run of Annual Leave. Remember you must take at least 2 separate runs per year.');
                        else if (runsCount >= 1)
                            oks.push(`You have taken ${runsCount} run(s) this year ✓`);
                    }
                }

                if (leaveType === 'Emergency Leave') {
                    if (!annualLeaveInfo || !annualLeaveInfo.is_eligible) {
                        warnings.push(
                            'You are applying for Emergency Leave before completing 12 months of service. ' +
                            'This is permitted, but these days will be automatically deducted from your Annual Leave balance once you become eligible.'
                        );
                    } else {
                        const remaining = annualLeaveInfo.remaining_days;
                        if (workingDays > remaining)
                            warnings.push(`This Emergency Leave (${workingDays} days) will exceed your remaining Annual Leave balance (${remaining} days).`);
                        else
                            oks.push(`Within Annual Leave balance (${remaining} remaining) ✓`);
                    }
                }

                if (leaveType === 'Casual Leave') {
                    if (annualLeaveInfo && annualLeaveInfo.is_eligible) {
                        const remaining = annualLeaveInfo.remaining_days;
                        const afterBalance = remaining - workingDays;
                        if (afterBalance < 0)
                            errors.push(`Insufficient Annual Leave balance for Casual Leave. Remaining: ${remaining} days · Requested: ${workingDays} days.`);
                        else if (afterBalance <= 3)
                            warnings.push(`After this Casual Leave, you will have only ${afterBalance} day(s) of Annual Leave remaining.`);
                        else
                            oks.push(`Within Annual Leave balance ✓`);
                    }
                }

                // ── Show banner ──────────────────────────────────────────────────────────
                renderBanner(errors, warnings, oks);
            }

            function renderBanner(errors, warnings, oks) {
                const banner = document.getElementById('assessment-banner');
                const icon   = document.getElementById('assessment-icon');
                const title  = document.getElementById('assessment-title');
                const list   = document.getElementById('assessment-list');
                const submit = document.getElementById('submit-button');

                banner.classList.remove('hidden', 'border-red-400', 'bg-red-50',
                                        'border-yellow-400', 'bg-yellow-50',
                                        'border-green-400', 'bg-green-50');
                list.innerHTML = '';

                if (errors.length > 0) {
                    // RED
                    banner.classList.add('border-red-400', 'bg-red-50');
                    icon.textContent  = '🚫';
                    title.textContent = 'This request has critical issues and will be rejected:';
                    title.className   = 'font-bold text-sm mb-1 text-red-800';
                    errors.forEach(e => {
                        const li = document.createElement('li');
                        li.className   = 'text-red-700 flex items-start gap-1';
                        li.innerHTML   = `<span class="mt-0.5">✗</span><span>${e}</span>`;
                        list.appendChild(li);
                    });
                    warnings.forEach(w => {
                        const li = document.createElement('li');
                        li.className = 'text-yellow-700 flex items-start gap-1 mt-1';
                        li.innerHTML = `<span class="mt-0.5">⚠</span><span>${w}</span>`;
                        list.appendChild(li);
                    });
                    if (submit) { submit.disabled = true; submit.classList.add('opacity-50', 'cursor-not-allowed'); }

                } else if (warnings.length > 0) {
                    // AMBER
                    banner.classList.add('border-yellow-400', 'bg-yellow-50');
                    icon.textContent  = '⚠️';
                    title.textContent = 'Your request has warnings — review before submitting:';
                    title.className   = 'font-bold text-sm mb-1 text-yellow-800';
                    warnings.forEach(w => {
                        const li = document.createElement('li');
                        li.className = 'text-yellow-700 flex items-start gap-1';
                        li.innerHTML = `<span class="mt-0.5">⚠</span><span>${w}</span>`;
                        list.appendChild(li);
                    });
                    oks.forEach(o => {
                        const li = document.createElement('li');
                        li.className = 'text-green-700 flex items-start gap-1';
                        li.innerHTML = `<span class="mt-0.5">✓</span><span>${o}</span>`;
                        list.appendChild(li);
                    });
                    if (submit) { submit.disabled = false; submit.classList.remove('opacity-50', 'cursor-not-allowed'); }

                } else if (oks.length > 0) {
                    // GREEN
                    banner.classList.add('border-green-400', 'bg-green-50');
                    icon.textContent  = '✅';
                    title.textContent = 'All checks passed — your request looks good:';
                    title.className   = 'font-bold text-sm mb-1 text-green-800';
                    oks.forEach(o => {
                        const li = document.createElement('li');
                        li.className = 'text-green-700 flex items-start gap-1';
                        li.innerHTML = `<span class="mt-0.5">✓</span><span>${o}</span>`;
                        list.appendChild(li);
                    });
                    if (submit) { submit.disabled = false; submit.classList.remove('opacity-50', 'cursor-not-allowed'); }
                } else {
                    hideBanner();
                }
            }

            function hideBanner() {
                document.getElementById('assessment-banner').classList.add('hidden');
                const submit = document.getElementById('submit-button');
                if (submit) { submit.disabled = false; submit.classList.remove('opacity-50', 'cursor-not-allowed'); }
            }

            // ─────────────────────────────────────────────────────────────────────────────
            // CONDITIONAL SECTIONS
            // ─────────────────────────────────────────────────────────────────────────────
            const leaveTypeSelect = document.getElementById('leave_type');

            leaveTypeSelect.addEventListener('change', function () {
                const leaveType           = this.value;
                const infoElement         = document.getElementById('leave-type-info');
                const medicalSection      = document.getElementById('medical-certificate-section');
                const studyAttemptSection = document.getElementById('study-leave-attempt');
                const supportingDocSection= document.getElementById('supporting-document-section');

                // Reset
                medicalSection.classList.add('hidden');
                studyAttemptSection.classList.add('hidden');
                supportingDocSection.classList.add('hidden');
                document.getElementById('medical_certificate').required = false;
                document.getElementById('supporting_document').required = false;

                infoElement.textContent = leaveTypeInfo[leaveType] || '';

                if (leaveType === 'Sick Leave') {
                    medicalSection.classList.remove('hidden');
                    document.getElementById('medical_certificate').required = true;
                }
                if (leaveType === 'Study Leave') {
                    studyAttemptSection.classList.remove('hidden');
                    supportingDocSection.classList.remove('hidden');
                    document.getElementById('supporting_document').required = true;
                }

                calculateDuration();
                runAssessment();
                if (document.getElementById('leave_from').value && document.getElementById('leave_to').value) {
                    checkDepartmentCongestion();
                    checkPublicHolidays();
                }
            });

            document.querySelectorAll('input[name="is_first_attempt"]').forEach(r => {
                r.addEventListener('change', function () {
                    leaveTypeLimits['Study Leave'].max = this.value === '1' ? 5 : 2;
                    calculateDuration();
                    runAssessment();
                });
            });

            // ─────────────────────────────────────────────────────────────────────────────
            // DURATION CALCULATION
            // ─────────────────────────────────────────────────────────────────────────────
            function calculateDuration() {
                const from = document.getElementById('leave_from').value;
                const to   = document.getElementById('leave_to').value;

                if (from && to) {
                    const f = new Date(from), t = new Date(to);
                    const total   = Math.ceil((t - f) / 86400000) + 1;
                    const working = calculateWorkingDays(f, t);

                    if (total > 0) {
                        document.getElementById('total-days-count').textContent   = total;
                        document.getElementById('working-days-count').textContent = working;
                        document.getElementById('duration-display').classList.remove('hidden');
                    } else {
                        document.getElementById('duration-display').classList.add('hidden');
                    }
                } else {
                    document.getElementById('duration-display').classList.add('hidden');
                }
            }

            // Re-run assessment when file inputs change
            ['medical_certificate','supporting_document'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.addEventListener('change', runAssessment);
            });

            // Fetch blocked dates on page load
            document.addEventListener('DOMContentLoaded', function() {
                fetchBlockedDates();
            });
        </script>
    @endpush

    @push('styles')
        <style>
            /* Visual styling for blocked dates in calendar */
            .flatpickr-day.blocked-date {
                background-color: #fee2e2 !important;
                color: #991b1b !important;
                cursor: not-allowed !important;
                position: relative;
                text-decoration: line-through;
            }

            .flatpickr-day.blocked-date:hover {
                background-color: #fecaca !important;
            }

            .flatpickr-day.blocked-date::after {
                content: '✕';
                position: absolute;
                top: 2px;
                right: 2px;
                font-size: 8px;
                color: #dc2626;
            }

            /* Visual styling for public holidays in calendar */
            .flatpickr-day.public-holiday {
                background-color: #f3e8ff !important;
                color: #6b21a8 !important;
                cursor: not-allowed !important;
                position: relative;
                font-weight: bold;
            }

            .flatpickr-day.public-holiday:hover {
                background-color: #e9d5ff !important;
            }

            .flatpickr-day.public-holiday::after {
                content: '🎉';
                position: absolute;
                top: 2px;
                right: 2px;
                font-size: 8px;
                color: #6b21a8;
            }
        </style>
    @endpush
</x-app-layout>
