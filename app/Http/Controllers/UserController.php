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

            // Calculate SAW results
            $results = $this->sawService->calculateSAW($idInput, Auth::id());

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

        // Get ranking results
        $results = HasilInputUser::getRankingResults($idInput);

        // Get user preferences for this input
        $userPreferences = InputUser::getInputSession($idInput);

        return view('user.results', compact('results', 'userPreferences', 'idInput'));
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
}
