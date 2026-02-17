<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany; 
use Carbon\Carbon;

class LeaveRequest extends Model
{
    use HasFactory;

    protected $table      = 'leave_requests';
    protected $primaryKey = 'id';
    public    $timestamps = true;

    protected $fillable = [
        'employee_id',
        'leave_type',
        'leave_from',
        'leave_to',
        'reason',
        'status',
        'comment',
        'medical_certificate',
        'is_first_attempt',
        'working_days_count',
        'is_out_of_recommended_period',
        'admin_notes',
        'supporting_document',
        'is_pre_annual_emergency',
        'assessment_status',
        'assessed_by',
        'assessed_at',
        'mp_status',
        'mp_reviewed_by',
        'mp_reviewed_at',
    ];

    protected $casts = [
        'is_first_attempt'             => 'boolean',
        'is_out_of_recommended_period' => 'boolean',
        'leave_from'                   => 'date',
        'leave_to'                     => 'date',
        'is_pre_annual_emergency'      => 'boolean',
        'assessed_at'                  => 'datetime',
        'mp_reviewed_at'               => 'datetime',
    ];

    protected $attributes = [
        'status' => 'pending',
    ];

    // =========================================================================
    // RELATIONSHIPS
    // =========================================================================
    
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    // who assessed at HOD / admin level
    public function assessor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assessed_by');
    }

    // who reviewed at Managing Partner level
    public function mpReviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mp_reviewed_by');
    }

    // comment / rejection-notice thread
    public function comments(): HasMany
    {
        return $this->hasMany(LeaveComment::class)->orderBy('created_at', 'asc');
    }

    // =========================================================================
    // DURATION HELPERS
    // =========================================================================

    public function getTotalDays(): int
    {
        return Carbon::parse($this->leave_from)->diffInDays(Carbon::parse($this->leave_to)) + 1;
    }

    public function calculateWorkingDays(): int
    {
        $from        = Carbon::parse($this->leave_from);
        $to          = Carbon::parse($this->leave_to);
        $workingDays = 0;

        while ($from->lte($to)) {
            if (!in_array($from->dayOfWeek, [Carbon::SATURDAY, Carbon::SUNDAY])) {
                $workingDays++;
            }
            $from->addDay();
        }

        return $workingDays;
    }

    public function isWithinRecommendedPeriod(): bool
    {
        $from             = Carbon::parse($this->leave_from);
        $to               = Carbon::parse($this->leave_to);
        $recommendedStart = Carbon::create($from->year, 7, 1);
        $recommendedEnd   = Carbon::create($from->year, 9, 30);

        return $from->between($recommendedStart, $recommendedEnd)
            || $to->between($recommendedStart, $recommendedEnd);
    }

    public function getDurationDescription(): string
    {
        $totalDays   = $this->getTotalDays();
        $workingDays = $this->working_days_count ?: $this->calculateWorkingDays();

        if (in_array($this->leave_type, ['Annual Leave', 'Paternity Leave', 'Casual Leave', 'Emergency Leave'])) {
            return "{$workingDays} working days ({$totalDays} calendar days)";
        }

        return "{$totalDays} days";
    }

    // =========================================================================
    // WORKFLOW STATE HELPERS
    // Used by: AssessorController, assessor dashboard view, leave-status component
    // =========================================================================

    /** Waiting for HOD to act — no assessment decision yet */
    public function isPendingAssessment(): bool
    {
        return $this->status === 'pending' && $this->assessment_status === null;
    }

    /** HOD approved — now sitting in Admin's queue */
    public function isAssessed(): bool
    {
        return $this->assessment_status === 'assessed_approved';
    }

    /** HOD application approved by HOD — awaiting Managing Partner review */
    public function isPendingMPReview(): bool
    {
        return $this->assessment_status === 'assessed_approved'
            && $this->mp_status === null;
    }

    // =========================================================================
    // BOOT HOOKS 
    // Auto-calculates working_days_count and is_out_of_recommended_period
    // on every create/update, so controllers don't need to do it manually.
    // =========================================================================

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($leaveRequest) {
            $leaveRequest->working_days_count = $leaveRequest->calculateWorkingDays();

            if ($leaveRequest->leave_type === 'Annual Leave') {
                $leaveRequest->is_out_of_recommended_period = !$leaveRequest->isWithinRecommendedPeriod();
            }
        });

        static::updating(function ($leaveRequest) {
            if ($leaveRequest->isDirty(['leave_from', 'leave_to'])) {
                $leaveRequest->working_days_count = $leaveRequest->calculateWorkingDays();

                if ($leaveRequest->leave_type === 'Annual Leave') {
                    $leaveRequest->is_out_of_recommended_period = !$leaveRequest->isWithinRecommendedPeriod();
                }
            }
        });
    }
}
