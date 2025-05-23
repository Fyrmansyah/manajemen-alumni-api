<?php

use App\Http\Controllers\AlumniController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\JurusanController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);
Route::get('/alumnis', [AlumniController::class, 'getAll']);
Route::get('/alumnis/{alumni}', [AlumniController::class, 'getDetail']);

Route::controller(JurusanController::class)->prefix('jurusans')->group(function () {
    Route::get('/', 'getAllJurusans');
    Route::post('/', 'createJurusan');
    Route::put('/{jurusan}', 'updateJurusan');
    Route::put('/{jurusan}', 'deleteJurusan');
});
