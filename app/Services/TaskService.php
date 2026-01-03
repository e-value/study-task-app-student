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
     * @param Project $project タスクが属するプロジェクト
     * @param User $user 作成者
     * @return Task
     */
    public function createTask(array $data, Project $project, User $user): Task
    {
        // タスク作成
        $task = Task::create([
            'project_id' => $project->id,
            'title' => data['title'],
            'description' => data['description'],
            'status' => 'todo',
            'created_by' => $user->id,
        ]);

        // リレーションをロード
        $this->loadTaskRelations($task);

        return $task;
    }
    /**
     * タスクを更新する
     *
     * @param array $data タスク作成データ（name, is_archived）
     * @param Task $Project タスクそのもの
     * @return Task
     */
    // 他のメソッドもここに記載
    public function updateTask(Task $task, array $data): Task
     {
        $task->update([
            'title' => $data['title'] ?? $task->title,
            'description' => $data['description'] ?? $task->description,
            'status' => $data['status'] ?? $task->status,
        ]);

        // リレーションをロード
        $this->loadTaskRelations($task);
    
        return $task;

     }

    // 他のメソッドもここに記載
    /**
     * タスクを削除する
     *
     * @param Task $task 削除するタスク
     * @return bool 削除成功時true
     */
    public function deleteTask(Task $task): bool
    {
        return $task->delete();
    }

    /**
     * タスクを開始する（todo → doing）
     *
     * @param Task $task 開始するタスク
     * @return Task
     * @throws \Exception 未着手のタスクでない場合
     */
    public function startTask(Task $task): Task
    {
        // 状態チェック
        if ($task->status !== 'todo') {
            throw new \Exception('未着手のタスクのみ開始できます');
        }

        $task->update(['status' => 'doing']);
        $this->loadTaskRelations($task);

        return $task;
    }
    
    /**
     * タスクを完了する（doing → done）
     *
     * @param Task $task 完了するタスク
     * @return Task
     * @throws \Exception 作業中のタスクでない場合
     */
    public function completeTask(Task $task): Task
    {
        // 状態チェック
        if ($task->status !== 'doing') {
            throw new \Exception('作業中のタスクのみ完了できます');
        }

        $task->update(['status' => 'done']);
        $this->loadTaskRelations($task);

        return $task;
    }

    // privateメソッドも定義
    /**
     * タスクのリレーションをロードする
     *
     * @param Task $task タスク
     * @return void
     */
    private function loadTaskRelations(Task $task): void
    {
        $task->load(['createdBy', 'project']);
    }
}
