<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('school_campus_users')) {
            return;
        }

        Schema::create('school_campus_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campus_id')->constrained('school_campuses')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('role_type', [
                'main_manager',
                'assistant_manager',
                'account_manager',
                'assistant_account_manager',
                'exam_manager',
                'exam_assistant_manager',
                'teacher',
            ]);
            $table->json('extra_permissions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['campus_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_campus_users');
    }
};
