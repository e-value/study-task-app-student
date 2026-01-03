<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\TaskResource;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\ProjectMemberRequest;

class TaskController extends ApiController
{
    /**
     * プロジェクトのタスク一覧を取得
     */
    public function index(ProjectMemberRequest $request, Project $project): AnonymousResourceCollection|JsonResponse
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
    public function store(ProjectMemberRequest $request, Project $project): JsonResponse
    {
        $validator = $request->validated();

        if ($validator->fails()) {
            return response()->json([
                'message' => 'バリデーションエラー',
                'errors' => $validator->errors(),
            ], 422);
        }

        $task = Task::create([
            'project_id' => $project->id,
            'title' => $request->title,
            'description' => $request->description,
            'status' => 'todo',
            'created_by' => $request->user()->id,
        ]);

        $task->load('createdBy');

        return (new TaskResource($task))
            ->additional(['message' => 'タスクを作成しました'])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * タスク詳細を取得
     */
    public function show(ProjectMemberRequest $request, Task $task): TaskResource|JsonResponse
    {
        $task->load(['createdBy', 'project']);
        return new TaskResource($task);
    }

    /**
     * タスク更新
     */
    public function update(ProjectMemberRequest $request, Task $task): TaskResource|JsonResponse
    {
        $task->update($request->only(['title', 'description', 'status']));
        $task->load('createdBy');

        return (new TaskResource($task))
            ->additional(['message' => 'タスクを更新しました']);
    }

    /**
     * タスク削除
     */
    public function destroy(ProjectMemberRequest $request, Task $task): JsonResponse
    {
        $task->delete();

        return response()->json([
            'message' => 'タスクを削除しました',
        ]);
    }

    /**
     * タスクを開始（todo → doing）
     */
    public function start(ProjectMemberRequest $request, Task $task): TaskResource|JsonResponse
    {
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

    /**
     * タスクを完了（doing → done）
     */
    public function complete(ProjectMemberRequest $request, Task $task): TaskResource|JsonResponse
    {
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
