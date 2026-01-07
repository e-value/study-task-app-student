<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\TaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Project;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TaskController extends ApiController
{
    public function __construct(
        private TaskService $taskService
    ) {}
    /**
     * プロジェクトのタスク一覧を取得
     *
     * @param Request $request
     * @param Project $project
     * @return AnonymousResourceCollection
     */
    public function index(Request $request, Project $project): AnonymousResourceCollection
    {
        $tasks = $this->taskService->getTasks($project, $request->user());

        return TaskResource::collection($tasks);
    }

    /**
     * タスク作成
     */
    public function store(TaskRequest $request, Project $project): JsonResponse
    {
        $task = $this->taskService->createTask(
            $request->validated(),
            $project,
            $request->user()
        );

        return (new TaskResource($task))
            ->additional(['message' => 'タスクを作成しました'])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * タスク詳細を取得
     */
    public function show(Request $request, Task $task): TaskResource
    {
        $task = $this->taskService->getTask($task, $request->user());
        return new TaskResource($task);
    }

    /**
     * タスク更新
     */
    public function update(TaskRequest $request, Task $task): TaskResource
    {
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
    public function destroy(Request $request, Task $task): JsonResponse
    {
        $this->taskService->deleteTask($task, $request->user());

        return $this->response()->success(null, 'タスクを削除しました');
    }

    /**
     * タスクを開始（todo → doing）
     */
    public function start(Request $request, Task $task): TaskResource
    {
        $task = $this->taskService->startTask($task, $request->user());
        return new TaskResource($task);
    }

    /**
     * タスクを完了（doing → done）
     */
    public function complete(Request $request, Task $task): TaskResource
    {
        $task = $this->taskService->completeTask($task, $request->user());
        return new TaskResource($task);
    }
}
