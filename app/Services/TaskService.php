<?php

namespace App\Services;

use App\Models\Project;
use App\Models\User;
use App\Models\Task;


class TaskService
{
    /**
     * タスクを作成する
     *
     * @param array $data タスク作成データ（title, description）
     * @param Project $project 作成者
     * @param User $user 作成者
     * @return Task
     */
    public function createTask(Project $project, array $data, User $user): Task
    {
        // タスク作成
        $task = Task::create([
            'project_id' => $project->id,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'status' => 'todo',
            'created_by' => $user->id,
        ]);

        // リレーションをロード
        $task->load('createdBy');

        return $task;
    }

    // 他のメソッドもここに記載
    /**
     * タスクを更新する
     *
     * @param Task $task 更新対象のタスク
     * @param array $data タスク更新データ（title, description, status）
     * @param User $user 実行者
     * @return Task
     */
    public function updateTask(Task $task, array $data, User $user): Task
    {
        // 自分が所属しているかチェック（users()リレーションを使用）
        $project = $task->project;
        $isMember = $project->users()
            ->where('users.id', $user->id)
            ->exists();

        if (!$isMember) {
            abort(403, 'このプロジェクトにアクセスする権限がありません');
        }

        $task->update($data);
        $task->load('createdBy');

        return $task;
    }

    /**
     * タスクを開始する（todo → doing）
     *
     * @param Task $task 開始するタスク
     * @param User $user 実行者
     * @return Task
     */
    public function startTask(Task $task, User $user): Task
    {
        // 自分が所属しているかチェック（users()リレーションを使用）
        $project = $task->project;
        $isMember = $project->users()
            ->where('users.id', $user->id)
            ->exists();

        if (!$isMember) {
            abort(403, 'このプロジェクトにアクセスする権限がありません');
        }

        // 状態チェック
        if ($task->status !== 'todo') {
            abort(409, '未着手のタスクのみ開始できます');
        }

        $task->update(['status' => 'doing']);
        $task->load('createdBy');

        return $task;
    }

    /**
     * タスクを完了する（doing → done）
     *
     * @param Task $task 完了するタスク
     * @param User $user 実行者
     * @return Task
     */
    public function completeTask(Task $task, User $user): Task
    {
        // 自分が所属しているかチェック（users()リレーションを使用）
        $project = $task->project;
        $isMember = $project->users()
            ->where('users.id', $user->id)
            ->exists();

        if (!$isMember) {
            abort(403, 'このプロジェクトにアクセスする権限がありません');
        }

        // 状態チェック
        if ($task->status !== 'doing') {
            abort(409, '作業中のタスクのみ完了できます');
        }

        $task->update(['status' => 'done']);
        $task->load('createdBy');

        return $task;
    }
    // privateメソッドも定義
    /**
     * ユーザーがプロジェクトメンバーかチェック
     * 権限がない場合は403エラーを投げる
     *
     * @param Project $project
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    private function getProjectMember(Project $project, User $user)
    {
        return $project->users()
            ->where('users.id', $user->id)
            ->first();
    }
}
