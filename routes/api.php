<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AlumniController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\JurusanController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('/admin/login', [AuthController::class, 'loginAdmin']); //1
Route::post('/alumni/login', [AuthController::class, 'loginAlumni']); //1

Route::apiResource('admins', AdminController::class); // 5

Route::controller(AlumniController::class)->group(function () {
    Route::get('/alumnis', 'getAll');
    Route::get('/alumnis/chart', 'getChart');
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
