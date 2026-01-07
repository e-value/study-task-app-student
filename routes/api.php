<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\MembershipController;
use App\Http\Controllers\Api\TaskController;


Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

// プロジェクト
Route::apiResource('projects',ProjectController::class);//一覧、作成、詳細、更新、削除が可能


// プロジェクト下のメンバー
Route::post('projects/{project}/members',[MembershipController::class,'store']); //メンバー追加
Route::delete('projects/{project}/members/{user}',[MembershipController::class,'delete']); //メンバー削除

//プロジェクト下のタスク
Route::get('projects/{project}/tasks',[TaskController::class,'index']); //タスク一覧
Route::post('projects/{project}/tasks',[TaskController::class,'store']); //タスク作成

//タスク単体
Route::get('projects/{project}/tasks/{task}',[TaskController::class,'show']); //タスク詳細
Route::patch('projects/{project}/tasks/{task}',[TaskController::class,'update']); //タスク更新
Route::delete('projects/{project}/tasks/{task}',[TaskController::class,'destroy']); //タスク削除

Route::post('tasks/{task}/start',[TaskController::class,'start']); //タスク開始
Route::post('tasks/{task}/complete',[TaskController::class,'complete']); //タスク完了

// 全てのタスク
Route::get('me/tasks',[TaskController::class,'myTask']); //タスク一覧

