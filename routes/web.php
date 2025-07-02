<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\AdminManagementController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\LaptopManagementController;
use App\Http\Controllers\Admin\CriteriaManagementController;
use App\Http\Controllers\KriteriaRatingController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Public route - no authentication required
Route::get('/', function () {
    return view('index');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');

    // Admin Management
    Route::resource('admins', AdminManagementController::class)->parameters([
        'admins' => 'admin'
    ]);

    // User Management
    Route::resource('users', UserManagementController::class)->parameters([
        'users' => 'user'
    ]);

    // Laptop Management
    Route::resource('laptops', LaptopManagementController::class)->parameters([
        'laptops' => 'laptop'
    ]);

    // Criteria Management
    Route::resource('criteria', CriteriaManagementController::class)->parameters([
        'criteria' => 'criteria'
    ]);

    // Rating API endpoint
    Route::post('/criteria/get-rating', [KriteriaRatingController::class, 'getRating'])->name('criteria-rating.get-rating');
});

// Protected User Routes  
Route::prefix('user')->name('user.')->middleware('auth')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('dashboard');

    // Profile Management
    Route::get('/profile', [UserController::class, 'showProfile'])->name('profile');
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('profile.update');

    // Input User Preferences
    Route::get('/input', [UserController::class, 'showInputForm'])->name('input');
    Route::post('/input', [UserController::class, 'submitInput'])->name('input.submit');

    // View Results
    Route::get('/results/{id_input}', [UserController::class, 'showResults'])->name('results');

    // History
    Route::get('/history', [UserController::class, 'showHistory'])->name('history');
    Route::get('/history/{id_input}', [UserController::class, 'showHistoryDetail'])->name('history.detail');

    // Compare Laptops
    Route::post('/compare', [UserController::class, 'compareLaptops'])->name('compare');
});
