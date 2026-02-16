<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add gender field to users table
        Schema::table('users', function (Blueprint $table) {
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('email');
        });

        // Add enhanced controls to leave_requests table
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->string('medical_certificate')->nullable()->after('reason');
            $table->boolean('is_first_attempt')->default(true)->after('medical_certificate');
            $table->integer('working_days_count')->default(0)->after('is_first_attempt');
            $table->boolean('is_out_of_recommended_period')->default(false)->after('working_days_count');
            $table->text('admin_notes')->nullable()->after('comment');
        });

        // Add tracking for annual leave splits to employees table
        Schema::table('employees', function (Blueprint $table) {
            $table->integer('annual_leave_runs_count')->default(0)->after('annual_leave');
            $table->json('annual_leave_history')->nullable()->after('annual_leave_runs_count');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('gender');
        });

        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropColumn([
                'medical_certificate',
                'is_first_attempt',
                'working_days_count',
                'is_out_of_recommended_period',
                'admin_notes'
            ]);
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'annual_leave_runs_count',
                'annual_leave_history'
            ]);
        });
    }
};