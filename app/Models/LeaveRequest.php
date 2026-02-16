<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class LeaveRequest extends Model
{
    use HasFactory;
    
    protected $table = 'leave_requests';
    protected $primaryKey = 'id';
    public $timestamps = true;
    
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
    ];

    protected $casts = [
        'is_first_attempt' => 'boolean',
        'is_out_of_recommended_period' => 'boolean',
        'leave_from' => 'date',
        'leave_to' => 'date',
    ];

    protected $attributes = [
        'status' => 'pending',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Calculate total days (calendar days)
     */
    public function getTotalDays(): int
    {
        return Carbon::parse($this->leave_from)->diffInDays(Carbon::parse($this->leave_to)) + 1;
    }

    /**
     * Calculate working days (excluding weekends)
     */
    public function calculateWorkingDays(): int
    {
        $from = Carbon::parse($this->leave_from);
        $to = Carbon::parse($this->leave_to);
        $workingDays = 0;

        while ($from->lte($to)) {
            // Exclude Saturday (6) and Sunday (0)
            if (!in_array($from->dayOfWeek, [Carbon::SATURDAY, Carbon::SUNDAY])) {
                $workingDays++;
            }
            $from->addDay();
        }

        return $workingDays;
    }

    /**
     * Check if leave is within recommended annual leave period (July-September)
     */
    public function isWithinRecommendedPeriod(): bool
    {
        $from = Carbon::parse($this->leave_from);
        $to = Carbon::parse($this->leave_to);

        // Check if the leave period overlaps with July-September
        $recommendedStart = Carbon::create($from->year, 7, 1);
        $recommendedEnd = Carbon::create($from->year, 9, 30);

        return $from->between($recommendedStart, $recommendedEnd) || 
               $to->between($recommendedStart, $recommendedEnd);
    }

    /**
     * Get leave duration description
     */
    public function getDurationDescription(): string
    {
        $totalDays = $this->getTotalDays();
        $workingDays = $this->working_days_count ?: $this->calculateWorkingDays();

        if (in_array($this->leave_type, ['Annual Leave', 'Paternity Leave', 'Casual Leave', 'Emergency Leave'])) {
            return "{$workingDays} working days ({$totalDays} calendar days)";
        }

        return "{$totalDays} days";
    }

    /**
     * Auto-set working days before saving
     */
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
