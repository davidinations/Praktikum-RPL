@extends('layouts.admin')

@section('title', 'Manage Laptops - Laptop Store')

@section('admin-content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="bi bi-laptop"></i> Manage Laptops</h4>
        <a href="{{ route('admin.laptops.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Add New Laptop
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            @if($laptops->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Brand</th>
                                <th>Model</th>
                                <th>Price</th>
                                <th>RAM</th>
                                <th>Storage</th>
                                <th>Created By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($laptops as $laptop)
                                <tr>
                                    <td>{{ $laptop->id_laptop }}</td>
                                    <td>{{ $laptop->merek }}</td>
                                    <td>{{ $laptop->model }}</td>
                                    <td>Rp {{ number_format($laptop->harga, 0, ',', '.') }}</td>
                                    <td>{{ $laptop->ram }} GB</td>
                                    <td>{{ $laptop->storage }} GB</td>
                                    <td>{{ $laptop->admin ? $laptop->admin->username_admin : '-' }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.laptops.show', $laptop->id_laptop) }}" class="btn btn-info btn-sm">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.laptops.edit', $laptop->id_laptop) }}" class="btn btn-warning btn-sm">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form method="POST" action="{{ route('admin.laptops.destroy', $laptop->id_laptop) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this laptop?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                {{ $laptops->links() }}
            @else
                <div class="text-center py-4">
                    <i class="bi bi-laptop" style="font-size: 3rem; color: #ccc;"></i>
                    <p class="mt-3 text-muted">No laptops found.</p>
                    <a href="{{ route('admin.laptops.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Add First Laptop
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection
