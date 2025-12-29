<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * プロジェクトのタスク一覧取得
     */
    public function index(Project $project)
    {
        abort(501, 'Not Implemented');
    }

    /**
     * プロジェクトにタスク作成
     */
    public function store(Project $project, Request $request)
    {
        abort(501, 'Not Implemented');
    }

    /**
     * プロジェクトのタスク詳細取得
     */
    public function show(Task $task)
    {
        abort(501, 'Not Implemented');
    }

    /**
     *  プロジェクトのタスク更新
     */
    public function update(Task $task ,Request $request)
    {
        abort(501, 'Not Implemented');
    }

    /**
     *  プロジェクトのタスク削除
     */
    public function destroy(Task $task)
    {
        abort(501, 'Not Implemented');
    }

    /**
     * プロジェクトのタスクを開始
     */
    public function updateToDo(Task $task)
    {
        abort(501, 'Not Implemented');
    }

    /**
     * プロジェクトのタスクを完了
     */
    public function updateDone(Task $task)
    {
        abort(501, 'Not Implemented');
    }

    /**
     * 自分のタスク一覧取得（自分が所属しているプロジェクトの全タスク）
     */
    public function getTasksforUser(User $user)
    {
        abort(501, 'Not Implemented');
    }
}
