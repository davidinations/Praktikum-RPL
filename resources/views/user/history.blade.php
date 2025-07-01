<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search History - Laptop Recommendation System</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3><i class="fas fa-history"></i> Your Search History</h3>
            <a href="{{ route('user.input') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> New Search
            </a>
        </div>

        @if($history->count() > 0)
            <div class="row">
                @foreach($history as $item)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="fas fa-calendar"></i> 
                                    {{ $item->created_at->format('M d, Y') }}
                                </small>
                                <small class="text-muted">
                                    <i class="fas fa-clock"></i> 
                                    {{ $item->created_at->format('H:i') }}
                                </small>
                            </div>
                        </div>
                        <div class="card-body">
                            <h6 class="card-title">
                                <i class="fas fa-search"></i> Search Session
                            </h6>
                            <p class="card-text">
                                <strong>ID:</strong> <code>{{ $item->id_input }}</code><br>
                                <strong>Date:</strong> {{ $item->created_at->format('F d, Y') }}<br>
                                <strong>Time:</strong> {{ $item->created_at->format('g:i A') }}
                            </p>
                            
                            <!-- Time ago -->
                            <small class="text-muted">
                                <i class="fas fa-clock"></i> {{ $item->created_at->diffForHumans() }}
                            </small>
                        </div>
                        <div class="card-footer bg-white border-top-0">
                            <div class="d-grid">
                                <a href="{{ route('user.results', $item->id_input) }}" 
                                   class="btn btn-outline-primary">
                                    <i class="fas fa-eye"></i> View Results
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Stats Card -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <div class="row">
                                <div class="col-md-4">
                                    <h3>{{ $history->count() }}</h3>
                                    <p class="mb-0">Total Searches</p>
                                </div>
                                <div class="col-md-4">
                                    <h3>{{ $history->first() ? $history->first()->created_at->format('M Y') : 'N/A' }}</h3>
                                    <p class="mb-0">Latest Search</p>
                                </div>
                                <div class="col-md-4">
                                    <h3>{{ $history->last() ? $history->last()->created_at->format('M Y') : 'N/A' }}</h3>
                                    <p class="mb-0">First Search</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        @else
            <!-- Empty State -->
            <div class="text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-search fa-5x text-muted opacity-50"></i>
                </div>
                <h4 class="text-muted">No Search History Yet</h4>
                <p class="text-muted mb-4">
                    You haven't performed any laptop searches yet. Start by creating your first search to get personalized recommendations!
                </p>
                <a href="{{ route('user.input') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-plus"></i> Start Your First Search
                </a>
            </div>
        @endif

        <!-- Help Section -->
        <div class="card mt-4 border-info">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0"><i class="fas fa-question-circle"></i> About Your History</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>What's saved?</h6>
                        <ul class="small">
                            <li>Your preference ratings for each criteria</li>
                            <li>Calculated recommendation results</li>
                            <li>Laptop rankings based on your preferences</li>
                            <li>Date and time of each search</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>How to use?</h6>
                        <ul class="small">
                            <li>Click "View Results" to see past recommendations</li>
                            <li>Compare different search results</li>
                            <li>Track how your preferences change over time</li>
                            <li>Reference previous searches when making decisions</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
