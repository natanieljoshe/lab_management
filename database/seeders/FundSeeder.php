<?php

namespace Database\Seeders;

use App\Models\FundCategory;
use Illuminate\Database\Seeder;

class FundSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Mengisi tabel fund_categories dengan data kategori kas awal.
     */
    public function run(): void
    {
        $categories = [
            // ===== PEMASUKAN =====
            [
                'name'        => 'Dana Prodi',
                'type'        => 'INCOME',
                'description' => 'Dana operasional yang diberikan oleh program studi kepada laboratorium setiap periode.',
            ],
            [
                'name'        => 'Sisa Kas Periode Lalu',
                'type'        => 'INCOME',
                'description' => 'Saldo kas yang tersisa dari periode akademik sebelumnya dan dibawa ke periode ini.',
            ],

            // ===== PENGELUARAN =====
            [
                'name'        => 'Operasional & ATK Lab',
                'type'        => 'EXPENSE',
                'description' => 'Pengeluaran operasional harian seperti alat tulis, tinta printer, kertas, sabun, dan kebutuhan rutin lab.',
            ],
            [
                'name'        => 'Service & Perbaikan Alat',
                'type'        => 'EXPENSE',
                'description' => 'Biaya servis atau perbaikan alat dan perangkat keras laboratorium yang mengalami kerusakan.',
            ],
            [
                'name'        => 'Pembelian Komponen / Sparepart',
                'type'        => 'EXPENSE',
                'description' => 'Pengeluaran untuk membeli komponen elektronik, kabel, atau suku cadang perangkat lab.',
            ],
        ];

        foreach ($categories as $category) {
            FundCategory::firstOrCreate(
                ['name' => $category['name']],
                [
                    'type'        => $category['type'],
                    'description' => $category['description'],
                ]
            );
        }

        $this->command->info('✅ FundSeeder: ' . count($categories) . ' kategori kas berhasil dibuat!');
    }
}

