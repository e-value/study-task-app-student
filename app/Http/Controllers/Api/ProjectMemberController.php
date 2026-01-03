<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ProjectMemberResource;
use App\Models\Project;
use App\Http\Requests\ProjectMemberRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Requests\OwnerAdminRequest;

class ProjectMemberController extends ApiController
{
    /**
     * プロジェクトのメンバー一覧を取得
     */
    public function index(ProjectMemberRequest $request, Project $project): AnonymousResourceCollection|JsonResponse
    {
        // 2. メンバー一覧の取得
        // withPivot に 'id' を含めることで、Membership の ID も取得できます
        $members = $project->users()
            ->withPivot('id', 'role')
            ->get();

        // 3. Resourceに渡すだけ
        return ProjectMemberResource::collection($members);
    }

    /**
     * プロジェクトにメンバーを追加
     */
    public function store(OwnerAdminRequest $request, Project $project): JsonResponse
    {
        // バリデーション
        $validated = $request->validated();

        // デフォルトロール設定
        $role = $validated['role'] ?? 'project_member';

        // 既にメンバーかチェック（users()リレーションを使用）
        $existingUser = $project->users()
            ->where('users.id', $validated['user_id'])
            ->first();

        if ($existingUser) {
            return response()->json([
                'message' => 'このユーザーは既にプロジェクトのメンバーです',
            ], 409);
        }

        // 自分自身を追加しようとしていないかチェック
        if ($validated['user_id'] == $request->user()->id) {
            return response()->json([
                'message' => 'あなたは既にこのプロジェクトのメンバーです',
            ], 409);
        }

        // メンバーシップ作成（users()リレーションのattach()を使用）
        $project->users()->attach($validated['user_id'], [
            'role' => $role,
        ]);

        // ユーザー情報を含めて返す
        $user = $project->users()->find($validated['user_id']);

        return response()->json([
            'message' => 'メンバーを追加しました',
            'membership' => new ProjectMemberResource($user),
        ], 201);
    }

    /**
     * プロジェクトからメンバーを削除（users()リレーションを使用）
     */
    public function destroy(OwnerAdminRequest $request, Project $project, $userId): JsonResponse
    {
        // 削除対象のユーザーを取得
        $targetUser = $project->users()
            ->where('users.id', $userId)
            ->first();

        if (!$targetUser) {
            return response()->json([
                'message' => 'User is not a member of this project.',
            ], 404);
        }

        // Owner維持チェック（Owner削除後に0人になる場合は不可）
        if ($targetUser->pivot->role === 'project_owner') {
            $ownerCount = $project->users()
                ->wherePivot('role', 'project_owner')
                ->count();

            if ($ownerCount <= 1) {
                return response()->json([
                    'message' => 'プロジェクトの最後のオーナーは削除できません',
                ], 409);
            }
        }

        // 未完了タスクチェック
        $hasIncompleteTasks = $project->tasks()
            ->where('created_by', $userId)
            ->whereIn('status', ['todo', 'doing'])
            ->exists();

        if ($hasIncompleteTasks) {
            return response()->json([
                'message' => '未完了のタスクがあるメンバーは削除できません',
            ], 409);
        }

        // 削除実行（users()リレーションのdetach()を使用）
        $project->users()->detach($userId);

        return response()->json([
            'message' => 'メンバーを削除しました',
        ]);
    }
}
