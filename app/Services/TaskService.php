<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Support\Collection;
use Illuminate\Auth\Access\AuthorizationException;

class TaskService
{
    /**
     * タスク一覧取得
     */
    public function index(Project $project, int $userId): Collection
    {
        $this->checkProjectMembership($project, $userId);
        
        return $project->tasks()
            ->with('createdBy')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * タスク作成
     */
    public function store(array $data, Project $project, int $userId): Task
    {
        // FormRequest で権限チェック済みなので不要
        
        $task = Task::create([
            ...$data,
            'project_id' => $project->id,
            'status' => 'todo',
            'created_by' => $userId,
        ]);

        $this->loadTaskRelations($task);

        return $task;
    }

    /**
     * タスク詳細取得
     */
    public function show(Task $task, int $userId): Task
    {
        $this->checkProjectMembership($task->project, $userId);
        
        $this->loadTaskRelations($task, ['project']);
        
        return $task;
    }

    /**
     * タスク更新
     */
    public function update(Task $task, array $data): Task
    {
        // FormRequest で権限チェック済みなので不要
        
        $task->update($data);
        $this->loadTaskRelations($task);

        return $task;
    }

    /**
     * タスク削除
     */
    public function destroy(Task $task, int $userId): void
    {
        $this->checkProjectMembership($task->project, $userId);
        
        $task->delete();
    }

    /**
     * タスク開始（todo → doing）
     */
    public function start(Task $task, int $userId): Task
    {
        $this->checkProjectMembership($task->project, $userId);
        
        if ($task->status !== 'todo') {
            throw new \Exception('未着手のタスクのみ開始できます');
        }

        $this->updateTaskStatus($task, 'doing');
        $this->loadTaskRelations($task);

        return $task;
    }

    /**
     * タスク完了（doing → done）
     */
    public function complete(Task $task, int $userId): Task
    {
        $this->checkProjectMembership($task->project, $userId);
        
        if ($task->status !== 'doing') {
            throw new \Exception('作業中のタスクのみ完了できます');
        }

        $this->updateTaskStatus($task, 'done');
        $this->loadTaskRelations($task);

        return $task;
    }

    /**
     * プロジェクトメンバーシップチェック
     */
    private function checkProjectMembership(Project $project, int $userId): void
    {
        $isMember = $project->users()
            ->where('users.id', $userId)
            ->exists();

        if (!$isMember) {
            throw new AuthorizationException('このプロジェクトにアクセスする権限がありません');
        }
    }

    /**
     * タスクのリレーション読み込み
     */
    private function loadTaskRelations(Task $task, array $additional = []): void
    {
        $relations = array_merge(['createdBy'], $additional);
        $task->load($relations);
    }

    /**
     * タスクのステータス更新
     */
    private function updateTaskStatus(Task $task, string $status): void
    {
        $task->update(['status' => $status]);
    }
}