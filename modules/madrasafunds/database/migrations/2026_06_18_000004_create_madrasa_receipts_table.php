<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('madrasa_receipts')) {
            return;
        }

        Schema::create('madrasa_receipts', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_number', 30)->unique();
            $table->date('receipt_date');
            $table->foreignId('fund_id')->constrained('madrasa_funds')->cascadeOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('madrasa_departments')->nullOnDelete();
            $table->foreignId('student_id')->nullable()->constrained('madrasa_students')->nullOnDelete();
            $table->string('donor_name')->nullable();
            $table->string('donor_phone')->nullable();
            $table->decimal('amount', 12, 2);
            $table->text('description')->nullable();
            $table->string('mad')->nullable();
            $table->string('given_to')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->text('notes')->nullable();
            $table->boolean('is_cancelled')->default(false);
            $table->string('cancelled_reason')->nullable();
            $table->timestamps();

            $table->index(['fund_id', 'receipt_date']);
            $table->index(['department_id', 'receipt_date']);
            $table->index('student_id');
            $table->index('is_cancelled');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('madrasa_receipts');
    }
};
