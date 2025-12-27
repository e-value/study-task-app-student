<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * プロジェクト内のタスク一覧取得
     */
    public function index(Request $request, Project $project)
    {
        abort(501, 'Not Implemented');
    }

    /**
     * プロジェクト内にタスク作成
     */
    public function store(Request $request, Project $project)
    {
        abort(501, 'Not Implemented');
    }

    /**
     * タスク詳細取得
     */
    public function show(Request $request, Task $task)
    {
        abort(501, 'Not Implemented');
    }

    /**
     * タスク更新
     */
    public function update(Request $request, Task $task)
    {
        abort(501, 'Not Implemented');
    }

    /**
     * タスク削除
     */
    public function destroy(Request $request, Task $task)
    {
        abort(501, 'Not Implemented');
    }

    /**
     * 自分のタスク一覧取得（自分が所属しているプロジェクトの全タスク）
     */
    public function myTasks(Request $request)
    {
        abort(501, 'Not Implemented');
    }
}
