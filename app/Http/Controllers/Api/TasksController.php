<?php

namespace App\Http\Controllers\Api;

use App\Models\Task;
use App\Models\Project; 
use Illuminate\Http\Request;

class TasksController extends Controller
{
    #プロジェクトのタスク一覧取得
    public function index(Request $request, Project $project)
    {
        abort(501, 'Not Implemented');
    }

    #プロジェクトにタスク作成
    public function create(Request $request, Project $project)
    {
        abort(501, 'Not Implemented');
    }

    #プロジェクトのタスク詳細取得
    public function store(Request $request, Project $project, Task $task)
    {
        abort(501, 'Not Implemented');
    }

    #プロジェクトのタスクを削除
    public function delete(Request $request, Project $project, Task $task)
    {
        abort(501, 'Not Implemented');
    }
    
    #プロジェクトのタスクを更新
    public function update(Request $request, Project $project, Task $task)
    {
        abort(501, 'Not Implemented');
    }

    #プロジェクトのタスクを開始
    public function start(Request $request, Project $project, Task $task)
    {
        abort(501, 'Not Implemented');
    }

    #プロジェクトのタスクを完了
    public function complete(Request $request, Project $project, Task $task)
    {
        abort(501, 'Not Implemented');
    }
}