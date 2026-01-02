<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;

class TaskService
{
    /**
     * タスクを作成する
     *
     * @param array $data タスク作成データ（title, description）
     * @param Project $project プロジェクト
     * @param User $user 作成者
     * @return Task
     */
    public function createTask(array $data, Project $project, User $user): Task
    {
        $task = Task::create([
            'project_id' => $project->id,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'status' => 'todo',
            'created_by' => $user->id,
        ]);

        $task->load('createdBy');

        return $task;
    }

    /**
     * タスクを更新する
     *
     * @param Task $task タスク
     * @param array $data 更新データ（title, description, status）
     * @return Task
     */
    public function updateTask(Task $task, array $data): Task
    {
        $task->update($data);
        $task->load('createdBy');

        return $task;
    }

    /**
     * タスクを削除する
     *
     * @param Task $task タスク
     * @return void
     */
    public function deleteTask(Task $task): void
    {
        $task->delete();
    }

    /**
     * タスクを開始する（todo → doing）
     *
     * @param Task $task タスク
     * @return Task
     */
    public function startTask(Task $task): Task
    {
        if ($task->status !== 'todo') {
            throw new \Exception('未着手のタスクのみ開始できます');
        }

        $task->update(['status' => 'doing']);
        $task->load('createdBy');

        return $task;
    }

    /**
     * タスクを完了する（doing → done）
     *
     * @param Task $task タスク
     * @return Task
     */
    public function completeTask(Task $task): Task
    {
        if ($task->status !== 'doing') {
            throw new \Exception('作業中のタスクのみ完了できます');
        }

        $task->update(['status' => 'done']);
        $task->load('createdBy');

        return $task;
    }

    /**
     * プロジェクトのメンバーかチェック
     *
     * @param Project $project プロジェクト
     * @param User $user ユーザー
     * @return bool
     */
    public function isProjectMember(Project $project, User $user): bool
    {
        return $project->users()
            ->where('users.id', $user->id)
            ->exists();
    }
}

