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
use App\Http\Controllers\Admin\NisnImportController;
use App\Http\Controllers\Alumni\AlumniDashboardController;
use App\Http\Controllers\Alumni\CVController;
use App\Http\Controllers\Admin\AdminAlumniController;
use App\Http\Controllers\Company\CompanyDashboardController;
use Illuminate\Support\Facades\Auth; 

// Public routes
// Fonnte Webhook endpoint
Route::match(['get', 'post'], '/fonnte/webhook', [WebhookController::class, 'handle']);
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/about', function() {
    return view('about');
})->name('about');

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


// Jobs routes
Route::prefix('jobs')->name('jobs.')->group(function () {
    Route::get('/', [JobController::class, 'indexWeb'])->name('index');
    Route::get('/{job}', [JobController::class, 'showWeb'])->name('show');
    
    // Apply job route for authenticated alumni
    Route::middleware(['auth:alumni'])->group(function () {
        Route::post('/{id}/apply', [JobController::class, 'applyWeb'])->name('apply');
    Route::post('/{job}/save', [JobController::class, 'save'])->name('save');
    Route::delete('/{job}/save', [JobController::class, 'unsave'])->name('unsave');
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
    // NISN import
    Route::get('/nisn/import', [NisnImportController::class, 'form'])->name('nisn.import');
    Route::post('/nisn/import', [NisnImportController::class, 'upload'])->name('nisn.upload');
    
    // Job management
    Route::resource('jobs', AdminJobController::class);
    Route::patch('/jobs/{job}/archive', [AdminJobController::class, 'archive'])->name('jobs.archive');
    Route::patch('/jobs/{job}/reactivate', [AdminJobController::class, 'reactivate'])->name('jobs.reactivate');
    Route::post('/jobs/bulk-archive', [AdminJobController::class, 'bulkArchive'])->name('jobs.bulk-archive');
    Route::post('/jobs/bulk-reactivate', [AdminJobController::class, 'bulkReactivate'])->name('jobs.bulk-reactivate');
    
    // News management
    Route::resource('news', AdminNewsController::class);
    
    // Company management
    Route::resource('companies', AdminCompanyController::class);
    
    // Application management
    Route::get('/applications', [AdminApplicationController::class, 'index'])->name('applications.index');
    Route::get('/applications/{application}', [AdminApplicationController::class, 'show'])->name('applications.show');
    Route::patch('/applications/{application}/status', [AdminApplicationController::class, 'updateStatus'])->name('applications.updateStatus');
    
    // Alumni management (Admin)
    Route::get('/alumni', [AdminAlumniController::class, 'index'])->name('alumni.index');
    Route::get('/alumni/create', [AdminAlumniController::class, 'create'])->name('alumni.create');
    Route::post('/alumni', [AdminAlumniController::class, 'store'])->name('alumni.store');
    Route::post('/alumni/import', [AdminAlumniController::class, 'import'])->name('alumni.import');
    Route::get('/alumni/export', [AdminAlumniController::class, 'export'])->name('alumni.export');
    Route::get('/alumni/statistics', [AdminAlumniController::class, 'statistics'])->name('alumni.statistics');
    Route::post('/alumni/bulk', [AdminAlumniController::class, 'bulkAction'])->name('alumni.bulk');
    Route::get('/alumni/{alumni}', [AdminAlumniController::class, 'show'])->name('alumni.show');
    Route::get('/alumni/{alumni}/edit', [AdminAlumniController::class, 'edit'])->name('alumni.edit');
    Route::put('/alumni/{alumni}', [AdminAlumniController::class, 'update'])->name('alumni.update');
    Route::delete('/alumni/{alumni}', [AdminAlumniController::class, 'destroy'])->name('alumni.destroy');
    Route::post('/alumni/{alumni}/verify', [AdminAlumniController::class, 'verify'])->name('alumni.verify');
});

// Alumni routes (using alumni guard)
Route::middleware('auth:alumni')->prefix('alumni')->name('alumni.')->group(function () {
    Route::get('/dashboard', [AlumniDashboardController::class, 'index'])->name('dashboard');
    Route::get('/applications', [AlumniDashboardController::class, 'applications'])->name('applications');
    Route::get('/saved-jobs', [\App\Http\Controllers\Alumni\AlumniSavedJobController::class, 'index'])->name('saved-jobs');
    Route::get('/profile', [AlumniDashboardController::class, 'profile'])->name('profile');
    Route::put('/profile', [AlumniDashboardController::class, 'updateProfile'])->name('profile.update');

    // Application detail for web (session-based, used by modal)
    Route::get('/applications/{id}', [\App\Http\Controllers\AlumniController::class, 'getApplicationDetail'])->name('applications.detail');

    // CV management routes
    Route::prefix('cv')->name('cv.')->group(function () {
        Route::get('/', [CVController::class, 'index'])->name('index');
        Route::get('/create', [CVController::class, 'create'])->name('create');
        Route::post('/', [CVController::class, 'store'])->name('store');
        Route::get('/{id}', [CVController::class, 'show'])->name('show');
        Route::get('/{id}/preview', [CVController::class, 'preview'])->name('preview');
        Route::get('/{id}/download', [CVController::class, 'download'])->name('download');
        Route::delete('/{id}', [CVController::class, 'destroy'])->name('destroy');
        Route::patch('/{id}/default', [CVController::class, 'setAsDefault'])->name('set-default');
    });
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
    Route::get('/applications/{id}/detail', [CompanyDashboardController::class, 'getApplicationDetail'])->name('applications.detail');
    Route::get('/profile', [CompanyDashboardController::class, 'profile'])->name('profile');
    Route::put('/profile', [CompanyDashboardController::class, 'updateProfile'])->name('profile.update');
    Route::put('/applications/{id}/status', [CompanyDashboardController::class, 'updateApplicationStatus'])->name('applications.update-status');
});


