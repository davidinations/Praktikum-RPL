<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MasterKriteria;
use App\Models\Admin;

class MasterKriteriaSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Get the first admin to assign criteria
        $admin = Admin::first();

        if (!$admin) {
            $this->command->warn('No admin found. Please run AdminSeeder first.');
            return;
        }

        $criteria = [
            [
                'id_admin' => 1,
                'nama' => 'Harga',
                'satuan' => 'Juta Rupiah',
                'jenis' => 'cost',
                'bobot' => 0.35,
                'rating_1_min' => 0.000,
                'rating_1_max' => 5.000,
                'rating_2_min' => 6.000,
                'rating_2_max' => 10.000,
                'rating_3_min' => 11.000,
                'rating_3_max' => 15.000,
                'rating_4_min' => 16.000,
                'rating_4_max' => 20.000,
                'rating_5_min' => 21.000,
                'rating_5_max' => 100.000,
                'created_at' => '2025-07-02 16:51:22',
                'updated_at' => '2025-07-02 16:51:22',
            ],
            [
                'id_admin' => 1,
                'nama' => 'Processor',
                'satuan' => 'Ghz',
                'jenis' => 'benefit',
                'bobot' => 0.30,
                'rating_1_min' => 0.000,
                'rating_1_max' => 1.700,
                'rating_2_min' => 1.800,
                'rating_2_max' => 2.200,
                'rating_3_min' => 2.300,
                'rating_3_max' => 2.700,
                'rating_4_min' => 2.800,
                'rating_4_max' => 3.200,
                'rating_5_min' => 3.300,
                'rating_5_max' => 7.000,
                'created_at' => '2025-07-02 17:15:51',
                'updated_at' => '2025-07-02 17:15:51',
            ],
            [
                'id_admin' => 1,
                'nama' => 'RAM',
                'satuan' => 'GB',
                'jenis' => 'benefit',
                'bobot' => 0.20,
                'rating_1_min' => 2.000,
                'rating_1_max' => 2.000,
                'rating_2_min' => 4.000,
                'rating_2_max' => 4.000,
                'rating_3_min' => 8.000,
                'rating_3_max' => 8.000,
                'rating_4_min' => 16.000,
                'rating_4_max' => 16.000,
                'rating_5_min' => 32.000,
                'rating_5_max' => 32.000,
                'created_at' => '2025-07-02 17:16:37',
                'updated_at' => '2025-07-02 17:16:37',
            ],
            [
                'id_admin' => 1,
                'nama' => 'SSD',
                'satuan' => 'GB',
                'jenis' => 'benefit',
                'bobot' => 0.15,
                'rating_1_min' => 128.000,
                'rating_1_max' => 128.000,
                'rating_2_min' => 256.000,
                'rating_2_max' => 256.000,
                'rating_3_min' => 512.000,
                'rating_3_max' => 512.000,
                'rating_4_min' => 1024.000,
                'rating_4_max' => 1024.000,
                'rating_5_min' => 2048.000,
                'rating_5_max' => 2048.000,
                'created_at' => '2025-07-02 17:17:25',
                'updated_at' => '2025-07-02 17:17:25',
            ],
        ];

        // Disable foreign key checks temporarily
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Clear existing criteria
        MasterKriteria::truncate();

        // Re-enable foreign key checks
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        foreach ($criteria as $criterion) {
            MasterKriteria::create($criterion);
        }

        $this->command->info('Master Kriteria seeded successfully with ' . count($criteria) . ' criteria!');
        $this->command->info('Criteria included: ' . implode(', ', array_column($criteria, 'nama')));
    }
}
