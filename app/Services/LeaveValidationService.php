<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LeaveValidationService
{
    /**
     * Validate leave request based on type and business rules
     */
    public function validateLeaveRequest(Employee $employee, array $data): array
    {
        $leaveType = $data['leave_type'];
        $leaveFrom = Carbon::parse($data['leave_from']);
        $leaveTo = Carbon::parse($data['leave_to']);

        $errors = [];
        $warnings = [];
        $info = [];

        switch ($leaveType) {
            case 'Maternity Leave':
                $result = $this->validateMaternityLeave($employee, $leaveFrom, $leaveTo);
                break;

            case 'Paternity Leave':
                $result = $this->validatePaternityLeave($employee, $leaveFrom, $leaveTo);
                break;

            case 'Annual Leave':
                $result = $this->validateAnnualLeave($employee, $leaveFrom, $leaveTo);
                break;

            case 'Casual Leave':
                $result = $this->validateCasualLeave($employee, $leaveFrom, $leaveTo);
                break;

            case 'Emergency Leave':
                $result = $this->validateEmergencyLeave($employee, $leaveFrom, $leaveTo);
                break;

            case 'Study Leave':
                $result = $this->validateStudyLeave($employee, $leaveFrom, $leaveTo, $data);
                break;

            case 'Sick Leave':
                $result = $this->validateSickLeave($employee, $leaveFrom, $leaveTo, $data);
                break;

            default:
                $result = $this->validateGenericLeave($employee, $leaveFrom, $leaveTo, $leaveType);
                break;
        }

        return $result;
    }

    /**
     * Validate Maternity Leave
     * UPDATED: Max 98 days (was 94)
     */
    private function validateMaternityLeave(Employee $employee, Carbon $from, Carbon $to): array
    {
        $errors = [];
        $warnings = [];
        $info = [];

        // Check gender
        if (!$employee->user->isFemale()) {
            $errors[] = 'Maternity leave is only applicable to female employees.';
        }

        // Check duration - UPDATED TO 98 DAYS
        $totalDays = $from->diffInDays($to) + 1;
        if ($totalDays > 98) {
            $errors[] = "Maternity leave cannot exceed 98 days. Requested: {$totalDays} days.";
        }

        $info[] = "Maternity leave duration: {$totalDays} of 98 allowed days.";

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings,
            'info' => $info
        ];
    }

    /**
     * Validate Paternity Leave
     * UPDATED: Max 7 working days (was 4)
     */
    private function validatePaternityLeave(Employee $employee, Carbon $from, Carbon $to): array
    {
        $errors = [];
        $warnings = [];
        $info = [];

        // Check gender
        if (!$employee->user->isMale()) {
            $errors[] = 'Paternity leave is only applicable to male employees.';
        }

        // Calculate working days - UPDATED TO 7 WORKING DAYS
        $workingDays = $this->calculateWorkingDays($from, $to);
        if ($workingDays > 7) {
            $errors[] = "Paternity leave cannot exceed 7 working days. Requested: {$workingDays} working days.";
        }

        $info[] = "Paternity leave duration: {$workingDays} of 7 allowed working days.";

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings,
            'info' => $info
        ];
    }

    /**
     * Validate Annual Leave
     * UPDATED: Dynamic limits based on years of service, casual/emergency deducted
     */
    private function validateAnnualLeave(Employee $employee, Carbon $from, Carbon $to): array
    {
        $errors = [];
        $warnings = [];
        $info = [];

        // Check eligibility - NEW: Requires 12 months of service
        if (!$employee->isEligibleForAnnualLeave()) {
            $monthsRemaining = 12 - $employee->getMonthsOfService();
            $errors[] = "You need at least 12 months of service to be eligible for annual leave. You have {$employee->getMonthsOfService()} months. Please wait {$monthsRemaining} more month(s).";
            
            return [
                'valid' => false,
                'errors' => $errors,
                'warnings' => $warnings,
                'info' => $info
            ];
        }

        // Get employee's entitlements based on service years
        $entitlement = $employee->getAnnualLeaveEntitlement();
        $maxPerRun = $employee->getMaxDaysPerRun();
        $stats = $employee->getAnnualLeaveStats();

        // Calculate working days for this request
        $workingDays = $this->calculateWorkingDays($from, $to);

        // Check single run limit (dynamic: 9, 10, or 11 days based on service)
        if ($workingDays > $maxPerRun) {
            $errors[] = "Each annual leave run cannot exceed {$maxPerRun} working days (based on your {$stats['years_of_service']} years of service). Requested: {$workingDays} working days.";
        }

        // Check total annual leave including casual and emergency (NEW)
        $totalAfterApproval = $stats['total_days_taken'] + $workingDays;

        if ($totalAfterApproval > $entitlement) {
            $errors[] = "Annual leave limit exceeded. You have {$stats['remaining_days']} working days remaining out of {$entitlement} allowed per year (including casual and emergency leave).";
        }

        // Info about breakdown
        if ($stats['casual_days_taken'] > 0 || $stats['emergency_days_taken'] > 0) {
            $info[] = "Note: Casual ({$stats['casual_days_taken']} days) and Emergency ({$stats['emergency_days_taken']} days) leaves are deducted from your annual leave allowance.";
        }

        // Check minimum runs requirement
        $currentRuns = $stats['annual_runs_count'];
        if ($currentRuns === 0) {
            $info[] = "This is your first annual leave run. Remember: Annual leave must be split into at least 2 runs.";
        } elseif ($currentRuns === 1) {
            $remainingDays = $stats['remaining_days'];
            if ($remainingDays > 0) {
                $info[] = "You have {$remainingDays} working days remaining for your second (or more) annual leave run(s).";
            }
        }

        // Check recommended period (July-September)
        if (!$this->isWithinRecommendedPeriod($from, $to)) {
            $warnings[] = "Annual leave is recommended to be taken between July and September. Your requested period is outside this timeframe.";
        }

        // Detailed statistics
        $info[] = "Your annual leave entitlement: {$entitlement} working days (based on {$stats['years_of_service']} years of service).";
        $info[] = "Statistics: {$stats['annual_days_taken']} annual + {$stats['casual_days_taken']} casual + {$stats['emergency_days_taken']} emergency = {$stats['total_days_taken']} of {$entitlement} days used. {$stats['remaining_days']} days remaining.";

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings,
            'info' => $info
        ];
    }

    /**
     * Validate Casual Leave
     * NEW: Deducted from annual leave allowance
     */
    private function validateCasualLeave(Employee $employee, Carbon $from, Carbon $to): array
    {
        $errors = [];
        $warnings = [];
        $info = [];

        $workingDays = $this->calculateWorkingDays($from, $to);
        $stats = $employee->getAnnualLeaveStats();
        
        // Check if this will exceed annual leave allowance
        $totalAfterApproval = $stats['total_days_taken'] + $workingDays;
        $entitlement = $stats['entitlement'];

        if ($totalAfterApproval > $entitlement) {
            $errors[] = "Casual leave is deducted from your annual leave allowance. You have {$stats['remaining_days']} working days remaining out of {$entitlement} allowed per year.";
        }

        $info[] = "Casual leave: {$workingDays} working days. This will be deducted from your annual leave allowance.";
        $info[] = "Current usage: {$stats['total_days_taken']} of {$entitlement} days used. After this request: {$totalAfterApproval} of {$entitlement}.";

        // Check 30-day auto-conversion
        $totalDays = $from->diffInDays($to) + 1;
        if ($totalDays > 30) {
            $warnings[] = "Leave duration exceeds 30 days ({$totalDays} days). This will be automatically converted to 'Leave Without Pay'.";
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings,
            'info' => $info
        ];
    }

    /**
     * Validate Emergency Leave
     * NEW: Deducted from annual leave allowance
     */
    private function validateEmergencyLeave(Employee $employee, Carbon $from, Carbon $to): array
    {
        $errors = [];
        $warnings = [];
        $info = [];

        $workingDays = $this->calculateWorkingDays($from, $to);
        $stats = $employee->getAnnualLeaveStats();
        
        // Check if this will exceed annual leave allowance
        $totalAfterApproval = $stats['total_days_taken'] + $workingDays;
        $entitlement = $stats['entitlement'];

        if ($totalAfterApproval > $entitlement) {
            $errors[] = "Emergency leave is deducted from your annual leave allowance. You have {$stats['remaining_days']} working days remaining out of {$entitlement} allowed per year.";
        }

        $info[] = "Emergency leave: {$workingDays} working days. This will be deducted from your annual leave allowance.";
        $info[] = "Current usage: {$stats['total_days_taken']} of {$entitlement} days used. After this request: {$totalAfterApproval} of {$entitlement}.";

        // Check 30-day auto-conversion
        $totalDays = $from->diffInDays($to) + 1;
        if ($totalDays > 30) {
            $warnings[] = "Leave duration exceeds 30 days ({$totalDays} days). This will be automatically converted to 'Leave Without Pay'.";
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings,
            'info' => $info
        ];
    }

    /**
     * Validate Study Leave
     * UPDATED: Requires supporting document for professional exams
     */
    private function validateStudyLeave(Employee $employee, Carbon $from, Carbon $to, array $data): array
    {
        $errors = [];
        $warnings = [];
        $info = [];

        $totalDays = $from->diffInDays($to) + 1;
        $isFirstAttempt = $data['is_first_attempt'] ?? true;

        // NEW: Check for supporting document
        if (empty($data['supporting_document'])) {
            $errors[] = 'Supporting document is required for study leave (exam registration, professional exam notice, etc.).';
        }

        if ($isFirstAttempt) {
            if ($totalDays > 5) {
                $errors[] = "Study leave for first attempt cannot exceed 5 days. Requested: {$totalDays} days.";
            }
            $info[] = "Study leave (first attempt): {$totalDays} of 5 allowed days.";
        } else {
            if ($totalDays > 2) {
                $errors[] = "Study leave for repeat exam cannot exceed 2 days. Requested: {$totalDays} days.";
            }
            $info[] = "Study leave (repeat attempt): {$totalDays} of 2 allowed days.";
        }

        $info[] = "Study leave is reserved for professional exams only. Supporting documentation is required.";

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings,
            'info' => $info
        ];
    }

    /**
     * Validate Sick Leave
     * Rule: Requires medical certificate
     */
    private function validateSickLeave(Employee $employee, Carbon $from, Carbon $to, array $data): array
    {
        $errors = [];
        $warnings = [];
        $info = [];

        $totalDays = $from->diffInDays($to) + 1;

        // Check if medical certificate is provided
        if (empty($data['medical_certificate'])) {
            $errors[] = 'Medical certificate is required for sick leave.';
        }

        $info[] = "Sick leave duration: {$totalDays} days. Medical certificate is mandatory.";

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings,
            'info' => $info
        ];
    }

    /**
     * Validate Generic Leave
     * Rule: Convert to unpaid if exceeds 30 days (except maternity)
     */
    private function validateGenericLeave(Employee $employee, Carbon $from, Carbon $to, string $leaveType): array
    {
        $errors = [];
        $warnings = [];
        $info = [];

        $totalDays = $from->diffInDays($to) + 1;

        if ($totalDays > 30) {
            $warnings[] = "Leave duration exceeds 30 days ({$totalDays} days). This will be automatically converted to 'Leave Without Pay'.";
        }

        $info[] = "{$leaveType} duration: {$totalDays} days.";

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings,
            'info' => $info
        ];
    }

    /**
     * Calculate working days (excluding weekends)
     */
    public function calculateWorkingDays(Carbon $from, Carbon $to): int
    {
        $workingDays = 0;
        $current = $from->copy();

        while ($current->lte($to)) {
            if (!in_array($current->dayOfWeek, [Carbon::SATURDAY, Carbon::SUNDAY])) {
                $workingDays++;
            }
            $current->addDay();
        }

        return $workingDays;
    }

    /**
     * Check if dates fall within recommended annual leave period (July-September)
     */
    public function isWithinRecommendedPeriod(Carbon $from, Carbon $to): bool
    {
        $recommendedStart = Carbon::create($from->year, 7, 1);
        $recommendedEnd = Carbon::create($from->year, 9, 30);

        return $from->between($recommendedStart, $recommendedEnd) || 
               $to->between($recommendedStart, $recommendedEnd) ||
               ($from->lte($recommendedStart) && $to->gte($recommendedEnd));
    }

    /**
     * Get annual leave statistics for current year
     */
    public function getAnnualLeaveStats(Employee $employee): array
    {
        return $employee->getAnnualLeaveStats();
    }

    /**
     * Auto-convert leave type if necessary
     */
    public function autoConvertLeaveType(string $leaveType, int $totalDays): string
    {
        // Don't convert maternity leave
        if ($leaveType === 'Maternity Leave') {
            return $leaveType;
        }

        // Convert to unpaid if exceeds 30 days
        if ($totalDays > 30) {
            return 'Without Pay';
        }

        return $leaveType;
    }

    /**
     * Get human-readable validation summary
     */
    public function getValidationSummary(array $validationResult): string
    {
        $summary = [];

        if (!empty($validationResult['errors'])) {
            $summary[] = "Errors: " . implode(' ', $validationResult['errors']);
        }

        if (!empty($validationResult['warnings'])) {
            $summary[] = "Warnings: " . implode(' ', $validationResult['warnings']);
        }

        if (!empty($validationResult['info'])) {
            $summary[] = "Info: " . implode(' ', $validationResult['info']);
        }

        return implode("\n", $summary);
    }
}
