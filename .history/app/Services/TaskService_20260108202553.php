<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Support\Collection;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;

class TaskService
{
    // 一覧取得
    public function index(Project $project, int $userId): Collection
    {
        $this->checkProjectMembership($project, $userId);
        
        return $project->tasks()
            ->with('createdBy')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    // 作成
    public function store(array $data, Project $project, int $userId): Task
    {
        
        $task = Task::create([
            ...$data,
            'project_id' => $project->id,
            'status' => 'todo',
            'created_by' => $userId,
        ]);

        $this->loadTaskRelations($task);

        return $task;
    }

    // 詳細の取得
    public function show(Task $task, int $userId): Task
    {
        $this->checkProjectMembership($task->project, $userId);
        
        $this->loadTaskRelations($task, ['project']);
        
        return $task;
    }

    // 更新
    public function update(Task $task, array $data): Task
    {
        // FormRequest で権限チェック済みなので不要
        
        $task->update($data);
        $this->loadTaskRelations($task);

        return $task;
    }

    // 削除
    public function destroy(Task $task, int $userId): void
    {
        $this->checkProjectMembership($task->project, $userId);
        
        $task->delete();
    }

    // 開始
    public function start(Request $request, Task $task): TaskResource|JsonResponse
    {
        // 自分が所属しているかチェック
        $project = $task->project;
        $isMember = $project->users()
            ->where('users.id', $request->user()->id)
            ->exists();

        if (!$isMember) {
            return response()->json([
                'message' => 'このプロジェクトにアクセスする権限がありません',
            ], 403);
        }

        // 状態チェック
        if ($task->status !== 'todo') {
            return response()->json([
                'message' => '未着手のタスクのみ開始できます',
            ], 409);
        }

        $task->update(['status' => 'doing']);
        $task->load('createdBy');

        return new TaskResource($task);
    }
    // 完了
    public function complete(Request $request, Task $task): TaskResource|JsonResponse
    {
        // 自分が所属しているかチェック
        $project = $task->project;
        $isMember = $project->users()
            ->where('users.id', $request->user()->id)
            ->exists();

        if (!$isMember) {
            return response()->json([
                'message' => 'このプロジェクトにアクセスする権限がありません',
            ], 403);
        }

        // 状態チェック
        if ($task->status !== 'doing') {
            return response()->json([
                'message' => '作業中のタスクのみ完了できます',
            ], 409);
        }

        $task->update(['status' => 'done']);
        $task->load('createdBy');

        return new TaskResource($task);
    }
    }

    // メンバーシップのチェック
    private function checkProjectMembership(Project $project, int $userId): void
    {
        $isMember = $project->users()
            ->where('users.id', $userId)
            ->exists();

        if (!$isMember) {
            throw new AuthorizationException('このプロジェクトにアクセスする権限がありません');
        }
    }

    // リレーションの読み込み
    private function loadTaskRelations(Task $task, array $additional = []): void
    {
        $relations = array_merge(['createdBy'], $additional);
        $task->load($relations);
    }


    // ステータスの更新
    private function updateTaskStatus(Task $task, string $status): void
    {
        $task->update(['status' => $status]);
    }
}