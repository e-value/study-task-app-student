<?php

namespace App\Services;

use App\Models\Task;
use App\Models\Project;
use App\Models\User;

class TaskService
{
    public function getTasks(Project $project, User $user)
    {
        // 自分が所属しているかチェック
        $this->checkBelongsProject($project, $user);

        $tasks = $project->tasks()
            ->with('createdBy')
            ->orderBy('created_at', 'desc')
            ->get();

        return $tasks;
    }

    public function createTask(array $data, Project $project,  User $user)
    {
        // 自分が所属しているかチェック
        $this->checkBelongsProject($project, $user);

        $task = Task::create([
            'project_id' => $project->id,
            'title' => $data['title'],
            'description' => $data['description'],
            'status' => 'todo',
            'created_by' => $user->id,
        ]);

        $task->load('createdBy');

        return $task;
    }

    public function getTask(Task $task, User $user)
    {
        // 自分が所属しているかチェック
        $project = $task->project;
        $this->checkBelongsProject($project, $user);

        $task->load(['createdBy', 'project']);

        return $task;
    }

    public function updateTask(array $data, Task $task, User $user)
    {

        // 自分が所属しているかチェック
        $project = $task->project;
        $this->checkBelongsProject($project, $user);

        $task->update([
            'title' => $data['title'],
            'description' => $data['description'],
            'status' => $data['status'],
        ]);

        $task->load('createdBy');
    }

    public function destroyTask(Task $task, User $user)
    {

        // 自分が所属しているかチェック
        $project = $task->project;
        $this->checkBelongsProject($project, $user);

        $task->delete();
    }

    public function startTask(Task $task, User $user)
    {

        // 自分が所属しているかチェック
        $project = $task->project;
        $this->checkBelongsProject($project, $user);

        // 状態チェック
        $this->checkTaskStatus($task);

        $task->update(['status' => 'doing']);
        $task->load('createdBy');

        return $task;
    }

    public function completeTask(Task $task, User $user)
    {
        // 自分が所属しているかチェック
        $project = $task->project;

        $this->checkBelongsProject($project, $user);

        // 状態チェック
        $this->checkTaskStatus($task);

        $task->update(['status' => 'done']);
        $task->load('createdBy');

        return $task;
    }

    //自分が操作対象のプロジェクトに所属しているかチェック
    private function checkBelongsProject(Project $project, User $user)
    {
        $isMember = $project->users()
            ->where('users.id', $user->id)
            ->exists();

        if (!$isMember) {
            return response()->json([
                'message' => 'このプロジェクトにアクセスする権限がありません',
            ], 403);
        }
    }

    private function checkTaskStatus($task)
    {
        // 状態チェック
        if ($task->status !== 'todo') {
            return response()->json([
                'message' => '未着手のタスクのみ開始できます',
            ], 409);
        }
        // 状態チェック
        if ($task->status !== 'doing') {
            return response()->json([
                'message' => '作業中のタスクのみ完了できます',
            ], 409);
        }
    }
}
