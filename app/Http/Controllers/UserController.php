<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\MasterKriteria;
use App\Models\MasterLaptop;
use App\Models\InputUser;
use App\Models\HasilInputUser;
use App\Services\SAWCalculationService;

class UserController extends Controller
{
    protected $sawService;

    public function __construct(SAWCalculationService $sawService)
    {
        $this->sawService = $sawService;
        $this->middleware(function ($request, $next) {
            if (!Auth::check() || session('user_type') !== 'user') {
                return redirect()->route('login')->with('error', 'Please login as user to access this page.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $user = Auth::user();
        $recentHistory = $user->getInputHistory()->take(5);

        return view('user.index', compact('user', 'recentHistory'));
    }

    // Profile Management
    public function showProfile()
    {
        $user = Auth::user();
        return view('user.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:users,username,' . Auth::id() . ',id_user',
            'email' => 'required|email|unique:users,email,' . Auth::id() . ',id_user',
            'nama_lengkap' => 'nullable|string|max:255',
            'no_telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string|max:500',
            'password' => 'nullable|min:6|confirmed',
        ]);

        $user = Auth::user();
        $user->username = $request->username;
        $user->email = $request->email;
        $user->nama_lengkap = $request->nama_lengkap;
        $user->no_telepon = $request->no_telepon;
        $user->alamat = $request->alamat;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('user.profile')->with('success', 'Profile updated successfully!');
    }

    // Input User Preferences
    public function showInputForm()
    {
        $criteria = MasterKriteria::orderBy('nama')->get();
        return view('user.input', compact('criteria'));
    }

    public function submitInput(Request $request)
    {
        $request->validate([
            'preferences' => 'required|array',
            'preferences.*' => 'required|integer|between:1,5'
        ]);

        // Extract budget range inputs if present
        $budgetRanges = [];
        foreach ($request->all() as $key => $value) {
            if (strpos($key, 'price_min_') === 0) {
                $criteriaId = str_replace('price_min_', '', $key);
                if (!empty($value)) {
                    $budgetRanges[$criteriaId]['min'] = (int) str_replace(['.', ','], '', $value);
                }
            } elseif (strpos($key, 'price_max_') === 0) {
                $criteriaId = str_replace('price_max_', '', $key);
                if (!empty($value)) {
                    $budgetRanges[$criteriaId]['max'] = (int) str_replace(['.', ','], '', $value);
                }
            }
        }

        try {
            DB::beginTransaction();

            // Generate unique input ID
            $idInput = time() . Auth::id();

            // Save user inputs
            foreach ($request->preferences as $kriteriaId => $value) {
                InputUser::create([
                    'id_input' => $idInput,
                    'id_user' => Auth::id(),
                    'id_kriteria' => $kriteriaId,
                    'value' => $value
                ]);
            }

            // Save budget ranges if provided
            foreach ($budgetRanges as $criteriaId => $range) {
                if (isset($range['min']) && isset($range['max'])) {
                    DB::table('input_user_budget_ranges')->insert([
                        'id_input' => $idInput,
                        'id_user' => Auth::id(),
                        'id_kriteria' => $criteriaId,
                        'min_budget' => $range['min'],
                        'max_budget' => $range['max'],
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            // Calculate SAW results with budget filtering
            $results = $this->sawService->calculateSAWWithBudgetFilter($idInput, Auth::id(), $budgetRanges);

            DB::commit();

            return redirect()->route('user.results', ['id_input' => $idInput])
                ->with('success', 'Preferences saved and calculation completed!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error processing your request: ' . $e->getMessage());
        }
    }

    // View Results
    public function showResults($idInput)
    {
        $user = Auth::user();

        // Verify this input belongs to the current user
        $userInput = InputUser::where('id_input', $idInput)
            ->where('id_user', $user->id_user)
            ->first();

        if (!$userInput) {
            return redirect()->route('user.dashboard')->with('error', 'Results not found.');
        }

        // Get ranking results using the new relationship method
        $results = HasilInputUser::getRankingResultsWithPreferences($idInput);

        // Get user preferences for this input (alternative using relationship)
        $userPreferences = InputUser::getInputSession($idInput);

        // Get detailed matching analysis with price ordering
        $matchingAnalysis = $this->sawService->getLaptopMatchingAnalysisWithPriceOrdering($idInput);

        // Get overlapping price rating bands if user has price input
        $overlappingPriceBands = [];
        $userBudgetRange = $this->sawService->getUserActualBudgetRange($idInput, $user->id_user);

        if ($userBudgetRange) {
            $priceCriteria = MasterKriteria::where('nama', 'like', '%harga%')->first();
            if ($priceCriteria) {
                $overlappingPriceBands = $this->sawService->findOverlappingPriceRatingBands(
                    $userBudgetRange['min'],
                    $userBudgetRange['max'],
                    $priceCriteria
                );
            }
        }

        return view('user.results', compact('results', 'userPreferences', 'idInput', 'matchingAnalysis', 'overlappingPriceBands', 'userBudgetRange'));
    }

    // View History
    public function showHistory()
    {
        $user = Auth::user();
        $history = $user->getInputHistory();

        return view('user.history', compact('history'));
    }

    public function showHistoryDetail($idInput)
    {
        return $this->showResults($idInput);
    }

    // Compare Laptops
    public function compareLaptops(Request $request)
    {
        $laptopIds = $request->input('laptops', []);

        if (empty($laptopIds) || count($laptopIds) < 2) {
            return back()->with('error', 'Please select at least 2 laptops to compare.');
        }

        $laptops = MasterLaptop::whereIn('id_laptop', $laptopIds)->get();
        $criteria = MasterKriteria::all();

        return view('user.compare', compact('laptops', 'criteria'));
    }

    // Test Enhanced SAW calculation
    public function testEnhancedSAW()
    {
        $result = $this->sawService->demoEnhancedSAW();

        if (isset($result['error'])) {
            return view('test.saw', ['error' => $result['error']]);
        }

        return view('test.saw', ['result' => $result]);
    }

    // Demonstrate model relationships
    public function testRelationships()
    {
        try {
            // Get a sample input session
            $inputUser = InputUser::with(['kriteria', 'hasilInputUser.laptop'])->first();

            if (!$inputUser) {
                return response()->json(['error' => 'No input data found'], 404);
            }

            $idInput = $inputUser->id_input;

            // Test 1: Get input session with results using relationship
            $inputWithResults = InputUser::getInputSessionWithResults($idInput);

            // Test 2: Get results with preferences using relationship
            $resultsWithPrefs = HasilInputUser::getRankingResultsWithPreferences($idInput);

            // Test 3: Get individual result with user preferences
            $sampleResult = HasilInputUser::where('id_input', $idInput)->first();
            $userPreferences = $sampleResult ? $sampleResult->getUserPreferences() : null;

            // Test 4: Get results from input user using relationship
            $resultsFromInput = $inputUser->getResults();

            return response()->json([
                'success' => true,
                'message' => 'Model relationships working correctly!',
                'id_input' => $idInput,
                'tests' => [
                    'input_with_results_count' => $inputWithResults->count(),
                    'results_with_prefs_count' => $resultsWithPrefs->count(),
                    'user_preferences_count' => $userPreferences ? $userPreferences->count() : 0,
                    'results_from_input_count' => $resultsFromInput->count(),
                ],
                'sample_data' => [
                    'input_user' => [
                        'id_input' => $inputUser->id_input,
                        'criterion' => $inputUser->kriteria->nama ?? 'N/A',
                        'value' => $inputUser->value,
                        'results_count' => $inputUser->hasilInputUser->count()
                    ],
                    'sample_result' => $sampleResult ? [
                        'id_hasil_input' => $sampleResult->id_hasil_input,
                        'laptop' => $sampleResult->laptop->merek . ' ' . $sampleResult->laptop->model,
                        'ranking' => $sampleResult->ranking,
                        'user_preferences_for_this_result' => $userPreferences ? $userPreferences->count() : 0
                    ] : null
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Test failed: ' . $e->getMessage()], 500);
        }
    }
}
