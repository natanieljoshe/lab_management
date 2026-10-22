<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $unitNames = [
            'MAHASISWA',
            'TU INFOR',
            'KEPALA LAB',
            'ASISTEN DOSEN',
            'ASISTEN LAB',
            'DOSEN',
            'PTIK',
            'UPPK',
            'ADMIN'
        ];

        foreach ($unitNames as $name) {
            Unit::firstOrCreate(
                ['name' => $name],
                ['description' => 'Unit kerja ' . ucfirst(strtolower($name))]
            );
        }

        $this->command->info('Units seeded.');
    }
}
