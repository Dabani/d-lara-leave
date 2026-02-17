<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leave_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('leave_request_id')
                  ->constrained('leave_requests')
                  ->cascadeOnDelete();
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
            $table->text('body');

            // Type distinguishes system-generated notices from human comments
            // values: comment | rejection_notice | suggestion | system
            $table->string('type')->default('comment');

            // Visibility: who can see this comment
            // values: all | admin_assessor | admin_only
            $table->string('visibility')->default('all');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_comments');
    }
};