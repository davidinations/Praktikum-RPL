<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recommendation Results - Laptop Recommendation System</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .laptop-card {
            transition: transform 0.2s;
        }
        .laptop-card:hover {
            transform: translateY(-5px);
        }
        .ranking-badge {
            position: absolute;
            top: -10px;
            left: -10px;
            font-size: 1.2rem;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .score-progress {
            height: 10px;
        }
        .preference-summary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
                <a href="{{ route('user.input') }}" class="btn btn-outline-light btn-sm me-2">
                    <i class="fas fa-plus"></i> New Search
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

        <!-- Preference Summary -->
        <div class="card mb-4 preference-summary text-white">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-user-cog"></i> Your Preferences Summary</h5>
                <div class="row">
                    @foreach($userPreferences as $pref)
                    <div class="col-md-4 mb-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>{{ $pref->kriteria->nama }}:</span>
                            <div>
                                @if(strtolower($pref->kriteria->nama) !== 'harga')
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= (int)$pref->value ? 'text-warning' : 'text-light opacity-50' }}"></i>
                                    @endfor
                                    <span class="ms-1">({{ (int)$pref->value }}/5)</span>
                                @else
                                    <span class="badge bg-light text-dark">Budget Preference</span>
                                @endif
                            </div>
                        </div>
                        <div class="mt-1">
                            <small class="text-light opacity-75">
                                @if(strtolower($pref->kriteria->nama) === 'harga')
                                    @if(isset($overlappingPriceBands) && count($overlappingPriceBands) > 1)
                                        <!-- Multiple overlapping price bands -->
                                        <strong>Your Budget Range covers multiple rating bands:</strong><br>
                                        @foreach($overlappingPriceBands as $band)
                                            <span class="badge bg-light text-dark me-1 mb-1">
                                                {{ $band['rating'] }}⭐: Rp {{ number_format($band['min'], 0, ',', '.') }} - Rp {{ number_format($band['max'], 0, ',', '.') }}
                                            </span>
                                        @endforeach
                                        @if(isset($userBudgetRange))
                                        <br><em>Your actual budget: Rp {{ number_format($userBudgetRange['min'], 0, ',', '.') }} - Rp {{ number_format($userBudgetRange['max'], 0, ',', '.') }}</em>
                                        @endif
                                    @elseif(isset($overlappingPriceBands) && count($overlappingPriceBands) == 1)
                                        <!-- Single overlapping price band -->
                                        @php $band = $overlappingPriceBands[0]; @endphp
                                        Budget Range ({{ $band['rating'] }}/5 ⭐): Rp {{ number_format($band['min'], 0, ',', '.') }} - Rp {{ number_format($band['max'], 0, ',', '.') }}
                                        @if(isset($userBudgetRange))
                                        <br><em>Your actual budget: Rp {{ number_format($userBudgetRange['min'], 0, ',', '.') }} - Rp {{ number_format($userBudgetRange['max'], 0, ',', '.') }}</em>
                                        @endif
                                    @else
                                        <!-- Fallback to original method if no overlapping bands data -->
                                        @php
                                            $ratingValue = (int)$pref->value; // Convert to integer
                                            $ratingField = "rating_{$ratingValue}";
                                            $minPrice = $pref->kriteria->{$ratingField . '_min'} ?? 0;
                                            $maxPrice = $pref->kriteria->{$ratingField . '_max'} ?? 0;
                                        @endphp
                                        Budget Range : Rp {{ number_format($minPrice * 1000000, 0, ',', '.') }} - Rp {{ number_format($maxPrice * 1000000, 0, ',', '.') }}
                                    @endif
                                @else
                                    @php
                                        $ratingValue = (int)$pref->value; // Convert to integer
                                        $ratingField = "rating_{$ratingValue}";
                                        $minValue = $pref->kriteria->{$ratingField . '_min'} ?? 0;
                                        $maxValue = $pref->kriteria->{$ratingField . '_max'} ?? 0;
                                        $unit = $pref->kriteria->satuan ?? '';
                                    @endphp
                                    Range : {{ $minValue }} - {{ $maxValue }} {{ $unit }}
                                @endif
                            </small>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="mt-3">
                    <small class="text-light opacity-75">
                        <i class="fas fa-info-circle"></i> Results ordered by laptops whose overall quality is closest to your ideal SAW score
                    </small>
                    @if(isset($matchingAnalysis[0]['user_preference_average_saw']))
                    <div class="mt-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-light"><i class="fas fa-calculator"></i> Your Ideal SAW Score:</small>
                            <div class="d-flex align-items-center">
                                <div class="progress bg-light bg-opacity-25" style="width: 100px; height: 6px;">
                                    <div class="progress-bar bg-warning" style="width: {{ $matchingAnalysis[0]['user_preference_average_saw'] }}%"></div>
                                </div>
                                <span class="ms-2 text-warning fw-bold">{{ $matchingAnalysis[0]['user_preference_average_saw'] }}%</span>
                            </div>
                        </div>
                        <small class="text-light opacity-75">
                            Laptops are ranked by how close their overall quality matches your ideal preference combination.
                        </small>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Results Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3><i class="fas fa-trophy"></i> Recommended Laptops for You</h3>
            <div>
                <span class="badge bg-primary">{{ $results->count() }} Results Found</span>
                <span class="badge bg-info">ID: {{ $idInput }}</span>
            </div>
        </div>

        @if($results->count() > 0)
            <!-- Top 3 Recommendations with Detailed Analysis -->
            <div class="row mb-4">
                @foreach($results->take(3) as $index => $result)
                @php
                    $analysis = isset($matchingAnalysis[$index]) ? $matchingAnalysis[$index] : null;
                @endphp
                <div class="col-lg-4 mb-4">
                    <div class="card laptop-card shadow position-relative h-100">
                        <!-- Ranking Badge -->
                        <div class="ranking-badge 
                            @if($result->ranking == 1) bg-warning
                            @elseif($result->ranking == 2) bg-secondary 
                            @elseif($result->ranking == 3) bg-info
                            @else bg-primary @endif 
                            text-white rounded-circle">
                            {{ $result->ranking }}
                        </div>

                        <!-- Laptop Image -->
                        @if($result->laptop->gambar && file_exists(storage_path('app/public/laptops/' . $result->laptop->gambar)))
                            <img src="{{ asset('storage/laptops/' . $result->laptop->gambar) }}" 
                                 class="card-img-top" 
                                 alt="{{ $result->laptop->merek }} {{ $result->laptop->model }}"
                                 style="height: 200px;"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        @else
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                 style="height: 200px;">
                                <div class="text-center">
                                    <i class="fas fa-laptop fa-3x text-muted mb-2"></i>
                                    <br>
                                    <small class="text-muted">{{ $result->laptop->merek }}</small>
                                </div>
                            </div>
                        @endif

                        <div class="card-body">
                            <h5 class="card-title">{{ $result->laptop->merek }} {{ $result->laptop->model }}</h5>
                            
                            <!-- Overall Match Score -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <small class="text-muted">Overall Match</small>
                                    <small class="text-muted">
                                        @if($analysis)
                                            {{ $analysis['combined_score'] }}%
                                        @else
                                            {{ number_format($result->rating * 100, 1) }}%
                                        @endif
                                    </small>
                                </div>
                                <div class="progress score-progress">
                                    <div class="progress-bar 
                                        @if($result->ranking == 1) bg-warning
                                        @elseif($result->ranking == 2) bg-secondary 
                                        @elseif($result->ranking == 3) bg-info
                                        @else bg-primary @endif" 
                                         style="width: {{ $analysis ? $analysis['combined_score'] : $result->rating * 100 }}%"></div>
                                </div>
                                @if($analysis)
                                <div class="mt-1">
                                    <small class="text-muted">
                                        <i class="fas fa-chart-pie"></i> Preference: {{ $analysis['preference_match_percentage'] }}% | 
                                        <i class="fas fa-dollar-sign"></i> Price: {{ $analysis['price_proximity_score'] }}%
                                        @if(isset($analysis['quality_closeness_to_ideal']))
                                        | <i class="fas fa-bullseye"></i> Gap: {{ $analysis['quality_closeness_to_ideal'] }}%
                                        @endif
                                    </small>
                                </div>
                                @endif
                            </div>

                            <!-- SAW Score Breakdown -->
                            @if($analysis && isset($analysis['user_saw_score']) && isset($analysis['pure_saw_score']))
                            <div class="mb-3">
                                <h6 class="text-muted mb-2"><i class="fas fa-calculator"></i> SAW Score Analysis:</h6>
                                <div class="row">
                                    <div class="col-4">
                                        <div class="text-center p-2 bg-light rounded">
                                            <small class="text-muted d-block">User-Laptop Match</small>
                                            <strong class="text-primary">{{ $analysis['user_saw_score'] }}%</strong>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="text-center p-2 bg-light rounded">
                                            <small class="text-muted d-block">Overall Quality</small>
                                            <strong class="text-success">{{ $analysis['pure_saw_score'] }}%</strong>
                                        </div>
                                    </div>
                                    @if(isset($analysis['user_preference_average_saw']))
                                    <div class="col-4">
                                        <div class="text-center p-2 bg-warning bg-opacity-10 rounded">
                                            <small class="text-muted d-block">Your Ideal</small>
                                            <strong class="text-warning">{{ $analysis['user_preference_average_saw'] }}%</strong>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                @if(isset($analysis['quality_closeness_to_ideal']))
                                <div class="mt-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">Quality Gap from Ideal:</small>
                                        <span class="badge 
                                            @if($analysis['quality_closeness_to_ideal'] <= 10) bg-success
                                            @elseif($analysis['quality_closeness_to_ideal'] <= 20) bg-warning
                                            @else bg-danger
                                            @endif">
                                            {{ $analysis['quality_closeness_to_ideal'] }}%
                                        </span>
                                    </div>
                                    <div class="progress mt-1" style="height: 4px;">
                                        <div class="progress-bar 
                                            @if($analysis['quality_closeness_to_ideal'] <= 10) bg-success
                                            @elseif($analysis['quality_closeness_to_ideal'] <= 20) bg-warning
                                            @else bg-danger
                                            @endif" 
                                             style="width: {{ min(100, $analysis['quality_closeness_to_ideal'] * 2) }}%"></div>
                                    </div>
                                </div>
                                @endif
                                <small class="text-muted mt-1 d-block">
                                    <i class="fas fa-info-circle"></i> User-Laptop: How well this laptop matches your specific preferences. 
                                    Overall Quality: The laptop's general performance rating across all criteria.
                                    @if(isset($analysis['user_preference_average_saw']))
                                    Your Ideal: The SAW score of your perfect laptop based on your ratings.
                                    @endif
                                    @if(isset($analysis['quality_closeness_to_ideal']))
                                    <br><i class="fas fa-bullseye"></i> Quality Gap: {{ $analysis['quality_closeness_to_ideal'] }}% from your ideal
                                    @endif
                                </small>
                            </div>
                            @endif

                            <!-- Criteria Match Details -->
                            @if($analysis && isset($analysis['criteria_match']))
                            <div class="mb-3">
                                <h6 class="text-muted mb-2">Criteria Match:</h6>
                                @foreach($analysis['criteria_match'] as $criteriaMatch)
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <small class="text-muted">{{ $criteriaMatch['criterion'] }}:</small>
                                        <span class="badge 
                                            @if($criteriaMatch['match_percentage'] >= 80) bg-success
                                            @elseif($criteriaMatch['match_percentage'] >= 60) bg-warning
                                            @else bg-danger
                                            @endif"
                                            style="font-size: 0.65rem;">
                                            {{ $criteriaMatch['match_percentage'] }}%
                                        </span>
                                    </div>
                                    
                                    @if(strtolower($criteriaMatch['criterion']) === 'harga' && isset($criteriaMatch['actual_price']))
                                        <!-- Price Information with Multiple Rating Bands -->
                                        <div class="small text-muted">
                                            <strong>Actual Price:</strong> Rp {{ number_format($criteriaMatch['actual_price'], 0, ',', '.') }}<br>
                                            
                                            @if(isset($overlappingPriceBands) && count($overlappingPriceBands) > 1)
                                                <strong>Your Budget covers {{ count($overlappingPriceBands) }} rating bands:</strong><br>
                                                @foreach($overlappingPriceBands as $band)
                                                    <div class="mt-1">
                                                        <span class="badge 
                                                            @if($criteriaMatch['actual_price'] >= $band['min'] && $criteriaMatch['actual_price'] <= $band['max']) 
                                                                bg-success 
                                                            @else 
                                                                bg-secondary 
                                                            @endif text-white me-1">
                                                            {{ $band['rating'] }}⭐
                                                        </span>
                                                        <small>Rp {{ number_format($band['min'], 0, ',', '.') }} - Rp {{ number_format($band['max'], 0, ',', '.') }}
                                                        @if($criteriaMatch['actual_price'] >= $band['min'] && $criteriaMatch['actual_price'] <= $band['max'])
                                                            <span class="text-success"> ✓ Match</span>
                                                        @endif
                                                        </small>
                                                    </div>
                                                @endforeach
                                                @if(isset($userBudgetRange))
                                                <div class="mt-2 p-2 bg-light rounded">
                                                    <small><strong>Your Budget Range:</strong> Rp {{ number_format($userBudgetRange['min'], 0, ',', '.') }} - Rp {{ number_format($userBudgetRange['max'], 0, ',', '.') }}</small>
                                                </div>
                                                @endif
                                            @elseif(isset($criteriaMatch['target_range']))
                                                <strong>Your Budget Range ({{ $criteriaMatch['target_range']['rating'] ?? 'N/A' }}/5 ⭐):</strong> 
                                                Rp {{ number_format($criteriaMatch['target_range']['min'], 0, ',', '.') }} - 
                                                Rp {{ number_format($criteriaMatch['target_range']['max'], 0, ',', '.') }}
                                            @endif
                                        </div>
                                    @else
                                        <!-- Rating Stars for Non-Price Criteria -->
                                        <div class="d-flex align-items-center">
                                            <div class="me-2">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star {{ $i <= $criteriaMatch['laptop_rating'] ? 'text-warning' : 'text-muted' }}" style="font-size: 0.7rem;"></i>
                                                @endfor
                                            </div>
                                            <small class="text-muted">{{ $criteriaMatch['laptop_rating'] }}/5 (Your preference: {{ $criteriaMatch['user_preference'] }}/5)</small>
                                        </div>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                            @endif

                            <!-- Strengths & Weaknesses -->
                            @if($analysis)
                            <div class="mb-3">
                                @if(count($analysis['strengths']) > 0)
                                <div class="mb-2">
                                    <h6 class="text-success mb-1"><i class="fas fa-thumbs-up"></i> Strengths:</h6>
                                    <div>
                                        @foreach($analysis['strengths'] as $strength)
                                            <span class="badge bg-success me-1">{{ $strength }}</span>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                                
                                @if(count($analysis['weaknesses']) > 0)
                                <div class="mb-2">
                                    <h6 class="text-danger mb-1"><i class="fas fa-thumbs-down"></i> Considerations:</h6>
                                    <div>
                                        @foreach($analysis['weaknesses'] as $weakness)
                                            <span class="badge bg-danger me-1">{{ $weakness }}</span>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            </div>
                            @endif

                            <!-- Specifications -->
                            <div class="specifications">
                                <p class="mb-1"><strong>Price:</strong> Rp {{ number_format($result->laptop->harga, 0, ',', '.') }}</p>
                                <p class="mb-1"><strong>Processor:</strong> {{ $result->laptop->processor }}</p>
                                <p class="mb-1"><strong>RAM:</strong> {{ $result->laptop->ram }}GB</p>
                                <p class="mb-1"><strong>Storage:</strong> {{ $result->laptop->storage }}GB</p>
                                <p class="mb-1"><strong>GPU:</strong> {{ $result->laptop->gpu }}</p>
                                @if($result->laptop->ukuran_baterai)
                                <p class="mb-0"><strong>Battery:</strong> {{ $result->laptop->ukuran_baterai }}mAh</p>
                                @endif
                            </div>
                        </div>

                        <!-- Ranking Label -->
                        <div class="card-footer text-center 
                            @if($result->ranking == 1) bg-warning text-dark
                            @elseif($result->ranking == 2) bg-secondary text-white 
                            @elseif($result->ranking == 3) bg-info text-white
                            @else bg-primary text-white @endif">
                            @if($result->ranking == 1)
                                <i class="fas fa-crown"></i> Best Match
                            @elseif($result->ranking == 2)
                                <i class="fas fa-medal"></i> Second Best
                            @elseif($result->ranking == 3)
                                <i class="fas fa-award"></i> Third Best
                            @else
                                Rank #{{ $result->ranking }}
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Detailed Results Table -->
            @if($results->count() > 3)
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-list"></i> All Results - Detailed Analysis</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Rank</th>
                                    <th>Laptop</th>
                                    <th>Price</th>
                                    <th>Quality Gap</th>
                                    <th>Overall Match</th>
                                    <th>SAW Scores</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($results->skip(3) as $index => $result)
                                @php
                                    $analysis = isset($matchingAnalysis[$index + 3]) ? $matchingAnalysis[$index + 3] : null;
                                @endphp
                                <tr>
                                    <td>
                                        <span class="badge bg-primary">#{{ $result->ranking }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ $result->laptop->merek }}</strong><br>
                                        <small class="text-muted">{{ $result->laptop->model }}</small>
                                    </td>
                                    <td>Rp {{ number_format($result->laptop->harga, 0, ',', '.') }}</td>
                                    <td>
                                        @if($analysis && isset($analysis['quality_closeness_to_ideal']))
                                            <div class="d-flex align-items-center">
                                                <span class="badge 
                                                    @if($analysis['quality_closeness_to_ideal'] <= 10) bg-success
                                                    @elseif($analysis['quality_closeness_to_ideal'] <= 20) bg-warning
                                                    @else bg-danger
                                                    @endif me-2">
                                                    {{ $analysis['quality_closeness_to_ideal'] }}%
                                                </span>
                                                <small class="text-muted">gap</small>
                                            </div>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($analysis)
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar 
                                                    @if($analysis['combined_score'] >= 80) bg-success
                                                    @elseif($analysis['combined_score'] >= 60) bg-warning
                                                    @else bg-danger
                                                    @endif" 
                                                     style="width: {{ $analysis['combined_score'] }}%">
                                                    {{ $analysis['combined_score'] }}%
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($analysis && isset($analysis['user_saw_score']) && isset($analysis['pure_saw_score']))
                                            <small class="text-muted d-block">User-Match: </small>
                                            <strong class="text-primary">{{ $analysis['user_saw_score'] }}%</strong>
                                            <small class="text-muted d-block">Quality: </small>
                                            <strong class="text-success">{{ $analysis['pure_saw_score'] }}%</strong>
                                            @if(isset($analysis['user_preference_average_saw']))
                                            <small class="text-muted d-block">Your Ideal: </small>
                                            <strong class="text-warning">{{ $analysis['user_preference_average_saw'] }}%</strong>
                                            @endif
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- Analysis Explanation -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> How We Calculate Your Match</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-calculator"></i> Quality Gap Ordering</h6>
                            <p class="text-muted">
                                Laptops are ordered by how close their overall quality (pure SAW score) is to your ideal SAW score. 
                                Lower gaps mean the laptop's specifications better match your preference combination.
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-target"></i> Quality Gap Indicator</h6>
                            <p class="text-muted">
                                Green: ≤10% gap (excellent match), Yellow: 11-20% gap (good match), Red: >20% gap (poor match). 
                                This helps you quickly identify laptops that best align with your preferences.
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-chart-line"></i> Combined Match Score</h6>
                            <p class="text-muted">
                                Overall match combines preference alignment (70%) and price proximity (30%). 
                                This score shows how well the laptop fits your specific needs and budget.
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-sort-amount-down"></i> Smart Ordering</h6>
                            <p class="text-muted">
                                Results are ordered by laptops whose overall quality SAW score is closest to your ideal preference score. 
                                This helps you find laptops that best match your quality expectations.
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <h6><i class="fas fa-user-cog"></i> User-Laptop SAW Score</h6>
                            <p class="text-muted">
                                This score reflects how well the laptop matches your specific preferences using the SAW method. 
                                It weighs each criterion based on your individual rating importance.
                            </p>
                        </div>
                        <div class="col-md-4">
                            <h6><i class="fas fa-laptop"></i> Laptop Overall Quality Score</h6>
                            <p class="text-muted">
                                This is the laptop's pure SAW score based only on admin-defined criteria weights. 
                                It shows the laptop's general performance quality regardless of your preferences.
                            </p>
                        </div>
                        <div class="col-md-4">
                            <h6><i class="fas fa-star"></i> Your Ideal SAW Score</h6>
                            <p class="text-muted">
                                This represents the SAW score your ideal laptop would have based on your preference ratings. 
                                Compare laptop scores to this to see how close they come to your perfect match.
                            </p>
                        </div>
                    </div>
                    @if(isset($overlappingPriceBands) && count($overlappingPriceBands) > 1)
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <h6><i class="fas fa-money-bill-wave"></i> Multiple Price Rating Bands</h6>
                                <p class="text-muted mb-0">
                                    Your budget range spans across {{ count($overlappingPriceBands) }} different price rating bands. 
                                    We show all relevant bands to help you understand how your budget compares to different laptop price categories. 
                                    Laptops matching any of these bands will be included in your results.
                                </p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

        @else
            <div class="alert alert-warning">
                <h5><i class="fas fa-exclamation-triangle"></i> No Results Found</h5>
                <p class="mb-0">No laptops found matching your criteria. Please try adjusting your preferences.</p>
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="text-center mt-4 mb-5">
            <a href="{{ route('user.input') }}" class="btn btn-primary btn-lg me-2">
                <i class="fas fa-plus"></i> New Search
            </a>
            <a href="{{ route('user.history') }}" class="btn btn-outline-secondary btn-lg">
                <i class="fas fa-history"></i> View History
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
