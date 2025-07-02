<?php

namespace App\Http\Controllers;

use App\Models\MasterKriteria;
use App\Services\RatingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KriteriaRatingController extends Controller
{
	protected $ratingService;

	public function __construct(RatingService $ratingService)
	{
		$this->ratingService = $ratingService;
	}

	/**
	 * Display the criteria rating management page
	 */
	public function index()
	{
		$criteria = MasterKriteria::all();
		return view('admin.criteria-rating.index', compact('criteria'));
	}

	/**
	 * Show the form for editing rating ranges of a specific criterion
	 */
	public function edit($id)
	{
		$criterion = MasterKriteria::findOrFail($id);
		$ratingRanges = $this->ratingService->getRatingRanges($criterion);

		return view('admin.criteria-rating.edit', compact('criterion', 'ratingRanges'));
	}

	/**
	 * Update the rating ranges for a specific criterion
	 */
	public function update(Request $request, $id)
	{
		$validator = Validator::make($request->all(), [
			'rating_1_min' => 'required|numeric',
			'rating_1_max' => 'required|numeric|gte:rating_1_min',
			'rating_2_min' => 'required|numeric|gt:rating_1_max',
			'rating_2_max' => 'required|numeric|gte:rating_2_min',
			'rating_3_min' => 'required|numeric|gt:rating_2_max',
			'rating_3_max' => 'required|numeric|gte:rating_3_min',
			'rating_4_min' => 'required|numeric|gt:rating_3_max',
			'rating_4_max' => 'required|numeric|gte:rating_4_min',
			'rating_5_min' => 'required|numeric|gt:rating_4_max',
			'rating_5_max' => 'required|numeric|gte:rating_5_min',
		]);

		if ($validator->fails()) {
			return redirect()->back()
				->withErrors($validator)
				->withInput();
		}

		try {
			$ranges = [];
			for ($rating = 1; $rating <= 5; $rating++) {
				$ranges[$rating] = [
					'min' => $request->input("rating_{$rating}_min"),
					'max' => $request->input("rating_{$rating}_max")
				];
			}

			$this->ratingService->updateRatingRanges($id, $ranges);

			return redirect()->route('admin.criteria-rating.index')
				->with('success', 'Rating ranges updated successfully');
		} catch (\Exception $e) {
			return redirect()->back()
				->with('error', 'Error updating rating ranges: ' . $e->getMessage())
				->withInput();
		}
	}

	/**
	 * Get rating for a specific value and criterion (API endpoint)
	 */
	public function getRating(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'criterion_id' => 'required|exists:master_kriteria,id_kriteria',
			'value' => 'required|numeric'
		]);

		if ($validator->fails()) {
			return response()->json(['error' => $validator->errors()->first()], 400);
		}

		$criterion = MasterKriteria::find($request->criterion_id);
		$rating = $this->ratingService->getRatingForValue($request->value, $criterion);
		$description = $this->ratingService->getRatingDescription($rating);

		return response()->json([
			'rating' => $rating,
			'description' => $description,
			'criterion' => $criterion->nama
		]);
	}
}
