<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Resources\ProjectsResource;
use App\Http\Resources\TasksResource;
use App\Http\Resources\TaskResource;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

#プロジェクト一覧取得
Route::get('/projects', function () {
    return ProjectsResource::collection(Project::all());
});

#プロジェクト作成
Route::post('/projects', 'ProjectsController@store');

#プロジェクト詳細取得
Route::get('/projects/{project}', function (Project $project) {
    return new ProjectsResource($project);
});

#プロジェクト更新
Route::put('/projects/{project}', 'ProjectsController@update');

#プロジェクト削除
Route::delete('/projects/{project}', 'ProjectsController@destroy');

#プロジェクト内のメンバー追加
Route::post('/projects/{project}/members', 'MembershipsController@store');

#プロジェクト内のタスク一覧取得
Route::get('/projects/{project}/tasks', function (Project $project) {
    return TasksResource::collection($project->tasks);
});

#プロジェクト内のメンバー削除
Route::delete('/projects/{project}/members', 'MembershipsController@destroy');

#プロジェクト内にタスク作成
Route::post('/projects/{project}/tasks', 'TasksController@store');

#プロジェクトのタスク詳細取得
Route::get('/tasks/{task}', function (Task $task) {
    return new TasksResource($task);
});

#プロジェクトのタスク削除
Route::delete('/tasks/{task}', 'TasksController@destroy');  

#プロジェクトのタスク更新
Route::put('/tasks/{task}', 'TasksController@update');

#プロジェクトのタスクを開始
Route::post('/tasks/{task}/start', 'TasksController@start');

#プロジェクトのタスクを完了
Route::post('/tasks/{task}/complete', 'TasksController@complete');

#自分のタスク一覧取得
Route::get('/me/tasks', function (Request $request) {
    $user = $request->user();
    return TaskResource::collection($user->createdTasks);
});