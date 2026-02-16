<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use Carbon\Carbon;

class SetHireDateForExistingEmployees extends Seeder
{
    public function run(): void
    {
        // Set hire date for existing employees
        // You can customize this logic based on your needs
        
        Employee::whereNull('hire_date')->each(function ($employee) {
            // Option 1: Set to their created_at date
            // $employee->hire_date = $employee->created_at->toDateString();
            
            // Option 2: Or set to a specific date (e.g., one year ago)
            $employee->hire_date = Carbon::now()->subYear()->toDateString();
            
            $employee->save();
        });

        $this->command->info('Hire dates set for ' . Employee::count() . ' employees.');
    }
}