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
        .price-input-container {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border: 2px solid #e9ecef;
        }
        .price-input {
            font-size: 1.1rem;
            font-weight: 500;
        }
        .price-rating-text {
            font-weight: 500;
            font-size: 0.95rem;
        }
        .price-rating-text.rated {
            color: #28a745 !important;
        }
        .rating-ranges {
            font-size: 0.85rem;
            line-height: 1.4;
        }
        .rating-ranges strong {
            color: #495057;
        }
        .individual-rating {
            display: flex;
            align-items: center;
            padding: 8px 12px;
            margin: 5px 0;
            background: #f8f9fa;
            border-radius: 6px;
            border-left: 4px solid #ffc107;
        }
        .individual-rating .stars {
            margin-right: 10px;
            color: #ffc107;
        }
        .individual-rating .range-text {
            font-weight: 500;
            color: #495057;
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
                                            @if(strtolower($criterion->nama) === 'harga' || strtolower($criterion->nama) === 'price')
                                                <!-- Price Input with Min/Max Budget -->
                                                <div class="price-input-container">
                                                    <div class="mb-3">
                                                        <label class="form-label">
                                                            <strong>Budget Range (in Rupiah)</strong>
                                                        </label>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <label class="form-label small">Minimum Budget</label>
                                                                <div class="input-group">
                                                                    <span class="input-group-text">Rp</span>
                                                                    <input type="text" 
                                                                           class="form-control price-input-min" 
                                                                           name="price_min_{{ $criterion->id_kriteria }}"
                                                                           id="price_min_{{ $criterion->id_kriteria }}"
                                                                           placeholder="e.g., 5000000"
                                                                           data-criterion="{{ $criterion->id_kriteria }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label small">Maximum Budget</label>
                                                                <div class="input-group">
                                                                    <span class="input-group-text">Rp</span>
                                                                    <input type="text" 
                                                                           class="form-control price-input-max" 
                                                                           name="price_max_{{ $criterion->id_kriteria }}"
                                                                           id="price_max_{{ $criterion->id_kriteria }}"
                                                                           placeholder="e.g., 15000000"
                                                                           data-criterion="{{ $criterion->id_kriteria }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="mt-3">
                                                            <div class="alert alert-info" id="rating_info_{{ $criterion->id_kriteria }}" style="display: none;">
                                                                <strong>Possible Star Ratings for Your Budget:</strong>
                                                                <div id="rating_display_{{ $criterion->id_kriteria }}" class="mt-2">
                                                                    <!-- Individual star ratings will be displayed here -->
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @if($criterion->rating_1_min && $criterion->rating_5_max)
                                                            <div class="mt-2">
                                                                <small class="text-info rating-ranges">
                                                                    <strong>Rating Ranges Reference:</strong><br>
                                                                    ⭐ {{ number_format($criterion->rating_1_min * 1000000, 0, ',', '.') }} - {{ number_format($criterion->rating_1_max * 1000000, 0, ',', '.') }} (Rating 1)<br>
                                                                    ⭐⭐ {{ number_format($criterion->rating_2_min * 1000000, 0, ',', '.') }} - {{ number_format($criterion->rating_2_max * 1000000, 0, ',', '.') }} (Rating 2)<br>
                                                                    ⭐⭐⭐ {{ number_format($criterion->rating_3_min * 1000000, 0, ',', '.') }} - {{ number_format($criterion->rating_3_max * 1000000, 0, ',', '.') }} (Rating 3)<br>
                                                                    ⭐⭐⭐⭐ {{ number_format($criterion->rating_4_min * 1000000, 0, ',', '.') }} - {{ number_format($criterion->rating_4_max * 1000000, 0, ',', '.') }} (Rating 4)<br>
                                                                    ⭐⭐⭐⭐⭐ {{ number_format($criterion->rating_5_min * 1000000, 0, ',', '.') }} - {{ number_format($criterion->rating_5_max * 1000000, 0, ',', '.') }} (Rating 5)
                                                                </small>
                                                            </div>
                                                        @else
                                                            <div class="mt-2">
                                                                <small class="text-warning rating-ranges">
                                                                    <strong>Default Rating Ranges:</strong><br>
                                                                    ⭐ > 25M (Rating 1)<br>
                                                                    ⭐⭐ 20M - 25M (Rating 2)<br>
                                                                    ⭐⭐⭐ 15M - 20M (Rating 3)<br>
                                                                    ⭐⭐⭐⭐ 5M - 15M (Rating 4)<br>
                                                                    ⭐⭐⭐⭐⭐ ≤ 5M (Rating 5)
                                                                </small>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <input type="hidden" 
                                                       name="preferences[{{ $criterion->id_kriteria }}]" 
                                                       id="preference_{{ $criterion->id_kriteria }}" 
                                                       value="" 
                                                       required>
                                            @else
                                                <!-- Star Rating for other criteria -->
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
                                                
                                                <!-- Show rating ranges for this criterion if available -->
                                                @if($criterion->rating_1_min !== null && $criterion->rating_5_max !== null)
                                                    <div class="mt-3 p-2 bg-light rounded">
                                                        <small class="text-info rating-ranges">
                                                            <strong>Rating Ranges ({{ $criterion->satuan }}):</strong><br>
                                                            ⭐ {{ number_format($criterion->rating_1_min, 1) }} - {{ number_format($criterion->rating_1_max, 1) }}<br>
                                                            ⭐⭐ {{ number_format($criterion->rating_2_min, 1) }} - {{ number_format($criterion->rating_2_max, 1) }}<br>
                                                            ⭐⭐⭐ {{ number_format($criterion->rating_3_min, 1) }} - {{ number_format($criterion->rating_3_max, 1) }}<br>
                                                            ⭐⭐⭐⭐ {{ number_format($criterion->rating_4_min, 1) }} - {{ number_format($criterion->rating_4_max, 1) }}<br>
                                                            ⭐⭐⭐⭐⭐ {{ number_format($criterion->rating_5_min, 1) }} - {{ number_format($criterion->rating_5_max, 1) }}
                                                        </small>
                                                    </div>
                                                @else
                                                    <div class="mt-3 p-2 bg-light rounded">
                                                        <small class="text-warning rating-ranges">
                                                            <strong>Note:</strong> Rating ranges not configured for this criterion. 
                                                            Contact administrator to set up value ranges.
                                                        </small>
                                                    </div>
                                                @endif
                                            @endif
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
                                        <li><strong>For Price:</strong> Enter your minimum and maximum budget range in Rupiah. The system will show each possible star rating separately within your budget range</li>
                                        <li><strong>For other criteria:</strong> Rate based on how important each feature is for your ideal laptop</li>
                                        <li><strong>Rating Scale:</strong> 1 star = Not important at all, 5 stars = Extremely important</li>
                                        <li><strong>Value Ranges:</strong> Each criterion shows the actual value ranges corresponding to each star rating</li>
                                        @php
                                            $priceCriterion = $criteria->where('nama', 'Harga')->first();
                                        @endphp
                                        @if($priceCriterion)
                                            <li><strong>Budget Analysis:</strong> Shows each applicable star rating separately within your budget range 
                                                (1⭐={{ $priceCriterion->rating_1_min }}-{{ $priceCriterion->rating_1_max }}M, 
                                                2⭐={{ $priceCriterion->rating_2_min }}-{{ $priceCriterion->rating_2_max }}M, 
                                                3⭐={{ $priceCriterion->rating_3_min }}-{{ $priceCriterion->rating_3_max }}M, 
                                                4⭐={{ $priceCriterion->rating_4_min }}-{{ $priceCriterion->rating_4_max }}M, 
                                                5⭐={{ $priceCriterion->rating_5_min }}M+)
                                            </li>
                                        @endif
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

            // Get criteria data from server
            const criteriaData = @json($criteria->keyBy('id_kriteria'));
            console.log('Criteria data loaded:', criteriaData);

            // Handle price input fields
            document.querySelectorAll('.price-input-min, .price-input-max').forEach(function(input) {
                const criterionId = input.dataset.criterion;
                const isMin = input.classList.contains('price-input-min');
                const otherInput = isMin ? 
                    document.getElementById('price_max_' + criterionId) : 
                    document.getElementById('price_min_' + criterionId);
                const hiddenInput = document.getElementById('preference_' + criterionId);
                const ratingInfo = document.getElementById('rating_info_' + criterionId);
                const ratingText = document.getElementById('rating_text_' + criterionId);

                // Format number input
                input.addEventListener('input', function() {
                    let value = this.value.replace(/[^\d]/g, '');
                    if (value) {
                        // Format with thousand separators
                        value = parseInt(value).toLocaleString('id-ID');
                        this.value = value;
                    }
                    
                    calculatePriceRating();
                });

                input.addEventListener('blur', function() {
                    calculatePriceRating();
                });

                function calculatePriceRating() {
                    const minInput = document.getElementById('price_min_' + criterionId);
                    const maxInput = document.getElementById('price_max_' + criterionId);
                    
                    const minValue = parseInt(minInput.value.replace(/[^\d]/g, '')) || 0;
                    const maxValue = parseInt(maxInput.value.replace(/[^\d]/g, '')) || 0;
                    
                    if (minValue > 0 && maxValue > 0 && minValue <= maxValue) {
                        // Convert to millions for easier comparison
                        const minBudgetMillion = minValue / 1000000;
                        const maxBudgetMillion = maxValue / 1000000;
                        
                        // Get the current criterion data
                        const criterion = criteriaData[criterionId];
                        
                        // Show all possible star ratings for the budget range
                        const possibleRatings = [];
                        
                        // Check each rating range from database
                        for (let rating = 1; rating <= 5; rating++) {
                            const minField = `rating_${rating}_min`;
                            const maxField = `rating_${rating}_max`;
                            
                            if (criterion[minField] !== null && criterion[maxField] !== null) {
                                const ratingMin = parseFloat(criterion[minField]);
                                const ratingMax = parseFloat(criterion[maxField]);
                                
                                // Check if budget range overlaps with this rating range
                                if (minBudgetMillion <= ratingMax && maxBudgetMillion >= ratingMin) {
                                    const stars = '⭐'.repeat(rating);
                                    let rangeText;
                                    
                                    // Format range text
                                    if (rating === 5 && ratingMax >= 100) {
                                        rangeText = `${ratingMin}M+`;
                                    } else {
                                        rangeText = `${ratingMin}-${ratingMax}M`;
                                    }
                                    
                                    possibleRatings.push({
                                        rating: rating, 
                                        stars: stars, 
                                        range: rangeText
                                    });
                                }
                            }
                        }
                        
                        // Use the highest rating as the primary rating for calculation
                        const rating = possibleRatings.length > 0 ? Math.max(...possibleRatings.map(r => r.rating)) : 1;
                        
                        // Update hidden input
                        const oldValue = hiddenInput.value;
                        hiddenInput.value = rating;
                        
                        // Create individual rating displays
                        const ratingDisplay = document.getElementById('rating_display_' + criterionId);
                        ratingDisplay.innerHTML = '';
                        
                        if (possibleRatings.length > 0) {
                            possibleRatings.forEach(ratingObj => {
                                const ratingDiv = document.createElement('div');
                                ratingDiv.className = 'individual-rating';
                                ratingDiv.innerHTML = `
                                    <div class="stars">${ratingObj.stars}</div>
                                    <div class="range-text">Rating ${ratingObj.rating} (${ratingObj.range} Rupiah)</div>
                                `;
                                ratingDisplay.appendChild(ratingDiv);
                            });
                        }
                        
                        // Show rating info
                        ratingInfo.style.display = 'block';
                        ratingInfo.className = 'alert alert-success';
                        
                        // Update counter
                        if (oldValue === '' && rating > 0) {
                            ratedCriteria++;
                        }
                        
                        updateSubmitButton();
                        
                    } else if (minValue > maxValue && minValue > 0 && maxValue > 0) {
                        // Show error for invalid range
                        ratingText.textContent = 'Minimum budget cannot be greater than maximum budget!';
                        ratingInfo.style.display = 'block';
                        ratingInfo.className = 'alert alert-danger';
                        hiddenInput.value = '';
                    } else {
                        // Hide rating info if inputs are incomplete
                        ratingInfo.style.display = 'none';
                        if (hiddenInput.value !== '') {
                            ratedCriteria--;
                            hiddenInput.value = '';
                            updateSubmitButton();
                        }
                    }
                }
            });

            // Handle star ratings (for non-price criteria)
            document.querySelectorAll('.rating-stars').forEach(function(container) {
                // Skip price criteria (they don't have rating-stars anymore)
                if (container.closest('.price-input-container')) {
                    return;
                }

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
                let hasInvalidPriceRange = false;
                let hasIncompletePriceRange = false;
                
                // Check all preference inputs
                document.querySelectorAll('input[name^="preferences"]').forEach(function(input) {
                    if (!input.value) {
                        unratedCriteria.push(input.name);
                    }
                });

                // Check for invalid and incomplete price ranges
                document.querySelectorAll('.price-input-min').forEach(function(minInput) {
                    const criterionId = minInput.dataset.criterion;
                    const maxInput = document.getElementById('price_max_' + criterionId);
                    
                    const minValue = parseInt(minInput.value.replace(/[^\d]/g, '')) || 0;
                    const maxValue = parseInt(maxInput.value.replace(/[^\d]/g, '')) || 0;
                    
                    if (minValue > 0 && maxValue > 0) {
                        if (minValue > maxValue) {
                            hasInvalidPriceRange = true;
                        }
                    } else if (minValue > 0 || maxValue > 0) {
                        // One field is filled but not the other
                        hasIncompletePriceRange = true;
                    } else if (minValue === 0 && maxValue === 0) {
                        // Both fields are empty for price criteria - this is invalid
                        hasIncompletePriceRange = true;
                    }
                });

                if (unratedCriteria.length > 0) {
                    e.preventDefault();
                    alert('Please complete all criteria before submitting.');
                } else if (hasIncompletePriceRange) {
                    e.preventDefault();
                    alert('Please enter both minimum and maximum budget values for price criteria.');
                } else if (hasInvalidPriceRange) {
                    e.preventDefault();
                    alert('Please ensure minimum budget is not greater than maximum budget.');
                }
            });
        });
    </script>
</body>

</html>
