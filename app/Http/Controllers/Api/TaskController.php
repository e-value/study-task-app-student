<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\TaskResource;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Requests\TaskRequest;
use App\Services\TaskService;

class TaskController extends ApiController
{

    public function __construct(
        private TaskService $taskService
    ) {
        $this->taskService = $taskService;
    }
    /**
     * プロジェクトのタスク一覧を取得
     */
    public function index(TaskRequest $request, Project $project): AnonymousResourceCollection|JsonResponse
    {
        // 自分が所属しているかチェック
        $tasks = $this->taskService->listTask(
                $request->user(),
                $project, 
        );

        return TaskResource::collection($tasks);
    }

    /**
     * タスク作成
     */
    public function store(TaskRequest $request, Project $project): JsonResponse
    {
        // 自分が所属しているかチェック
        // タスクの作成
        $task = $this->taskService->createTask(
            $request->validated(),
            $project,
            $request->user(),
        );

        return (new TaskResource($task))
            ->additional(['message' => 'タスクを作成しました'])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * タスク詳細を取得
     */
    public function show(Task $task, TaskRequest $request): TaskResource|JsonResponse
    {
        // 自分が所属しているかチェック
        // 表示
        $task = $this->taskService->showTask(
            $task, 
            $request->user(),
        );

        return new TaskResource($task);
    }

    /**
     * タスク更新
     */
    public function update(TaskRequest $request, Task $task): TaskResource|JsonResponse
    {
        // 自分が所属しているかチェック
        // タスクのupdate

        $task = $this->taskService->updateTask(
        $task, 
        $request->validated(),
        $request->user()
        );

        return (new TaskResource($task))
            ->additional(['message' => 'タスクを更新しました']);
    }

    /**
     * タスク削除
     */
    public function destroy(Task $task,TaskRequest $request): JsonResponse
    {
        // 自分が所属しているかチェック
        // タスクの削除
        $this->taskService->deleteTask(
            $task, 
            $request->user(),
        );


        return response()->json([
            'message' => 'タスクを削除しました',
        ]);
    }

    /**
     * タスクを開始（todo → doing）
     */
    public function start(Task $task, TaskRequest $request): TaskResource|JsonResponse
    {
        // 自分が所属しているかチェック
      // 状態チェック
        $this->taskService->startTask(
            $task,
            $request->user(),
        );

        return new TaskResource($task);
    }

    /**
     * タスクを完了（doing → done）
     */
    public function complete(Task $task, TaskRequest $request): TaskResource|JsonResponse
    {
        // 自分が所属しているかチェック
        // 状態チェック
        $this->taskService->completeTask(
            $task,
            $request->user(),
        );


        return new TaskResource($task);
    }
}
