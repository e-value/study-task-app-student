<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});


#プロジェクトの一覧取得
Route::get('/projects', [ProjectController::class, 'index']);

#プロジェクトの作成
Route::post('/projects', [ProjectController::class, 'create']);

#プロジェクトの詳細取得
Route::get('/projects/{project}', [ProjectController::class, 'store']);

#プロジェクトの更新
Route::put('/projects/{project}', [ProjectController::class, 'update']);

#プロジェクトの削除
Route::delete('/projects/{project}',  [ProjectController::class, 'delete']);

#プロジェクトのメンバー追加
Route::post('/projects/{project}/members', [MembershipsController::class,'add']);

#プロジェクトのタスク一覧取得
Route::get('/projects/{project}/tasks', [TasksController::class,'index']);

#プロジェクトのメンバーを削除
Route::delete('/projects/{project}/members', [MembershipsController::class,'delete']);

#プロジェクトにタスク作成
Route::post('/projects/{project}/tasks', [TasksController::class, 'create']);

#プロジェクトのタスク詳細取得
Route::get('/projects/{project}/tasks/{task}', [TasksController::class, 'store']);

#プロジェクトのタスクを削除
Route::delete('/projects/{project}/tasks/{task}', [TasksController::class,'delete']);

#プロジェクトのタスクを更新
Route::put('/projects/{project}/tasks/{task}', [TasksController::class,'update']);

#プロジェクトのタスクを開始
Route::post('/projects/{project}/tasks/{task}/start',  [TasksController::class,'start']);

#プロジェクトのタスクを完了
Route::post('/projects/{project}/tasks/{task}/complete',  [TasksController::class,'complete']);

#自分のタスク一覧取得（自分が所属しているプロジェクトの全タスク）
Route::get('/me/tasks', [TaskController::class,'index']);

#httpメソッド
#get:情報取得
#pot:情報送信・作成
#put:更新・置換
#delete:削除
#patch:部分更新

#一覧＊index
#詳細*store
#作成*create
#削除*delete
#追加*add
#開始*start
#完了*complete


#【質問】
# ① putとpatchの違いは？どのタイミングで使用するのか。
# ② URLは長くてわかりやすいのか、短くて少しわかりずらいのがよいのか？
#   ※最初の実装と2回目の実装でURLを迷って変えた。