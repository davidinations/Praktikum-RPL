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
            'bobot' => 'required|numeric|min:0|max:1',
        ]);

        try {
            MasterKriteria::create([
                'id_admin' => session('admin_id'),
                'nama' => $request->nama,
                'satuan' => $request->satuan,
                'bobot' => $request->bobot,
            ]);

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
            'bobot' => 'required|numeric|min:0|max:1',
        ]);

        try {
            $criteria->update([
                'nama' => $request->nama,
                'satuan' => $request->satuan,
                'bobot' => $request->bobot,
            ]);

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
