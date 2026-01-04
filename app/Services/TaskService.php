<?php

namespace App\Services;

use App\Models\Project;
use App\Models\User;
use App\Models\Task;

class TaskService
{  /**
    * タスクの一覧取得
    *
    * @param Project $project
    * @param User $user 作成者
    */
   public function indexTask(Project $project, User $user)
   {
      // 自分が所属しているかチェック
      $this->checkBelongsToProject($project, $user);

        $tasks = $project->tasks()
            ->with('createdBy')
            ->orderBy('created_at', 'desc')
            ->get();

        return $tasks;
   }

    /**
     * タスクを作成する
     *
     * @param Project $project
     * @param array $data プロジェクト作成データ（name, is_archived）
     * @param User $user 作成者
     * @return Task
     */
    public function createTask(Project $project,array $data, User $user): Task
    {
         // 自分が所属しているかチェック
        $this->checkBelongsToProject($project, $user);
  
          $additionalData =  [
              'project_id' => $project->id,
              'status' => 'todo',
              'created_by' => $user->id,
          ];
          $dataToSave = array_merge($data, $additionalData);
          $task = Task::create($dataToSave);
  
          $task->load('createdBy');

          return $task;
    }

    /**
     * タスクの詳細取得
     *
     * @param Task $task 
     * @param User $user 作成者
     * @return Task
     */
    public function showTask(Task $task, User $user): Task
    {
        // 自分が所属しているかチェック
        $project = $task->project;
        $this->checkBelongsToProject($project, $user);

        $task->load(['createdBy', 'project']);

        return $task;
    }

    /**
     * タスクを更新する
     *
     * @param Task $task 
     * @param array $data プロジェクト作成データ（name, is_archived）
     * @param User $user 作成者
     * @return Task
     */
    public function updateTask(Task $task, array $data, User $user): Task
    {
        // 自分が所属しているかチェック
        $project = $task->project;
        $this->checkBelongsToProject($project, $user);

        $task->update($data);
        $task->load('createdBy');

        return $task;
    }

    /**
     * タスクを削除する
     *
     * @param Task $task 
     * @param User $user 作成者
     * @return void
     */
     public function deleteTask(Task $task, User $user): void
     {
         // 自分が所属しているかチェック
         $project = $task->project;
         $this->checkBelongsToProject($project, $user);

         $task->delete();
     }

    /**
     * タスクをスタートする
     *
     * @param Task $task 
     * @param User $user 作成者
     * @return Task
     */
    public function startTask(Task $task, User $user): Task
    {
        // 自分が所属しているかチェック
        $project = $task->project;
        $this->checkBelongsToProject($project, $user);

        // 状態チェック
        if ($task->status !== 'todo') {
            abort(409, '未着手のタスクのみ開始できます');
        }

        $task->update(['status' => 'doing']);
        $task->load('createdBy');

        return $task;
    }

    /**
     * タスクを完了する
     *
     * @param Task $task 
     * @param User $user 作成者
     * @return Task
     */
    public function completeTask(Task $task, User $user): Task
    {
        // 自分が所属しているかチェック
        $project = $task->project;
        $this->checkBelongsToProject($project, $user);

        // 状態チェック
        if ($task->status !== 'doing') {
            abort(409, '作業中のタスクのみ完了できます');
        }

        $task->update(['status' => 'done']);
        $task->load('createdBy');

        return $task;
    }

    private function checkBelongsToProject(Project $project, User $user): void
    {
        $isMember = $project->users()
        ->where('users.id', $user->id)
        ->exists();

        if (!$isMember) {
            abort(403, 'このプロジェクトにアクセスする権限がありません');
        }
    }
}
