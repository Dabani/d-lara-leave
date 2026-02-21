<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Leave Request') }}
        </h2>
    </x-slot>

    <div class="py-10">
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

            {{-- Status Warning --}}
            @if($leaveRequest->status != 'pending')
                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6" role="alert">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 mt-0.5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <p class="font-bold">Note:</p>
                            <p>This leave request has already been <strong>{{ $leaveRequest->status }}</strong>. Editing will reset the status to pending and require re-approval.</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Annual Leave Statistics --}}
            @if(isset($annualLeaveStats) && $leaveRequest->leave_type === 'Annual Leave')
                <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4 mb-4">
                    <h3 class="font-semibold text-indigo-900 mb-2">Your Annual Leave Summary</h3>
                    <div class="grid grid-cols-3 gap-4 text-sm">
                        <div>
                            <span class="text-gray-600">Days Used:</span>
                            <span class="font-bold text-indigo-900">{{ $annualLeaveStats['total_days'] }}/18</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Remaining:</span>
                            <span class="font-bold text-green-600">{{ $annualLeaveStats['remaining_days'] }} days</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Runs Taken:</span>
                            <span class="font-bold text-indigo-900">{{ $annualLeaveStats['total_runs'] }}</span>
                        </div>
                    </div>
                    <p class="text-xs text-gray-600 mt-2">
                        <strong>Note:</strong> Annual leave must be split into at least 2 runs, each not exceeding 9 working days.
                    </p>
                </div>
            @endif
            
            {{-- Pre-submission Assessment Banner --}}
            <div id="assessment-banner" class="hidden mb-6 border-l-4 p-4 rounded-lg">
                <div class="flex items-start">
                    <div class="flex-shrink-0 text-2xl mr-3" id="banner-icon"></div>
                    <div class="flex-1">
                        <h3 class="font-bold text-lg mb-2" id="banner-title"></h3>
                        <ul class="list-disc ml-5 space-y-1 text-sm" id="banner-list"></ul>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('leave-request.update', $leaveRequest->id) }}" method="POST" enctype="multipart/form-data" id="leaveRequestForm">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-6">
                            <label for="leave_type" class="block text-sm font-medium text-gray-700 mb-2">
                                Leave Type <span class="text-red-500">*</span>
                            </label>
                            <select id="leave_type" name="leave_type" required
                                    class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Select Leave Type</option>
                                
                                @if($employee->user->isFemale())
                                    <option value="Maternity Leave" 
                                            {{ old('leave_type', $leaveRequest->leave_type) === 'Maternity Leave' ? 'selected' : '' }}>
                                        Maternity Leave (Max 98 days)
                                    </option>
                                @endif
                                
                                @if($employee->user->isMale())
                                    <option value="Paternity Leave" 
                                            {{ old('leave_type', $leaveRequest->leave_type) === 'Paternity Leave' ? 'selected' : '' }}>
                                        Paternity Leave (Max 7 working days)
                                    </option>
                                @endif
                                
                                <option value="Annual Leave" 
                                        {{ old('leave_type', $leaveRequest->leave_type) === 'Annual Leave' ? 'selected' : '' }}>
                                    Annual Leave 
                                    @if(isset($annualLeaveStats))
                                        (Max {{ $annualLeaveStats['max_days_per_run'] ?? 9 }} working days per run)
                                    @endif
                                </option>
                                
                                <option value="Casual Leave" 
                                        {{ old('leave_type', $leaveRequest->leave_type) === 'Casual Leave' ? 'selected' : '' }}>
                                    Casual Leave
                                </option>
                                
                                <option value="Sick Leave" 
                                        {{ old('leave_type', $leaveRequest->leave_type) === 'Sick Leave' ? 'selected' : '' }}>
                                    Sick Leave (Medical certificate required)
                                </option>
                                
                                <option value="Emergency Leave" 
                                        {{ old('leave_type', $leaveRequest->leave_type) === 'Emergency Leave' ? 'selected' : '' }}>
                                    Emergency Leave
                                </option>
                                
                                <option value="Study Leave" 
                                        {{ old('leave_type', $leaveRequest->leave_type) === 'Study Leave' ? 'selected' : '' }}>
                                    Study Leave (Supporting document required)
                                </option>
                                
                                <option value="Without Pay" 
                                        {{ old('leave_type', $leaveRequest->leave_type) === 'Without Pay' ? 'selected' : '' }}>
                                    Without Pay
                                </option>
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
                        <div id="study-leave-attempt" class="mb-6 {{ $leaveRequest->leave_type === 'Study Leave' ? '' : 'hidden' }}">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Is this your first attempt? <span class="text-red-500">*</span>
                            </label>
                            <div class="flex gap-4">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="is_first_attempt" value="1" class="form-radio" 
                                           {{ $leaveRequest->is_first_attempt ? 'checked' : '' }}>
                                    <span class="ml-2">First Attempt (Max 5 days)</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="is_first_attempt" value="0" class="form-radio"
                                           {{ !$leaveRequest->is_first_attempt ? 'checked' : '' }}>
                                    <span class="ml-2">Repeat Attempt (Max 2 days)</span>
                                </label>
                            </div>
                        </div>

                        {{-- Medical Certificate Upload --}}
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
                        <div id="annual-leave-warning" class="mb-6 p-4 bg-red-50 border border-red-300 rounded-md {{ $leaveRequest->is_out_of_recommended_period && $leaveRequest->leave_type === 'Annual Leave' ? '' : 'hidden' }}">
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

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                            <div>
                                <label for="leave_from" class="block text-sm font-medium text-gray-700 mb-2">
                                    Leave From <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="leave_from" id="leave_from" required
                                       value="{{ old('leave_from', $leaveRequest->leave_from) }}"
                                       class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                @error('leave_from')
                                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="leave_to" class="block text-sm font-medium text-gray-700 mb-2">
                                    Leave To <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="leave_to" id="leave_to" required
                                       value="{{ old('leave_to', $leaveRequest->leave_to) }}"
                                       class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                @error('leave_to')
                                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Duration Display -->
                        <div class="mb-6 p-4 bg-blue-50 rounded-md" id="duration-display">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-medium text-blue-900">
                                        Total Days: <span id="total-days-count" class="font-bold">
                                            {{ \Carbon\Carbon::parse($leaveRequest->leave_from)->diffInDays(\Carbon\Carbon::parse($leaveRequest->leave_to)) + 1 }}
                                        </span>
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-blue-900">
                                        Working Days: <span id="working-days-count" class="font-bold">
                                            {{ $leaveRequest->working_days_count ?: \Carbon\Carbon::parse($leaveRequest->leave_from)->diffInDays(\Carbon\Carbon::parse($leaveRequest->leave_to)) + 1 }}
                                        </span>
                                        <span class="text-xs text-gray-600">(excluding weekends)</span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="mb-6">
                            <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                                Reason <span class="text-gray-400">(Optional)</span>
                            </label>
                            <textarea id="reason" name="reason" rows="4" 
          placeholder="Briefly explain the reason for your leave..."
          class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ old('reason', $leaveRequest->reason) }}</textarea>
                            @error('reason')
                                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex flex-col sm:flex-row gap-3 justify-center">
                            <button type="submit"
                                    id="submit-button"
                                    style="background-color:#3f35da" 
                                    class="hover:opacity-90 text-white font-bold py-3 px-6 rounded-md transition duration-150">
                                Save Changes
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
        // Get dynamic annual leave info from blade variable
        const annualLeaveInfo = @json($annualLeaveStats ?? null);

        const leaveTypeInfo = {
            'Casual Leave': 'For personal matters and short-term absences (Deducted from annual allowance)',
            'Sick Leave': 'For medical reasons and health-related issues (Medical certificate required)',
            'Emergency Leave': 'For urgent, unforeseen circumstances (Deducted from annual allowance)',
            'Study Leave': 'For professional exams only (Max 5 days first attempt, 2 days repeat) - Supporting document required',
            'Maternity Leave': 'For expecting mothers before and after childbirth (Max 98 days)',
            'Paternity Leave': 'For new fathers to support their family (Max 7 working days)',
            'Annual Leave': annualLeaveInfo && annualLeaveInfo.is_eligible 
                ? `Regular vacation time (${annualLeaveInfo.entitlement} working days/year based on ${annualLeaveInfo.years_of_service} years service, max ${annualLeaveInfo.max_days_per_run} days per run, min 2 runs required)`
                : 'Not eligible - You need at least 12 months of service to apply for annual leave',
            'Without Pay': 'Leave without salary compensation'
        };

        // Leave type specific limits
        const leaveTypeLimits = {
            'Maternity Leave': { type: 'calendar', max: 98 },
            'Paternity Leave': { type: 'working', max: 7 },
            'Annual Leave': { type: 'working', max: 9, yearlyMax: 18 },
            'Study Leave': { type: 'calendar', max: 5 }
        };

        // Update info text on load
        const leaveTypeSelect = document.getElementById('leave_type');
        if (leaveTypeSelect.value && leaveTypeInfo[leaveTypeSelect.value]) {
            document.getElementById('leave-type-info').textContent = leaveTypeInfo[leaveTypeSelect.value];
        }

        // Update leave type selection handler
        leaveTypeSelect.addEventListener('change', function() {
            const leaveType = this.value;
            const infoElement = document.getElementById('leave-type-info');
            const medicalSection = document.getElementById('medical-certificate-section');
            const studyAttemptSection = document.getElementById('study-leave-attempt');
            const supportingDocSection = document.getElementById('supporting-document-section');
            
            // Reset all sections
            medicalSection.classList.add('hidden');
            studyAttemptSection.classList.add('hidden');
            supportingDocSection.classList.add('hidden');
            document.getElementById('medical_certificate').required = false;
            document.getElementById('supporting_document').required = false;
            
            // Update info text
            if (leaveType && leaveTypeInfo[leaveType]) {
                infoElement.textContent = leaveTypeInfo[leaveType];
            } else {
                infoElement.textContent = '';
            }
            
            // Show medical certificate for sick leave
            if (leaveType === 'Sick Leave') {
                medicalSection.classList.remove('hidden');
                document.getElementById('medical_certificate').required = true;
            }
            
            // Show supporting document and attempt selection for study leave
            if (leaveType === 'Study Leave') {
                studyAttemptSection.classList.remove('hidden');
                supportingDocSection.classList.remove('hidden');
                document.getElementById('supporting_document').required = true;
            }
            
            // Recalculate duration if dates are set
            calculateDuration();
        });

        // Update study leave max based on attempt
        document.querySelectorAll('input[name="is_first_attempt"]').forEach(radio => {
            radio.addEventListener('change', function() {
                leaveTypeLimits['Study Leave'].max = this.value === '1' ? 5 : 2;
                calculateDuration();
            });
        });

        // Calculate working days (excluding weekends)
        function calculateWorkingDays(fromDate, toDate) {
            let count = 0;
            const current = new Date(fromDate);
            const end = new Date(toDate);
            
            while (current <= end) {
                const dayOfWeek = current.getDay();
                // Exclude Saturday (6) and Sunday (0)
                if (dayOfWeek !== 0 && dayOfWeek !== 6) {
                    count++;
                }
                current.setDate(current.getDate() + 1);
            }
            
            return count;
        }

        // Check if dates fall in July-September
        function isInRecommendedPeriod(fromDate, toDate) {
            const from = new Date(fromDate);
            const to = new Date(toDate);
            
            for (let d = new Date(from); d <= to; d.setDate(d.getDate() + 1)) {
                const month = d.getMonth() + 1;
                if (month >= 7 && month <= 9) {
                    return true;
                }
            }
            return false;
        }

        // Calculate duration
        function calculateDuration() {
            const from = document.getElementById('leave_from').value;
            const to = document.getElementById('leave_to').value;
            const leaveType = document.getElementById('leave_type').value;
            const annualWarning = document.getElementById('annual-leave-warning');
            
            if (from && to) {
                const fromDate = new Date(from);
                const toDate = new Date(to);
                const diffTime = toDate - fromDate;
                const totalDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                const workingDays = calculateWorkingDays(fromDate, toDate);
                
                if (totalDays > 0) {
                    document.getElementById('total-days-count').textContent = totalDays;
                    document.getElementById('working-days-count').textContent = workingDays;
                    
                    // Show warning for annual leave outside recommended period
                    if (leaveType === 'Annual Leave') {
                        if (!isInRecommendedPeriod(from, to)) {
                            annualWarning.classList.remove('hidden');
                        } else {
                            annualWarning.classList.add('hidden');
                        }
                    } else {
                        annualWarning.classList.add('hidden');
                    }
                    
                    // Validate against limits
                    validateDuration(leaveType, totalDays, workingDays);
                }
            }
        }

        // Validate duration against leave type limits
        function validateDuration(leaveType, totalDays, workingDays) {
            if (!leaveTypeLimits[leaveType]) return;
            
            const limit = leaveTypeLimits[leaveType];
            const daysToCheck = limit.type === 'working' ? workingDays : totalDays;
            
            if (daysToCheck > limit.max) {
                alert(`Warning: ${leaveType} cannot exceed ${limit.max} ${limit.type} days. You have requested ${daysToCheck} ${limit.type} days.`);
            }
        }

        document.getElementById('leave_from').addEventListener('change', calculateDuration);
        document.getElementById('leave_to').addEventListener('change', calculateDuration);

        // Set min date for leave_to based on leave_from
        document.getElementById('leave_from').addEventListener('change', function() {
            document.getElementById('leave_to').min = this.value;
        });

        // Calculate on page load
        calculateDuration();
        
        function runEditAssessment() {
            const leaveType = document.getElementById('leave_type').value;
            const from = document.getElementById('leave_from').value;
            const to = document.getElementById('leave_to').value;
            
            if (!leaveType || !from || !to) {
                hideBanner();
                return;
            }
            
            const errors = [];
            const warnings = [];
            const oks = [];
            
            const fromDate = new Date(from);
            const toDate = new Date(to);
            const totalDays = Math.ceil((toDate - fromDate) / (1000 * 60 * 60 * 24)) + 1;
            const workingDays = calculateWorkingDays(fromDate, toDate);
            
            // Check past dates
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            if (fromDate < today) {
                errors.push('Start date cannot be in the past');
            }
            
            // Check date order
            if (toDate < fromDate) {
                errors.push('End date must be after start date');
            }
            
            // Type-specific validation
            switch(leaveType) {
                case 'Sick Leave':
                    const medCert = document.getElementById('medical_certificate');
                    if (!medCert.files || medCert.files.length === 0) {
                        errors.push('Medical certificate is required for sick leave');
                    } else {
                        oks.push('Medical certificate provided');
                    }
                    break;
                    
                case 'Study Leave':
                    const suppDoc = document.getElementById('supporting_document');
                    const isFirstAttempt = document.querySelector('input[name="is_first_attempt"]:checked');
                    const maxDays = isFirstAttempt && isFirstAttempt.value === '1' ? 5 : 2;
                    
                    if (!suppDoc.files || suppDoc.files.length === 0) {
                        errors.push('Supporting document is required for study leave');
                    } else {
                        oks.push('Supporting document provided');
                    }
                    
                    if (totalDays > maxDays) {
                        errors.push(`Study leave cannot exceed ${maxDays} days (${isFirstAttempt && isFirstAttempt.value === '1' ? 'first attempt' : 'repeat attempt'})`);
                    } else {
                        oks.push(`Within ${maxDays}-day limit`);
                    }
                    break;
                    
                case 'Maternity Leave':
                    if (totalDays > 98) {
                        errors.push('Maternity leave cannot exceed 98 days');
                    } else {
                        oks.push(`Within 98-day limit (${totalDays} days requested)`);
                    }
                    break;
                    
                case 'Paternity Leave':
                    if (workingDays > 7) {
                        errors.push('Paternity leave cannot exceed 7 working days');
                    } else {
                        oks.push(`Within 7-day limit (${workingDays} working days)`);
                    }
                    break;
                    
                case 'Annual Leave':
                    if (annualLeaveInfo && annualLeaveInfo.is_eligible) {
                        const maxPerRun = annualLeaveInfo.max_days_per_run || 9;
                        const remaining = annualLeaveInfo.remaining_days || 0;
                        
                        if (workingDays > maxPerRun) {
                            errors.push(`Annual leave cannot exceed ${maxPerRun} working days per run`);
                        }
                        
                        if (workingDays > remaining) {
                            errors.push(`Insufficient balance (${remaining} days remaining, ${workingDays} requested)`);
                        } else {
                            oks.push(`Sufficient balance (${remaining} days available)`);
                        }
                        
                        if (!isInRecommendedPeriod(from, to)) {
                            warnings.push('Outside recommended period (July-September)');
                        } else {
                            oks.push('Within recommended period');
                        }
                    } else {
                        errors.push('Not eligible for annual leave (requires 12+ months of service)');
                    }
                    break;
                    
                case 'Casual Leave':
                case 'Emergency Leave':
                    if (annualLeaveInfo) {
                        const remaining = annualLeaveInfo.remaining_days || 0;
                        if (workingDays > remaining) {
                            warnings.push(`This will use your annual leave balance (${remaining} days available)`);
                        } else {
                            oks.push(`Will be deducted from annual balance`);
                        }
                    }
                    break;
            }
            
            // Show results
            if (errors.length > 0) {
                showBanner('red', '🚫 Cannot Submit', [...errors, ...warnings, ...oks]);
                document.getElementById('submit-button').disabled = true;
                document.getElementById('submit-button').classList.add('opacity-50', 'cursor-not-allowed');
            } else if (warnings.length > 0) {
                showBanner('amber', '⚠️ Warnings', [...warnings, ...oks]);
                document.getElementById('submit-button').disabled = false;
                document.getElementById('submit-button').classList.remove('opacity-50', 'cursor-not-allowed');
            } else if (oks.length > 0) {
                showBanner('green', '✅ Ready to Submit', oks);
                document.getElementById('submit-button').disabled = false;
                document.getElementById('submit-button').classList.remove('opacity-50', 'cursor-not-allowed');
            } else {
                hideBanner();
            }
        }

        function showBanner(color, title, items) {
            const banner = document.getElementById('assessment-banner');
            const icon = document.getElementById('banner-icon');
            const titleEl = document.getElementById('banner-title');
            const list = document.getElementById('banner-list');
            
            // Color schemes
            const colors = {
                red: 'border-red-400 bg-red-50',
                amber: 'border-yellow-400 bg-yellow-50',
                green: 'border-green-400 bg-green-50'
            };
            
            // Update classes
            banner.className = `mb-6 border-l-4 p-4 rounded-lg ${colors[color]}`;
            banner.classList.remove('hidden');
            
            // Update content
            icon.textContent = color === 'red' ? '🚫' : color === 'amber' ? '⚠️' : '✅';
            titleEl.textContent = title;
            
            list.innerHTML = items.map(item => {
                const itemColor = item.includes('Insufficient') || item.includes('cannot exceed') || item.includes('required') 
                    ? 'text-red-700'
                    : item.includes('Outside') || item.includes('Warning')
                    ? 'text-yellow-700'
                    : 'text-green-700';
                return `<li class="${itemColor}">${item}</li>`;
            }).join('');
        }

        function hideBanner() {
            document.getElementById('assessment-banner').classList.add('hidden');
            document.getElementById('submit-button').disabled = false;
            document.getElementById('submit-button').classList.remove('opacity-50', 'cursor-not-allowed');
        }

        // Hook into existing events
        const originalCalculate = calculateDuration;
        calculateDuration = function() {
            originalCalculate();
            runEditAssessment();
        };

        // Run on page load
        document.addEventListener('DOMContentLoaded', function() {
            runEditAssessment();
        });

        // Add event listeners
        document.getElementById('leave_type').addEventListener('change', runEditAssessment);
        document.getElementById('leave_from').addEventListener('change', runEditAssessment);
        document.getElementById('leave_to').addEventListener('change', runEditAssessment);

        // Also run when files are selected
        const medCertInput = document.getElementById('medical_certificate');
        const suppDocInput = document.getElementById('supporting_document');
        if (medCertInput) medCertInput.addEventListener('change', runEditAssessment);
        if (suppDocInput) suppDocInput.addEventListener('change', runEditAssessment);

    </script>
    @endpush
</x-app-layout>
