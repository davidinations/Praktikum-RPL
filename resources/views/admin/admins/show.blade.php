@extends('layouts.admin')

@section('title', 'Admin Details - Laptop Store')

@section('admin-content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="bi bi-eye"></i> Admin Details</h4>
        <div>
            <a href="{{ route('admin.admins.edit', $admin->id_admin) }}" class="btn btn-warning me-2">
                <i class="bi bi-pencil"></i> Edit
            </a>
            <a href="{{ route('admin.admins.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th width="30%">ID:</th>
                            <td>{{ $admin->id_admin }}</td>
                        </tr>
                        <tr>
                            <th>Username:</th>
                            <td>{{ $admin->username_admin }}</td>
                        </tr>
                        <tr>
                            <th>Created At:</th>
                            <td>{{ $admin->created_at->format('Y-m-d H:i:s') }}</td>
                        </tr>
                        <tr>
                            <th>Updated At:</th>
                            <td>{{ $admin->updated_at->format('Y-m-d H:i:s') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
