<?php

namespace App\Console\Commands;

use App\Models\Employee;
use Illuminate\Console\Command;

class ApplyPreAnnualEmergencyDeductions extends Command
{
    protected $signature   = 'leave:apply-emergency-deductions';
    protected $description = 'Deduct pre-annual emergency leave from annual balance for newly eligible employees';

    public function handle(): int
    {
        $affected = Employee::where('emergency_deduction_applied', false)
            ->where('pre_annual_emergency_leave', '>', 0)
            ->whereNotNull('hire_date')
            ->get()
            ->filter(fn($e) => $e->isAnnualLeaveEligible());

        foreach ($affected as $employee) {
            $employee->applyPreAnnualEmergencyDeduction();
            $this->info("Applied deduction for employee #{$employee->id} — {$employee->pre_annual_emergency_leave} days");
        }

        $this->info("Done. {$affected->count()} employee(s) updated.");
        return 0;
    }
}
