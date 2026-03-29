<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            UserSeeder::class,
            CalculationOptionSeeder::class,
            // يمكنك إضافة seeders أخرى هنا لاحقاً
        ]);
    }
}
