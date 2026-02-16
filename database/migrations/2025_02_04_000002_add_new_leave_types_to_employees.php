<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->integer('study_leave')->default(0)->after('emergency_leave');
            $table->integer('maternity_leave')->default(0)->after('study_leave');
            $table->integer('paternity_leave')->default(0)->after('maternity_leave');
            $table->integer('annual_leave')->default(0)->after('paternity_leave');
            $table->integer('without_pay_leave')->default(0)->after('annual_leave');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'study_leave',
                'maternity_leave',
                'paternity_leave',
                'annual_leave',
                'without_pay_leave'
            ]);
        });
    }
};
