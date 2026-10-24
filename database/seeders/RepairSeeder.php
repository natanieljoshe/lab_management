<?php

namespace Database\Seeders;

use App\Models\Repair;
use App\Models\Repairs_item;
use App\Models\User;
use App\Models\Period;
use App\Models\Items;
use App\Models\Components;
use Illuminate\Database\Seeder;

class RepairSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $period = Period::where('active', true)->first();
        $brokenItems = Items::where('condition', false)->get();
        $brokenComponents = Components::where('condition', false)->get();
        
        if ($users->isEmpty() || !$period) {
            return;
        }

        // Create repair records for broken items - increase to 50
        foreach ($brokenItems->take(50) as $item) {
            $reportedAt = now()->subDays(rand(1, 30));
            $reporter = $users->random();
            
            $repair = Repair::create([
                'name' => $reporter->name . '_' . $reportedAt->format('Ymd') . '_' . $item->serial_code,
                'period_id' => $period->id,
                'reported_by' => $reporter->id,
            ]);

            Repairs_item::create([
                'repair_id' => $repair->id,
                'itemable_type' => 'App\Models\Items',
                'itemable_id' => $item->id,
                'started_at' => $reportedAt->copy()->addDays(rand(1, 3)),
                'completed_at' => rand(0, 10) > 3 ? $reportedAt->copy()->addDays(rand(4, 10)) : null,
                'issue_description' => $this->getRandomIssueDescription(),
                'status' => $this->getRandomRepairStatus(),
                'is_successful' => rand(0, 10) > 2,
                'repair_notes' => $this->getRandomRepairNote(),
            ]);
        }

        // Create repair records for broken components - increase to 50
        foreach ($brokenComponents->take(50) as $component) {
            $reportedAt = now()->subDays(rand(1, 30));
            $reporter = $users->random();
            
            $repair = Repair::create([
                'name' => $reporter->name . '_' . $reportedAt->format('Ymd') . '_' . $component->serial_code,
                'period_id' => $period->id,
                'reported_by' => $reporter->id,
            ]);

            Repairs_item::create([
                'repair_id' => $repair->id,
                'itemable_type' => 'App\Models\Components',
                'itemable_id' => $component->id,
                'started_at' => $reportedAt->copy()->addDays(rand(1, 3)),
                'completed_at' => rand(0, 10) > 4 ? $reportedAt->copy()->addDays(rand(4, 10)) : null,
                'issue_description' => $this->getRandomComponentIssue(),
                'status' => $this->getRandomRepairStatus(),
                'is_successful' => rand(0, 10) > 3,
                'repair_notes' => $this->getRandomRepairNote(),
            ]);
        }
    }
    
    private function getRandomRepairNote(): string
    {
        $notes = [
            'Perlu penggantian spare part',
            'Dibersihkan dan dikalibrasi ulang',
            'Software perlu diupdate',
            'Hardware mengalami kerusakan fisik',
            'Perlu maintenance rutin',
            'Komponen internal perlu diganti',
            'Sistem perlu direstart dan dikonfigurasi ulang',
            'Memerlukan penanganan khusus dari teknisi ahli'
        ];
        
        return $notes[array_rand($notes)];
    }
    
    private function getRandomIssueDescription(): string
    {
        $issues = [
            'Monitor tidak menyala',
            'PC tidak bisa boot',
            'Keyboard beberapa tombol tidak berfungsi',
            'Mouse sensor tidak responsif',
            'VR headset display bermasalah',
            'Layar monitor bergaris',
            'PC sering restart sendiri',
            'Suara speaker tidak keluar',
            'Webcam tidak terdeteksi',
            'Koneksi USB tidak stabil'
        ];
        
        return $issues[array_rand($issues)];
    }
    
    private function getRandomComponentIssue(): string
    {
        $issues = [
            'Processor overheating',
            'RAM tidak terdeteksi',
            'Hard disk bad sector',
            'Graphics card artifacting',
            'Motherboard capacitor rusak',
            'Power supply voltage tidak stabil',
            'Cooling fan tidak berputar',
            'Network card tidak connect',
            'Sound card no output',
            'SSD tidak terbaca'
        ];
        
        return $issues[array_rand($issues)];
    }
    
    private function getRandomRepairStatus(): int
    {
        // 0: Pending, 1: In Progress, 2: Completed
        $statuses = [0, 1, 2, 2, 2]; // More completed repairs
        return $statuses[array_rand($statuses)];
    }
}