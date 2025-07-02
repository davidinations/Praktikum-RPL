@extends('layouts.admin')

@section('title', 'Edit Criteria - Laptop Store')

@section('admin-content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="bi bi-pencil"></i> Edit Criteria</h4>
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

            <form method="POST" action="{{ route('admin.criteria.update', $criteria->id_kriteria) }}">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nama" class="form-label">Criteria Name *</label>
                            <input type="text" class="form-control" id="nama" name="nama"
                                value="{{ old('nama', $criteria->nama) }}" required>
                            <div class="form-text">e.g., Performance, Battery Life, Price</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="satuan" class="form-label">Unit *</label>
                            <input type="text" class="form-control" id="satuan" name="satuan"
                                value="{{ old('satuan', $criteria->satuan) }}" required>
                            <div class="form-text">e.g., Score, Hours, Rupiah</div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="jenis" class="form-label">Type *</label>
                            <select class="form-select" id="jenis" name="jenis" required>
                                <option value="" disabled>Select Type</option>
                                <option value="benefit" {{ old('jenis', $criteria->jenis) == 'benefit' ? 'selected' : '' }}>
                                    Benefit</option>
                                <option value="cost" {{ old('jenis', $criteria->jenis) == 'cost' ? 'selected' : '' }}>Cost
                                </option>
                            </select>
                            <div class="form-text">Select whether this criteria is a benefit or cost type</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="bobot" class="form-label">Weight (%) *</label>
                            <input type="number" class="form-control" id="bobot" name="bobot"
                                value="{{ old('bobot', $criteria->bobot) }}" min="0" max="100" step="0.1"
                                required>
                            <div class="form-text">Weight percentage (e.g., 35 for 35%)</div>
                        </div>
                    </div>
                </div>

                <!-- Rating Ranges Section -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">Rating Ranges</h5>
                        <small class="text-muted">Define value ranges for each rating level (1-5 stars)</small>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @for($i = 1; $i <= 5; $i++)
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <strong>Rating {{ $i }}</strong>
                                    @for($j = 1; $j <= 5; $j++)
                                        @if($j <= $i)
                                            <span class="text-warning">★</span>
                                        @else
                                            <span class="text-muted">☆</span>
                                        @endif
                                    @endfor
                                </label>
                                <div class="row">
                                    <div class="col-6">
                                        <input type="number" class="form-control" 
                                               name="rating_{{ $i }}_min" 
                                               placeholder="Min value" 
                                               step="0.001"
                                               value="{{ old('rating_' . $i . '_min', $criteria->{'rating_' . $i . '_min'}) }}">
                                    </div>
                                    <div class="col-6">
                                        <input type="number" class="form-control" 
                                               name="rating_{{ $i }}_max" 
                                               placeholder="Max value" 
                                               step="0.001"
                                               value="{{ old('rating_' . $i . '_max', $criteria->{'rating_' . $i . '_max'}) }}">
                                    </div>
                                </div>
                            </div>
                            @endfor
                        </div>
                        <div class="alert alert-info mt-3">
                            <small>
                                <strong>Note:</strong> For benefit criteria (higher is better), Rating 5 should have the highest values.
                                For cost criteria (lower is better), Rating 5 should have the lowest values.
                            </small>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('admin.criteria.index') }}" class="btn btn-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Update Criteria
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
