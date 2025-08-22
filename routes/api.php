<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AlumniController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\JurusanController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DurasiKerjaController;
use App\Http\Controllers\JalurMasukKuliahController;
use App\Http\Controllers\JenisPerusahaanController;
use App\Http\Controllers\MasaTungguKerjaController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\RangeGajiController;
use App\Http\Controllers\WhatsAppController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('/admin/login', [AuthController::class, 'loginAdmin']); //1
Route::post('/alumni/login', [AuthController::class, 'loginAlumni']); //1
Route::post('/company/register', [CompanyController::class, 'register']); //1

Route::apiResource('admins', AdminController::class); // 5
Route::apiResource('jalur-masuk-kuliah', JalurMasukKuliahController::class);
Route::apiResource('masa-tunggu-kerja', MasaTungguKerjaController::class);
Route::apiResource('jenis-perusahaan', JenisPerusahaanController::class);
Route::apiResource('durasi-kerja', DurasiKerjaController::class);
Route::apiResource('range-gaji', RangeGajiController::class);

Route::controller(AlumniController::class)->group(function () {
    Route::get('/alumnis', 'getAll');
    Route::get('/alumnis/chart', 'getChart');
    Route::get('/alumnis/check-nisn-valid/{nisn}', 'checkNisnValid');
    Route::get('/alumnis/{alumni_id}', 'getDetail');
    Route::post('/check-email-exist', 'checkEmailExist');
    Route::post('/alumnis', 'create');
    Route::put('/alumnis/{alumni_id}', 'update');
    Route::delete('/alumnis/{alumni_id}', 'destroy');
    Route::post('/alumnis/import', 'importExcel');
});

Route::controller(JurusanController::class)->prefix('jurusans')->group(function () {
    Route::get('/', 'getAllJurusans');
    Route::post('/', 'createJurusan');
    Route::put('/{jurusan_id}', 'updateJurusan');
    Route::delete('/{jurusan_id}', 'deleteJurusan');
});

// Public routes
Route::controller(JobController::class)->prefix('jobs')->group(function () {
    Route::get('/', 'index');
    Route::get('/{id}', 'show');
});

Route::controller(CompanyController::class)->prefix('companies')->group(function () {
    Route::get('/', 'index');
});

// Public application detail route
Route::get('/applications/{id}', [CompanyController::class, 'getApplicationDetail']);

Route::controller(NewsController::class)->prefix('news')->group(function () {
    Route::get('/', 'index');
    Route::get('/latest', 'latest');
    Route::get('/{slug}', 'show');
});

// Protected routes for Alumni
Route::middleware(['auth:sanctum'])->group(function () {
    // Alumni job applications
    Route::controller(JobController::class)->prefix('jobs')->group(function () {
        Route::post('/{id}/apply', 'apply');
        Route::get('/my/applications', 'myApplications');
    });

    // Alumni application details
    Route::get('/alumni/applications/{id}', [AlumniController::class, 'getApplicationDetail']);

    // Admin dashboard and management
    Route::controller(AdminController::class)->prefix('admin')->group(function () {
        Route::get('/dashboard', 'dashboard');
        Route::get('/companies', 'companies');
        Route::get('/jobs', 'jobs');
        Route::get('/applications', 'applications');
        Route::post('/companies/{id}/approve', 'approveCompany');
    });

    // Company management
    Route::controller(CompanyController::class)->prefix('company')->group(function () {
        Route::get('/dashboard', 'dashboard');
        Route::get('/jobs', 'jobs');
        Route::post('/jobs', 'createJob');
        Route::put('/jobs/{id}', 'updateJob');
        Route::get('/applications', 'applications');
        Route::get('/applications/{id}', 'getApplicationDetail');
        Route::put('/applications/{id}/status', 'updateApplicationStatus');
    });

    // News management (Admin only)
    Route::controller(NewsController::class)->prefix('admin/news')->group(function () {
        Route::post('/', 'store');
        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });

    // WhatsApp notification management
    Route::controller(WhatsAppController::class)->prefix('whatsapp')->group(function () {
        // Alumni settings
        Route::get('/alumni/settings', 'getAlumniSettings')->middleware('auth:alumni');
        Route::put('/alumni/settings', 'updateAlumniSettings')->middleware('auth:alumni');

        // Company settings
        Route::get('/company/settings', 'getCompanySettings')->middleware('auth:company');
        Route::put('/company/settings', 'updateCompanySettings')->middleware('auth:company');

        // Admin functions
        Route::post('/bulk-send', 'sendBulkNotification')->middleware('auth:admin');
        Route::get('/stats', 'getNotificationStats')->middleware('auth:admin');
    });
});
