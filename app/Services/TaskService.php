<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;

class TaskService
{
    public function createTask(array $data, Project $project, User $user): Task
    {
        $task = Task::create([
            'project_id' => $project->id,
            'title' => $data->title,
            'description' => $data->description,
            'status' => 'todo',
            'created_by' => $user->id,
        ]);

        $task->load('createdBy');

        return $task;
    }

    public function updateTask(Task $task, array $data): Task
    {
        $task->fill($data);
        $task->save();

        $task->load('createdBy');

        return $task;
    }

    public function startTask(Task $task): Task
    {
        $task->update(['status' => 'doing']);
        $task->load('createdBy');

        return $task;
    }

    public function completeTask(Task $task): Task
    {
        $task->update(['status' => 'done']);
        $task->load('createdBy');

        return $task;
    }

    // 条件が増える・変わることを考慮して切り出す判断で良いのか？
    public function canStartTask(Task $task): bool
    {
        return $task->status === 'todo';
    }

    public function canCompleteTask(Task $task): bool
    {
        return $task->status === 'doing';
    }

    // 特にprivateメソッドを用意する対象が内容に感じたがOKか？
}
