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
                            <th>Type:</th>
                            <td>
                                <span class="badge {{ $criteria->jenis == 'cost' ? 'bg-danger' : 'bg-success' }}">
                                    {{ ucfirst($criteria->jenis) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Weight:</th>
                            <td>{{ $criteria->bobot }}%</td>
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
                <div class="col-md-6">
                    <h6>Rating Ranges</h6>
                    @if($criteria->rating_1_min !== null)
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Rating</th>
                                        <th>Range</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @for($i = 1; $i <= 5; $i++)
                                        @php
                                            $minField = 'rating_' . $i . '_min';
                                            $maxField = 'rating_' . $i . '_max';
                                        @endphp
                                        <tr>
                                            <td>
                                                @for($j = 1; $j <= 5; $j++)
                                                    @if($j <= $i)
                                                        <span class="text-warning">★</span>
                                                    @else
                                                        <span class="text-muted">☆</span>
                                                    @endif
                                                @endfor
                                            </td>
                                            <td>
                                                {{ $criteria->$minField ?? 'Not set' }} - {{ $criteria->$maxField ?? 'Not set' }}
                                                {{ $criteria->satuan }}
                                            </td>
                                            <td>
                                                @switch($i)
                                                    @case(1) Very Poor @break
                                                    @case(2) Poor @break
                                                    @case(3) Fair @break
                                                    @case(4) Good @break
                                                    @case(5) Excellent @break
                                                @endswitch
                                            </td>
                                        </tr>
                                    @endfor
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i> Rating ranges not configured yet.
                            <a href="{{ route('admin.criteria.edit', $criteria->id_kriteria) }}" class="alert-link">
                                Click here to configure them.
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Test Rating Calculator -->
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0">Test Rating Calculator</h5>
            <small class="text-muted">Enter a value to see which rating it would receive</small>
        </div>
        <div class="card-body">
            <div class="row align-items-end">
                <div class="col-md-4">
                    <label for="test_value" class="form-label">Test Value</label>
                    <input type="number" class="form-control" id="test_value" placeholder="Enter value" step="0.001">
                </div>
                <div class="col-md-4">
                    <button type="button" class="btn btn-primary" onclick="calculateRating()">
                        <i class="bi bi-calculator"></i> Calculate Rating
                    </button>
                </div>
                <div class="col-md-4">
                    <div id="rating_result" style="display: none;">
                        <div class="alert alert-info mb-0">
                            <div id="rating_display"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
function calculateRating() {
    const testValue = parseFloat(document.getElementById('test_value').value);
    
    if (isNaN(testValue)) {
        alert('Please enter a valid number');
        return;
    }
    
    const ranges = [
        @for($i = 1; $i <= 5; $i++)
        {
            rating: {{ $i }},
            min: {{ $criteria->{'rating_' . $i . '_min'} ?? 'null' }},
            max: {{ $criteria->{'rating_' . $i . '_max'} ?? 'null' }}
        },
        @endfor
    ];
    
    let foundRating = null;
    
    for (let range of ranges) {
        if (range.min !== null && range.max !== null) {
            if (testValue >= range.min && testValue <= range.max) {
                foundRating = range.rating;
                break;
            }
        }
    }
    
    const resultDiv = document.getElementById('rating_result');
    const displayDiv = document.getElementById('rating_display');
    
    if (foundRating) {
        let stars = '';
        for (let i = 1; i <= 5; i++) {
            stars += i <= foundRating ? '★' : '☆';
        }
        
        const descriptions = {
            1: 'Very Poor',
            2: 'Poor', 
            3: 'Fair',
            4: 'Good',
            5: 'Excellent'
        };
        
        displayDiv.innerHTML = `
            <strong>Rating:</strong> ${stars} (${foundRating}/5)<br>
            <strong>Description:</strong> ${descriptions[foundRating]}
        `;
    } else {
        displayDiv.innerHTML = `
            <strong>No rating found</strong><br>
            <small>Value ${testValue} doesn't fall within any configured range</small>
        `;
    }
    
    resultDiv.style.display = 'block';
}
</script>
@endsection
