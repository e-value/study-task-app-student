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

    // TODO: ここにエンドポイントを定義してください
    Route::group(['prefix'=>'projects', 'as'=>'projects.'],function(){
        // プロジェクト一覧取得
        Route::get('/',[ProjectController::class,'index'])->name('index');
        // プロジェクト作成
        Route::post('/',[ProjectController::class,'store'])->name('store');
        // プロジェクト詳細取得
        Route::get('{project}',[ProjectController::class,'show'])->name('show');
        // プロジェクト更新
        Route::put('{project}',[ProjectController::class,'update'])->name('update');
        // プロジェクト削除
        Route::delete('{project}',[ProjectController::class,'destroy'])->name('destroy');

        Route::group(['prefix'=>'{project}/tasks', 'as'=>'tasks.'],function(){
            // プロジェクト内のタスク一覧取得
            Route::get('/',[TaskController::class,'index'])->name('index');
            // プロジェクト内にタスク作成
            Route::post('/',[TaskController::class,'store'])->name('store');
            // タスク詳細取得
            Route::get('{task}',[TaskController::class,'show'])->name('show');
            // タスク更新
            Route::put('{task}',[TaskController::class,'update'])->name('update');
            // タスク削除
            Route::delete('{task}',[TaskController::class,'destroy'])->name('destroy');
            // 自分のタスク一覧取得
            Route::get('own',[TaskController::class,'index'])->name('own'); # ルートの名前で条件分岐
        });
    });
    


});
