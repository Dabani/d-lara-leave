<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // hire_date — may already exist; guard with hasColumn
            if (!Schema::hasColumn('employees', 'hire_date')) {
                $table->date('hire_date')->nullable()->after('department');
            }

            // Tracks emergency leave taken BEFORE the employee's 12-month mark.
            // Once eligible, this is deducted from annual leave automatically.
            if (!Schema::hasColumn('employees', 'pre_annual_emergency_leave')) {
                $table->unsignedInteger('pre_annual_emergency_leave')
                      ->default(0)
                      ->after('emergency_leave')
                      ->comment('Emergency days taken before 12-month eligibility — deducted from annual on maturation');
            }

            // Flag: has the pre-annual deduction been applied?
            if (!Schema::hasColumn('employees', 'emergency_deduction_applied')) {
                $table->boolean('emergency_deduction_applied')
                      ->default(false)
                      ->after('pre_annual_emergency_leave');
            }
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'pre_annual_emergency_leave',
                'emergency_deduction_applied',
            ]);
        });
    }
};