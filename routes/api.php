<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\ProjectController;
use App\Http\Controllers\api\StatusController;
use App\Http\Controllers\api\TaskController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/user', [AuthController::class, 'user']);

    Route::apiResource('projects',ProjectController::class);

    Route::apiResource('statuses', StatusController::class);


    Route::apiResource('tasks',TaskController::class);

    Route::put('/tasks/{id}/position', [TaskController::class, 'updatePosition']);
});
