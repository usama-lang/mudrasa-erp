<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Step 0: Drop the roll_no unique index first so SQLite can later drop the column cleanly.
        if (Schema::hasColumn('school_students', 'roll_no')) {
            Schema::table('school_students', function (Blueprint $table) {
                try {
                    $table->dropUnique('school_students_roll_no_unique');
                } catch (\Throwable $e) {
                    // Index may not exist on some drivers; safe to ignore.
                }
            });
        }

        // Step 1: Drop old columns that are no longer needed
        Schema::table('school_students', function (Blueprint $table) {
            if (Schema::hasColumn('school_students', 'roll_no')) {
                $table->dropColumn('roll_no');
            }
            if (Schema::hasColumn('school_students', 'cnic')) {
                $table->dropColumn('cnic');
            }
            if (Schema::hasColumn('school_students', 'phone')) {
                $table->dropColumn('phone');
            }
            if (Schema::hasColumn('school_students', 'address')) {
                $table->dropColumn('address');
            }
        });

        // Step 2: Make user_id nullable (optional login)
        Schema::table('school_students', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->unsignedBigInteger('user_id')->nullable()->change();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });

        // Step 3: Make section_id nullable (section optional per student)
        Schema::table('school_students', function (Blueprint $table) {
            $table->dropForeign(['section_id']);
            $table->unsignedBigInteger('section_id')->nullable()->change();
            $table->foreign('section_id')->references('id')->on('school_sections')->nullOnDelete();
        });

        // Step 4: Drop old enum status, add boolean status, add department_id FK
        Schema::table('school_students', function (Blueprint $table) {
            if (Schema::hasColumn('school_students', 'status')) {
                $table->dropColumn('status');
            }
        });

        Schema::table('school_students', function (Blueprint $table) {
            if (!Schema::hasColumn('school_students', 'department_id')) {
                $table->foreignId('department_id')
                    ->nullable()
                    ->after('campus_id')
                    ->constrained('school_departments')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('school_students', 'status')) {
                $table->boolean('status')->default(true)->after('admission_date');
            }
        });

        // Step 5: Add all new student fields
        Schema::table('school_students', function (Blueprint $table) {
            if (!Schema::hasColumn('school_students', 'student_name')) {
                $table->string('student_name')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('school_students', 'b_form_no')) {
                $table->string('b_form_no', 50)->nullable()->after('father_name');
            }
            if (!Schema::hasColumn('school_students', 'father_cnic')) {
                $table->string('father_cnic', 20)->nullable()->after('b_form_no');
            }
            if (!Schema::hasColumn('school_students', 'current_address')) {
                $table->text('current_address')->nullable()->after('father_cnic');
            }
            if (!Schema::hasColumn('school_students', 'permanent_address')) {
                $table->text('permanent_address')->nullable()->after('current_address');
            }
            if (!Schema::hasColumn('school_students', 'date_of_birth')) {
                $table->date('date_of_birth')->nullable()->after('permanent_address');
            }
            if (!Schema::hasColumn('school_students', 'age')) {
                $table->unsignedSmallInteger('age')->nullable()->after('date_of_birth');
            }
            if (!Schema::hasColumn('school_students', 'education_level')) {
                $table->string('education_level')->nullable()->after('age');
            }
            if (!Schema::hasColumn('school_students', 'father_profession')) {
                $table->string('father_profession')->nullable()->after('education_level');
            }
            if (!Schema::hasColumn('school_students', 'designation')) {
                $table->string('designation')->nullable()->after('father_profession');
            }
            if (!Schema::hasColumn('school_students', 'monthly_fee')) {
                $table->decimal('monthly_fee', 10, 2)->default(0)->after('designation');
            }
            if (!Schema::hasColumn('school_students', 'phone_no')) {
                $table->string('phone_no', 30)->nullable()->after('monthly_fee');
            }
            if (!Schema::hasColumn('school_students', 'whatsapp_no')) {
                $table->string('whatsapp_no', 30)->nullable()->after('phone_no');
            }
            if (!Schema::hasColumn('school_students', 'residential_status')) {
                $table->enum('residential_status', ['resident', 'non_resident'])->default('non_resident')->after('whatsapp_no');
            }
            if (!Schema::hasColumn('school_students', 'guardian1_name')) {
                $table->string('guardian1_name')->nullable()->after('residential_status');
            }
            if (!Schema::hasColumn('school_students', 'guardian1_relation')) {
                $table->string('guardian1_relation')->nullable()->after('guardian1_name');
            }
            if (!Schema::hasColumn('school_students', 'guardian1_phone')) {
                $table->string('guardian1_phone', 30)->nullable()->after('guardian1_relation');
            }
            if (!Schema::hasColumn('school_students', 'guardian2_name')) {
                $table->string('guardian2_name')->nullable()->after('guardian1_phone');
            }
            if (!Schema::hasColumn('school_students', 'guardian2_relation')) {
                $table->string('guardian2_relation')->nullable()->after('guardian2_name');
            }
            if (!Schema::hasColumn('school_students', 'guardian2_phone')) {
                $table->string('guardian2_phone', 30)->nullable()->after('guardian2_relation');
            }
            if (!Schema::hasColumn('school_students', 'agreement_date')) {
                $table->date('agreement_date')->nullable()->after('guardian2_phone');
            }
            if (!Schema::hasColumn('school_students', 'student_image')) {
                $table->string('student_image')->nullable()->after('agreement_date');
            }
            if (!Schema::hasColumn('school_students', 'document_file')) {
                $table->string('document_file')->nullable()->after('student_image');
            }
        });
    }

    public function down(): void
    {
        Schema::table('school_students', function (Blueprint $table) {
            $table->dropColumn([
                'student_name', 'b_form_no', 'father_cnic', 'current_address',
                'permanent_address', 'date_of_birth', 'age', 'education_level',
                'father_profession', 'designation', 'monthly_fee', 'phone_no',
                'whatsapp_no', 'residential_status', 'guardian1_name', 'guardian1_relation',
                'guardian1_phone', 'guardian2_name', 'guardian2_relation', 'guardian2_phone',
                'agreement_date', 'student_image', 'document_file',
            ]);

            $table->dropForeign(['department_id']);
            $table->dropColumn('department_id');
            $table->dropColumn('status');
            $table->enum('status', ['active', 'inactive'])->default('active');

            $table->string('roll_no', 20)->unique();
            $table->string('cnic', 20)->nullable();
            $table->string('phone', 30)->nullable();
            $table->text('address')->nullable();
        });
    }
};
