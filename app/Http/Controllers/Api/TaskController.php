<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\TaskResource;
use App\Http\Requests\TaskRequest;
use App\Services\TaskService;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Validator;

class TaskController extends ApiController
{
    public function __construct(
        private TaskService $taskService
    ){}
    /**
     * プロジェクトのタスク一覧を取得
     */
    public function index(TaskRequest $request, Project $project): AnonymousResourceCollection|JsonResponse
    {
        $tasks = $project->tasks()
            ->with('createdBy')
            ->orderBy('created_at', 'desc')
            ->get();

        return TaskResource::collection($tasks);
    }

    /**
     * タスク作成
     */
    public function store(TaskRequest $request, Project $project): JsonResponse
    {
        $task = $this->taskService->createTask([
            $request->validated(),
            $project,
            $reuqest->user(),
        ]);

        return (new TaskResource($task))
            ->additional(['message' => 'タスクを作成しました'])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * タスク詳細を取得
     */
    public function show(TaskRequest $request, Task $task): TaskResource|JsonResponse
    {
        $task->load(['createdBy', 'project']);

        return new TaskResource($task);
    }

    /**
     * タスク更新
     */
    public function update(TaskRequest $request, Task $task): TaskResource|JsonResponse
    {
        $updatedTask = $this->taskService->updateTask([
            $task,
            $request->validated()->only(['title', 'description', 'status'])
        ]);

        return (new TaskResource($updatedTask))
            ->additional(['message' => 'タスクを更新しました']);
    }

    /**
     * タスク削除
     */
    public function destroy(TaskRequest $request, Task $task): JsonResponse
    {
        $task->delete();

        return response()->json([
            'message' => 'タスクを削除しました',
        ]);
    }

    /**
     * タスクを開始（todo → doing）
     */
    public function start(TaskRequest $request, Task $task): TaskResource|JsonResponse
    {
        $canStartTask = $this->taskService->canStartTask($task); // 記述量自体は増えているがこれでいいのか？優先順位は変更のしやすさ？考慮しすぎ？
        
        // 状態チェック
        if (!$canStartTask) {
            return response()->json([
                'message' => '未着手のタスクのみ開始できます',
            ], 409);
        }

        $updatedTask = $this->taskService->startTask([
            $task,
        ]);

        return new TaskResource($task);
    }

    /**
     * タスクを完了（doing → done）
     */
    public function complete(TaskRequest $request, Task $task): TaskResource|JsonResponse
    {
        $canCompleteTask = $this->taskService->canCompleteTask($task);
        
        // 状態チェック
        if (!$canCompleteTask) {
            return response()->json([
                'message' => '作業中のタスクのみ完了できます',
            ], 409);
        }
        $updatedTask = $this->taskService->completeTask([
            $task,
        ]);

        return new TaskResource($task);
    }
}
