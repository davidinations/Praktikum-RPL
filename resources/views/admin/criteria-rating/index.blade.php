@extends('layouts.admin')

@section('title', 'Manage Criteria Ratings')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Criteria Rating Management</h3>
                    <p class="card-subtitle">Configure rating ranges for each criterion</p>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Criterion Name</th>
                                    <th>Unit</th>
                                    <th>Type</th>
                                    <th>Weight (%)</th>
                                    <th>Rating Ranges</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($criteria as $index => $criterion)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $criterion->nama }}</strong>
                                    </td>
                                    <td>{{ $criterion->satuan }}</td>
                                    <td>
                                        <span class="badge {{ $criterion->jenis == 'cost' ? 'bg-danger' : 'bg-success' }}">
                                            {{ ucfirst($criterion->jenis) }}
                                        </span>
                                    </td>
                                    <td>{{ $criterion->bobot }}%</td>
                                    <td>
                                        <small class="text-muted">
                                            @if($criterion->rating_1_min !== null)
                                                <div>★☆☆☆☆: {{ $criterion->rating_1_min }} - {{ $criterion->rating_1_max }}</div>
                                                <div>★★☆☆☆: {{ $criterion->rating_2_min }} - {{ $criterion->rating_2_max }}</div>
                                                <div>★★★☆☆: {{ $criterion->rating_3_min }} - {{ $criterion->rating_3_max }}</div>
                                                <div>★★★★☆: {{ $criterion->rating_4_min }} - {{ $criterion->rating_4_max }}</div>
                                                <div>★★★★★: {{ $criterion->rating_5_min }} - {{ $criterion->rating_5_max }}</div>
                                            @else
                                                <span class="text-warning">Not configured</span>
                                            @endif
                                        </small>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.criteria-rating.edit', $criterion->id_kriteria) }}" 
                                           class="btn btn-primary btn-sm">
                                            <i class="fas fa-edit"></i> Edit Ratings
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">
                                        No criteria found. Please add criteria first.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Rating Test Modal -->
<div class="modal fade" id="testRatingModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Test Rating Calculation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="testRatingForm">
                    <div class="mb-3">
                        <label for="test_criterion" class="form-label">Select Criterion</label>
                        <select class="form-select" id="test_criterion" name="criterion_id" required>
                            <option value="">Choose criterion...</option>
                            @foreach($criteria as $criterion)
                                <option value="{{ $criterion->id_kriteria }}">{{ $criterion->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="test_value" class="form-label">Test Value</label>
                        <input type="number" class="form-control" id="test_value" name="value" step="0.001" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Calculate Rating</button>
                </form>
                <div id="ratingResult" class="mt-3" style="display: none;">
                    <div class="alert alert-info">
                        <h6>Result:</h6>
                        <p id="ratingText"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Test rating calculation
    $('#testRatingForm').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '{{ route("admin.criteria-rating.get-rating") }}',
            method: 'POST',
            data: $(this).serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#ratingText').html(
                    '<strong>Criterion:</strong> ' + response.criterion + '<br>' +
                    '<strong>Rating:</strong> ' + '★'.repeat(response.rating) + '☆'.repeat(5-response.rating) + ' (' + response.rating + '/5)<br>' +
                    '<strong>Description:</strong> ' + response.description
                );
                $('#ratingResult').show();
            },
            error: function(xhr) {
                alert('Error: ' + xhr.responseJSON.error);
            }
        });
    });
});
</script>
@endsection
