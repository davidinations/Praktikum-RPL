<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - Laptop Recommendation System</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <!-- Header -->
    <header class="bg-primary text-white py-3">
        <div class="container d-flex justify-content-between align-items-center">
            <h1 class="h4"><i class="fas fa-laptop"></i> Laptop Recommendation System</h1>
            <div>
                <span class="text-light me-3">Welcome, {{ $user->username }}!</span>
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
        
        <!-- Quick Actions -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card h-100 text-center border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-primary mb-3">
                            <i class="fas fa-plus-circle fa-3x"></i>
                        </div>
                        <h5 class="card-title">New Recommendation</h5>
                        <p class="card-text">Input your preferences to get personalized laptop recommendations.</p>
                        <a href="{{ route('user.input') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-right"></i> Start Now
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="card h-100 text-center border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-success mb-3">
                            <i class="fas fa-user-circle fa-3x"></i>
                        </div>
                        <h5 class="card-title">Edit Profile</h5>
                        <p class="card-text">Update your personal information and account settings.</p>
                        <a href="{{ route('user.profile') }}" class="btn btn-success">
                            <i class="fas fa-edit"></i> Edit Profile
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="card h-100 text-center border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-warning mb-3">
                            <i class="fas fa-history fa-3x"></i>
                        </div>
                        <h5 class="card-title">View History</h5>
                        <p class="card-text">Check your previous recommendations and saved preferences.</p>
                        <a href="{{ route('user.history') }}" class="btn btn-warning">
                            <i class="fas fa-list"></i> View History
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="card h-100 text-center border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-info mb-3">
                            <i class="fas fa-balance-scale fa-3x"></i>
                        </div>
                        <h5 class="card-title">Compare Laptops</h5>
                        <p class="card-text">Compare different laptop specifications side by side.</p>
                        <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#compareModal">
                            <i class="fas fa-search"></i> Compare
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        @if($recentHistory->count() > 0)
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-clock"></i> Recent Activity</h5>
                        <a href="{{ route('user.history') }}" class="btn btn-outline-primary btn-sm">View All</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Input ID</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentHistory as $history)
                                    <tr>
                                        <td>{{ $history->created_at->format('M d, Y H:i') }}</td>
                                        <td><code>{{ $history->id_input }}</code></td>
                                        <td>
                                            <a href="{{ route('user.results', $history->id_input) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i> View Results
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Compare Modal -->
    <div class="modal fade" id="compareModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Compare Laptops</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Feature coming soon! You'll be able to compare laptop specifications here.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

    <!-- Footer -->
    <footer class="bg-primary text-white py-3 mt-5">
        <div class="container text-center">
            <p class="mb-0">&copy; {{ date('Y') }} Laptop Store. All rights reserved.</p>
        </div>
    </footer>

    <!-- Include Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
