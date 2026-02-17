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
        'pre_annual_emergency_leave',
        'emergency_deduction_applied',
    ];

    protected $casts = [
        'leave_year'                  => 'integer',
        'annual_leave_history'        => 'array',  
        'hire_date'                   => 'date',   
        'emergency_deduction_applied' => 'boolean',
    ];

    // =========================================================================
    // RELATIONSHIPS  (all UNCHANGED)
    // =========================================================================

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

    // =========================================================================
    // SERVICE DURATION 
    // =========================================================================

    public function getYearsOfService(): float
    {
        if (!$this->hire_date) {
            return 0;
        }

        $hireDate = Carbon::parse($this->hire_date);
        $now = Carbon::now();

        return $hireDate->diffInYears($now, true);
    }

    public function getMonthsOfService(): int
    {
        if (!$this->hire_date) {
            return 0;
        }

        $hireDate = Carbon::parse($this->hire_date);
        return $hireDate->diffInMonths(Carbon::now());
    }

    // NOTE: Your existing method is named isEligibleForAnnualLeave().
    // Part 10A used isAnnualLeaveEligible() — we keep YOUR name here.
    // The ApplyPreAnnualEmergencyDeductions command and AssessorController
    // both call isEligibleForAnnualLeave() to match.
    public function isEligibleForAnnualLeave(): bool
    {
        return $this->getMonthsOfService() >= 12;
    }

    // =========================================================================
    // ANNUAL LEAVE ENTITLEMENT
    // =========================================================================

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

    // =========================================================================
    // ANNUAL LEAVE USAGE BREAKDOWN
    // =========================================================================

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

    public function getAnnualLeaveDaysThisYear(): int
    {
        return $this->leaveRequests()
            ->where('leave_type', 'Annual Leave')
            ->where('status', 'Approved')
            ->whereYear('leave_from', $this->leave_year ?? date('Y'))
            ->sum('working_days_count');
    }

    public function getCasualLeaveDaysThisYear(): int
    {
        return $this->leaveRequests()
            ->where('leave_type', 'Casual Leave')
            ->where('status', 'Approved')
            ->whereYear('leave_from', $this->leave_year ?? date('Y'))
            ->sum('working_days_count');
    }

    public function getEmergencyLeaveDaysThisYear(): int
    {
        return $this->leaveRequests()
            ->where('leave_type', 'Emergency Leave')
            ->where('status', 'Approved')
            ->whereYear('leave_from', $this->leave_year ?? date('Y'))
            ->sum('working_days_count');
    }

    public function getRemainingAnnualLeave(): int
    {
        $entitlement = $this->getAnnualLeaveEntitlement();
        $taken       = $this->getTotalAnnualEquivalentLeavesTaken();

        return max(0, $entitlement - $taken);
    }

    // =========================================================================
    // ANNUAL LEAVE STATS
    // Used by: profile.blade.php, create.blade.php JS payload, Exports
    // =========================================================================

    public function getAnnualLeaveStats(): array
    {
        $currentYear = $this->leave_year ?? date('Y');

        $annualRuns = $this->leaveRequests()
            ->where('leave_type', 'Annual Leave')
            ->where('status', 'Approved')
            ->whereYear('leave_from', $currentYear)
            ->count();

        $annualDays    = $this->getAnnualLeaveDaysThisYear();
        $casualDays    = $this->getCasualLeaveDaysThisYear();
        $emergencyDays = $this->getEmergencyLeaveDaysThisYear();
        $totalTaken    = $annualDays + $casualDays + $emergencyDays;

        return [
            'entitlement'          => $this->getAnnualLeaveEntitlement(),
            'annual_days_taken'    => $annualDays,
            'casual_days_taken'    => $casualDays,
            'emergency_days_taken' => $emergencyDays,
            'total_days_taken'     => $totalTaken,
            'remaining_days'       => $this->getRemainingAnnualLeave(),
            'annual_runs_count'    => $annualRuns,
            'max_days_per_run'     => $this->getMaxDaysPerRun(),
            'years_of_service'     => round($this->getYearsOfService(), 1),
            'is_eligible'          => $this->isEligibleForAnnualLeave(),
        ];
    }

    public function canTakeAnnualLeave(int $requestedDays): array
    {
        if (!$this->isEligibleForAnnualLeave()) {
            return [
                'can_take'          => false,
                'reason'            => 'You need at least 12 months of service to be eligible for annual leave.',
                'months_remaining'  => max(0, 12 - $this->getMonthsOfService()),
            ];
        }

        $remaining = $this->getRemainingAnnualLeave();

        return [
            'can_take'       => $remaining >= $requestedDays,
            'remaining_days' => $remaining,
            'requested_days' => $requestedDays,
            'entitlement'    => $this->getAnnualLeaveEntitlement(),
        ];
    }

    // =========================================================================
    // OTHER LEAVE HELPERS
    // =========================================================================

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
            'Casual Leave'    => 'casual_leave',
            'Sick Leave'      => 'sick_leave',
            'Emergency Leave' => 'emergency_leave',
            'Study Leave'     => 'study_leave',
            'Maternity Leave' => 'maternity_leave',
            'Paternity Leave' => 'paternity_leave',
            'Annual Leave'    => 'annual_leave',
            'Without Pay'     => 'without_pay_leave',
        ];

        $field = $leaveTypes[$leaveType] ?? null;
        return $field ? $this->$field : 0;
    }

    public function getAnnualLeaveRunsThisYear(): int
    {
        return $this->leaveRequests()
            ->where('leave_type', 'Annual Leave')
            ->where('status', 'Approved')
            ->whereYear('leave_from', $this->leave_year)
            ->count();
    }

    public function recordAnnualLeave(int $leaveRequestId, int $days): void
    {
        $history   = $this->annual_leave_history ?? [];
        $history[] = [
            'leave_request_id' => $leaveRequestId,
            'days'             => $days,
            'date'             => now()->toDateString(),
            'year'             => $this->leave_year,
        ];

        $this->annual_leave_history      = $history;
        $this->annual_leave_runs_count   = $this->getAnnualLeaveRunsThisYear();
        $this->save();
    }

    // =========================================================================
    // PRE-ANNUAL EMERGENCY DEDUCTION
    // Called by: artisan leave:apply-emergency-deductions (daily scheduled)
    // =========================================================================

    /**
     * Once an employee completes 12 months, deduct any emergency leave
     * they took before the eligibility threshold from their annual balance.
     * Guarded by emergency_deduction_applied so it only ever runs once.
     */
    public function applyPreAnnualEmergencyDeduction(): void
    {
        if (
            $this->isEligibleForAnnualLeave()
            && !$this->emergency_deduction_applied
            && ($this->pre_annual_emergency_leave ?? 0) > 0
        ) {
            $this->annual_leave                = max(0, ($this->annual_leave ?? 0) - $this->pre_annual_emergency_leave);
            $this->emergency_deduction_applied = true;
            $this->save();
        }
    }
}
