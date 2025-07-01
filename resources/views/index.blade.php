<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laptop Website</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Set carousel image dimensions */
        .carousel-inner img {
            width: 100%;
            /* Full width */
            height: 500px;
            /* Fixed height */
            /* Ensure the image covers the area */
        }
    </style>
</head>

<body class="bg-light">
    <!-- Header -->
    <header class="bg-dark text-white py-3">
        <div class="container d-flex justify-content-between align-items-center">
            <h1 class="h4">Laptop Store</h1>
            <div>
                <a href="{{ url('/') }}" class="text-light text-decoration-none me-3">Home</a>
                <a href="{{ route('login') }}" class="text-light text-decoration-none me-3">Login</a>
                <a href="{{ route('register') }}" class="text-light text-decoration-none">Register</a>
            </div>
        </div>
    </header>

    <!-- Welcome Section -->
    <div class="container mt-4">
        <div class="row">
            <div class="col-12 text-center">
                <h2 class="display-4">Welcome to Laptop Store</h2>
                <p class="lead">Find the perfect laptop that matches your needs and budget</p>
                <div class="mt-3">
                    <a href="{{ route('login') }}" class="btn btn-primary me-3">Login to Get Started</a>
                    <a href="{{ route('register') }}" class="btn btn-outline-primary">Create Account</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Carousel -->
    <div class="container mt-4 mb-4">
        <div id="carouselExample" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                @foreach (glob(public_path('images') . '/*') as $image)
                    <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                        <img src="{{ asset('images/' . basename($image)) }}" class="d-block w-100" alt="Laptop Image">
                    </div>
                @endforeach
            </div>
            <!-- Carousel Controls -->
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-3 mt-4">
        <div class="container text-center">
            <p class="mb-0">&copy; {{ date('Y') }} Laptop Store. All rights reserved.</p>
        </div>
    </footer>

    <!-- Include Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
