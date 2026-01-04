<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;

// 認証ルートを読み込む
require __DIR__ . '/auth.php';

// 研修用に必要に応じてコメントアウト
// SPA用のエントリーポイント - すべてのルートでVueを返す
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');

// 研修用に必要に応じてコメントアウト
Route::get('/test', function () {
    throw new \Exception('テストエラー');
});
