<?php

namespace App\Services;

use App\Models\MasterKriteria;
use Illuminate\Support\Facades\Log;

class RatingService
{
	/**
	 * Get rating for a specific value based on criterion ranges
	 */
	public function getRatingForValue($value, $criterion)
	{
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
	 * Get rating description
	 */
	public function getRatingDescription($rating)
	{
		$descriptions = [
			1 => 'Very Poor',
			2 => 'Poor',
			3 => 'Fair',
			4 => 'Good',
			5 => 'Excellent'
		];

		return $descriptions[$rating] ?? 'Unknown';
	}

	/**
	 * Validate rating ranges for a criterion
	 */
	public function validateRatingRanges($criterion)
	{
		$errors = [];

		// Check if all rating ranges are defined
		for ($rating = 1; $rating <= 5; $rating++) {
			$minField = "rating_{$rating}_min";
			$maxField = "rating_{$rating}_max";

			if (!isset($criterion->$minField) || !isset($criterion->$maxField)) {
				$errors[] = "Rating {$rating} range is not defined";
				continue;
			}

			if ($criterion->$minField > $criterion->$maxField) {
				$errors[] = "Rating {$rating} minimum value is greater than maximum value";
			}
		}

		// Check for overlapping ranges
		for ($rating = 1; $rating <= 4; $rating++) {
			$currentMaxField = "rating_{$rating}_max";
			$nextMinField = "rating_" . ($rating + 1) . "_min";

			if (isset($criterion->$currentMaxField) && isset($criterion->$nextMinField)) {
				if ($criterion->$currentMaxField >= $criterion->$nextMinField) {
					$errors[] = "Rating {$rating} and " . ($rating + 1) . " ranges overlap";
				}
			}
		}

		return $errors;
	}

	/**
	 * Get all rating ranges for a criterion
	 */
	public function getRatingRanges($criterion)
	{
		$ranges = [];

		for ($rating = 1; $rating <= 5; $rating++) {
			$minField = "rating_{$rating}_min";
			$maxField = "rating_{$rating}_max";

			if (isset($criterion->$minField) && isset($criterion->$maxField)) {
				$ranges[$rating] = [
					'min' => $criterion->$minField,
					'max' => $criterion->$maxField,
					'description' => $this->getRatingDescription($rating)
				];
			}
		}

		return $ranges;
	}

	/**
	 * Update rating ranges for a criterion
	 */
	public function updateRatingRanges($criterionId, $ranges)
	{
		$criterion = MasterKriteria::find($criterionId);

		if (!$criterion) {
			throw new \Exception('Criterion not found');
		}

		// Update each rating range
		for ($rating = 1; $rating <= 5; $rating++) {
			$minField = "rating_{$rating}_min";
			$maxField = "rating_{$rating}_max";

			if (isset($ranges[$rating])) {
				$criterion->$minField = $ranges[$rating]['min'];
				$criterion->$maxField = $ranges[$rating]['max'];
			}
		}

		// Validate ranges before saving
		$errors = $this->validateRatingRanges($criterion);
		if (!empty($errors)) {
			throw new \Exception('Invalid rating ranges: ' . implode(', ', $errors));
		}

		$criterion->save();

		Log::info("Updated rating ranges for criterion: {$criterion->nama}");

		return $criterion;
	}
}
