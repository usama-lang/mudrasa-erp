<?php

declare(strict_types=1);

namespace Modules\SchoolManagement\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\SchoolManagement\Models\Campus;
use Modules\SchoolManagement\Models\Department;
use Modules\SchoolManagement\Models\SchoolClass;
use Modules\SchoolManagement\Models\Section;
use Modules\SchoolManagement\Models\Student;
use Modules\SchoolManagement\Models\Teacher;

class SchoolDemoSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $this->seedCampuses();
        });
    }

    private function seedCampuses(): void
    {
        $campusData = [
            ['name' => 'Main Campus', 'code' => 'MAIN', 'email' => 'main@school.edu', 'phone' => '+1-555-0100'],
            ['name' => 'North Campus', 'code' => 'NORTH', 'email' => 'north@school.edu', 'phone' => '+1-555-0200'],
            ['name' => 'South Campus', 'code' => 'SOUTH', 'email' => 'south@school.edu', 'phone' => '+1-555-0300'],
        ];

        foreach ($campusData as $data) {
            $campus = Campus::create(array_merge($data, ['status' => 'active']));

            $this->seedDepartmentsForCampus($campus);
        }
    }

    private function seedDepartmentsForCampus(Campus $campus): void
    {
        $deptNames = ['Science', 'Arts', 'Commerce'];

        foreach ($deptNames as $name) {
            $dept = Department::create([
                'campus_id' => $campus->id,
                'name' => $name,
                'description' => $name . ' department at ' . $campus->name,
                'status' => 'active',
            ]);

            $this->seedClassesForDepartment($campus, $dept);
        }
    }

    private function seedClassesForDepartment(Campus $campus, Department $dept): void
    {
        foreach (range(9, 12) as $level) {
            $class = SchoolClass::create([
                'campus_id' => $campus->id,
                'department_id' => $dept->id,
                'name' => 'Class ' . $level,
                'numeric_name' => $level,
                'status' => 'active',
            ]);

            foreach (['A', 'B'] as $sectionName) {
                Section::create([
                    'campus_id' => $campus->id,
                    'class_id' => $class->id,
                    'name' => 'Section ' . $sectionName,
                    'capacity' => 30,
                    'status' => 'active',
                ]);
            }
        }
    }
}
