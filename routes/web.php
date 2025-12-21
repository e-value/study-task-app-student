<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;

// SPA用のエントリーポイント - すべてのルートでVueを返す
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');
