<?php

namespace App\Services;

use App\Models\MasterLaptop;
use App\Models\MasterKriteria;
use App\Models\InputUser;
use App\Models\HasilInputUser;
use App\Models\DetailHasilInputUser;
use Illuminate\Support\Facades\DB;

class SAWCalculationService
{
	/**
	 * Calculate SAW method for laptop recommendation
	 */
	public function calculateSAW($idInput, $idUser)
	{
		try {
			DB::beginTransaction();

			// Get user input values
			$userInputs = InputUser::where('id_input', $idInput)
				->where('id_user', $idUser)
				->with('kriteria')
				->get();

			if ($userInputs->isEmpty()) {
				throw new \Exception('No user input data found');
			}

			// Get all laptops
			$laptops = MasterLaptop::all();

			if ($laptops->isEmpty()) {
				throw new \Exception('No laptops found in database');
			}

			// Get criteria with weights
			$criteria = MasterKriteria::all();

			// Prepare laptop data matrix
			$laptopMatrix = $this->prepareLaptopMatrix($laptops, $criteria);

			// Normalize the matrix
			$normalizedMatrix = $this->normalizeMatrix($laptopMatrix, $criteria);

			// Calculate weighted scores
			$scores = $this->calculateWeightedScores($normalizedMatrix, $userInputs, $criteria);

			// Rank laptops
			$rankedLaptops = $this->rankLaptops($scores);

			// Save results
			$this->saveResults($idInput, $rankedLaptops, $normalizedMatrix, $criteria);

			DB::commit();

			return $rankedLaptops;
		} catch (\Exception $e) {
			DB::rollback();
			throw $e;
		}
	}

	/**
	 * Prepare laptop data matrix
	 */
	private function prepareLaptopMatrix($laptops, $criteria)
	{
		$matrix = [];

		foreach ($laptops as $laptop) {
			$matrix[$laptop->id_laptop] = [
				'laptop' => $laptop,
				'values' => []
			];

			foreach ($criteria as $criterion) {
				$value = $this->getLaptopValueForCriterion($laptop, $criterion);
				$matrix[$laptop->id_laptop]['values'][$criterion->id_kriteria] = $value;
			}
		}

		return $matrix;
	}

	/**
	 * Get laptop value for specific criterion
	 */
	private function getLaptopValueForCriterion($laptop, $criterion)
	{
		switch (strtolower($criterion->nama)) {
			case 'harga':
				return (float) $laptop->harga;
			case 'processor':
				return $this->normalizeProcessor($laptop->processor);
			case 'ram':
				return $this->extractNumericValue($laptop->ram);
			case 'storage':
				return $this->extractStorageValue($laptop->storage);
			case 'gpu':
				return $this->normalizeGPU($laptop->gpu);
			case 'baterai':
			case 'ukuran_baterai':
				return $this->extractNumericValue($laptop->ukuran_baterai);
			default:
				return 1; // Default value
		}
	}

	/**
	 * Normalize processor to numeric value
	 */
	private function normalizeProcessor($processor)
	{
		$processor = strtolower($processor);

		// Intel processors
		if (strpos($processor, 'i9') !== false) return 9;
		if (strpos($processor, 'i7') !== false) return 7;
		if (strpos($processor, 'i5') !== false) return 5;
		if (strpos($processor, 'i3') !== false) return 3;

		// AMD processors
		if (strpos($processor, 'ryzen 9') !== false) return 9;
		if (strpos($processor, 'ryzen 7') !== false) return 7;
		if (strpos($processor, 'ryzen 5') !== false) return 5;
		if (strpos($processor, 'ryzen 3') !== false) return 3;

		return 1; // Default for other processors
	}

	/**
	 * Normalize GPU to numeric value
	 */
	private function normalizeGPU($gpu)
	{
		$gpu = strtolower($gpu);

		// NVIDIA RTX series
		if (strpos($gpu, 'rtx 4090') !== false) return 10;
		if (strpos($gpu, 'rtx 4080') !== false) return 9;
		if (strpos($gpu, 'rtx 4070') !== false) return 8;
		if (strpos($gpu, 'rtx 3080') !== false) return 7;
		if (strpos($gpu, 'rtx 3070') !== false) return 6;
		if (strpos($gpu, 'rtx 3060') !== false) return 5;

		// NVIDIA GTX series
		if (strpos($gpu, 'gtx 1660') !== false) return 4;
		if (strpos($gpu, 'gtx 1650') !== false) return 3;

		// AMD Radeon
		if (strpos($gpu, 'rx 6800') !== false) return 7;
		if (strpos($gpu, 'rx 6700') !== false) return 6;
		if (strpos($gpu, 'rx 6600') !== false) return 5;

		// Integrated graphics
		if (strpos($gpu, 'integrated') !== false || strpos($gpu, 'intel') !== false) return 2;

		return 1; // Default
	}

	/**
	 * Extract numeric value from string
	 */
	private function extractNumericValue($value)
	{
		preg_match('/(\d+)/', $value, $matches);
		return isset($matches[1]) ? (int) $matches[1] : 1;
	}

	/**
	 * Extract storage value and convert to GB
	 */
	private function extractStorageValue($storage)
	{
		$storage = strtolower($storage);
		preg_match('/(\d+)\s*(gb|tb)/', $storage, $matches);

		if (isset($matches[1]) && isset($matches[2])) {
			$value = (int) $matches[1];
			$unit = $matches[2];

			if ($unit === 'tb') {
				$value *= 1024; // Convert TB to GB
			}

			return $value;
		}

		return 256; // Default 256GB
	}

	/**
	 * Normalize the matrix using min-max normalization
	 */
	private function normalizeMatrix($matrix, $criteria)
	{
		$normalizedMatrix = [];

		foreach ($criteria as $criterion) {
			$values = [];
			foreach ($matrix as $laptopId => $data) {
				$values[] = $data['values'][$criterion->id_kriteria];
			}

			$maxValue = max($values);
			$minValue = min($values);

			foreach ($matrix as $laptopId => $data) {
				$value = $data['values'][$criterion->id_kriteria];

				// For cost criteria (like price), use min/max for benefit criteria use max/min
				if (strtolower($criterion->nama) === 'harga') {
					// Cost criterion - lower is better
					$normalized = $maxValue != $minValue ? ($maxValue - $value) / ($maxValue - $minValue) : 1;
				} else {
					// Benefit criterion - higher is better
					$normalized = $maxValue != $minValue ? ($value - $minValue) / ($maxValue - $minValue) : 1;
				}

				$normalizedMatrix[$laptopId][$criterion->id_kriteria] = $normalized;
			}
		}

		return $normalizedMatrix;
	}

	/**
	 * Calculate weighted scores based on user preferences
	 */
	private function calculateWeightedScores($normalizedMatrix, $userInputs, $criteria)
	{
		$scores = [];

		// Create user preference weights (scale of 1-5 converted to weights)
		$userWeights = [];
		$totalUserWeight = 0;

		foreach ($userInputs as $input) {
			$userWeights[$input->id_kriteria] = $input->value;
			$totalUserWeight += $input->value;
		}

		// Normalize user weights to sum to 1
		foreach ($userWeights as $kriteriaId => $weight) {
			$userWeights[$kriteriaId] = $weight / $totalUserWeight;
		}

		foreach ($normalizedMatrix as $laptopId => $values) {
			$score = 0;

			foreach ($criteria as $criterion) {
				if (isset($values[$criterion->id_kriteria]) && isset($userWeights[$criterion->id_kriteria])) {
					// Combine criterion weight with user preference weight
					$combinedWeight = ($criterion->bobot / 100) * $userWeights[$criterion->id_kriteria];
					$score += $values[$criterion->id_kriteria] * $combinedWeight;
				}
			}

			$scores[$laptopId] = $score;
		}

		return $scores;
	}

	/**
	 * Rank laptops based on scores
	 */
	private function rankLaptops($scores)
	{
		arsort($scores); // Sort descending

		$ranking = [];
		$rank = 1;

		foreach ($scores as $laptopId => $score) {
			$ranking[] = [
				'id_laptop' => $laptopId,
				'score' => $score,
				'rank' => $rank++
			];
		}

		return $ranking;
	}

	/**
	 * Save calculation results to database
	 */
	private function saveResults($idInput, $rankedLaptops, $normalizedMatrix, $criteria)
	{
		foreach ($rankedLaptops as $result) {
			$hasilInput = HasilInputUser::create([
				'id_input' => $idInput,
				'id_laptop' => $result['id_laptop'],
				'rating' => $result['score'],
				'ranking' => $result['rank']
			]);

			// Save detailed calculations
			foreach ($criteria as $criterion) {
				if (isset($normalizedMatrix[$result['id_laptop']][$criterion->id_kriteria])) {
					DetailHasilInputUser::create([
						'id_hasil_input' => $hasilInput->id_hasil_input,
						'id_kriteria' => $criterion->id_kriteria,
						'hasil_kalkulasi' => $normalizedMatrix[$result['id_laptop']][$criterion->id_kriteria]
					]);
				}
			}
		}
	}
}
