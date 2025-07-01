@extends('layouts.app')

@section('header-class', 'bg-dark')
@section('header-title', 'Laptop Store - Admin Dashboard')

@section('header-links')
    <div class="d-flex align-items-center">
        <div class="dropdown me-3">
            <button class="btn btn-outline-light dropdown-toggle" type="button" id="adminDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-gear"></i> Management
            </button>
            <ul class="dropdown-menu" aria-labelledby="adminDropdown">
                <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="{{ route('admin.admins.index') }}"><i class="bi bi-person-gear"></i> Manage Admins</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.users.index') }}"><i class="bi bi-people"></i> Manage Users</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.laptops.index') }}"><i class="bi bi-laptop"></i> Manage Laptops</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.criteria.index') }}"><i class="bi bi-list-check"></i> Manage Criteria</a></li>
            </ul>
        </div>
        <span class="text-light me-3">Welcome, {{ session('admin_username') }}!</span>
        <form method="POST" action="{{ route('logout') }}" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-outline-light btn-sm">
                <i class="bi bi-box-arrow-right"></i> Logout
            </button>
        </form>
    </div>
@endsection

@section('footer-text', 'Laptop Store Admin Panel. All rights reserved.')

@section('content')
    <div class="container mt-4">
        @yield('admin-content')
    </div>
@endsection
