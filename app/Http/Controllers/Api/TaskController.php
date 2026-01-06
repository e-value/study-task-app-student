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
        // 自分がプロジェクトのメンバーかチェック
        $user_id = auth()->user()->id;
        if (!$project->users()->find($user_id)){  
            // メンバーでなければエラーを返す
            // エラーコード: 403, エラーメッセージ: このプロジェクトにアクセスする権限がありません
            return response()->json(['message'=> 'このプロジェクトにアクセスする権限がありません'],403);
        };

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
        $user_id = auth()->user()->id;
        if (!$project->users()->find($user_id)){  
            // メンバーでなければエラーを返す
            // エラーコード: 403, エラーメッセージ: このプロジェクトにアクセスする権限がありません
            return response()->json(['message'=> 'このプロジェクトにアクセスする権限がありません'],403);
        };

        // バリデーションを記載
        $validated = $request->validate([
            'project_id' => ['required','exists:projects,id'],
            'title' => ['required','string'],
            'description' => ['nullable','text'],
            'status' => ['nullable',Rule::in(['todo', 'doing', 'done']),],
        ]);

        $task = Task::create($validated);

        // 作成者を自動的に追加（ProjectControllerに倣って実装）
        $user_id = auth()->user()->id;
        $project->createdBy()->attach($user_id);

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
        $user_id = auth()->user()->id;
        if (!$project->users()->find($user_id)){  
            // メンバーでなければエラーを返す
            // エラーコード: 403, エラーメッセージ: このプロジェクトにアクセスする権限がありません
            return response()->json(['message'=> 'このプロジェクトにアクセスする権限がありません'],403);
        };

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
        $user_id = auth()->user()->id;
        if (!$project->users()->find($user_id)){  
            // メンバーでなければエラーを返す
            // エラーコード: 403, エラーメッセージ: このプロジェクトにアクセスする権限がありません
            return response()->json(['message'=> 'このプロジェクトにアクセスする権限がありません'],403);
        };

        // statusが“doneは更新不可（409）”を追加
        if ($request->status === "done"){
            // エラーコード: 409, エラーメッジ: doneは更新不可です
            return response()->json(['message'=> 'doneは更新不可です'],409);
        }

        // バリデーションを記載
        $validated = $request->validate([
            'title' => ['required','string'],
            'description' => ['nullable','text'],
            'status' => ['nullable',Rule::in(['todo', 'doing']),],
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
        $user_id = auth()->user()->id;
        if (!$project->users()->find($user_id)){  
            // メンバーでなければエラーを返す
            // エラーコード: 403, エラーメッセージ: このプロジェクトにアクセスする権限がありません
            return response()->json(['message'=> 'このプロジェクトにアクセスする権限がありません'],403);
        };
        
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
        $user_id = auth()->user()->id;
        if (!$project->users()->find($user_id)){  
            // メンバーでなければエラーを返す
            // エラーコード: 403, エラーメッセージ: このプロジェクトにアクセスする権限がありません
            return response()->json(['message'=> 'このプロジェクトにアクセスする権限がありません'],403);
        };

        // ステータスがtodoでなければエラーを返す
        if ($request->status !== 'done'){
            // エラーコード: 409, エラーメッセージ: 未着手のタスクのみ開始できます
            return response()->json(['message'=> '未着手のタスクのみ開始できます'],409);
        };

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
        $user_id = auth()->user()->id;
        if (!$project->users()->find($user_id)){  
            // メンバーでなければエラーを返す
            // エラーコード: 403, エラーメッセージ: このプロジェクトにアクセスする権限がありません
            return response()->json(['message'=> 'このプロジェクトにアクセスする権限がありません'],403);
        };
        
        // ステータスがdoingでなければエラーを返す
        if ($request->status !== 'done'){
            // エラーコード: 409, エラーメッセージ: 作業中のタスクのみ完了できます
            return response()->json(['message'=> '作業中のタスクのみ開始できます'],409);
        };

        // タスクを完了
        $task->update(['status' => 'done']);

        // タスクを読み込み（N+1問題を防ぐため）
        $task->load(['createdBy', 'project']);

        return (new TaskResource($task))
            ->additional(['message' => 'タスクを完了しました']);
    }
}
