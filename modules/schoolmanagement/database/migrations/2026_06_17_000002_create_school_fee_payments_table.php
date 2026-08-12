<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('school_fee_payments')) {
            return;
        }

        Schema::create('school_fee_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('school_students')->cascadeOnDelete();
            $table->foreignId('campus_id')->constrained('school_campuses')->cascadeOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('school_departments')->nullOnDelete();
            $table->foreignId('class_id')->nullable()->constrained('school_classes')->nullOnDelete();
            $table->foreignId('section_id')->nullable()->constrained('school_sections')->nullOnDelete();
            $table->date('fee_month');
            $table->decimal('monthly_fee', 10, 2);
            $table->decimal('food_charges', 10, 2)->nullable();
            $table->decimal('amount_paid', 10, 2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('balance', 10, 2)->default(0);
            $table->date('payment_date');
            $table->enum('payment_method', ['cash', 'bank_transfer', 'cheque'])->default('cash');
            $table->string('receipt_no', 30)->unique();
            $table->foreignId('collected_by')->constrained('users')->cascadeOnDelete();
            $table->text('notes')->nullable();
            $table->enum('status', ['paid', 'partial', 'waived'])->default('paid');
            $table->timestamps();

            $table->index(['student_id', 'fee_month']);
            $table->unique(['student_id', 'fee_month'], 'school_fee_student_month_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_fee_payments');
    }
};
