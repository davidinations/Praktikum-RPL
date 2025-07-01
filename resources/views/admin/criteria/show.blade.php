@extends('layouts.admin')

@section('title', 'Criteria Details - Laptop Store')

@section('admin-content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="bi bi-eye"></i> Criteria Details</h4>
        <div>
            <a href="{{ route('admin.criteria.edit', $criteria->id_kriteria) }}" class="btn btn-warning me-2">
                <i class="bi bi-pencil"></i> Edit
            </a>
            <a href="{{ route('admin.criteria.index') }}" class="btn btn-secondary">
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
                            <td>{{ $criteria->id_kriteria }}</td>
                        </tr>
                        <tr>
                            <th>Name:</th>
                            <td>{{ $criteria->nama }}</td>
                        </tr>
                        <tr>
                            <th>Unit:</th>
                            <td>{{ $criteria->satuan }}</td>
                        </tr>
                        <tr>
                            <th>Weight:</th>
                            <td>{{ $criteria->bobot }} ({{ $criteria->bobot * 100 }}%)</td>
                        </tr>
                        <tr>
                            <th>Created By:</th>
                            <td>{{ $criteria->admin ? $criteria->admin->username_admin : '-' }}</td>
                        </tr>
                        <tr>
                            <th>Created At:</th>
                            <td>{{ $criteria->created_at->format('Y-m-d H:i:s') }}</td>
                        </tr>
                        <tr>
                            <th>Updated At:</th>
                            <td>{{ $criteria->updated_at->format('Y-m-d H:i:s') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
