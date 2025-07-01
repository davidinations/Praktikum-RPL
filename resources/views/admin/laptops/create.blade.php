@extends('layouts.admin')

@section('title', 'Add New Laptop - Laptop Store')

@section('admin-content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="bi bi-plus-circle"></i> Add New Laptop</h4>
        <a href="{{ route('admin.laptops.index') }}" class="btn btn-secondary">
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

            <form method="POST" action="{{ route('admin.laptops.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="merek" class="form-label">Brand *</label>
                            <input type="text" class="form-control" id="merek" name="merek" value="{{ old('merek') }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="model" class="form-label">Model *</label>
                            <input type="text" class="form-control" id="model" name="model" value="{{ old('model') }}" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="harga" class="form-label">Price (Rp) *</label>
                            <input type="number" class="form-control" id="harga" name="harga" value="{{ old('harga') }}" min="0" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="processor" class="form-label">Processor *</label>
                            <input type="text" class="form-control" id="processor" name="processor" value="{{ old('processor') }}" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="ram" class="form-label">RAM (GB) *</label>
                            <input type="number" class="form-control" id="ram" name="ram" value="{{ old('ram') }}" min="1" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="storage" class="form-label">Storage (GB) *</label>
                            <input type="number" class="form-control" id="storage" name="storage" value="{{ old('storage') }}" min="1" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="ukuran_baterai" class="form-label">Battery (mAh) *</label>
                            <input type="number" class="form-control" id="ukuran_baterai" name="ukuran_baterai" value="{{ old('ukuran_baterai') }}" min="1" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="gpu" class="form-label">GPU *</label>
                            <input type="text" class="form-control" id="gpu" name="gpu" value="{{ old('gpu') }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="gambar" class="form-label">Laptop Image</label>
                            <input type="file" class="form-control" id="gambar" name="gambar" accept="image/jpeg,image/jpg,image/png">
                            <div class="form-text">Upload JPG or PNG image file (max 2MB)</div>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-end">
                    <a href="{{ route('admin.laptops.index') }}" class="btn btn-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Create Laptop
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
