<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;

// 認証ルートを読み込む
require __DIR__ . '/auth.php';

// SPA用のエントリーポイント - すべてのルートでVueを返す
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');
