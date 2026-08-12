<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('school_students', function (Blueprint $table) {
            if (! Schema::hasColumn('school_students', 'food_at_madrasa')) {
                $table->boolean('food_at_madrasa')->default(false)->after('residential_status');
            }
            if (! Schema::hasColumn('school_students', 'food_charges')) {
                $table->decimal('food_charges', 10, 2)->nullable()->after('food_at_madrasa');
            }
            if (! Schema::hasColumn('school_students', 'enrollment_status')) {
                $table->string('enrollment_status', 30)->default('enrolled')->after('status');
            }
            if (! Schema::hasColumn('school_students', 'leaving_date')) {
                $table->date('leaving_date')->nullable()->after('enrollment_status');
            }
            if (! Schema::hasColumn('school_students', 'status_remarks')) {
                $table->text('status_remarks')->nullable()->after('leaving_date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('school_students', function (Blueprint $table) {
            $table->dropColumn(['food_at_madrasa', 'food_charges', 'enrollment_status', 'leaving_date', 'status_remarks']);
        });
    }
};
