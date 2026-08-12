<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('school_teachers')) {
            return;
        }

        Schema::create('school_teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campus_id')->constrained('school_campuses')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('employee_id', 50)->unique();
            $table->string('designation')->nullable();
            $table->string('qualification')->nullable();
            $table->decimal('salary', 10, 2)->default(0);
            $table->date('joining_date')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_teachers');
    }
};
