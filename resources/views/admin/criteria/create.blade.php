@extends('layouts.admin')

@section('title', 'Add New Criteria - Laptop Store')

@section('admin-content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="bi bi-plus-circle"></i> Add New Criteria</h4>
        <a href="{{ route('admin.criteria.index') }}" class="btn btn-secondary">
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

            <form method="POST" action="{{ route('admin.criteria.store') }}">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nama" class="form-label">Criteria Name *</label>
                            <input type="text" class="form-control" id="nama" name="nama"
                                value="{{ old('nama') }}" required>
                            <div class="form-text">e.g., Performance, Battery Life, Price</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="satuan" class="form-label">Unit *</label>
                            <input type="text" class="form-control" id="satuan" name="satuan"
                                value="{{ old('satuan') }}" required>
                            <div class="form-text">e.g., Score, Hours, Rupiah</div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="jenis" class="form-label">Type *</label>
                            <select class="form-select" id="jenis" name="jenis" required>
                                <option value="" disabled selected>Select Type</option>
                                <option value="benefit">Benefit</option>
                                <option value="cost">Cost</option>
                            </select>
                            <div class="form-text">Select whether this criteria is a benefit or cost type</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="bobot" class="form-label">Weight *</label>
                            <input type="number" class="form-control" id="bobot" name="bobot"
                                value="{{ old('bobot') }}" min="0" max="1" step="0.1" required>
                            <div class="form-text">Weight between 0 and 1 (e.g., 0.3 for 30%)</div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('admin.criteria.index') }}" class="btn btn-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Create Criteria
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
