<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            // Assessment workflow status
            // pending → assessed_approved / assessed_rejected → Approved / Rejected
            if (!Schema::hasColumn('leave_requests', 'assessment_status')) {
                $table->string('assessment_status')
                      ->nullable()
                      ->after('status')
                      ->comment('null=not yet assessed | assessed_approved | assessed_rejected');
            }

            if (!Schema::hasColumn('leave_requests', 'assessed_by')) {
                $table->foreignId('assessed_by')
                      ->nullable()
                      ->after('assessment_status')
                      ->constrained('users')
                      ->nullOnDelete();
            }

            if (!Schema::hasColumn('leave_requests', 'assessed_at')) {
                $table->timestamp('assessed_at')->nullable()->after('assessed_by');
            }

            // For managing-partner review of HOD applications
            if (!Schema::hasColumn('leave_requests', 'mp_status')) {
                $table->string('mp_status')
                      ->nullable()
                      ->after('assessed_at')
                      ->comment('null | mp_approved | mp_rejected — for HOD applications only');
            }

            if (!Schema::hasColumn('leave_requests', 'mp_reviewed_by')) {
                $table->foreignId('mp_reviewed_by')
                      ->nullable()
                      ->after('mp_status')
                      ->constrained('users')
                      ->nullOnDelete();
            }

            if (!Schema::hasColumn('leave_requests', 'mp_reviewed_at')) {
                $table->timestamp('mp_reviewed_at')->nullable()->after('mp_reviewed_by');
            }

            if (!Schema::hasColumn('leave_requests', 'supporting_document')) {
                $table->string('supporting_document')->nullable();
            }

            if (!Schema::hasColumn('leave_requests', 'is_first_attempt')) {
                $table->boolean('is_first_attempt')->default(true);
            }

            if (!Schema::hasColumn('leave_requests', 'is_out_of_recommended_period')) {
                $table->boolean('is_out_of_recommended_period')->default(false);
            }

            // Flag: this was an early-emergency-leave (before 12-month threshold)
            if (!Schema::hasColumn('leave_requests', 'is_pre_annual_emergency')) {
                $table->boolean('is_pre_annual_emergency')
                      ->default(false)
                      ->comment('Emergency leave taken before 12-month service milestone');
            }
        });
    }

    public function down(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropForeign(['assessed_by']);
            $table->dropForeign(['mp_reviewed_by']);
            $table->dropColumn([
                'assessment_status', 'assessed_by', 'assessed_at',
                'mp_status', 'mp_reviewed_by', 'mp_reviewed_at',
                'is_pre_annual_emergency',
            ]);
        });
    }
};