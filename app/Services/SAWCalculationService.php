<?php

namespace App\Services;

use App\Models\MasterLaptop;
use App\Models\MasterKriteria;
use App\Models\InputUser;
use App\Models\HasilInputUser;
use App\Models\DetailHasilInputUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SAWCalculationService
{
	/**
	 * Calculate SAW method for laptop recommendation
	 */
	public function calculateSAW($idInput, $idUser)
	{
		try {
			DB::beginTransaction();

			Log::info("Starting SAW calculation for input: {$idInput}, user: {$idUser}");

			// Get user input values
			$userInputs = InputUser::where('id_input', $idInput)
				->where('id_user', $idUser)
				->with('kriteria')
				->get();

			if ($userInputs->isEmpty()) {
				throw new \Exception('No user input data found');
			}

			Log::info("Found {$userInputs->count()} user preferences");

			// Get all laptops
			$laptops = MasterLaptop::all();

			if ($laptops->isEmpty()) {
				throw new \Exception('No laptops found in database');
			}

			Log::info("Found {$laptops->count()} laptops to evaluate");

			// Get criteria with weights
			$criteria = MasterKriteria::all();

			Log::info("Using {$criteria->count()} criteria for calculation");

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

			Log::info("SAW calculation completed successfully. Top laptop: " . $rankedLaptops[0]['id_laptop'] . " with score: " . number_format($rankedLaptops[0]['score'], 4));

			return $rankedLaptops;
		} catch (\Exception $e) {
			DB::rollback();
			Log::error('SAWCalculationService error: ' . $e->getMessage());
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
				return (float) ($laptop->harga / 1000000); // Convert to millions
			case 'processor':
				return (float) $laptop->ghz; // Use the GHz value directly
			case 'ram':
				return (float) $laptop->ram;
			case 'storage':
				return (float) $laptop->storage;
			default:
				return 1; // Default value
		}
	}

	/**
	 * Normalize processor to numeric value
	 */
	private function normalizeProcessor($processor)
	{
		// This method is now deprecated as we use GHz values directly
		return 1;
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
	 * Get laptop rating for specific criterion based on admin-defined ranges
	 */
	private function getLaptopRatingForCriterion($laptop, $criterion)
	{
		$value = $this->getLaptopValueForCriterion($laptop, $criterion);

		// Check which rating range the value falls into
		for ($rating = 1; $rating <= 5; $rating++) {
			$minField = "rating_{$rating}_min";
			$maxField = "rating_{$rating}_max";

			if (isset($criterion->$minField) && isset($criterion->$maxField)) {
				if ($value >= $criterion->$minField && $value <= $criterion->$maxField) {
					return $rating;
				}
			}
		}

		// Default rating if no range matches
		return 1;
	}

	/**
	 * Normalize the matrix using rating-based approach
	 * Uses admin-defined rating ranges for each criterion
	 */
	private function normalizeMatrix($matrix, $criteria)
	{
		$normalizedMatrix = [];

		foreach ($criteria as $criterion) {
			foreach ($matrix as $laptopId => $data) {
				$rating = $this->getLaptopRatingForCriterion($data['laptop'], $criterion);

				// Normalize rating to 0-1 scale (1=0.2, 2=0.4, 3=0.6, 4=0.8, 5=1.0)
				$normalizedValue = $rating / 5.0;

				$normalizedMatrix[$laptopId][$criterion->id_kriteria] = $normalizedValue;
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

	/**
	 * Determine if a criterion is a cost type (lower is better) or benefit type (higher is better)
	 */
	private function isCostCriterion($criterion)
	{
		// Check the 'jenis' field first
		if (!empty($criterion->jenis)) {
			return strtolower($criterion->jenis) === 'cost';
		}

		// Fallback to name-based detection for backward compatibility
		$costCriteria = ['harga', 'price', 'cost'];
		return in_array(strtolower($criterion->nama), $costCriteria);
	}

	/**
	 * Get default laptop image based on laptop ID
	 */
	public function getDefaultLaptopImage($laptopId)
	{
		$defaultImages = ['laptop1.webp', 'laptop2.webp', 'laptop3.webp', 'laptop4.webp', 'laptop5.webp'];
		return $defaultImages[($laptopId - 1) % count($defaultImages)];
	}

	/**
	 * Get laptop image path (either custom or default)
	 */
	public function getLaptopImagePath($laptop)
	{
		if ($laptop->gambar && file_exists(storage_path('app/public/laptops/' . $laptop->gambar))) {
			return 'storage/laptops/' . $laptop->gambar;
		}

		return 'images/' . $this->getDefaultLaptopImage($laptop->id_laptop);
	}

	/**
	 * Debug method to analyze the normalization results
	 */
	public function debugNormalization($matrix, $criteria)
	{
		foreach ($criteria as $criterion) {
			$values = [];
			foreach ($matrix as $laptopId => $data) {
				$values[] = $data['values'][$criterion->id_kriteria];
			}

			$maxValue = max($values);
			$minValue = min($values);
			$isCost = $this->isCostCriterion($criterion);

			Log::info("Criterion: {$criterion->nama} | Type: " . ($isCost ? 'COST' : 'BENEFIT') . " | Min: {$minValue} | Max: {$maxValue} | Jenis: {$criterion->jenis}");
		}
	}
}
