<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\ProjectMemberController;
use App\Http\Controllers\Api\ProjectTaskController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // プロジェクトに関する操作
    Route::apiResource('projects', ProjectController::class)
        ->only(['index', 'store', 'show', 'update', 'destroy']);

    // プロジェクトメンバーに関する操作
    Route::post('projects/{project}/members', [ProjectMemberController::class, 'store']);
    Route::delete('projects/{project}/members/{member}', [ProjectMemberController::class, 'destroy']);

    // プロジェクトタスクに関する操作
    Route::get('projects/{project}/tasks', [ProjectTaskController::class, 'index']);
    Route::post('projects/{project}/tasks', [ProjectTaskController::class, 'store']);
    Route::get('projects/{project}/tasks/{task}', [ProjectTaskController::class, 'show']);
    Route::patch('projects/{project}/tasks/{task}', [ProjectTaskController::class, 'update']);
    Route::delete('projects/{project}/tasks/{task}', [ProjectTaskController::class, 'destroy']);

    Route::post('projects/{project}/tasks/{task}/start', [ProjectTaskController::class, 'start']);
    Route::post('projects/{project}/tasks/{task}/complete', [ProjectTaskController::class, 'complete']);

    // ユーザーが持つプロジェクトのタスク関する操作
    Route::get('users/{user}/projects/tasks', [ProjectTaskController::class, 'getUserAllTasks']);
});
