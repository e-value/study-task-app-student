<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\TaskResource;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;


class TaskController extends ApiController
{
    /**
     * プロジェクトのタスク一覧を取得
     */
    public function index(Request $request, Project $project): AnonymousResourceCollection|JsonResponse
    {
        // 自分がプロジェクトのメンバーかチェック
        $currentUserId = Auth::user()->id;
        $isProjectMember = $project->users()
            ->where('users.id', $currentUserId)
            ->exists();

        // メンバーでなければエラーを返す
        // エラーコード: 403, エラーメッセージ: このプロジェクトにアクセスする権限がありません
        if (!$isProjectMember) {
            abort(403, 'このプロジェクトにアクセスする権限がありません');
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
        // 自分がプロジェクトのメンバーかチェック
        $userId = Auth::user()->id;
        $isProjectMember = $project->users()
            ->where('users.id', $userId)
            ->exists();

        // メンバーでなければエラーを返す
        // エラーコード: 403, エラーメッセージ: このプロジェクトにアクセスする権限がありません
        if (!$isProjectMember) {
            abort(403, 'このプロジェクトにアクセスする権限がありません');
        }


        // バリデーションを記載
        $validated = $request->validate([
            'project_id' => 'required|integer ',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255',

        ]);

        $task = Task::create([
            'project_id' => $validated['project_id'],
            'title'       => $validated['title'],
            'description' => $validated['description'],
            'status' => $validated['status'],
            'created_by' => $request->user()->id,
        ]);

        // タスクを読み込み（N+1問題を防ぐため）
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
        // 自分がプロジェクトのメンバーかチェック

        $currentUserId = Auth::user()->id;
        $project = $task->project();
        $isProjectMember = $project->users()
            ->where('users.id', $currentUserId)
            ->exists();

        // メンバーでなければエラーを返す
        // エラーコード: 403, エラーメッセージ: このプロジェクトにアクセスする権限がありません
        if (!$isProjectMember) {
            abort(403, 'このプロジェクトにアクセスする権限がありません');
        }

        // タスクを読み込み（N+1問題を防ぐため）
        $task->load(['createdBy', 'project']);

        return new TaskResource($task);
    }

    /**
     * タスクを更新
     */
    public function update(Request $request, Task $task): TaskResource|JsonResponse
    {
        // 自分がプロジェクトのメンバーかチェック

        $currentUserId = Auth::user()->id;
        $project = $task->project();
        $isProjectMember = $project->users()
            ->where('users.id', $currentUserId)
            ->exists();


        // メンバーでなければエラーを返す
        // エラーコード: 403, エラーメッセージ: このプロジェクトにアクセスする権限がありません
        if (!$isProjectMember) {
            abort(403, 'このプロジェクトにアクセスする権限がありません');
        }

        // statusが“doneは更新不可（409）”を追加
        // エラーコード: 409, エラーメッジ: doneは更新不可です
        if ($task->status = 'done') {
            abort(409, 'doneは更新不可です');
        }

        // バリデーションを記載
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255',
        ]);


        $task->update($request->only(['title', 'description', 'status']));

        // タスクを読み込み（N+1問題を防ぐため）
        $task->load(['createdBy', 'project']);

        return (new TaskResource($task))
            ->additional(['message' => 'タスクを更新しました']);
    }

    /**
     * タスクを削除
     */
    public function destroy(Request $request, Task $task): JsonResponse
    {
        // 自分がプロジェクトのメンバーかチェック
        $currentUserId = Auth::user()->id;
        $project = $task->project();
        $isProjectMember = $project->users()
            ->where('users.id', $currentUserId)
            ->exists();

        // メンバーでなければエラーを返す
        // エラーコード: 403, エラーメッセージ: このプロジェクトにアクセスする権限がありません
        if (!$isProjectMember) {
            abort(403, 'このプロジェクトにアクセスする権限がありません');
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
        // 自分がプロジェクトのメンバーかチェック

        $currentUserId = Auth::user()->id;
        $project = $task->project();
        $isProjectMember = $project->users()
            ->where('users.id', $currentUserId)
            ->exists();

        // メンバーでなければエラーを返す
        // エラーコード: 403, エラーメッセージ: このプロジェクトにアクセスする権限がありません

        if (!$isProjectMember) {
            abort(403, 'このプロジェクトにアクセスする権限がありません');
        }

        // ステータスがtodoでなければエラーを返す
        // エラーコード: 409, エラーメッセージ: 未着手のタスクのみ開始できます

        if (!$task->status = 'todo') {
            abort(409, '未着手のタスクのみ開始できます');
        }
        // タスクを開始
        $task->update(['status' => 'doing']);

        // タスクを読み込み（N+1問題を防ぐため）
        $task->load(['createdBy', 'project']);

        return (new TaskResource($task))
            ->additional(['message' => 'タスクを開始しました']);
    }

    /**
     * タスクを完了（status: doing → done）
     */
    public function complete(Request $request, Task $task): TaskResource|JsonResponse
    {
        // 自分がプロジェクトのメンバーかチェック
        $currentUserId = Auth::user()->id;
        $project = $task->project();
        $isProjectMember = $project->users()
            ->where('users.id', $currentUserId)
            ->exists();

        // メンバーでなければエラーを返す
        // エラーコード: 403, エラーメッセージ: このプロジェクトにアクセスする権限がありません
        if (!$isProjectMember) {
            abort(403, 'このプロジェクトにアクセスする権限がありません');
        }

        // ステータスがdoingでなければエラーを返す
        // エラーコード: 409, エラーメッセージ: 作業中のタスクのみ完了できます
        if (!$task->status = 'doing') {
            abort(409, '作業中のタスクのみ完了できます');
        }
        // タスクを完了
        $task->update(['status' => 'done']);

        // タスクを読み込み（N+1問題を防ぐため）
        $task->load(['createdBy', 'project']);

        return (new TaskResource($task))
            ->additional(['message' => 'タスクを完了しました']);
    }
}
