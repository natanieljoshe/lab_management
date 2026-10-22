<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $units = Unit::all();
        if ($units->isEmpty()) return;

        $ptikUnit = $units->where('name', 'PTIK')->first();
        $uppkUnit = $units->where('name', 'UPPK')->first();
        $mhswUnit = $units->where('name', 'MAHASISWA')->first();
        $tuUnit = $units->where('name', 'TU INFOR')->first();
        $kepalaLabUnit = $units->where('name', 'KEPALA LAB')->first();
        $asistenDosenUnit = $units->where('name', 'ASISTEN DOSEN')->first();
        $asistenLabUnit = $units->where('name', 'ASISTEN LAB')->first();
        $dosenUnit = $units->where('name', 'DOSEN')->first();
        $adminUnit = $units->where('name', 'ADMIN')->first();

        // Super Admin users
        $superAdmins = [
            ['name' => 'Kevin - Super Admin', 'email' => 'c14230088@john.petra.ac.id'],
            ['name' => 'Nataniel - Super Admin', 'email' => 'c14240154@john.petra.ac.id'],
            ['name' => 'Evan - Super Admin', 'email' => 'c14240069@john.petra.ac.id'],
            ['name' => 'Jeremy - Super Admin', 'email' => 'c14240090@john.petra.ac.id'],
            ['name' => 'Bryan - Super Admin', 'email' => 'c14240085@john.petra.ac.id'],
            ['name' => 'Axel - Super Admin', 'email' => 'c14240157@john.petra.ac.id'],
        ];
        foreach ($superAdmins as $admin) {
            User::firstOrCreate(['email' => $admin['email']], [
                'name' => $admin['name'],
                'unit_id' => $adminUnit?->id ?? $units->first()->id,
            ]);
        }

        // Admin/Staff users
        User::firstOrCreate(['email' => 'admin@petra.ac.id'], [
            'name' => 'Admin System',
            'unit_id' => $ptikUnit?->id ?? $units->first()->id,
        ]);

        // TU INFOR Staff
        $tuStaff = [
            ['name' => 'Budi Santoso', 'email' => 'budi.santoso@petra.ac.id'],
            ['name' => 'Siti Nurhaliza', 'email' => 'siti.nurhaliza@petra.ac.id'],
        ];
        foreach ($tuStaff as $staff) {
            User::firstOrCreate(['email' => $staff['email']], [
                'name' => $staff['name'],
                'unit_id' => $tuUnit?->id ?? $units->first()->id,
            ]);
        }

        // Kepala Lab
        User::firstOrCreate(['email' => 'kepala.lab@petra.ac.id'], [
            'name' => 'Dr. Ahmad Wijaya',
            'unit_id' => $kepalaLabUnit?->id ?? $units->first()->id,
        ]);

        // Asisten Lab
        $asistenLab = [
            ['name' => 'Rina Kusuma', 'email' => 'rina.kusuma@petra.ac.id'],
            ['name' => 'Dedi Prasetyo', 'email' => 'dedi.prasetyo@petra.ac.id'],
            ['name' => 'Maya Sari', 'email' => 'maya.sari@petra.ac.id'],
        ];
        foreach ($asistenLab as $asisten) {
            User::firstOrCreate(['email' => $asisten['email']], [
                'name' => $asisten['name'],
                'unit_id' => $asistenLabUnit?->id ?? $units->first()->id,
            ]);
        }

        // Lecturers/Dosen
        $lecturers = [
            ['name' => 'Dr. John Doe', 'email' => 'john.doe@petra.ac.id'],
            ['name' => 'Prof. Jane Smith', 'email' => 'jane.smith@petra.ac.id'],
            ['name' => 'Dr. Michael Johnson', 'email' => 'michael.johnson@petra.ac.id'],
            ['name' => 'Dr. Andi Wijaya', 'email' => 'andi.wijaya@petra.ac.id'],
            ['name' => 'Prof. Sri Mulyani', 'email' => 'sri.mulyani@petra.ac.id'],
            ['name' => 'Dr. Bambang Sutopo', 'email' => 'bambang.sutopo@petra.ac.id'],
        ];
        foreach ($lecturers as $lecturer) {
            User::firstOrCreate(['email' => $lecturer['email']], [
                'name' => $lecturer['name'],
                'unit_id' => $dosenUnit?->id ?? $units->first()->id,
            ]);
        }

        // Asisten Dosen
        $asistenDosen = [
            ['name' => 'Rudi Hartono', 'email' => 'rudi.hartono@petra.ac.id'],
            ['name' => 'Dewi Lestari', 'email' => 'dewi.lestari@petra.ac.id'],
        ];
        foreach ($asistenDosen as $asisten) {
            User::firstOrCreate(['email' => $asisten['email']], [
                'name' => $asisten['name'],
                'unit_id' => $asistenDosenUnit?->id ?? $units->first()->id,
            ]);
        }

        // UPPK Staff
        $uppkStaff = [
            ['name' => 'Hendra Gunawan', 'email' => 'hendra.gunawan@petra.ac.id'],
            ['name' => 'Lina Marlina', 'email' => 'lina.marlina@petra.ac.id'],
        ];
        foreach ($uppkStaff as $staff) {
            User::firstOrCreate(['email' => $staff['email']], [
                'name' => $staff['name'],
                'unit_id' => $uppkUnit?->id ?? $units->first()->id,
            ]);
        }

        // Students
        $students = [
            ['name' => 'Alice Student', 'email' => 'alice@student.petra.ac.id'],
            ['name' => 'Bob Student', 'email' => 'bob@student.petra.ac.id'],
            ['name' => 'Charlie Student', 'email' => 'charlie@student.petra.ac.id'],
            ['name' => 'David Tan', 'email' => 'c14230001@john.petra.ac.id'],
            ['name' => 'Eva Susanti', 'email' => 'c14230002@john.petra.ac.id'],
            ['name' => 'Felix Kurniawan', 'email' => 'c14230003@john.petra.ac.id'],
            ['name' => 'Grace Angelina', 'email' => 'c14230004@john.petra.ac.id'],
            ['name' => 'Henry Wijaya', 'email' => 'c14230005@john.petra.ac.id'],
            ['name' => 'Irene Putri', 'email' => 'c14230006@john.petra.ac.id'],
            ['name' => 'Jason Lim', 'email' => 'c14230007@john.petra.ac.id'],
            ['name' => 'Karina Sari', 'email' => 'c14230008@john.petra.ac.id'],
            ['name' => 'Leo Pratama', 'email' => 'c14230009@john.petra.ac.id'],
            ['name' => 'Maria Santoso', 'email' => 'c14230010@john.petra.ac.id'],
            ['name' => 'Nathan Chen', 'email' => 'c14230011@john.petra.ac.id'],
            ['name' => 'Olivia Tanjung', 'email' => 'c14230012@john.petra.ac.id'],
        ];
        foreach ($students as $student) {
            User::firstOrCreate(['email' => $student['email']], [
                'name' => $student['name'],
                'unit_id' => $mhswUnit?->id ?? $units->first()->id,
            ]);
        }
    }
}