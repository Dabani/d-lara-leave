<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Employee extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'department',
        'profile_image',
        'casual_leave',
        'sick_leave',
        'emergency_leave',
        'study_leave',
        'maternity_leave',
        'paternity_leave',
        'annual_leave',
        'without_pay_leave',
        'total_leave',
        'leave_year',
        'status',
        'annual_leave_runs_count',
        'annual_leave_history',
        'hire_date',
    ];

    protected $casts = [
        'leave_year' => 'integer',
        'annual_leave_history' => 'array',
        'hire_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function leaveRequestsByYear($year = null): HasMany
    {
        $year = $year ?? date('Y');
        return $this->hasMany(LeaveRequest::class)
            ->whereYear('leave_from', $year);
    }

    public function departmentModel(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department', 'name');
    }

    /**
     * Calculate years of service based on hire date
     */
    public function getYearsOfService(): float
    {
        if (!$this->hire_date) {
            return 0;
        }

        $hireDate = Carbon::parse($this->hire_date);
        $now = Carbon::now();
        
        return $hireDate->diffInYears($now, true); // true = absolute difference including decimals
    }

    /**
     * Get months of service
     */
    public function getMonthsOfService(): int
    {
        if (!$this->hire_date) {
            return 0;
        }

        $hireDate = Carbon::parse($this->hire_date);
        return $hireDate->diffInMonths(Carbon::now());
    }

    /**
     * Check if employee is eligible for annual leave (requires 12+ months of service)
     */
    public function isEligibleForAnnualLeave(): bool
    {
        return $this->getMonthsOfService() >= 12;
    }

    /**
     * Get annual leave entitlement based on years of service
     * 
     * Rules:
     * - Less than 1 year: 0 days (not eligible)
     * - 1-2 years: 18 working days
     * - 3-5 years: 19 working days
     * - 6-8 years: 20 working days
     * - 9+ years: 21 working days
     */
    public function getAnnualLeaveEntitlement(): int
    {
        $years = $this->getYearsOfService();

        if ($years < 1) {
            return 0;
        } elseif ($years < 3) {
            return 18;
        } elseif ($years < 6) {
            return 19;
        } elseif ($years < 9) {
            return 20;
        } else {
            return 21;
        }
    }

    /**
     * Get maximum days per run based on years of service
     * 
     * Rules:
     * - 1-2 years: 9 working days per run
     * - 3+ years: 10 working days per run
     * - 9+ years: 11 working days per run
     */
    public function getMaxDaysPerRun(): int
    {
        $years = $this->getYearsOfService();

        if ($years < 3) {
            return 9;
        } elseif ($years < 9) {
            return 10;
        } else {
            return 11;
        }
    }

    /**
     * Get total leaves taken this year (annual + casual + emergency)
     * Casual and emergency are deducted from annual leave allowance
     */
    public function getTotalAnnualEquivalentLeavesTaken(): int
    {
        $currentYear = $this->leave_year ?? date('Y');

        $approvedLeaves = $this->leaveRequests()
            ->where('status', 'Approved')
            ->whereYear('leave_from', $currentYear)
            ->whereIn('leave_type', ['Annual Leave', 'Casual Leave', 'Emergency Leave'])
            ->get();

        return $approvedLeaves->sum('working_days_count');
    }

    /**
     * Get annual leave days taken (excluding casual and emergency)
     */
    public function getAnnualLeaveDaysThisYear(): int
    {
        return $this->leaveRequests()
            ->where('leave_type', 'Annual Leave')
            ->where('status', 'Approved')
            ->whereYear('leave_from', $this->leave_year ?? date('Y'))
            ->sum('working_days_count');
    }

    /**
     * Get casual leave days taken
     */
    public function getCasualLeaveDaysThisYear(): int
    {
        return $this->leaveRequests()
            ->where('leave_type', 'Casual Leave')
            ->where('status', 'Approved')
            ->whereYear('leave_from', $this->leave_year ?? date('Y'))
            ->sum('working_days_count');
    }

    /**
     * Get emergency leave days taken
     */
    public function getEmergencyLeaveDaysThisYear(): int
    {
        return $this->leaveRequests()
            ->where('leave_type', 'Emergency Leave')
            ->where('status', 'Approved')
            ->whereYear('leave_from', $this->leave_year ?? date('Y'))
            ->sum('working_days_count');
    }

    /**
     * Get remaining annual leave (including casual and emergency deduction)
     */
    public function getRemainingAnnualLeave(): int
    {
        $entitlement = $this->getAnnualLeaveEntitlement();
        $taken = $this->getTotalAnnualEquivalentLeavesTaken();
        
        return max(0, $entitlement - $taken);
    }

    /**
     * Get annual leave statistics with breakdown
     */
    public function getAnnualLeaveStats(): array
    {
        $currentYear = $this->leave_year ?? date('Y');

        $annualRuns = $this->leaveRequests()
            ->where('leave_type', 'Annual Leave')
            ->where('status', 'Approved')
            ->whereYear('leave_from', $currentYear)
            ->count();

        $annualDays = $this->getAnnualLeaveDaysThisYear();
        $casualDays = $this->getCasualLeaveDaysThisYear();
        $emergencyDays = $this->getEmergencyLeaveDaysThisYear();
        $totalTaken = $annualDays + $casualDays + $emergencyDays;

        return [
            'entitlement' => $this->getAnnualLeaveEntitlement(),
            'annual_days_taken' => $annualDays,
            'casual_days_taken' => $casualDays,
            'emergency_days_taken' => $emergencyDays,
            'total_days_taken' => $totalTaken,
            'remaining_days' => $this->getRemainingAnnualLeave(),
            'annual_runs_count' => $annualRuns,
            'max_days_per_run' => $this->getMaxDaysPerRun(),
            'years_of_service' => round($this->getYearsOfService(), 1),
            'is_eligible' => $this->isEligibleForAnnualLeave(),
        ];
    }

    /**
     * Check if employee can take annual leave
     */
    public function canTakeAnnualLeave(int $requestedDays): array
    {
        if (!$this->isEligibleForAnnualLeave()) {
            return [
                'can_take' => false,
                'reason' => 'You need at least 12 months of service to be eligible for annual leave.',
                'months_remaining' => max(0, 12 - $this->getMonthsOfService()),
            ];
        }

        $remaining = $this->getRemainingAnnualLeave();

        return [
            'can_take' => $remaining >= $requestedDays,
            'remaining_days' => $remaining,
            'requested_days' => $requestedDays,
            'entitlement' => $this->getAnnualLeaveEntitlement(),
        ];
    }

    public function getTotalLeavesThisYear(): int
    {
        return $this->leaveRequests()
            ->where('status', 'Approved')
            ->whereYear('leave_from', $this->leave_year)
            ->sum(\DB::raw('DATEDIFF(leave_to, leave_from) + 1'));
    }

    public function getLeaveBalance(string $leaveType): int
    {
        $leaveTypes = [
            'Casual Leave' => 'casual_leave',
            'Sick Leave' => 'sick_leave',
            'Emergency Leave' => 'emergency_leave',
            'Study Leave' => 'study_leave',
            'Maternity Leave' => 'maternity_leave',
            'Paternity Leave' => 'paternity_leave',
            'Annual Leave' => 'annual_leave',
            'Without Pay' => 'without_pay_leave',
        ];

        $field = $leaveTypes[$leaveType] ?? null;
        return $field ? $this->$field : 0;
    }

    /**
     * Get the count of annual leave runs for current year
     */
    public function getAnnualLeaveRunsThisYear(): int
    {
        return $this->leaveRequests()
            ->where('leave_type', 'Annual Leave')
            ->where('status', 'Approved')
            ->whereYear('leave_from', $this->leave_year)
            ->count();
    }

    /**
     * Record annual leave history
     */
    public function recordAnnualLeave(int $leaveRequestId, int $days): void
    {
        $history = $this->annual_leave_history ?? [];
        $history[] = [
            'leave_request_id' => $leaveRequestId,
            'days' => $days,
            'date' => now()->toDateString(),
            'year' => $this->leave_year
        ];
        
        $this->annual_leave_history = $history;
        $this->annual_leave_runs_count = $this->getAnnualLeaveRunsThisYear();
        $this->save();
    }
}
