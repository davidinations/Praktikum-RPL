@extends('layouts.admin')

@section('title', 'Manage Admins - Laptop Store')

@section('admin-content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="bi bi-person-gear"></i> Manage Admins</h4>
        <a href="{{ route('admin.admins.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Add New Admin
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            @if($admins->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($admins as $admin)
                                <tr>
                                    <td>{{ $admin->id_admin }}</td>
                                    <td>{{ $admin->username_admin }}</td>
                                    <td>{{ $admin->created_at->format('Y-m-d H:i:s') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.admins.show', $admin->id_admin) }}" class="btn btn-info btn-sm">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.admins.edit', $admin->id_admin) }}" class="btn btn-warning btn-sm">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            @if($admin->id_admin != session('admin_id'))
                                                <form method="POST" action="{{ route('admin.admins.destroy', $admin->id_admin) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this admin?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                {{ $admins->links() }}
            @else
                <div class="text-center py-4">
                    <i class="bi bi-person-gear" style="font-size: 3rem; color: #ccc;"></i>
                    <p class="mt-3 text-muted">No admins found.</p>
                    <a href="{{ route('admin.admins.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Add First Admin
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection
