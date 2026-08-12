<?php

declare(strict_types=1);

namespace Modules\SchoolManagement\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\SchoolManagement\Models\Campus;
use Modules\SchoolManagement\Models\SchoolClass;
use Modules\SchoolManagement\Models\Section;
use Modules\SchoolManagement\Models\Student;

class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        $campus = Campus::factory()->create();
        $class = SchoolClass::factory()->create(['campus_id' => $campus->id]);
        $section = Section::factory()->create(['campus_id' => $campus->id, 'class_id' => $class->id]);

        return [
            'campus_id' => $campus->id,
            'class_id' => $class->id,
            'section_id' => $section->id,
            'user_id' => null,
            'student_name' => $this->faker->name(),
            'admission_no' => 'ADM-' . date('Y') . '-' . str_pad((string) $this->faker->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'father_name' => $this->faker->name('male'),
            'father_cnic' => $this->faker->numerify('#####-#######-#'),
            'phone_no' => $this->faker->phoneNumber(),
            'current_address' => $this->faker->address(),
            'admission_date' => $this->faker->dateTimeBetween('-2 years', 'now'),
            'monthly_fee' => $this->faker->randomElement([1000, 1500, 2000]),
            'residential_status' => 'non_resident',
            'food_at_madrasa' => false,
            'food_charges' => 0,
            'status' => true,
            'enrollment_status' => 'enrolled',
        ];
    }
}
