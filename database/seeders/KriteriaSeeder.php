<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KriteriaSeeder extends Seeder
{
	public function run(): void
	{
		$kriteria = [
			[
				'id_admin' => 1,
				'nama' => 'Harga',
				'satuan' => 'Juta',
				'jenis' => 'cost',
				'bobot' => 35.0, // 0.35 * 100
				// Rating ranges for price (cost - lower is better)
				'rating_1_min' => 50.0,
				'rating_1_max' => 999.0, // Very expensive
				'rating_2_min' => 30.0,
				'rating_2_max' => 49.999, // Expensive
				'rating_3_min' => 20.0,
				'rating_3_max' => 29.999, // Moderate
				'rating_4_min' => 10.0,
				'rating_4_max' => 19.999, // Affordable
				'rating_5_min' => 0.0,
				'rating_5_max' => 9.999, // Very affordable
				'created_at' => now(),
				'updated_at' => now(),
			],
			[
				'id_admin' => 1,
				'nama' => 'Processor',
				'satuan' => 'Ghz',
				'jenis' => 'benefit',
				'bobot' => 30.0, // 0.30 * 100
				// Rating ranges for processor speed (benefit - higher is better)
				'rating_1_min' => 1.0,
				'rating_1_max' => 1.5, // Very slow
				'rating_2_min' => 1.501,
				'rating_2_max' => 2.0, // Slow
				'rating_3_min' => 2.001,
				'rating_3_max' => 2.5, // Moderate
				'rating_4_min' => 2.501,
				'rating_4_max' => 3.0, // Fast
				'rating_5_min' => 3.001,
				'rating_5_max' => 5.0, // Very fast
				'created_at' => now(),
				'updated_at' => now(),
			],
			[
				'id_admin' => 1,
				'nama' => 'RAM',
				'satuan' => 'Gb',
				'jenis' => 'benefit',
				'bobot' => 20.0, // 0.20 * 100
				// Rating ranges for RAM (benefit - higher is better)
				'rating_1_min' => 1,
				'rating_1_max' => 2, // Very low
				'rating_2_min' => 3,
				'rating_2_max' => 4, // Low
				'rating_3_min' => 5,
				'rating_3_max' => 8, // Moderate
				'rating_4_min' => 9,
				'rating_4_max' => 16, // High
				'rating_5_min' => 17,
				'rating_5_max' => 64, // Very high
				'created_at' => now(),
				'updated_at' => now(),
			],
			[
				'id_admin' => 1,
				'nama' => 'Storage',
				'satuan' => 'Gb',
				'jenis' => 'benefit',
				'bobot' => 15.0, // 0.15 * 100
				// Rating ranges for storage (benefit - higher is better)
				'rating_1_min' => 32,
				'rating_1_max' => 64, // Very low
				'rating_2_min' => 65,
				'rating_2_max' => 128, // Low
				'rating_3_min' => 129,
				'rating_3_max' => 256, // Moderate
				'rating_4_min' => 257,
				'rating_4_max' => 512, // High
				'rating_5_min' => 513,
				'rating_5_max' => 2000, // Very high
				'created_at' => now(),
				'updated_at' => now(),
			],
		];

		DB::table('master_kriteria')->insert($kriteria);
	}
}
