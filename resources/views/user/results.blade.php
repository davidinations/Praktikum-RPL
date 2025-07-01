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
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= $pref->value ? 'text-warning' : 'text-light opacity-50' }}"></i>
                                @endfor
                                <span class="ms-1">({{ $pref->value }}/5)</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <small class="text-light opacity-75">
                    <i class="fas fa-info-circle"></i> Results calculated using SAW (Simple Additive Weighting) method based on your preferences
                </small>
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
            <!-- Top 3 Recommendations -->
            <div class="row mb-4">
                @foreach($results->take(3) as $result)
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
                            
                            <!-- Score -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <small class="text-muted">Match Score</small>
                                    <small class="text-muted">{{ number_format($result->rating * 100, 1) }}%</small>
                                </div>
                                <div class="progress score-progress">
                                    <div class="progress-bar 
                                        @if($result->ranking == 1) bg-warning
                                        @elseif($result->ranking == 2) bg-secondary 
                                        @elseif($result->ranking == 3) bg-info
                                        @else bg-primary @endif" 
                                         style="width: {{ $result->rating * 100 }}%"></div>
                                </div>
                            </div>

                            <!-- Specifications -->
                            <div class="specifications">
                                <p class="mb-1"><strong>Price:</strong> Rp {{ number_format($result->laptop->harga, 0, ',', '.') }}</p>
                                <p class="mb-1"><strong>Processor:</strong> {{ $result->laptop->processor }}</p>
                                <p class="mb-1"><strong>RAM:</strong> {{ $result->laptop->ram }}</p>
                                <p class="mb-1"><strong>Storage:</strong> {{ $result->laptop->storage }}</p>
                                <p class="mb-1"><strong>GPU:</strong> {{ $result->laptop->gpu }}</p>
                                <p class="mb-0"><strong>Battery:</strong> {{ $result->laptop->ukuran_baterai }}</p>
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
                    <h5 class="mb-0"><i class="fas fa-list"></i> All Results</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Rank</th>
                                    <th>Laptop</th>
                                    <th>Price</th>
                                    <th>Processor</th>
                                    <th>RAM</th>
                                    <th>Storage</th>
                                    <th>Score</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($results->skip(3) as $result)
                                <tr>
                                    <td>
                                        <span class="badge bg-primary">#{{ $result->ranking }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ $result->laptop->merek }}</strong><br>
                                        <small class="text-muted">{{ $result->laptop->model }}</small>
                                    </td>
                                    <td>Rp {{ number_format($result->laptop->harga, 0, ',', '.') }}</td>
                                    <td>{{ $result->laptop->processor }}</td>
                                    <td>{{ $result->laptop->ram }}</td>
                                    <td>{{ $result->laptop->storage }}</td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-primary" 
                                                 style="width: {{ $result->rating * 100 }}%">
                                                {{ number_format($result->rating * 100, 1) }}%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

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
