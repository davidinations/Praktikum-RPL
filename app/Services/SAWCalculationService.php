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
	private $detailedScores = []; // Store detailed scoring information

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

			// Rank laptops by SAW scores initially
			$rankedLaptops = $this->rankLaptops($scores);

			// Save results with initial ranking
			$this->saveResults($idInput, $rankedLaptops, $normalizedMatrix, $criteria);

			// Update rankings based on match percentage
			$this->updateRankingsByMatchPercentage($idInput);

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
	 * Calculate SAW method with budget filtering
	 */
	public function calculateSAWWithBudgetFilter($idInput, $idUser, $budgetRanges = [])
	{
		try {
			DB::beginTransaction();

			Log::info("Starting SAW calculation with budget filter for input: {$idInput}, user: {$idUser}");

			// Get user input values
			$userInputs = InputUser::where('id_input', $idInput)
				->where('id_user', $idUser)
				->with('kriteria')
				->get();

			if ($userInputs->isEmpty()) {
				throw new \Exception('No user input data found');
			}

			Log::info("Found {$userInputs->count()} user preferences");

			// Get all laptops initially
			$allLaptops = MasterLaptop::all();

			if ($allLaptops->isEmpty()) {
				throw new \Exception('No laptops found in database');
			}

			// Filter laptops by budget range if specified
			$laptops = $this->filterLaptopsByBudget($allLaptops, $budgetRanges);

			Log::info("After budget filtering: {$laptops->count()} out of {$allLaptops->count()} laptops qualify");

			if ($laptops->isEmpty()) {
				throw new \Exception('No laptops found within your budget range. Please adjust your budget criteria.');
			}

			// Get criteria with weights
			$criteria = MasterKriteria::all();

			Log::info("Using {$criteria->count()} criteria for calculation");

			// Prepare laptop data matrix
			$laptopMatrix = $this->prepareLaptopMatrix($laptops, $criteria);

			// Normalize the matrix
			$normalizedMatrix = $this->normalizeMatrix($laptopMatrix, $criteria);

			// Calculate weighted scores
			$scores = $this->calculateWeightedScores($normalizedMatrix, $userInputs, $criteria);

			// Rank laptops by SAW scores initially
			$rankedLaptops = $this->rankLaptops($scores);

			// Save results with initial ranking
			$this->saveResults($idInput, $rankedLaptops, $normalizedMatrix, $criteria);

			// Update rankings based on match percentage
			$this->updateRankingsByMatchPercentage($idInput);

			DB::commit();

			Log::info("SAW calculation with budget filter completed successfully. Top laptop: " . $rankedLaptops[0]['id_laptop'] . " with score: " . number_format($rankedLaptops[0]['score'], 4));

			return $rankedLaptops;
		} catch (\Exception $e) {
			DB::rollback();
			Log::error('SAWCalculationService with budget filter error: ' . $e->getMessage());
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
	 * Enhanced version with detailed matching analysis
	 */
	private function calculateWeightedScores($normalizedMatrix, $userInputs, $criteria)
	{
		$scores = [];
		$detailedScores = []; // Store detailed breakdown

		// Create user preference weights (scale of 1-5 converted to weights)
		$userWeights = [];
		$userPreferences = []; // Store user preference ratings
		$totalUserWeight = 0;

		foreach ($userInputs as $input) {
			$userWeights[$input->id_kriteria] = $input->value;
			$userPreferences[$input->id_kriteria] = $input->value; // Store user rating (1-5)
			$totalUserWeight += $input->value;
		}

		// Normalize user weights to sum to 1
		foreach ($userWeights as $kriteriaId => $weight) {
			$userWeights[$kriteriaId] = $weight / $totalUserWeight;
		}

		// Create a mapping of laptop actual ratings for comparison
		$laptopRatings = [];
		foreach ($normalizedMatrix as $laptopId => $values) {
			$laptopRatings[$laptopId] = [];
			foreach ($criteria as $criterion) {
				// Get the laptop from the matrix data
				$laptop = null;
				$laptops = \App\Models\MasterLaptop::all();
				foreach ($laptops as $l) {
					if ($l->id_laptop == $laptopId) {
						$laptop = $l;
						break;
					}
				}

				if ($laptop) {
					$laptopRatings[$laptopId][$criterion->id_kriteria] = $this->getLaptopRatingForCriterion($laptop, $criterion);
				}
			}
		}

		foreach ($normalizedMatrix as $laptopId => $values) {
			$score = 0;
			$detailedScore = [
				'laptop_id' => $laptopId,
				'criteria_scores' => [],
				'preference_match' => 0,
				'total_score' => 0
			];

			$preferenceMatchSum = 0;
			$preferenceMatchCount = 0;

			foreach ($criteria as $criterion) {
				if (isset($values[$criterion->id_kriteria]) && isset($userWeights[$criterion->id_kriteria])) {
					// Get laptop actual rating (1-5) for this criterion
					$laptopRating = isset($laptopRatings[$laptopId][$criterion->id_kriteria])
						? $laptopRatings[$laptopId][$criterion->id_kriteria]
						: 1;
					$userRating = $userPreferences[$criterion->id_kriteria];

					// Calculate preference match (how close laptop rating is to user preference)
					$preferenceDifference = abs($laptopRating - $userRating);
					$preferenceMatch = max(0, (5 - $preferenceDifference) / 5); // 1 = perfect match, 0 = maximum difference

					// Combine criterion weight with user preference weight
					$combinedWeight = ($criterion->bobot / 100) * $userWeights[$criterion->id_kriteria];
					$criterionScore = $values[$criterion->id_kriteria] * $combinedWeight;
					$score += $criterionScore;

					// Store detailed information
					$detailedScore['criteria_scores'][$criterion->id_kriteria] = [
						'criterion_name' => $criterion->nama,
						'laptop_rating' => $laptopRating,
						'user_preference' => $userRating,
						'preference_match' => round($preferenceMatch, 3),
						'normalized_score' => round($values[$criterion->id_kriteria], 3),
						'weighted_score' => round($criterionScore, 3),
						'weight' => round($combinedWeight, 3)
					];

					$preferenceMatchSum += $preferenceMatch * $userWeights[$criterion->id_kriteria];
					$preferenceMatchCount += $userWeights[$criterion->id_kriteria];
				}
			}

			// Calculate overall preference match
			$detailedScore['preference_match'] = $preferenceMatchCount > 0 ? $preferenceMatchSum / $preferenceMatchCount : 0;
			$detailedScore['total_score'] = $score;

			$scores[$laptopId] = $score;
			$detailedScores[$laptopId] = $detailedScore;
		}

		// Store detailed scores for later use
		$this->detailedScores = $detailedScores;

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
			// First create the HasilInputUser record with temporary rating
			$hasilInput = HasilInputUser::create([
				'id_input' => $idInput,
				'id_laptop' => $result['id_laptop'],
				'rating' => 0, // Temporary value, will be updated after details are saved
				'ranking' => $result['rank']
			]);

			// Save detailed calculations
			$detailCalculations = [];
			foreach ($criteria as $criterion) {
				if (isset($normalizedMatrix[$result['id_laptop']][$criterion->id_kriteria])) {
					$detailHasil = DetailHasilInputUser::create([
						'id_hasil_input' => $hasilInput->id_hasil_input,
						'id_kriteria' => $criterion->id_kriteria,
						'hasil_kalkulasi' => $normalizedMatrix[$result['id_laptop']][$criterion->id_kriteria]
					]);
					$detailCalculations[] = $detailHasil->hasil_kalkulasi;
				}
			}

			// Update the rating to be the average of detail calculations
			if (!empty($detailCalculations)) {
				$averageRating = array_sum($detailCalculations) / count($detailCalculations);
				$hasilInput->update(['rating' => $averageRating]);
			}
		}
	}

	/**
	 * Get detailed scores for analysis
	 */
	public function getDetailedScores()
	{
		return $this->detailedScores;
	}

	/**
	 * Get laptop matching analysis
	 */
	public function getLaptopMatchingAnalysis($idInput)
	{
		// Get user inputs
		$userInputs = InputUser::where('id_input', $idInput)
			->with('kriteria')
			->get();

		// Get results (ordered by ranking initially)
		$results = HasilInputUser::where('id_input', $idInput)
			->with(['laptop', 'detailHasil.kriteria'])
			->orderBy('ranking')
			->get();

		$analysis = [];

		foreach ($results as $result) {
			$laptopAnalysis = [
				'laptop' => $result->laptop,
				'score' => $result->rating,
				'rank' => $result->ranking,
				'criteria_match' => [],
				'preference_match_percentage' => 0,
				'combined_score' => 0, // Add combined score field
				'price_proximity_score' => 0, // Add price proximity score field
				'strengths' => [],
				'weaknesses' => []
			];

			$totalMatch = 0;
			$criteriaCount = 0;

			foreach ($result->detailHasil as $detail) {
				if ($detail->kriteria) {
					// Get user preference for this criterion
					$userPreference = $userInputs->where('id_kriteria', $detail->id_kriteria)->first();

					if ($userPreference) {
						// Convert normalized score back to rating (1-5)
						$laptopRating = round($detail->hasil_kalkulasi * 5, 1);
						$userRating = $userPreference->value;

						// Calculate match percentage for this criterion
						$difference = abs($laptopRating - $userRating);
						$matchPercentage = max(0, (5 - $difference) / 5 * 100);

						$criteriaMatch = [
							'criterion' => $detail->kriteria->nama,
							'laptop_rating' => $laptopRating,
							'user_preference' => $userRating,
							'match_percentage' => round($matchPercentage, 1),
							'is_strength' => $laptopRating >= $userRating,
							'is_weakness' => $laptopRating < $userRating && $difference > 1
						];

						$laptopAnalysis['criteria_match'][] = $criteriaMatch;

						// Track strengths and weaknesses
						if ($criteriaMatch['is_strength'] && $matchPercentage > 80) {
							$laptopAnalysis['strengths'][] = $detail->kriteria->nama;
						} elseif ($criteriaMatch['is_weakness']) {
							$laptopAnalysis['weaknesses'][] = $detail->kriteria->nama;
						}

						$totalMatch += $matchPercentage;
						$criteriaCount++;
					}
				}
			}

			$laptopAnalysis['preference_match_percentage'] = $criteriaCount > 0 ? round($totalMatch / $criteriaCount, 1) : 0;

			// Calculate price proximity score
			$priceProximityScore = 0;
			$priceMatch = collect($laptopAnalysis['criteria_match'])->where('criterion', 'Harga')->first();
			if ($priceMatch) {
				$priceProximityScore = $priceMatch['match_percentage'];
			}
			$laptopAnalysis['price_proximity_score'] = round($priceProximityScore, 1);

			// Calculate combined score: preference match (70%) + price proximity (30%)
			$laptopAnalysis['combined_score'] = round(
				($laptopAnalysis['preference_match_percentage'] * 0.7) +
					($priceProximityScore * 0.3),
				1
			);

			$analysis[] = $laptopAnalysis;
		}

		// Sort analysis by combined score (descending) - highest overall match first
		usort($analysis, function ($a, $b) {
			return $b['combined_score'] <=> $a['combined_score'];
		});

		// Update rankings based on match percentage order
		foreach ($analysis as $index => &$item) {
			$item['rank'] = $index + 1;
		}

		return $analysis;
	}

	/**
	 * Recalculate and update rankings based on match percentage for existing results
	 */
	public function updateRankingsByMatchPercentage($idInput)
	{
		try {
			DB::beginTransaction();

			// Get all results for this input session
			$results = HasilInputUser::where('id_input', $idInput)
				->with(['laptop', 'detailHasil.kriteria'])
				->get();

			if ($results->isEmpty()) {
				throw new \Exception('No results found for input session: ' . $idInput);
			}

			// Get user inputs for this session
			$userInputs = InputUser::where('id_input', $idInput)
				->with('kriteria')
				->get();

			$analysisData = [];

			// Calculate match percentage for each result
			foreach ($results as $result) {
				// Update average rating first
				$result->updateAverageRating();

				$totalMatch = 0;
				$criteriaCount = 0;

				foreach ($result->detailHasil as $detail) {
					if ($detail->kriteria) {
						$userPreference = $userInputs->where('id_kriteria', $detail->id_kriteria)->first();
						if ($userPreference) {
							$laptopRating = round($detail->hasil_kalkulasi * 5, 1);
							$userRating = $userPreference->value;
							$difference = abs($laptopRating - $userRating);
							$matchPercentage = max(0, (5 - $difference) / 5 * 100);
							$totalMatch += $matchPercentage;
							$criteriaCount++;
						}
					}
				}

				$overallMatchPercentage = $criteriaCount > 0 ? round($totalMatch / $criteriaCount, 1) : 0;

				$analysisData[] = [
					'result' => $result,
					'match_percentage' => $overallMatchPercentage
				];
			}

			// Sort by match percentage (descending)
			usort($analysisData, function ($a, $b) {
				return $b['match_percentage'] <=> $a['match_percentage'];
			});

			// Update rankings
			foreach ($analysisData as $index => $data) {
				$data['result']->update(['ranking' => $index + 1]);
			}

			DB::commit();

			Log::info("Rankings updated successfully for input session: {$idInput}");
			return true;
		} catch (\Exception $e) {
			DB::rollback();
			Log::error('Error updating rankings: ' . $e->getMessage());
			throw $e;
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

	/**
	 * Demo method to test enhanced SAW calculation
	 */
	public function demoEnhancedSAW()
	{
		try {
			// Get sample criteria
			$criteria = MasterKriteria::all();

			if ($criteria->isEmpty()) {
				return ['error' => 'No criteria found. Please set up criteria first.'];
			}

			// Get sample laptops
			$laptops = MasterLaptop::take(5)->get();

			if ($laptops->isEmpty()) {
				return ['error' => 'No laptops found. Please add laptops to the database first.'];
			}

			// Sample user preferences (simulating user input)
			$samplePreferences = [];
			foreach ($criteria as $criterion) {
				switch (strtolower($criterion->nama)) {
					case 'harga':
					case 'price':
						$samplePreferences[] = ['id_kriteria' => $criterion->id_kriteria, 'value' => 4]; // High importance for budget-friendly
						break;
					case 'processor':
						$samplePreferences[] = ['id_kriteria' => $criterion->id_kriteria, 'value' => 5]; // Very important
						break;
					case 'ram':
						$samplePreferences[] = ['id_kriteria' => $criterion->id_kriteria, 'value' => 4]; // Important
						break;
					case 'storage':
						$samplePreferences[] = ['id_kriteria' => $criterion->id_kriteria, 'value' => 3]; // Moderate
						break;
					default:
						$samplePreferences[] = ['id_kriteria' => $criterion->id_kriteria, 'value' => 3]; // Moderate
						break;
				}
			}

			// Prepare laptop data matrix
			$laptopMatrix = $this->prepareLaptopMatrix($laptops, $criteria);

			// Normalize the matrix
			$normalizedMatrix = $this->normalizeMatrix($laptopMatrix, $criteria);

			// Create user inputs objects for calculation
			$userInputs = collect($samplePreferences)->map(function ($pref) {
				return (object) $pref;
			});

			// Calculate weighted scores
			$scores = $this->calculateWeightedScores($normalizedMatrix, $userInputs, $criteria);

			// Rank laptops
			$rankedLaptops = $this->rankLaptops($scores);

			// Create demo analysis
			$demoAnalysis = [];
			foreach ($rankedLaptops as $result) {
				$laptop = $laptops->where('id_laptop', $result['id_laptop'])->first();
				if ($laptop) {
					$laptopAnalysis = [
						'laptop' => [
							'id' => $laptop->id_laptop,
							'brand' => $laptop->merek,
							'model' => $laptop->model,
							'price' => $laptop->harga,
							'processor' => $laptop->processor,
							'ram' => $laptop->ram,
							'storage' => $laptop->storage,
							'gpu' => $laptop->gpu,
						],
						'score' => $result['score'],
						'rank' => $result['rank'],
						'criteria_breakdown' => []
					];

					// Calculate criteria breakdown
					foreach ($criteria as $criterion) {
						$laptopRating = $this->getLaptopRatingForCriterion($laptop, $criterion);
						$userRating = collect($samplePreferences)->where('id_kriteria', $criterion->id_kriteria)->first()['value'] ?? 3;
						$matchPercentage = max(0, (5 - abs($laptopRating - $userRating)) / 5 * 100);

						$laptopAnalysis['criteria_breakdown'][] = [
							'criterion' => $criterion->nama,
							'laptop_rating' => $laptopRating,
							'user_preference' => $userRating,
							'match_percentage' => round($matchPercentage, 1),
							'laptop_value' => $this->getLaptopValueForCriterion($laptop, $criterion),
							'is_strength' => $laptopRating >= $userRating,
						];
					}

					$demoAnalysis[] = $laptopAnalysis;
				}
			}

			return [
				'success' => true,
				'sample_preferences' => $samplePreferences,
				'criteria' => $criteria->toArray(),
				'analysis' => $demoAnalysis,
				'message' => 'Enhanced SAW calculation completed successfully!'
			];
		} catch (\Exception $e) {
			return ['error' => 'Demo failed: ' . $e->getMessage()];
		}
	}

	/**
	 * Filter laptops by budget range with tolerance
	 */
	private function filterLaptopsByBudget($laptops, $budgetRanges)
	{
		if (empty($budgetRanges)) {
			return $laptops; // No budget filter applied
		}

		$filteredLaptops = collect();
		$tolerance = 500000; // 500K Rupiah tolerance as requested

		foreach ($laptops as $laptop) {
			$laptopPrice = (float) $laptop->harga;
			$shouldInclude = true;

			foreach ($budgetRanges as $criteriaId => $range) {
				// Check if this is a price criteria
				$criteria = MasterKriteria::find($criteriaId);
				if ($criteria && (strtolower($criteria->nama) === 'harga' || strtolower($criteria->nama) === 'price')) {
					$minBudget = (float) $range['min'];
					$maxBudget = (float) $range['max'];

					// Apply tolerance: allow laptops within 500K of the budget range
					$effectiveMinBudget = $minBudget - $tolerance;
					$effectiveMaxBudget = $maxBudget + $tolerance;

					if ($laptopPrice < $effectiveMinBudget || $laptopPrice > $effectiveMaxBudget) {
						$shouldInclude = false;
						Log::info("Laptop {$laptop->merek} {$laptop->model} (Price: " . number_format($laptopPrice) . ") excluded - outside budget range " . number_format($minBudget) . " - " . number_format($maxBudget) . " with ±500K tolerance");
						break;
					} else {
						Log::info("Laptop {$laptop->merek} {$laptop->model} (Price: " . number_format($laptopPrice) . ") included - within budget range " . number_format($minBudget) . " - " . number_format($maxBudget) . " with ±500K tolerance");
					}
				}
			}

			if ($shouldInclude) {
				$filteredLaptops->push($laptop);
			}
		}

		return $filteredLaptops;
	}

	/**
	 * Get laptop matching analysis with price ordering and pure SAW scores
	 */
	public function getLaptopMatchingAnalysisWithPriceOrdering($idInput)
	{
		// Get the basic analysis first
		$analysis = $this->getLaptopMatchingAnalysis($idInput);

		// Get all laptops and criteria to calculate pure SAW scores
		$laptops = collect();
		foreach ($analysis as $item) {
			$laptops->push($item['laptop']);
		}
		$criteria = MasterKriteria::all();

		// Get user inputs to calculate average preference SAW score
		$userInputs = InputUser::where('id_input', $idInput)->with('kriteria')->get();
		$userPreferenceAverageSAW = $this->calculateUserPreferenceAverageSAW($userInputs, $criteria);

		// Calculate pure SAW scores for all laptops
		$pureScores = $this->calculatePureSAWScores($laptops, $criteria);

		// Add pure SAW scores and user preference average to analysis
		foreach ($analysis as &$item) {
			$laptopId = $item['laptop']->id_laptop;
			$item['pure_saw_score'] = isset($pureScores[$laptopId]) ? round($pureScores[$laptopId] * 100, 1) : 0;
			$item['user_saw_score'] = round($item['score'] * 100, 1); // Convert to percentage for consistency
			$item['user_preference_average_saw'] = round($userPreferenceAverageSAW * 100, 1); // User's ideal SAW score

			// Calculate how close the laptop's overall quality is to user's ideal
			$item['quality_closeness_to_ideal'] = abs($item['pure_saw_score'] - $item['user_preference_average_saw']);
		}

		// Get price criterion
		$priceCriterion = MasterKriteria::where('nama', 'Harga')->first();

		if (!$priceCriterion) {
			// If no price criterion found, return basic analysis
			return $analysis;
		}

		// Sort by closest overall quality to user's ideal (ascending difference) - closest to ideal first
		usort($analysis, function ($a, $b) {
			return $a['quality_closeness_to_ideal'] <=> $b['quality_closeness_to_ideal'];
		});

		// Update rankings after quality closeness ordering
		foreach ($analysis as $index => &$item) {
			$item['rank'] = $index + 1;
		}

		return $analysis;
	}

	/**
	 * Calculate pure SAW scores for laptops without user preference weighting
	 * This gives the raw SAW score based only on criteria weights
	 */
	public function calculatePureSAWScores($laptops, $criteria)
	{
		$pureScores = [];

		// Prepare laptop data matrix
		$laptopMatrix = $this->prepareLaptopMatrix($laptops, $criteria);

		// Normalize the matrix
		$normalizedMatrix = $this->normalizeMatrix($laptopMatrix, $criteria);

		// Calculate pure SAW scores using only criteria weights (no user preference weighting)
		foreach ($normalizedMatrix as $laptopId => $values) {
			$score = 0;
			$totalWeight = 0;

			foreach ($criteria as $criterion) {
				if (isset($values[$criterion->id_kriteria])) {
					// Use only the criterion weight (no user preference multiplication)
					$weight = $criterion->bobot / 100;
					$criterionScore = $values[$criterion->id_kriteria] * $weight;
					$score += $criterionScore;
					$totalWeight += $weight;
				}
			}

			// Normalize score to 0-1 range
			$pureScores[$laptopId] = $totalWeight > 0 ? $score / $totalWeight : 0;
		}

		return $pureScores;
	}

	/**
	 * Calculate average SAW score based on user preferences
	 * This shows what the average SAW score would be for the user's preference combination
	 */
	public function calculateUserPreferenceAverageSAW($userInputs, $criteria)
	{
		$totalScore = 0;
		$totalWeight = 0;

		foreach ($userInputs as $input) {
			$criterion = $criteria->where('id_kriteria', $input->id_kriteria)->first();
			if ($criterion) {
				// Convert user preference (1-5) to normalized score (0.2-1.0)
				$userPreferenceScore = $input->value / 5.0;

				// Weight by criterion importance
				$weight = $criterion->bobot / 100;
				$weightedScore = $userPreferenceScore * $weight;

				$totalScore += $weightedScore;
				$totalWeight += $weight;
			}
		}

		return $totalWeight > 0 ? $totalScore / $totalWeight : 0;
	}

	/**
	 * Find all price rating bands that overlap with user's budget input
	 * Returns array of overlapping rating bands for display
	 */
	public function findOverlappingPriceRatingBands($userBudgetMin, $userBudgetMax, $priceCriteria)
	{
		$overlappingBands = [];

		if (!$priceCriteria) {
			return $overlappingBands;
		}

		for ($i = 1; $i <= 5; $i++) {
			$bandMin = $priceCriteria->{"rating_{$i}_min"};
			$bandMax = $priceCriteria->{"rating_{$i}_max"};

			// Check if user budget overlaps with this rating band
			// Convert millions to actual price for comparison
			$bandMinPrice = $bandMin * 1000000;
			$bandMaxPrice = $bandMax * 1000000;

			if ($userBudgetMax >= $bandMinPrice && $userBudgetMin <= $bandMaxPrice) {
				$overlappingBands[] = [
					'rating' => $i,
					'min' => $bandMinPrice,
					'max' => $bandMaxPrice,
					'min_million' => $bandMin,
					'max_million' => $bandMax
				];
			}
		}

		return $overlappingBands;
	}

	/**
	 * Get user's actual budget range from their input
	 * This extracts the actual min/max budget if they provided price range input
	 */
	public function getUserActualBudgetRange($idInput, $idUser)
	{
		// Check if there's budget range data stored
		$budgetRange = DB::table('input_user_budget_ranges')
			->where('id_input', $idInput)
			->where('id_user', $idUser)
			->first();

		if ($budgetRange) {
			return [
				'min' => $budgetRange->min_budget,
				'max' => $budgetRange->max_budget
			];
		}

		// If no explicit budget range, try to infer from price criteria rating
		$priceInput = InputUser::where('id_input', $idInput)
			->where('id_user', $idUser)
			->whereHas('kriteria', function ($query) {
				$query->where('nama', 'like', '%harga%');
			})
			->first();

		if ($priceInput) {
			$priceCriteria = $priceInput->kriteria;
			$rating = (int)$priceInput->value;
			$ratingField = "rating_{$rating}";

			return [
				'min' => $priceCriteria->{$ratingField . '_min'} * 1000000,
				'max' => $priceCriteria->{$ratingField . '_max'} * 1000000,
				'inferred_from_rating' => $rating
			];
		}

		return null;
	}
}
