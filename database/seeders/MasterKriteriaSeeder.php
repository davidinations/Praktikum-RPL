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
                'id_admin' => $admin->id_admin,
                'nama' => 'Harga',
                'satuan' => 'IDR',
                'bobot' => 0.2,
            ],
            [
                'id_admin' => $admin->id_admin,
                'nama' => 'RAM',
                'satuan' => 'GB',
                'bobot' => 0.1,
            ],
            [
                'id_admin' => $admin->id_admin,
                'nama' => 'Storage',
                'satuan' => 'GB',
                'bobot' => 0.1,
            ],
        ];

        foreach ($criteria as $criterion) {
            MasterKriteria::create($criterion);
        }

        $this->command->info('Master Kriteria seeded successfully!');
    }
}
