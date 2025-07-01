<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
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
        $stats = [
            'total_admins' => \App\Models\Admin::count(),
            'total_users' => \App\Models\User::count(),
            'total_laptops' => \App\Models\MasterLaptop::count(),
            'total_criteria' => \App\Models\MasterKriteria::count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
