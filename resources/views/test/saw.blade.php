<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enhanced SAW System Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-4">
        <h2><i class="fas fa-cogs"></i> Enhanced SAW System Test</h2>
        
        @if(isset($error))
            <div class="alert alert-danger">
                <h5><i class="fas fa-exclamation-triangle"></i> Error</h5>
                <p>{{ $error }}</p>
            </div>
        @else
            <div class="alert alert-success">
                <h5><i class="fas fa-check-circle"></i> Success</h5>
                <p>{{ $result['message'] }}</p>
            </div>

            <!-- Sample Preferences -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-user-cog"></i> Sample User Preferences</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($result['sample_preferences'] as $pref)
                            @php
                                $criterion = collect($result['criteria'])->where('id_kriteria', $pref['id_kriteria'])->first();
                            @endphp
                            <div class="col-md-3 mb-2">
                                <strong>{{ $criterion['nama'] ?? 'Unknown' }}:</strong>
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= $pref['value'] ? 'text-warning' : 'text-muted' }}"></i>
                                @endfor
                                ({{ $pref['value'] }}/5)
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Results Analysis -->
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-trophy"></i> Laptop Recommendations & Analysis</h5>
                </div>
                <div class="card-body">
                    @foreach($result['analysis'] as $index => $analysis)
                        <div class="border rounded p-3 mb-3 {{ $index === 0 ? 'border-warning bg-warning bg-opacity-10' : '' }}">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>
                                        <span class="badge {{ $index === 0 ? 'bg-warning text-dark' : 'bg-primary' }}">#{{ $analysis['rank'] }}</span>
                                        {{ $analysis['laptop']['brand'] }} {{ $analysis['laptop']['model'] }}
                                    </h6>
                                    <p class="text-muted mb-2">
                                        <strong>Price:</strong> Rp {{ number_format($analysis['laptop']['price'], 0, ',', '.') }}<br>
                                        <strong>Processor:</strong> {{ $analysis['laptop']['processor'] }}<br>
                                        <strong>RAM:</strong> {{ $analysis['laptop']['ram'] }}GB<br>
                                        <strong>Storage:</strong> {{ $analysis['laptop']['storage'] }}GB<br>
                                        <strong>GPU:</strong> {{ $analysis['laptop']['gpu'] }}
                                    </p>
                                    <div class="mb-2">
                                        <strong>SAW Score:</strong> 
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-primary" style="width: {{ $analysis['score'] * 100 }}%">
                                                {{ number_format($analysis['score'] * 100, 1) }}%
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h6>Criteria Match Analysis:</h6>
                                    @foreach($analysis['criteria_breakdown'] as $criteria)
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <small>{{ $criteria['criterion'] }}:</small>
                                            <div class="d-flex align-items-center">
                                                <div class="me-2">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <i class="fas fa-star {{ $i <= $criteria['laptop_rating'] ? 'text-warning' : 'text-muted' }}" style="font-size: 0.7rem;"></i>
                                                    @endfor
                                                    <small class="text-muted">({{ $criteria['laptop_rating'] }})</small>
                                                </div>
                                                <span class="badge 
                                                    @if($criteria['match_percentage'] >= 80) bg-success
                                                    @elseif($criteria['match_percentage'] >= 60) bg-warning
                                                    @else bg-danger
                                                    @endif"
                                                    style="font-size: 0.65rem;">
                                                    {{ $criteria['match_percentage'] }}%
                                                </span>
                                                @if($criteria['is_strength'])
                                                    <i class="fas fa-thumbs-up text-success ms-1" title="Strength"></i>
                                                @else
                                                    <i class="fas fa-minus text-muted ms-1" title="Consideration"></i>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Explanation -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5><i class="fas fa-info-circle"></i> How It Works</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Rating System:</h6>
                            <ul>
                                <li><strong>1-5 Star Rating:</strong> Each laptop gets rated on each criterion based on its specifications</li>
                                <li><strong>User Preferences:</strong> You rate how important each criterion is to you (1-5 stars)</li>
                                <li><strong>Match Percentage:</strong> Shows how well each laptop matches your specific preferences</li>
                                <li><strong>SAW Score:</strong> Mathematical score using Simple Additive Weighting method</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>Analysis Features:</h6>
                            <ul>
                                <li><strong>Criteria Breakdown:</strong> See how each laptop performs on each criterion</li>
                                <li><strong>Strengths:</strong> Areas where laptop meets/exceeds your preferences</li>
                                <li><strong>Match Indicators:</strong> Color-coded badges show preference alignment</li>
                                <li><strong>Ranking:</strong> Laptops ordered by best overall match to your needs</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="text-center mt-4">
            <a href="/" class="btn btn-primary">
                <i class="fas fa-home"></i> Back to Home
            </a>
        </div>
    </div>
</body>
</html>
