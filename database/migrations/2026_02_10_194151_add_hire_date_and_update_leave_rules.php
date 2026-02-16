<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add hire_date to employees table
        Schema::table('employees', function (Blueprint $table) {
            $table->date('hire_date')->nullable()->after('leave_year');
        });

        // Add supporting_document field for study leave
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->string('supporting_document')->nullable()->after('medical_certificate');
        });

        // Update existing maternity leave limit (94 -> 98)
        // Update existing paternity leave limit (4 -> 7)
        // These are enforced in validation, not database constraints
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('hire_date');
        });

        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropColumn('supporting_document');
        });
    }
};