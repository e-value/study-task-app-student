<?php

namespace App\Http\Controllers\Api;

use App\Models\Membership;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProjectController extends ApiController
{
    /**
     * 自分が所属しているプロジェクト一覧を返す
     */
    public function index(Request $request): JsonResponse
    {
        $projects = $request->user()
            ->projects()
            ->with('users')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'projects' => $projects,
        ]);
    }

    /**
     * プロジェクト新規作成
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'is_archived' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'バリデーションエラー',
                'errors' => $validator->errors(),
            ], 422);
        }

        // プロジェクト作成
        $project = Project::create([
            'name' => $request->name,
            'is_archived' => $request->is_archived ?? false,
        ]);

        // 作成者を自動的にオーナーとして追加
        Membership::create([
            'project_id' => $project->id,
            'user_id' => $request->user()->id,
            'role' => 'project_owner',
        ]);

        $project->load(['users']);

        return response()->json([
            'project' => $project,
            'message' => 'プロジェクトを作成しました',
        ], 201);
    }

    /**
     * プロジェクト詳細を返す
     */
    public function show(Request $request, Project $project): JsonResponse
    {
        // 自分が所属しているかチェック（users()リレーションを使用）
        $isMember = $project->users()
            ->where('users.id', $request->user()->id)
            ->exists();

        if (!$isMember) {
            return response()->json([
                'message' => 'このプロジェクトにアクセスする権限がありません',
            ], 403);
        }

        $project->load(['users', 'tasks.createdBy']);

        return response()->json([
            'project' => $project,
        ]);
    }

    /**
     * プロジェクト更新
     */
    public function update(Request $request, Project $project): JsonResponse
    {
        // 自分がオーナーまたは管理者かチェック（users()リレーションを使用）
        $myUser = $project->users()
            ->where('users.id', $request->user()->id)
            ->first();

        if (!$myUser || !in_array($myUser->pivot->role, ['project_owner', 'project_admin'])) {
            return response()->json([
                'message' => 'プロジェクトを編集する権限がありません',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'is_archived' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'バリデーションエラー',
                'errors' => $validator->errors(),
            ], 422);
        }

        $project->update($request->only(['name', 'is_archived']));
        $project->load(['users', 'tasks.createdBy']);

        return response()->json([
            'project' => $project,
            'message' => 'プロジェクトを更新しました',
        ]);
    }

    /**
     * プロジェクト削除
     */
    public function destroy(Request $request, Project $project): JsonResponse
    {
        // 自分がオーナーかチェック（users()リレーションを使用）
        $myUser = $project->users()
            ->where('users.id', $request->user()->id)
            ->first();

        if (!$myUser || $myUser->pivot->role !== 'project_owner') {
            return response()->json([
                'message' => 'プロジェクトを削除する権限がありません（オーナーのみ）',
            ], 403);
        }

        $project->delete();

        return response()->json([
            'message' => 'プロジェクトを削除しました',
        ]);
    }
}
