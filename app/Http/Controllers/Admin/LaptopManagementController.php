<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasterLaptop;
use Illuminate\Http\Request;

class LaptopManagementController extends Controller
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
        $laptops = MasterLaptop::with('admin')->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.laptops.index', compact('laptops'));
    }

    public function create()
    {
        return view('admin.laptops.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'merek' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'harga' => 'required|integer|min:0',
            'processor' => 'required|string|max:255',
            'ram' => 'required|integer|min:1',
            'storage' => 'required|integer|min:1',
            'gpu' => 'required|string|max:255',
            'ukuran_baterai' => 'required|integer|min:1',
            'gambar' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        try {
            $imageName = null;

            // Handle file upload
            if ($request->hasFile('gambar')) {
                $image = $request->file('gambar');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->storeAs('public/laptops', $imageName);
            }

            MasterLaptop::create([
                'id_admin' => session('admin_id'),
                'merek' => $request->merek,
                'model' => $request->model,
                'harga' => $request->harga,
                'processor' => $request->processor,
                'ram' => $request->ram,
                'storage' => $request->storage,
                'gpu' => $request->gpu,
                'ukuran_baterai' => $request->ukuran_baterai,
                'gambar' => $imageName,
            ]);

            return redirect()->route('admin.laptops.index')->with('success', 'Laptop created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create laptop: ' . $e->getMessage())->withInput();
        }
    }

    public function show(MasterLaptop $laptop)
    {
        return view('admin.laptops.show', compact('laptop'));
    }

    public function edit(MasterLaptop $laptop)
    {
        return view('admin.laptops.edit', compact('laptop'));
    }

    public function update(Request $request, MasterLaptop $laptop)
    {
        $request->validate([
            'merek' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'harga' => 'required|integer|min:0',
            'processor' => 'required|string|max:255',
            'ram' => 'required|integer|min:1',
            'storage' => 'required|integer|min:1',
            'gpu' => 'required|string|max:255',
            'ukuran_baterai' => 'required|integer|min:1',
            'gambar' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        try {
            $updateData = [
                'merek' => $request->merek,
                'model' => $request->model,
                'harga' => $request->harga,
                'processor' => $request->processor,
                'ram' => $request->ram,
                'storage' => $request->storage,
                'gpu' => $request->gpu,
                'ukuran_baterai' => $request->ukuran_baterai,
            ];

            // Handle file upload
            if ($request->hasFile('gambar')) {
                // Delete old image if exists
                if ($laptop->gambar && \Storage::exists('public/laptops/' . $laptop->gambar)) {
                    \Storage::delete('public/laptops/' . $laptop->gambar);
                }

                $image = $request->file('gambar');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->storeAs('public/laptops', $imageName);
                $updateData['gambar'] = $imageName;
            }

            $laptop->update($updateData);

            return redirect()->route('admin.laptops.index')->with('success', 'Laptop updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update laptop: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(MasterLaptop $laptop)
    {
        try {
            $laptop->delete();
            return redirect()->route('admin.laptops.index')->with('success', 'Laptop deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete laptop: ' . $e->getMessage());
        }
    }
}
