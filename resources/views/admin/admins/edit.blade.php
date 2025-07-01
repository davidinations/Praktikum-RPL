@extends('layouts.admin')

@section('title', 'Edit Admin - Laptop Store')

@section('admin-content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="bi bi-pencil"></i> Edit Admin: {{ $admin->username_admin }}</h4>
        <a href="{{ route('admin.admins.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.admins.update', $admin->id_admin) }}">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="username_admin" class="form-label">Username *</label>
                            <input type="text" class="form-control" id="username_admin" name="username_admin" value="{{ old('username_admin', $admin->username_admin) }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="password" class="form-label">New Password (leave blank to keep current)</label>
                            <input type="password" class="form-control" id="password" name="password">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-end">
                    <a href="{{ route('admin.admins.index') }}" class="btn btn-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-save"></i> Update Admin
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
