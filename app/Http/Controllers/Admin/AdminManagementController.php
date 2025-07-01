<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminManagementController extends Controller
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
        $admins = Admin::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.admins.index', compact('admins'));
    }

    public function create()
    {
        return view('admin.admins.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'username_admin' => 'required|string|max:255|unique:admins',
            'password' => 'required|string|min:6|confirmed',
        ]);

        try {
            Admin::create([
                'username_admin' => $request->username_admin,
                'password' => Hash::make($request->password),
            ]);

            return redirect()->route('admin.admins.index')->with('success', 'Admin created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create admin: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Admin $admin)
    {
        return view('admin.admins.show', compact('admin'));
    }

    public function edit(Admin $admin)
    {
        return view('admin.admins.edit', compact('admin'));
    }

    public function update(Request $request, Admin $admin)
    {
        $request->validate([
            'username_admin' => 'required|string|max:255|unique:admins,username_admin,' . $admin->id_admin . ',id_admin',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        try {
            $updateData = [
                'username_admin' => $request->username_admin,
            ];

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $admin->update($updateData);

            return redirect()->route('admin.admins.index')->with('success', 'Admin updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update admin: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Admin $admin)
    {
        try {
            if ($admin->id_admin == session('admin_id')) {
                return redirect()->back()->with('error', 'You cannot delete your own account!');
            }

            $admin->delete();
            return redirect()->route('admin.admins.index')->with('success', 'Admin deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete admin: ' . $e->getMessage());
        }
    }
}
