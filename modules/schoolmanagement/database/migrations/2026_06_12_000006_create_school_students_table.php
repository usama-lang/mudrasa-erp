<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('school_students')) {
            return;
        }

        Schema::create('school_students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campus_id')->constrained('school_campuses')->cascadeOnDelete();
            $table->foreignId('class_id')->constrained('school_classes')->cascadeOnDelete();
            $table->foreignId('section_id')->constrained('school_sections')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('roll_no', 20)->unique();
            $table->string('admission_no', 50)->unique();
            $table->string('father_name')->nullable();
            $table->string('cnic', 20)->nullable();
            $table->string('phone', 30)->nullable();
            $table->text('address')->nullable();
            $table->date('admission_date')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_students');
    }
};
