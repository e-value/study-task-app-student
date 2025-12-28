<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\TaskResource;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Validator;

class TaskController extends ApiController
{
    /**
     * プロジェクトのタスク一覧を取得
     */
    public function index(Request $request, Project $project): AnonymousResourceCollection|JsonResponse
    {
        // 自分が所属しているかチェック
        $isMember = $project->users()
            ->where('users.id', $request->user()->id)
            ->exists();

        if (!$isMember) {
            return response()->json([
                'message' => 'このプロジェクトにアクセスする権限がありません',
            ], 403);
        }

        $tasks = $project->tasks()
            ->with('createdBy')
            ->orderBy('created_at', 'desc')
            ->get();

        return TaskResource::collection($tasks);
    }

    /**
     * タスクを作成
     */
    public function store(Request $request, Project $project): TaskResource|JsonResponse
    {
        // 自分が所属しているかチェック
        $isMember = $project->users()
            ->where('users.id', $request->user()->id)
            ->exists();

        if (!$isMember) {
            return response()->json([
                'message' => 'このプロジェクトにアクセスする権限がありません',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|in:todo,doing,done',
        ]);

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
            'status' => $request->status ?? 'todo',
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
    public function show(Request $request, Task $task): TaskResource|JsonResponse
    {
        // タスクが属するプロジェクトを取得
        $project = $task->project;

        // 自分が所属しているかチェック
        $isMember = $project->users()
            ->where('users.id', $request->user()->id)
            ->exists();

        if (!$isMember) {
            return response()->json([
                'message' => 'このタスクにアクセスする権限がありません',
            ], 403);
        }

        $task->load(['createdBy', 'project']);

        return new TaskResource($task);
    }

    /**
     * タスクを更新
     */
    public function update(Request $request, Task $task): TaskResource|JsonResponse
    {
        // タスクが属するプロジェクトを取得
        $project = $task->project;

        // 自分が所属しているかチェック
        $isMember = $project->users()
            ->where('users.id', $request->user()->id)
            ->exists();

        if (!$isMember) {
            return response()->json([
                'message' => 'このタスクを編集する権限がありません',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'sometimes|in:todo,doing,done',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'バリデーションエラー',
                'errors' => $validator->errors(),
            ], 422);
        }

        $task->update($request->only(['title', 'description', 'status']));
        $task->load(['createdBy', 'project']);

        return (new TaskResource($task))
            ->additional(['message' => 'タスクを更新しました']);
    }

    /**
     * タスクを削除
     */
    public function destroy(Request $request, Task $task): JsonResponse
    {
        // タスクが属するプロジェクトを取得
        $project = $task->project;

        // 自分が所属しているかチェック
        $isMember = $project->users()
            ->where('users.id', $request->user()->id)
            ->exists();

        if (!$isMember) {
            return response()->json([
                'message' => 'このタスクを削除する権限がありません',
            ], 403);
        }

        $task->delete();

        return response()->json([
            'message' => 'タスクを削除しました',
        ]);
    }

    /**
     * タスクを開始（status: todo → doing）
     */
    public function start(Request $request, Task $task): TaskResource|JsonResponse
    {
        // タスクが属するプロジェクトを取得
        $project = $task->project;

        // 自分が所属しているかチェック
        $isMember = $project->users()
            ->where('users.id', $request->user()->id)
            ->exists();

        if (!$isMember) {
            return response()->json([
                'message' => 'このタスクを操作する権限がありません',
            ], 403);
        }

        if ($task->status !== 'todo') {
            return response()->json([
                'message' => '未着手のタスクのみ開始できます',
            ], 422);
        }

        $task->update(['status' => 'doing']);
        $task->load(['createdBy', 'project']);

        return (new TaskResource($task))
            ->additional(['message' => 'タスクを開始しました']);
    }

    /**
     * タスクを完了（status: doing → done）
     */
    public function complete(Request $request, Task $task): TaskResource|JsonResponse
    {
        // タスクが属するプロジェクトを取得
        $project = $task->project;

        // 自分が所属しているかチェック
        $isMember = $project->users()
            ->where('users.id', $request->user()->id)
            ->exists();

        if (!$isMember) {
            return response()->json([
                'message' => 'このタスクを操作する権限がありません',
            ], 403);
        }

        if ($task->status !== 'doing') {
            return response()->json([
                'message' => '作業中のタスクのみ完了できます',
            ], 422);
        }

        $task->update(['status' => 'done']);
        $task->load(['createdBy', 'project']);

        return (new TaskResource($task))
            ->additional(['message' => 'タスクを完了しました']);
    }
}
