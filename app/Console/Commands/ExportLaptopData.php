<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ExportLaptopData extends Command
{
	protected $signature = 'export:laptop-data';
	protected $description = 'Export laptop data into a seeder file';

	public function handle()
	{
		$laptops = DB::table('master_laptop')->get();

		if ($laptops->isEmpty()) {
			$this->error('No laptop data found to export!');
			return;
		}

		$seederContent = "<?php\n\n";
		$seederContent .= "namespace Database\Seeders;\n\n";
		$seederContent .= "use Illuminate\Database\Seeder;\n";
		$seederContent .= "use Illuminate\Support\Facades\DB;\n\n";
		$seederContent .= "class LaptopSeeder extends Seeder\n";
		$seederContent .= "{\n";
		$seederContent .= "    public function run(): void\n";
		$seederContent .= "    {\n";
		$seederContent .= "        \$laptops = [\n";

		foreach ($laptops as $laptop) {
			$seederContent .= "            [\n";
			$seederContent .= "                'id_laptop' => {$laptop->id_laptop},\n";
			$seederContent .= "                'merek' => '{$this->escapeString($laptop->merek)}',\n";
			$seederContent .= "                'model' => '{$this->escapeString($laptop->model)}',\n";
			$seederContent .= "                'harga' => {$laptop->harga},\n";
			$seederContent .= "                'processor' => '{$this->escapeString($laptop->processor)}',\n";
			$seederContent .= "                'ram' => {$laptop->ram},\n";
			$seederContent .= "                'storage' => {$laptop->storage},\n";
			$seederContent .= "                'gpu' => '{$this->escapeString($laptop->gpu)}',\n";
			$seederContent .= "                'ukuran_baterai' => {$laptop->ukuran_baterai},\n";
			$seederContent .= "                'gambar' => " . ($laptop->gambar ? "'{$this->escapeString($laptop->gambar)}'" : "null") . ",\n";
			$seederContent .= "                'created_at' => '{$laptop->created_at}',\n";
			$seederContent .= "                'updated_at' => '{$laptop->updated_at}',\n";
			$seederContent .= "            ],\n";
		}

		$seederContent .= "        ];\n\n";
		$seederContent .= "        DB::table('master_laptop')->insert(\$laptops);\n";
		$seederContent .= "    }\n\n";

		$seederContent .= "    private function escapeString(\$string)\n";
		$seederContent .= "    {\n";
		$seederContent .= "        return str_replace(\"'\", \"\\'\", \$string);\n";
		$seederContent .= "    }\n";
		$seederContent .= "}\n";

		file_put_contents(database_path('seeders/LaptopSeeder.php'), $seederContent);

		$this->info('Laptop data has been exported to LaptopSeeder.php successfully!');
	}

	private function escapeString($string)
	{
		return str_replace("'", "\'", $string);
	}
}
