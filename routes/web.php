<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebhookController;
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
use Illuminate\Support\Facades\Auth; 

// Public routes
// Fonnte Webhook endpoint
Route::match(['get', 'post'], '/fonnte/webhook', [WebhookController::class, 'handle']);
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

// Alternative logout route using GET method as backup
Route::get('/logout-alt', [BaseAuthController::class, 'logout'])->name('logout.alt');

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

// Debug admin authentication
Route::get('/debug-admin', function() {
    $admin = \App\Models\Admin::where('username', 'admin')->first();
    
    if (!$admin) {
        return ['error' => 'Admin not found'];
    }
    
    // Test admin login
    $loginResult = Auth::guard('admin')->attempt([
        'username' => 'admin',
        'password' => 'admin123'
    ]);
    
    return [
        'admin_exists' => true,
        'admin_data' => [
            'id' => $admin->id,
            'username' => $admin->username,
            'password_length' => strlen($admin->password),
            'password_starts_with' => substr($admin->password, 0, 7)
        ],
        'login_attempt_result' => $loginResult,
        'auth_check_after_login' => Auth::guard('admin')->check(),
        'admin_user_after_login' => Auth::guard('admin')->user()
    ];
});

// Debug login process
Route::get('/debug-login', function() {
    $loginField = 'admin';
    $password = 'admin123';
    
    // Test admin login step by step
    $adminAttempt = Auth::guard('admin')->attempt(['username' => $loginField, 'password' => $password]);
    
    return [
        'login_field' => $loginField,
        'password' => $password,
        'admin_attempt_result' => $adminAttempt,
        'admin_auth_check' => Auth::guard('admin')->check(),
        'admin_user' => Auth::guard('admin')->user(),
        'session_data' => session()->all()
    ];
});

// Test login form submission
Route::post('/test-login', function(\Illuminate\Http\Request $request) {
    $loginField = $request->input('email');
    $password = $request->input('password');
    $remember = $request->filled('remember');
    
    // Try admin login (username)
    if (Auth::guard('admin')->attempt(['username' => $loginField, 'password' => $password], $remember)) {
        $request->session()->regenerate();
        return redirect()->intended('/admin/dashboard');
    }

    // Try alumni login (email)
    if (Auth::guard('alumni')->attempt(['email' => $loginField, 'password' => $password], $remember)) {
        $request->session()->regenerate();
        return redirect()->intended('/alumni/dashboard');
    }

    // Try company login (email)
    if (Auth::guard('company')->attempt(['email' => $loginField, 'password' => $password], $remember)) {
        $request->session()->regenerate();
        return redirect()->intended('/company/dashboard');
    }

    return back()->withErrors([
        'email' => 'Kredensial tidak valid.',
    ])->onlyInput('email');
});

// Jobs routes
Route::prefix('jobs')->name('jobs.')->group(function () {
    Route::get('/', [JobController::class, 'indexWeb'])->name('index');
    Route::get('/{job}', [JobController::class, 'showWeb'])->name('show');
    
    // Apply job route for authenticated alumni
    Route::middleware(['auth:alumni'])->group(function () {
        Route::post('/{id}/apply', [JobController::class, 'applyWeb'])->name('apply');
    });
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
    
    // CV routes
    Route::resource('cv', \App\Http\Controllers\Alumni\CVController::class);
    Route::get('/cv/{cv}/download', [\App\Http\Controllers\Alumni\CVController::class, 'download'])->name('cv.download');
    Route::patch('/cv/{cv}/default', [\App\Http\Controllers\Alumni\CVController::class, 'setAsDefault'])->name('cv.default');
});

// Company routes (using company guard)
Route::middleware('auth:company')->prefix('company')->name('company.')->group(function () {
    Route::get('/dashboard', [CompanyDashboardController::class, 'index'])->name('dashboard');
    Route::get('/jobs', function (\Illuminate\Http\Request $request) {
        if ($request->query('action') === 'create') {
            return redirect()->route('company.jobs.create');
        }
        if ($request->query('edit')) {
            return redirect()->route('company.jobs.edit', $request->query('edit'));
        }
        return app(\App\Http\Controllers\Company\CompanyDashboardController::class)->jobs($request);
    })->name('jobs');
    Route::get('/jobs/manage', [CompanyDashboardController::class, 'manageJobs'])->name('jobs.manage');
    Route::get('/jobs/create', [CompanyDashboardController::class, 'createJobForm'])->name('jobs.create');
    Route::post('/jobs', [CompanyDashboardController::class, 'createJob'])->name('jobs.createJob');
    Route::get('/jobs/{id}/edit', [CompanyDashboardController::class, 'editJobForm'])->name('jobs.edit');
    Route::put('/jobs/{id}', [CompanyDashboardController::class, 'updateJob'])->name('jobs.update');
    Route::delete('/jobs/{id}', [CompanyDashboardController::class, 'deleteJob'])->name('jobs.delete');
    Route::patch('/jobs/{id}/close', [CompanyDashboardController::class, 'closeJob'])->name('jobs.close');
    Route::get('/applications', [CompanyDashboardController::class, 'applications'])->name('applications');
    Route::get('/profile', [CompanyDashboardController::class, 'profile'])->name('profile');
    Route::put('/profile', [CompanyDashboardController::class, 'updateProfile'])->name('profile.update');
    Route::put('/applications/{id}/status', [CompanyDashboardController::class, 'updateApplicationStatus'])->name('applications.update-status');
});

// Debug session restoration
Route::get('/debug-session', function() {
    // Get the session data->
    $sessionData = session()->all();
    
    // Try to manually login admin with ID 6
    $admin = \App\Models\Admin::find(6);
    
    if (!$admin) {
        return ['error' => 'Admin with ID 6 not found'];
    }
    
    // Manually login the admin
    Auth::guard('admin')->login($admin);
    
    return [
        'session_data' => $sessionData,
        'admin_exists' => $admin ? true : false,
        'admin_data' => $admin ? [
            'id' => $admin->id,
            'username' => $admin->username
        ] : null,
        'auth_check_before_login' => Auth::guard('admin')->check(),
        'auth_check_after_login' => Auth::guard('admin')->check(),
        'admin_user_after_login' => Auth::guard('admin')->user()
    ];
});

// Simple login test
Route::get('/simple-login-test', function() {
    // Clear any existing auth
    Auth::guard('admin')->logout();
    Auth::guard('alumni')->logout();
    Auth::guard('company')->logout();
    
    // Try to login admin
    $result = Auth::guard('admin')->attempt([
        'username' => 'admin',
        'password' => 'admin123'
    ]);
    
    return [
        'login_attempt_result' => $result,
        'auth_check_immediately' => Auth::guard('admin')->check(),
        'admin_user' => Auth::guard('admin')->user(),
        'session_id' => session()->getId(),
        'session_data' => session()->all()
    ];
});


