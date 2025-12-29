<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\TaskController;


/**
 * Lesson1: API エンドポイント設計
 * 
 * TODO: 以下の機能を実現するエンドポイントを設計してください
 * 
 * 【必要な機能】
 * - プロジェクト一覧取得
 * - プロジェクト作成
 * - プロジェクト詳細取得
 * - プロジェクト更新
 * - プロジェクト削除
 * 
 * - プロジェクト内のタスク一覧取得
 * - プロジェクト内にタスク作成
 * 
 * - タスク詳細取得
 * - タスク更新
 * - タスク削除
 * 
 * - 自分のタスク一覧取得
 * 
 * 【考えるべきポイント】
 * 1. RESTful な URL 設計になっているか？
 * 2. HTTP メソッド（GET/POST/PATCH/DELETE）を適切に使い分けているか？
 * 3. リソースの階層構造を正しく表現できているか？
 * 4. どの Controller に実装すべきか？（責務分離）
 */

// 認証済みユーザー情報を取得
Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

// 認証が必要な API
Route::middleware(['auth:sanctum'])->group(function () {

    //Route::apiResource('projects', ProjectController::class);
    Route::get('/projects', [ProjectController::class, 'index']); // プロジェクト一覧取得
    Route::post('/projects', [ProjectController::class, 'store']); // プロジェクト作成
    Route::get('/projects/{project}', [ProjectController::class, 'show']); // プロジェクト詳細取得
    Route::put('/projects/{project}', [ProjectController::class, 'update']); //プロジェクト更新
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy']); //プロジェクト削除

    Route::post('/projects/{project}/uses/(user)', [ProjectController::class, 'storeProjectMember']); //プロジェクトのメンバー追加
    Route::delete('/projects/{project}/uses/(user)', [ProjectController::class, 'deleteProjectMember']); //プロジェクトのメンバー削除

    Route::get('/projects/{project}/tasks', [TaskController::class, 'index']); //プロジェクト内のタスク一覧取得
    Route::post('/projects/{project}/tasks', [TaskController::class, 'store']); //プロジェクトにタスク作成
    Route::get('/tasks/{task}', [TaskController::class, 'show']); // プロジェクトのタスク詳細取得
    Route::put('/tasks/{task}', [TaskController::class, 'update']); //プロジェクトのタスク更新
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy']); //プロジェクトのタスク削除
    
    Route::put('/tasks/{task}/update-todo', [TaskController::class, 'updateToDo']); //プロジェクトのタスクを開始
    Route::put('/tasks/{task}/update-done', [TaskController::class, 'updateDone']); //プロジェクトのタスクを完了
    
    Route::get('users/{user}/tasks',[TaskController::class, 'getTasksforUser']);// 自分のタスク一覧取得（自分が所属しているプロジェクトの全タスク）

});

