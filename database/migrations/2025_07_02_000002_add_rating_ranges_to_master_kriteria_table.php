<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		Schema::table('master_kriteria', function (Blueprint $table) {
			$table->decimal('rating_1_min', 10, 3)->nullable()->after('bobot');
			$table->decimal('rating_1_max', 10, 3)->nullable()->after('rating_1_min');
			$table->decimal('rating_2_min', 10, 3)->nullable()->after('rating_1_max');
			$table->decimal('rating_2_max', 10, 3)->nullable()->after('rating_2_min');
			$table->decimal('rating_3_min', 10, 3)->nullable()->after('rating_2_max');
			$table->decimal('rating_3_max', 10, 3)->nullable()->after('rating_3_min');
			$table->decimal('rating_4_min', 10, 3)->nullable()->after('rating_3_max');
			$table->decimal('rating_4_max', 10, 3)->nullable()->after('rating_4_min');
			$table->decimal('rating_5_min', 10, 3)->nullable()->after('rating_4_max');
			$table->decimal('rating_5_max', 10, 3)->nullable()->after('rating_5_min');
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::table('master_kriteria', function (Blueprint $table) {
			$table->dropColumn([
				'rating_1_min',
				'rating_1_max',
				'rating_2_min',
				'rating_2_max',
				'rating_3_min',
				'rating_3_max',
				'rating_4_min',
				'rating_4_max',
				'rating_5_min',
				'rating_5_max'
			]);
		});
	}
};
