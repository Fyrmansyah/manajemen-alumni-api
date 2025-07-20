<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\AuthController as BaseAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminJobController;
use App\Http\Controllers\Admin\AdminNewsController;
use App\Http\Controllers\Admin\AdminCompanyController;
use App\Http\Controllers\Admin\AdminApplicationController;
use App\Http\Controllers\Alumni\AlumniDashboardController;
use App\Http\Controllers\Company\CompanyDashboardController;
use Illuminate\Support\Facades\Auth; // Added this import for Auth facade

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', function() {
    return view('about');
})->name('about');
Route::get('/test', function() {
    return view('home', [
        'stats' => [
            'total_jobs' => 25,
            'total_companies' => 15,
            'total_alumni' => 150,
            'total_applications' => 75
        ],
        'latest_jobs' => [],
        'latest_news' => []
    ]);
})->name('test');

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [BaseAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [BaseAuthController::class, 'login']);
    Route::get('/register', [BaseAuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [BaseAuthController::class, 'register']);
    Route::get('/register/company', [BaseAuthController::class, 'showCompanyRegisterForm'])->name('company.register');
    Route::post('/register/company', [BaseAuthController::class, 'registerCompany']);
    
    // Password reset routes
    Route::get('/forgot-password', function () {
        return view('auth.forgot-password');
    })->name('password.request');
    Route::post('/forgot-password', [BaseAuthController::class, 'sendPasswordResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', function ($token) {
        return view('auth.reset-password', ['token' => $token]);
    })->name('password.reset');
    Route::post('/reset-password', [BaseAuthController::class, 'resetPassword'])->name('password.update');
});

Route::post('/logout', [BaseAuthController::class, 'logout'])->name('logout');

// Force clear all authentication and redirect to login
Route::get('/force-logout', function() {
    // Clear all session data
    session()->flush();
    
    // Logout from all guards
    Auth::guard('admin')->logout();
    Auth::guard('alumni')->logout();
    Auth::guard('company')->logout();
    
    // Invalidate and regenerate session
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    
    return redirect('/login')->with('success', 'All sessions cleared. Please login again.');
});

// Test current authentication status
Route::get('/test-auth', function() {
    return [
        'admin_auth' => Auth::guard('admin')->check(),
        'admin_user' => Auth::guard('admin')->user(),
        'alumni_auth' => Auth::guard('alumni')->check(),
        'alumni_user' => Auth::guard('alumni')->user(),
        'company_auth' => Auth::guard('company')->check(),
        'company_user' => Auth::guard('company')->user(),
        'session_id' => session()->getId(),
        'session_data' => session()->all()
    ];
});

// Test logout functionality
Route::get('/test-logout', function() {
    // Test logout
    Auth::guard('admin')->logout();
    Auth::guard('alumni')->logout();
    Auth::guard('company')->logout();
    
    session()->flush();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    
    return [
        'message' => 'Logout test completed',
        'admin_auth' => Auth::guard('admin')->check(),
        'alumni_auth' => Auth::guard('alumni')->check(),
        'company_auth' => Auth::guard('company')->check(),
        'session_data' => session()->all()
    ];
});

// Jobs routes
Route::prefix('jobs')->name('jobs.')->group(function () {
    Route::get('/', [JobController::class, 'indexWeb'])->name('index');
    Route::get('/{job}', [JobController::class, 'showWeb'])->name('show');
});

// News routes
Route::prefix('news')->name('news.')->group(function () {
    Route::get('/', [NewsController::class, 'indexWeb'])->name('index');
    Route::get('/{news}', [NewsController::class, 'showWeb'])->name('show');
});

// Protected routes
// Admin routes (using admin guard) - RESTORED MIDDLEWARE  
Route::middleware('auth:admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Job management
    Route::resource('jobs', AdminJobController::class);
    
    // News management
    Route::resource('news', AdminNewsController::class);
    
    // Company management
    Route::resource('companies', AdminCompanyController::class);
    
    // Application management
    Route::get('/applications', [AdminApplicationController::class, 'index'])->name('applications.index');
    Route::get('/applications/{application}', [AdminApplicationController::class, 'show'])->name('applications.show');
    Route::patch('/applications/{application}/status', [AdminApplicationController::class, 'updateStatus'])->name('applications.updateStatus');
    
    // Alumni management
    Route::get('/alumni', [App\Http\Controllers\AlumniController::class, 'adminIndex'])->name('alumni.index');
    Route::get('/alumni/{alumni}', [App\Http\Controllers\AlumniController::class, 'adminShow'])->name('alumni.show');
});

// Alumni routes (using alumni guard)
Route::middleware('auth:alumni')->prefix('alumni')->name('alumni.')->group(function () {
    Route::get('/dashboard', [AlumniDashboardController::class, 'index'])->name('dashboard');
    Route::get('/applications', [AlumniDashboardController::class, 'applications'])->name('applications');
    Route::get('/profile', [AlumniDashboardController::class, 'profile'])->name('profile');
    Route::put('/profile', [AlumniDashboardController::class, 'updateProfile'])->name('profile.update');
});


