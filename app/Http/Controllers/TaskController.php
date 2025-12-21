<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    /**
     * プロジェクトのタスク一覧を取得
     */
    public function index(Request $request, Project $project): JsonResponse
    {
        // メンバーチェック
        if (!$this->isMember($request, $project)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $tasks = $project->tasks()
            ->with('createdBy')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'tasks' => $tasks,
        ]);
    }

    /**
     * タスク作成
     */
    public function store(Request $request, Project $project): JsonResponse
    {
        // メンバーチェック
        if (!$this->isMember($request, $project)) {
            return response()->json(['message' => '権限がありません'], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
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
            'status' => 'todo',
            'created_by' => $request->user()->id,
        ]);

        $task->load('createdBy');

        return response()->json([
            'task' => $task,
            'message' => 'タスクを作成しました',
        ], 201);
    }

    /**
     * タスク詳細を取得
     */
    public function show(Request $request, Task $task): JsonResponse
    {
        // メンバーチェック
        if (!$this->isMember($request, $task->project)) {
            return response()->json(['message' => '権限がありません'], 403);
        }

        $task->load(['createdBy', 'project']);

        return response()->json([
            'task' => $task,
        ]);
    }

    /**
     * タスク更新
     */
    public function update(Request $request, Task $task): JsonResponse
    {
        // メンバーチェック
        if (!$this->isMember($request, $task->project)) {
            return response()->json(['message' => '権限がありません'], 403);
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
        $task->load('createdBy');

        return response()->json([
            'task' => $task,
            'message' => 'タスクを更新しました',
        ]);
    }

    /**
     * タスク削除
     */
    public function destroy(Request $request, Task $task): JsonResponse
    {
        // メンバーチェック
        if (!$this->isMember($request, $task->project)) {
            return response()->json(['message' => '権限がありません'], 403);
        }

        $task->delete();

        return response()->json([
            'message' => 'タスクを削除しました',
        ]);
    }

    /**
     * タスクを開始（todo → doing）
     */
    public function start(Request $request, Task $task): JsonResponse
    {
        // メンバーチェック
        if (!$this->isMember($request, $task->project)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        // 状態チェック
        if ($task->status !== 'todo') {
            return response()->json([
                'message' => '未着手のタスクのみ開始できます',
            ], 409);
        }

        $task->update(['status' => 'doing']);
        $task->load('createdBy');

        return response()->json([
            'task' => $task,
        ]);
    }

    /**
     * タスクを完了（doing → done）
     */
    public function complete(Request $request, Task $task): JsonResponse
    {
        // メンバーチェック
        if (!$this->isMember($request, $task->project)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        // 状態チェック
        if ($task->status !== 'doing') {
            return response()->json([
                'message' => '作業中のタスクのみ完了できます',
            ], 409);
        }

        $task->update(['status' => 'done']);
        $task->load('createdBy');

        return response()->json([
            'task' => $task,
        ]);
    }

    /**
     * ユーザーがプロジェクトのメンバーかチェック
     */
    private function isMember(Request $request, Project $project): bool
    {
        return $project->memberships()
            ->where('user_id', $request->user()->id)
            ->exists();
    }
}
