<?php

use App\Http\Controllers\Api\MembershipController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// 認証済みユーザー情報を取得
Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

// 認証が必要なAPI
Route::middleware(['auth:sanctum'])->group(function () {
    // Projects
    Route::get('/projects', [ProjectController::class, 'index']);           // プロジェクト一覧取得
    Route::post('/projects', [ProjectController::class, 'store']);          // プロジェクト作成
    Route::get('/projects/{project}', [ProjectController::class, 'show']);  // プロジェクト詳細取得
    Route::put('/projects/{project}', [ProjectController::class, 'update']); // プロジェクト更新
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy']); // プロジェクト削除

    // Project Members
    Route::post('/projects/{project}/members', [MembershipController::class, 'store']); // プロジェクト内のメンバー追加

    // Tasks
    Route::get('/projects/{project}/tasks', [TaskController::class, 'index']);  // プロジェクト内のタスク一覧取得
    Route::post('/projects/{project}/tasks', [TaskController::class, 'store']); // プロジェクト内にタスク作成
    Route::get('/tasks/{task}', [TaskController::class, 'show']);              // タスク詳細取得
    Route::put('/tasks/{task}', [TaskController::class, 'update']);            // タスク更新
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy']);        // タスク削除
    Route::get('/tasks', [TaskController::class, 'myTasks']);                  // 自分のタスク一覧取得
});
