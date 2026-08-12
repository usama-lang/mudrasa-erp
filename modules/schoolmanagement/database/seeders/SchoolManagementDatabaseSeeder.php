<?php

declare(strict_types=1);

namespace Modules\SchoolManagement\Database\Seeders;

use Illuminate\Database\Seeder;

class SchoolManagementDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            IslamicSchoolSeeder::class,
        ]);
    }
}
