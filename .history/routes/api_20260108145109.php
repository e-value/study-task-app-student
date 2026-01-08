<?php

use App\Http\Controllers\Api\ProjectMemberController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// 認証済みユーザー情報を取得
Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

// 私の作成したものと比較して確認済みです。
// 違った部分→コントローラーの命名やHTTPメソッドの名前
// 認証が必要なAPI
Route::middleware(['auth:sanctum'])->group(function () {
    // Projects
    Route::get('/projects', [ProjectController::class, 'index']);           // プロジェクト一覧取得
    Route::post('/projects', [ProjectController::class, 'store']);          // プロジェクト作成
    Route::get('/projects/{project}', [ProjectController::class, 'show']);  // プロジェクト詳細取得
    Route::put('/projects/{project}', [ProjectController::class, 'update']); // プロジェクト更新
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy']); // プロジェクト削除

    // Tasks
    Route::get('/projects/{project}/tasks', [TaskController::class, 'index']);
    Route::post('/projects/{project}/tasks', [TaskController::class, 'store']);
    Route::get('/tasks/{task}', [TaskController::class, 'show']);
    Route::put('/tasks/{task}', [TaskController::class, 'update']);
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy']);
    Route::post('/tasks/{task}/start', [TaskController::class, 'start']);
    Route::post('/tasks/{task}/complete', [TaskController::class, 'complete']);

    // Members
    Route::get('/projects/{project}/members', [ProjectMemberController::class, 'index']);
    Route::post('/projects/{project}/members', [ProjectMemberController::class, 'store']); // プロジェクト内のメンバー追加
    Route::delete('/projects/{project}/members/{user}', [ProjectMemberController::class, 'destroy']);
});
