<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('school_students', function (Blueprint $table) {
            if (! Schema::hasColumn('school_students', 'para_quantity')) {
                $table->decimal('para_quantity', 4, 1)->nullable()->after('education_level');
            }
        });
    }

    public function down(): void
    {
        Schema::table('school_students', function (Blueprint $table) {
            if (Schema::hasColumn('school_students', 'para_quantity')) {
                $table->dropColumn('para_quantity');
            }
        });
    }
};
