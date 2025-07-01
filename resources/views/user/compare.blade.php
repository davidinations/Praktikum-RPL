<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compare Laptops - Laptop Recommendation System</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .compare-table {
            font-size: 0.9rem;
        }
        .laptop-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .spec-row:nth-child(even) {
            background-color: #f8f9fa;
        }
        .price-highlight {
            font-size: 1.1rem;
            font-weight: bold;
            color: #28a745;
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3><i class="fas fa-balance-scale"></i> Compare Laptops</h3>
            <a href="{{ route('user.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>

        @if($laptops->count() > 0)
            <div class="card shadow">
                <div class="card-header">
                    <h5 class="mb-0">Laptop Comparison</h5>
                    <small class="text-muted">Comparing {{ $laptops->count() }} laptops</small>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered compare-table mb-0">
                            <!-- Laptop Headers -->
                            <thead>
                                <tr>
                                    <th style="width: 200px;" class="bg-light">Specification</th>
                                    @foreach($laptops as $laptop)
                                    <th class="text-center laptop-header">
                                        <div class="p-3">
                                            @if($laptop->gambar)
                                                <img src="{{ asset('images/' . $laptop->gambar) }}" 
                                                     alt="{{ $laptop->merek }} {{ $laptop->model }}"
                                                     class="img-fluid mb-2" 
                                                     style="max-height: 100px; object-fit: cover;">
                                            @else
                                                <i class="fas fa-laptop fa-3x mb-2"></i>
                                            @endif
                                            <h6 class="mb-0">{{ $laptop->merek }}</h6>
                                            <small>{{ $laptop->model }}</small>
                                        </div>
                                    </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Price -->
                                <tr class="spec-row">
                                    <td class="fw-bold bg-light">
                                        <i class="fas fa-tag text-success"></i> Price
                                    </td>
                                    @foreach($laptops as $laptop)
                                    <td class="text-center price-highlight">
                                        Rp {{ number_format($laptop->harga, 0, ',', '.') }}
                                    </td>
                                    @endforeach
                                </tr>

                                <!-- Processor -->
                                <tr class="spec-row">
                                    <td class="fw-bold bg-light">
                                        <i class="fas fa-microchip text-primary"></i> Processor
                                    </td>
                                    @foreach($laptops as $laptop)
                                    <td class="text-center">{{ $laptop->processor }}</td>
                                    @endforeach
                                </tr>

                                <!-- RAM -->
                                <tr class="spec-row">
                                    <td class="fw-bold bg-light">
                                        <i class="fas fa-memory text-info"></i> RAM
                                    </td>
                                    @foreach($laptops as $laptop)
                                    <td class="text-center">{{ $laptop->ram }}</td>
                                    @endforeach
                                </tr>

                                <!-- Storage -->
                                <tr class="spec-row">
                                    <td class="fw-bold bg-light">
                                        <i class="fas fa-hdd text-warning"></i> Storage
                                    </td>
                                    @foreach($laptops as $laptop)
                                    <td class="text-center">{{ $laptop->storage }}</td>
                                    @endforeach
                                </tr>

                                <!-- GPU -->
                                <tr class="spec-row">
                                    <td class="fw-bold bg-light">
                                        <i class="fas fa-desktop text-danger"></i> GPU
                                    </td>
                                    @foreach($laptops as $laptop)
                                    <td class="text-center">{{ $laptop->gpu }}</td>
                                    @endforeach
                                </tr>

                                <!-- Battery -->
                                <tr class="spec-row">
                                    <td class="fw-bold bg-light">
                                        <i class="fas fa-battery-full text-success"></i> Battery
                                    </td>
                                    @foreach($laptops as $laptop)
                                    <td class="text-center">{{ $laptop->ukuran_baterai }}</td>
                                    @endforeach
                                </tr>

                                <!-- Criteria Scores (if available) -->
                                @foreach($criteria as $criterion)
                                <tr class="spec-row">
                                    <td class="fw-bold bg-light">
                                        <i class="fas fa-star text-warning"></i> {{ $criterion->nama }} Score
                                    </td>
                                    @foreach($laptops as $laptop)
                                    <td class="text-center">
                                        <span class="badge bg-secondary">N/A</span>
                                        <small class="text-muted d-block">Weight: {{ $criterion->bobot }}%</small>
                                    </td>
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="row mt-4">
                @foreach($laptops as $index => $laptop)
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card h-100">
                        <div class="card-header bg-primary text-white text-center">
                            <h6 class="mb-0">{{ $laptop->merek }} {{ $laptop->model }}</h6>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <h4 class="text-success">Rp {{ number_format($laptop->harga, 0, ',', '.') }}</h4>
                            </div>
                            
                            <!-- Quick Specs -->
                            <ul class="list-unstyled small">
                                <li><strong>Processor:</strong> {{ Str::limit($laptop->processor, 30) }}</li>
                                <li><strong>RAM:</strong> {{ $laptop->ram }}</li>
                                <li><strong>Storage:</strong> {{ $laptop->storage }}</li>
                                <li><strong>GPU:</strong> {{ Str::limit($laptop->gpu, 30) }}</li>
                            </ul>
                        </div>
                        <div class="card-footer">
                            <small class="text-muted">Added by: {{ $laptop->admin->username ?? 'Admin' }}</small>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

        @else
            <div class="alert alert-warning">
                <h5><i class="fas fa-exclamation-triangle"></i> No Laptops to Compare</h5>
                <p class="mb-0">Please select laptops to compare from the search results.</p>
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="text-center mt-4 mb-5">
            <a href="{{ route('user.input') }}" class="btn btn-primary btn-lg me-2">
                <i class="fas fa-search"></i> New Search
            </a>
            <a href="{{ route('user.history') }}" class="btn btn-outline-secondary btn-lg">
                <i class="fas fa-history"></i> View History
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
