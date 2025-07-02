@extends('layouts.admin')

@section('title', 'Manage Criteria - Laptop Store')

@section('admin-content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="bi bi-list-check"></i> Manage Criteria</h4>
        <a href="{{ route('admin.criteria.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Add New Criteria
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            @if ($criteria->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Unit</th>
                                <th>Type</th>
                                <th>Weight</th>
                                <th>Created By</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($criteria as $criterium)
                                <tr>
                                    <td>{{ $criterium->id_kriteria }}</td>
                                    <td>{{ $criterium->nama }}</td>
                                    <td>{{ $criterium->satuan }}</td>
                                    <td>{{ $criterium->jenis }}</td>
                                    <td>{{ $criterium->bobot }}</td>
                                    <td>{{ $criterium->admin ? $criterium->admin->username_admin : '-' }}</td>
                                    <td>{{ $criterium->created_at->format('Y-m-d H:i:s') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.criteria.show', $criterium->id_kriteria) }}"
                                                class="btn btn-info btn-sm">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.criteria.edit', $criterium->id_kriteria) }}"
                                                class="btn btn-warning btn-sm">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form method="POST"
                                                action="{{ route('admin.criteria.destroy', $criterium->id_kriteria) }}"
                                                class="d-inline"
                                                onsubmit="return confirm('Are you sure you want to delete this criteria?')">
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

                {{ $criteria->links() }}
            @else
                <div class="text-center py-4">
                    <i class="bi bi-list-check" style="font-size: 3rem; color: #ccc;"></i>
                    <p class="mt-3 text-muted">No criteria found.</p>
                    <a href="{{ route('admin.criteria.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Add First Criteria
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection
