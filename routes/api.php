<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\TeacherController;
use App\Http\Controllers\Api\ResultController;

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

    /*
    |--------------------------------------------------------------------------
    | Auth
    |--------------------------------------------------------------------------
    */

    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);


    /*
    |--------------------------------------------------------------------------
    | Teacher Management
    |--------------------------------------------------------------------------
    */

    Route::post('/teachers', [TeacherController::class, 'store']);

    Route::put(
        '/teachers/{teacher}',
        [TeacherController::class, 'update']
    );

    Route::patch(
        '/teachers/{teacher}',
        [TeacherController::class, 'update']
    );

    Route::delete(
        '/teachers/{teacher}',
        [TeacherController::class, 'destroy']
    );


    /*
    |--------------------------------------------------------------------------
    | Student Management
    |--------------------------------------------------------------------------
    */

    Route::apiResource(
        'students',
        StudentController::class
    );


    /*
    |--------------------------------------------------------------------------
    | Result Management
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/results',
        [ResultController::class, 'index']
    );

    Route::post(
        '/results',
        [ResultController::class, 'store']
    );

    Route::put(
        '/results/{result}',
        [ResultController::class, 'update']
    );

    Route::patch(
        '/results/{result}',
        [ResultController::class, 'update']
    );

    Route::delete(
        '/results/{result}',
        [ResultController::class, 'destroy']
    );
});