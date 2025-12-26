<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

});
