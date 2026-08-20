<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\TeacherController;
use App\Http\Controllers\Api\ResultController;
use App\Http\Controllers\Api\RoutineController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\NoticeController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Public Teacher Routes
Route::get('/teachers', [TeacherController::class, 'index']);
Route::get('/teachers/{teacher}', [TeacherController::class, 'show']);

// Public Result Routes
Route::get('/results/options', [ResultController::class, 'options']);
Route::get('/results/search', [ResultController::class, 'search']);

// Public Routine Routes
Route::get('/routines/options', [RoutineController::class, 'options']);
Route::get('/routines/search', [RoutineController::class, 'search']);
Route::get('/routines', [RoutineController::class, 'index']);

// Public Notice Routes
Route::get('/notices', [NoticeController::class, 'index']);
Route::get('/notices/{id}', [NoticeController::class, 'show']);

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

Route::post('/auth/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Protected Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // Dashboard Stats
    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);

    // Teacher Management
    Route::post('/teachers', [TeacherController::class, 'store']);
    Route::put('/teachers/{teacher}', [TeacherController::class, 'update']);
    Route::patch('/teachers/{teacher}', [TeacherController::class, 'update']);
    Route::delete('/teachers/{teacher}', [TeacherController::class, 'destroy']);

    // Student Management
    Route::apiResource('students', StudentController::class);

    // Result Management
    Route::get('/results', [ResultController::class, 'index']);
    Route::post('/results', [ResultController::class, 'store']);
    Route::put('/results/{result}', [ResultController::class, 'update']);
    Route::patch('/results/{result}', [ResultController::class, 'update']);
    Route::delete('/results/{result}', [ResultController::class, 'destroy']);

    // Routine Management
    Route::post('/routines', [RoutineController::class, 'store']);
    Route::put('/routines/{routine}', [RoutineController::class, 'update']);
    Route::patch('/routines/{routine}', [RoutineController::class, 'update']);
    Route::delete('/routines/{routine}', [RoutineController::class, 'destroy']);

    // Notice Management
   // Public Notice Routes
Route::get('/notices', [NoticeController::class, 'index']);
Route::get('/notices/{id}/download', [NoticeController::class, 'download']);
Route::get('/notices/{id}', [NoticeController::class, 'show']);
});