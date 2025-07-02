<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HasilInputUser extends Model
{
	use HasFactory;

	protected $table = 'hasil_input_user';
	protected $primaryKey = 'id_hasil_input';

	protected $fillable = [
		'id_input',
		'id_laptop',
		'rating',
		'ranking'
	];

	public function laptop()
	{
		return $this->belongsTo(MasterLaptop::class, 'id_laptop', 'id_laptop');
	}

	// Relationship to input_user via id_input
	public function inputUsers()
	{
		return $this->hasMany(InputUser::class, 'id_input', 'id_input');
	}

	// Get the input session data for this result
	public function inputSession()
	{
		return $this->inputUsers()->with('kriteria');
	}

	public function detailHasil()
	{
		return $this->hasMany(DetailHasilInputUser::class, 'id_hasil_input', 'id_hasil_input');
	}

	// Get ranking results for a specific input session
	public static function getRankingResults($idInput)
	{
		return self::where('id_input', $idInput)
			->with('laptop')
			->orderBy('ranking', 'asc')
			->get();
	}

	// Get ranking results with input preferences (ordered by match percentage)
	public static function getRankingResultsWithPreferences($idInput)
	{
		// Get all results with relationships
		$results = self::where('id_input', $idInput)
			->with(['laptop', 'inputUsers.kriteria', 'detailHasil.kriteria'])
			->get();

		// Calculate match percentage for each result
		$resultsWithMatch = $results->map(function ($result) {
			$userInputs = $result->inputUsers;
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

			$result->preference_match_percentage = $criteriaCount > 0 ? round($totalMatch / $criteriaCount, 1) : 0;
			return $result;
		});

		// Sort by match percentage (descending)
		return $resultsWithMatch->sortByDesc('preference_match_percentage')->values();
	}

	// Update the average rating based on detail calculations
	public function updateAverageRating()
	{
		$detailCalculations = $this->detailHasil()->pluck('hasil_kalkulasi');

		if ($detailCalculations->isNotEmpty()) {
			$averageRating = $detailCalculations->avg();
			$this->update(['rating' => $averageRating]);
			return $averageRating;
		}

		return $this->rating;
	}

	// Get user preferences for this result's input session
	public function getUserPreferences()
	{
		return $this->inputUsers()->with('kriteria')->get();
	}

	// Handle common relationship naming mistakes
	public function details()
	{
		// Log the error for debugging but return the correct relationship
		\Log::warning('Attempted to access "details" relationship on HasilInputUser. Using "detailHasil" instead.', [
			'model_id' => $this->id_hasil_input ?? null,
			'backtrace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3)
		]);

		return $this->detailHasil();
	}
}
