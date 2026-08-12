<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('madrasa_funds')) {
            return;
        }

        Schema::create('madrasa_funds', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_urdu')->nullable();
            $table->enum('type', ['donation', 'fees', 'trust'])->default('donation');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('color', 20)->nullable();
            $table->timestamps();

            $table->index(['type', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('madrasa_funds');
    }
};
