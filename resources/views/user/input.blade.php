<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Preferences - Laptop Recommendation System</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .rating-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
        }
        .rating-stars {
            display: flex;
            gap: 5px;
        }
        .star {
            font-size: 1.5rem;
            color: #ddd;
            cursor: pointer;
            transition: color 0.2s;
        }
        .star.active, .star:hover {
            color: #ffc107;
        }
        .importance-label {
            font-size: 0.9rem;
            color: #666;
        }
    </style>
</head>

<body class="bg-light">
    <!-- Header -->
    <header class="bg-primary text-white py-3">
        <div class="container d-flex justify-content-between align-items-center">
            <h1 class="h4"><i class="fas fa-laptop"></i> Laptop Recommendation System</h1>
            <div>
                <a href="{{ route('user.dashboard') }}" class="btn btn-outline-light btn-sm me-2">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container mt-4">
        <!-- Alert Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-sliders-h"></i> Input Your Preferences</h5>
                        <small>Rate the importance of each criteria for your ideal laptop (1 = Not Important, 5 = Very Important)</small>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('user.input.submit') }}" id="preferencesForm">
                            @csrf

                            @if($criteria->count() > 0)
                                @foreach($criteria as $criterion)
                                <div class="mb-4 p-3 border rounded">
                                    <div class="row align-items-center">
                                        <div class="col-md-4">
                                            <h6 class="mb-1">{{ $criterion->nama }}</h6>
                                            <small class="text-muted">
                                                @if($criterion->satuan)
                                                    Unit: {{ $criterion->satuan }}
                                                @endif
                                                @if($criterion->bobot)
                                                    | Weight: {{ $criterion->bobot }}%
                                                @endif
                                            </small>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="rating-container">
                                                <div class="importance-label">
                                                    <small>Not Important</small>
                                                </div>
                                                <div class="rating-stars" data-criterion="{{ $criterion->id_kriteria }}">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <i class="fas fa-star star" data-rating="{{ $i }}"></i>
                                                    @endfor
                                                </div>
                                                <div class="importance-label">
                                                    <small>Very Important</small>
                                                </div>
                                            </div>
                                            <input type="hidden" 
                                                   name="preferences[{{ $criterion->id_kriteria }}]" 
                                                   id="preference_{{ $criterion->id_kriteria }}" 
                                                   value="" 
                                                   required>
                                            <div class="text-center mt-2">
                                                <small class="rating-text text-muted" id="text_{{ $criterion->id_kriteria }}">
                                                    Click stars to rate
                                                </small>
                                            </div>
                                            @error("preferences.{$criterion->id_kriteria}")
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                @endforeach

                                <!-- Instructions -->
                                <div class="alert alert-info">
                                    <h6><i class="fas fa-info-circle"></i> How it works:</h6>
                                    <ul class="mb-0">
                                        <li><strong>Rate each criterion</strong> based on how important it is for your ideal laptop</li>
                                        <li><strong>1 star</strong> = Not important at all</li>
                                        <li><strong>5 stars</strong> = Extremely important</li>
                                        <li>Our system will use these preferences to recommend the best laptops for you</li>
                                    </ul>
                                </div>

                                <!-- Submit Buttons -->
                                <div class="d-flex justify-content-between mt-4">
                                    <a href="{{ route('user.dashboard') }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary btn-lg" id="submitBtn" disabled>
                                        <i class="fas fa-magic"></i> Get Recommendations
                                    </button>
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    <h6><i class="fas fa-exclamation-triangle"></i> No Criteria Available</h6>
                                    <p class="mb-0">Please contact administrator to set up the criteria for laptop evaluation.</p>
                                </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ratingTexts = ['', 'Not Important', 'Slightly Important', 'Moderately Important', 'Important', 'Very Important'];
            const requiredCriteria = {{ $criteria->count() }};
            let ratedCriteria = 0;

            // Handle star ratings
            document.querySelectorAll('.rating-stars').forEach(function(container) {
                const criterionId = container.dataset.criterion;
                const stars = container.querySelectorAll('.star');
                const hiddenInput = document.getElementById('preference_' + criterionId);
                const textElement = document.getElementById('text_' + criterionId);

                stars.forEach(function(star, index) {
                    star.addEventListener('click', function() {
                        const rating = parseInt(star.dataset.rating);
                        
                        // Update hidden input
                        const oldValue = hiddenInput.value;
                        hiddenInput.value = rating;
                        
                        // Update star display
                        stars.forEach(function(s, i) {
                            if (i < rating) {
                                s.classList.add('active');
                            } else {
                                s.classList.remove('active');
                            }
                        });

                        // Update text
                        textElement.textContent = ratingTexts[rating];
                        textElement.className = 'rating-text text-primary fw-bold';

                        // Update counter
                        if (oldValue === '' && rating > 0) {
                            ratedCriteria++;
                        } else if (oldValue !== '' && rating === 0) {
                            ratedCriteria--;
                        }

                        // Enable/disable submit button
                        updateSubmitButton();
                    });

                    // Hover effect
                    star.addEventListener('mouseenter', function() {
                        const rating = parseInt(star.dataset.rating);
                        stars.forEach(function(s, i) {
                            if (i < rating) {
                                s.style.color = '#ffc107';
                            } else {
                                s.style.color = '#ddd';
                            }
                        });
                    });
                });

                // Reset hover effect
                container.addEventListener('mouseleave', function() {
                    const currentRating = parseInt(hiddenInput.value) || 0;
                    stars.forEach(function(s, i) {
                        if (i < currentRating) {
                            s.style.color = '#ffc107';
                        } else {
                            s.style.color = '#ddd';
                        }
                    });
                });
            });

            function updateSubmitButton() {
                const submitBtn = document.getElementById('submitBtn');
                if (ratedCriteria === requiredCriteria) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-magic"></i> Get Recommendations';
                } else {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = `<i class="fas fa-magic"></i> Rate All Criteria (${ratedCriteria}/${requiredCriteria})`;
                }
            }

            // Form validation
            document.getElementById('preferencesForm').addEventListener('submit', function(e) {
                const unratedCriteria = [];
                document.querySelectorAll('input[name^="preferences"]').forEach(function(input) {
                    if (!input.value) {
                        unratedCriteria.push(input.name);
                    }
                });

                if (unratedCriteria.length > 0) {
                    e.preventDefault();
                    alert('Please rate all criteria before submitting.');
                }
            });
        });
    </script>
</body>

</html>
