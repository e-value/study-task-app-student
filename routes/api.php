<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\MemberController;
use App\Http\Controllers\Api\TaskController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // プロジェクトに関する操作
    Route::apiResource('projects', ProjectController::class)
        ->only(['index', 'store', 'show', 'update', 'destroy']);

    // メンバーに関する操作
    Route::post('projects/{project}/members', [MemberController::class, 'store']);
    Route::delete('projects/{project}/members/{member}', [MemberController::class, 'destroy']);

    // タスクに関する操作
    Route::get('projects/{project}/tasks', [TaskController::class, 'index']);
    Route::post('projects/{project}/tasks', [TaskController::class, 'store']);
    Route::get('projects/{project}/tasks/{task}', [TaskController::class, 'show']);
    Route::patch('projects/{project}/tasks/{task}', [TaskController::class, 'update']);
    Route::delete('projects/{project}/tasks/{task}', [TaskController::class, 'destroy']);

    Route::post('projects/{project}/tasks/{task}/start', [TaskController::class, 'start']);
    Route::post('projects/{project}/tasks/{task}/complete', [TaskController::class, 'complete']);

    // 自分のタスクに関する操作
    Route::get('users/{user}/projects/tasks', [TaskController::class, 'getUserAllTasks']);
});
