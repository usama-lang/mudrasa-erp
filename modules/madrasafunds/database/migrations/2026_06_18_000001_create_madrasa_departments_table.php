<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('madrasa_departments')) {
            return;
        }

        Schema::create('madrasa_departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_urdu')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('madrasa_departments');
    }
};
