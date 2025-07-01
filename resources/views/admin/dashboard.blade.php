@extends('layouts.admin')

@section('title', 'Admin Dashboard - Laptop Store')

@section('admin-content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-speedometer2"></i> Admin Dashboard</h5>
                </div>
                <div class="card-body">
                    <p class="lead">Welcome to the Admin Dashboard!</p>
                    <p>Here you can manage the entire laptop recommendation system:</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Dashboard Cards -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-header"><i class="bi bi-laptop"></i> Total Laptops</div>
                <div class="card-body">
                    <h5 class="card-title">{{ $stats['total_laptops'] }}</h5>
                    <p class="card-text">Laptops in system</p>
                    <a href="{{ route('admin.laptops.index') }}" class="btn btn-outline-light btn-sm">Manage</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-header"><i class="bi bi-people"></i> Total Users</div>
                <div class="card-body">
                    <h5 class="card-title">{{ $stats['total_users'] }}</h5>
                    <p class="card-text">Registered users</p>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-light btn-sm">Manage</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-header"><i class="bi bi-person-gear"></i> Total Admins</div>
                <div class="card-body">
                    <h5 class="card-title">{{ $stats['total_admins'] }}</h5>
                    <p class="card-text">System administrators</p>
                    <a href="{{ route('admin.admins.index') }}" class="btn btn-outline-light btn-sm">Manage</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info">
                <div class="card-header"><i class="bi bi-list-check"></i> Total Criteria</div>
                <div class="card-body">
                    <h5 class="card-title">{{ $stats['total_criteria'] }}</h5>
                    <p class="card-text">Evaluation criteria</p>
                    <a href="{{ route('admin.criteria.index') }}" class="btn btn-outline-light btn-sm">Manage</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-lightning"></i> Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('admin.laptops.create') }}" class="btn btn-primary w-100">
                                <i class="bi bi-plus-circle"></i> Add Laptop
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('admin.users.create') }}" class="btn btn-success w-100">
                                <i class="bi bi-person-plus"></i> Add User
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('admin.admins.create') }}" class="btn btn-warning w-100">
                                <i class="bi bi-person-gear"></i> Add Admin
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('admin.criteria.create') }}" class="btn btn-info w-100">
                                <i class="bi bi-plus-square"></i> Add Criteria
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
