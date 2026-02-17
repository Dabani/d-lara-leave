<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Expand role beyond just 'user'/'admin'
            // Values: user | assessor | managing_partner | admin
            $table->string('role')->default('user')->change();

            // Which department this user heads (only for assessors)
            $table->string('heads_department')->nullable()->after('role');

            // Gender — kept here because the User model owns it
            // (already exists in some installs; use ->change() guards below)
        });

        // Add gender if it doesn't already exist
        if (!Schema::hasColumn('users', 'gender')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('gender')->nullable()->after('email');
            });
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('heads_department');
        });
    }
};