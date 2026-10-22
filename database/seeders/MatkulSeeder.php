<?php

namespace Database\Seeders;

use App\Models\Matkul;
use App\Models\Folders;
use Illuminate\Database\Seeder;

class MatkulSeeder extends Seeder
{
    public function run(): void
    {
        $rootFolder = Folders::whereNull('parent_id')->first();
        if (!$rootFolder) {
            $this->command->warn('Root folder not found. Skipping MatkulSeeder.');
            return;
        }

        $matkulsFolder = Folders::create([
            'name' => 'Matkuls',
            'parent_id' => $rootFolder->id,
            'full_path' => '/Matkuls',
            'is_root' => false,
            'creator_id' => null
        ]);

        $matkuls = [
            ['kode' => 'IF101', 'nama' => 'Algoritma dan Pemrograman', 'sks' => '3', 'curriculum_semester' => '1', 'open_file_access' => true],
            ['kode' => 'IF102', 'nama' => 'Struktur Data', 'sks' => '3', 'curriculum_semester' => '3', 'open_file_access' => true],
            ['kode' => 'IF201', 'nama' => 'Basis Data', 'sks' => '3', 'curriculum_semester' => '2', 'open_file_access' => false],
            ['kode' => 'IF202', 'nama' => 'Pemrograman Web', 'sks' => '3', 'curriculum_semester' => '3', 'open_file_access' => true],
            ['kode' => 'IF301', 'nama' => 'Rekayasa Perangkat Lunak', 'sks' => '3', 'curriculum_semester' => '4', 'open_file_access' => false],
            ['kode' => 'IF302', 'nama' => 'Kecerdasan Buatan', 'sks' => '3', 'curriculum_semester' => '4', 'open_file_access' => true],
            ['kode' => 'IF303', 'nama' => 'Jaringan Komputer', 'sks' => '3', 'curriculum_semester' => '2', 'open_file_access' => false],
            ['kode' => 'IF401', 'nama' => 'Keamanan Informasi', 'sks' => '2', 'curriculum_semester' => '5', 'open_file_access' => true],
            ['kode' => 'IF402', 'nama' => 'Sistem Terdistribusi', 'sks' => '3', 'curriculum_semester' => '5', 'open_file_access' => false],
            ['kode' => 'IF403', 'nama' => 'Machine Learning', 'sks' => '3', 'curriculum_semester' => '4', 'open_file_access' => true],
        ];

        foreach ($matkuls as $data) {
            $folder = Folders::create([
                'name' => $data['nama'],
                'parent_id' => $matkulsFolder->id,
                'full_path' => '/Matkuls/' . $data['nama'],
                'is_root' => true,
                'creator_id' => null
            ]);

            Matkul::create([
                'kode' => $data['kode'],
                'nama' => $data['nama'],
                'sks' => $data['sks'],
                'curriculum_semester' => $data['curriculum_semester'],
                'root_folder_id' => $folder->id,
                'open_file_access' => $data['open_file_access']
            ]);
        }
    }
}
