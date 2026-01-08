<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\TaskResource;
use App\Http\Requests\TaskStoreRequest;    
use App\Http\Requests\TaskUpdateRequest; 
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Validator;

class TaskController extends ApiController
{   
    // コンストラクタ
    public function __construct(
        private TaskService $taskService
    ) {
    }

    /**
     * プロジェクトのタスク一覧を取得
     */
    public function index(Request $request, Project $project): AnonymousResourceCollection
    {
    $tasks = $this->taskService->index($project, $request->user()->id);
    
    return TaskResource::collection($tasks);
    }
    /**
     * タスク作成
     */
    public function store(TaskStoreRequest $request, Project $project): JsonResponse
    {
    $task = $this->taskService->store(
        $request->validated(),
        $project,
        $request->user()->id
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
    $task = $this->taskService->show($task, $request->user()->id);
    
    return new TaskResource($task);
    }

    /**
     * タスク更新
     */
    public function update(TaskUpdateRequest $request, Task $task): TaskResource|JsonResponse
    {
    $task = $this->taskService->update($task, $request->validated());

    return (new TaskResource($task))
        ->additional(['message' => 'タスクを更新しました']);
    }
    /**
     * タスク削除
     */
    public function destroy(Request $request, Task $task): JsonResponse
    {
    $this->taskService->destroy($task, $request->user()->id);

    return response()->json([
        'message' => 'タスクを削除しました',
    ]);
    }

    /**
     * タスクを開始（todo → doing）
     */
    public function start(Request $request, Task $task): TaskResource|JsonResponse
    {   
        $task = $this->taskService->start($task, $request->user()->id);
        return new TaskResource($task);
    }

    /**
     * タスクを完了（doing → done）
     */
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
