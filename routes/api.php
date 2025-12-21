<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// 認証済みユーザー情報を取得
Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});
