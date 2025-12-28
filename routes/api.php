<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

#プロジェクト一覧取得
Route::get('/projects', 'ProjectsController@index');

#プロジェクト作成
Route::post('/projects', 'ProjectsController@store');

#プロジェクト詳細取得
Route::get('/projects/{project}', 'ProjectsController@show');

#プロジェクト更新
Route::put('/tasks/{project}', 'ProjectsController@update');

#プロジェクト削除
Route::delete('/projects/{project}', 'ProjectsController@destroy');

#プロジェクト内のメンバー追加
Route::post('/projects/{project}/members', 'MembershipsController@store');

#プロジェクト内のタスク一覧取得
Route::get('/projects/{project}/tasks', 'TasksController@index');

#プロジェクト内にタスク作成
Route::post('/projects/{project}/tasks', 'TasksController@store');

#プロジェクトのタスク詳細取得
Route::get('/tasks/{task}', 'TasksController@show');

#プロジェクトのタスク更新
Route::put('/tasks/{task}', 'TasksController@update');

#プロジェクトのタスク削除
Route::delete('/tasks/{task}', 'TasksController@destroy');  

#プロジェクトのタスクを開始
Route::post('/tasks/{task}/start', 'TasksController@start');

#プロジェクトのタスクを完了
Route::post('/tasks/{task}/complete', 'TasksController@complete');

#自分のタスク一覧取得
Route::get('/me/tasks', 'TasksController@index');