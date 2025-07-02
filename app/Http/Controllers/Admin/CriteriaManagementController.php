<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasterKriteria;
use Illuminate\Http\Request;

class CriteriaManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!session('admin_id') || session('user_type') !== 'admin') {
                return redirect()->route('login')->with('error', 'Please login as admin to access this page.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $criteria = MasterKriteria::with('admin')->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.criteria.index', compact('criteria'));
    }

    public function create()
    {
        return view('admin.criteria.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'satuan' => 'required|string|max:255',
            'jenis' => 'required|string|in:cost,benefit',
            'bobot' => 'required|numeric|min:0|max:100',
            // Rating range validations
            'rating_1_min' => 'nullable|numeric',
            'rating_1_max' => 'nullable|numeric|gte:rating_1_min',
            'rating_2_min' => 'nullable|numeric|gt:rating_1_max',
            'rating_2_max' => 'nullable|numeric|gte:rating_2_min',
            'rating_3_min' => 'nullable|numeric|gt:rating_2_max',
            'rating_3_max' => 'nullable|numeric|gte:rating_3_min',
            'rating_4_min' => 'nullable|numeric|gt:rating_3_max',
            'rating_4_max' => 'nullable|numeric|gte:rating_4_min',
            'rating_5_min' => 'nullable|numeric|gt:rating_4_max',
            'rating_5_max' => 'nullable|numeric|gte:rating_5_min',
        ]);

        try {
            $data = [
                'id_admin' => session('admin_id'),
                'nama' => $request->nama,
                'satuan' => $request->satuan,
                'jenis' => $request->jenis,
                'bobot' => $request->bobot,
            ];

            // Add rating ranges if provided
            for ($i = 1; $i <= 5; $i++) {
                $minField = "rating_{$i}_min";
                $maxField = "rating_{$i}_max";

                if ($request->filled($minField)) {
                    $data[$minField] = $request->$minField;
                }
                if ($request->filled($maxField)) {
                    $data[$maxField] = $request->$maxField;
                }
            }

            MasterKriteria::create($data);

            return redirect()->route('admin.criteria.index')->with('success', 'Criteria created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create criteria: ' . $e->getMessage())->withInput();
        }
    }

    public function show(MasterKriteria $criteria)
    {
        return view('admin.criteria.show', compact('criteria'));
    }

    public function edit(MasterKriteria $criteria)
    {
        return view('admin.criteria.edit', compact('criteria'));
    }

    public function update(Request $request, MasterKriteria $criteria)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'satuan' => 'required|string|max:255',
            'jenis' => 'required|string|in:cost,benefit',
            'bobot' => 'required|numeric|min:0|max:100',
            // Rating range validations
            'rating_1_min' => 'nullable|numeric',
            'rating_1_max' => 'nullable|numeric|gte:rating_1_min',
            'rating_2_min' => 'nullable|numeric|gt:rating_1_max',
            'rating_2_max' => 'nullable|numeric|gte:rating_2_min',
            'rating_3_min' => 'nullable|numeric|gt:rating_2_max',
            'rating_3_max' => 'nullable|numeric|gte:rating_3_min',
            'rating_4_min' => 'nullable|numeric|gt:rating_3_max',
            'rating_4_max' => 'nullable|numeric|gte:rating_4_min',
            'rating_5_min' => 'nullable|numeric|gt:rating_4_max',
            'rating_5_max' => 'nullable|numeric|gte:rating_5_min',
        ]);

        try {
            $data = [
                'nama' => $request->nama,
                'satuan' => $request->satuan,
                'jenis' => $request->jenis,
                'bobot' => $request->bobot,
            ];

            // Add rating ranges if provided
            for ($i = 1; $i <= 5; $i++) {
                $minField = "rating_{$i}_min";
                $maxField = "rating_{$i}_max";

                if ($request->filled($minField)) {
                    $data[$minField] = $request->$minField;
                }
                if ($request->filled($maxField)) {
                    $data[$maxField] = $request->$maxField;
                }
            }

            $criteria->update($data);

            return redirect()->route('admin.criteria.index')->with('success', 'Criteria updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update criteria: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(MasterKriteria $criteria)
    {
        try {
            $criteria->delete();
            return redirect()->route('admin.criteria.index')->with('success', 'Criteria deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete criteria: ' . $e->getMessage());
        }
    }
}
