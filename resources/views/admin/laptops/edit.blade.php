@extends('layouts.admin')

@section('title', 'Edit Laptop - Laptop Store')

@section('admin-content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="bi bi-pencil"></i> Edit Laptop</h4>
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

            <form method="POST" action="{{ route('admin.laptops.update', $laptop->id_laptop) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="merek" class="form-label">Brand *</label>
                            <input type="text" class="form-control" id="merek" name="merek" value="{{ old('merek', $laptop->merek) }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="model" class="form-label">Model *</label>
                            <input type="text" class="form-control" id="model" name="model" value="{{ old('model', $laptop->model) }}" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="harga" class="form-label">Price (Rp) *</label>
                            <input type="number" class="form-control" id="harga" name="harga" value="{{ old('harga', $laptop->harga) }}" min="0" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="processor" class="form-label">Processor *</label>
                            <input type="text" class="form-control" id="processor" name="processor" value="{{ old('processor', $laptop->processor) }}" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="ram" class="form-label">RAM (GB) *</label>
                            <input type="number" class="form-control" id="ram" name="ram" value="{{ old('ram', $laptop->ram) }}" min="1" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="storage" class="form-label">Storage (GB) *</label>
                            <input type="number" class="form-control" id="storage" name="storage" value="{{ old('storage', $laptop->storage) }}" min="1" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="ukuran_baterai" class="form-label">Battery (mAh) *</label>
                            <input type="number" class="form-control" id="ukuran_baterai" name="ukuran_baterai" value="{{ old('ukuran_baterai', $laptop->ukuran_baterai) }}" min="1" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="gpu" class="form-label">GPU *</label>
                            <input type="text" class="form-control" id="gpu" name="gpu" value="{{ old('gpu', $laptop->gpu) }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="gambar" class="form-label">Laptop Image</label>
                            <input type="file" class="form-control" id="gambar" name="gambar" accept="image/jpeg,image/jpg,image/png">
                            <div class="form-text">Upload JPG or PNG image file (max 2MB) - leave empty to keep current image</div>
                            @if($laptop->gambar)
                                <div class="mt-2">
                                    <small class="text-muted">Current image:</small><br>
                                    <img src="{{ asset('storage/laptops/' . $laptop->gambar) }}" alt="Current image" class="img-thumbnail" style="max-width: 150px;">
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-end">
                    <a href="{{ route('admin.laptops.index') }}" class="btn btn-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Update Laptop
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
