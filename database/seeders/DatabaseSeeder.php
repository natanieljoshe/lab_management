<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UnitSeeder::class,
            PermissionSeeder::class,
            PeriodSeeder::class,
            UserSeeder::class,
            LabsSeeder::class,
            DesksSeeder::class,
            TypeSeeder::class,
            SetSeeder::class,
            specification::class,
            ItemsSeeder::class,
            ComponentsSeeder::class,
            ToolSpecSeeder::class,
            SoftwareSeeder::class,
            BookingSeeder::class,
            RepairSeeder::class,
            FilesSeeder::class,
            MatkulSeeder::class,
            ModuleSeeder::class,
            FundSeeder::class
        ]);
    }
}
