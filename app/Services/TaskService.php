<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use App\Exceptions\ConflictException;

class TaskService
{
    /**
     * プロジェクトのタスク一覧を取得
     *
     * @param Project $project プロジェクト
     * @param User $user ユーザー
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTasks(Project $project, User $user): \Illuminate\Database\Eloquent\Collection
    {
        // メンバーかチェック
        if (!$this->isProjectMember($project, $user)) {
            throw new AuthorizationException('このプロジェクトにアクセスする権限がありません');
        }

        return $project->tasks()
            ->with('createdBy')
            ->orderBy('created_at', 'desc')
            ->get();
    }

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
        // プロジェクトメンバーかチェック
        if (!$this->isProjectMember($project, $user)) {
            throw new AuthorizationException('このプロジェクトのタスクを作成する権限がありません');
        }

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
     * タスク詳細を取得
     *
     * @param Task $task タスク
     * @param User $user ユーザー
     * @return Task
     */
    public function getTask(Task $task, User $user): Task
    {
        // 権限チェック
        $this->checkTaskPermission($task, $user);

        // リレーションをロード
        $task->load(['createdBy', 'project']);

        return $task;
    }

    /**
     * タスクを更新する
     *
     * @param Task $task タスク
     * @param array $data 更新データ（title, description, status）
     * @param User $user ユーザー
     * @return Task
     */
    public function updateTask(Task $task, array $data, User $user): Task
    {
        // 権限チェック
        $this->checkTaskPermission($task, $user);

        $task->update($data);
        $task->load('createdBy');

        return $task;
    }

    /**
     * タスクを削除する
     *
     * @param Task $task タスク
     * @param User $user ユーザー
     * @return void
     */
    public function deleteTask(Task $task, User $user): void
    {
        // 権限チェック
        $this->checkTaskPermission($task, $user);

        $task->delete();
    }

    /**
     * タスクを開始する（todo → doing）
     *
     * @param Task $task タスク
     * @param User $user ユーザー
     * @return Task
     */
    public function startTask(Task $task, User $user): Task
    {
        // 権限チェック
        $this->checkTaskPermission($task, $user);

        // 状態チェック
        $this->validateTaskStatusForStart($task);

        $task->update(['status' => 'doing']);
        $task->load('createdBy');

        return $task;
    }

    /**
     * タスクを完了する（doing → done）
     *
     * @param Task $task タスク
     * @param User $user ユーザー
     * @return Task
     */
    public function completeTask(Task $task, User $user): Task
    {
        // 権限チェック
        $this->checkTaskPermission($task, $user);

        // 状態チェック
        $this->validateTaskStatusForComplete($task);

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
    private function isProjectMember(Project $project, User $user): bool
    {
        return $project->users()
            ->where('users.id', $user->id)
            ->exists();
    }

    /**
     * タスクに対する権限チェック
     *
     * @param Task $task タスク
     * @param User $user ユーザー
     * @return void
     * @throws \Exception
     */
    private function checkTaskPermission(Task $task, User $user): void
    {
        $project = $task->project;
        if (!$this->isProjectMember($project, $user)) {
            throw new AuthorizationException('このプロジェクトにアクセスする権限がありません');
        }
    }

    /**
     * タスク開始時の状態チェック
     *
     * @param Task $task タスク
     * @return void
     * @throws \Exception
     */
    private function validateTaskStatusForStart(Task $task): void
    {
        if ($task->status !== 'todo') {
            throw new ConflictException('未着手のタスクのみ開始できます');
        }
    }

    /**
     * タスク完了時の状態チェック
     *
     * @param Task $task タスク
     * @return void
     * @throws \Exception
     */
    private function validateTaskStatusForComplete(Task $task): void
    {
        if ($task->status !== 'doing') {
            throw new ConflictException('作業中のタスクのみ完了できます');
        }
    }
}
