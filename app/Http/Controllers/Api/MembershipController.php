<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\MembershipResource;
use App\Models\Membership;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MembershipController extends ApiController
{
    /**
     * プロジェクトのメンバー一覧を取得
     */
    public function index(Request $request, Project $project): AnonymousResourceCollection|JsonResponse
    {
        // メンバーチェック
        if (!$this->isMember($request, $project)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $memberships = $project->memberships()
            ->with('user')
            ->get();

        return MembershipResource::collection($memberships);
    }

    /**
     * プロジェクトにメンバーを追加
     */
    public function store(Request $request, Project $project): JsonResponse
    {
        // 自分がowner/adminかチェック
        $myMembership = $project->memberships()
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$myMembership || !in_array($myMembership->role, ['project_owner', 'project_admin'])) {
            return response()->json([
                'message' => 'Forbidden: Only owners and admins can add members.',
            ], 403);
        }

        // バリデーション
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'nullable|in:project_owner,project_admin,project_member',
        ]);

        // デフォルトロール設定
        $role = $validated['role'] ?? 'project_member';

        // 既にメンバーかチェック
        $existingMembership = $project->memberships()
            ->where('user_id', $validated['user_id'])
            ->first();

        if ($existingMembership) {
            return response()->json([
                'message' => 'User is already a member of this project.',
            ], 409);
        }

        // 自分自身を追加しようとしていないかチェック
        if ($validated['user_id'] == $request->user()->id) {
            return response()->json([
                'message' => 'You are already a member of this project.',
            ], 409);
        }

        // メンバーシップ作成
        $membership = Membership::create([
            'project_id' => $project->id,
            'user_id' => $validated['user_id'],
            'role' => $role,
        ]);

        // ユーザー情報を含めて返す
        $membership->load('user');

        return response()->json([
            'message' => 'Member added successfully.',
            'membership' => new MembershipResource($membership),
        ], 201);
    }

    /**
     * メンバーシップ削除
     */
    public function destroy(Request $request, Membership $membership): JsonResponse
    {
        $project = $membership->project;

        // 自分がowner/adminかチェック
        $myMembership = $project->memberships()
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$myMembership || !in_array($myMembership->role, ['project_owner', 'project_admin'])) {
            return response()->json([
                'message' => 'Forbidden: Only owners and admins can delete members.',
            ], 403);
        }

        // Owner維持チェック（Owner削除後に0人になる場合は不可）
        if ($membership->role === 'project_owner') {
            $ownerCount = $project->memberships()
                ->where('role', 'project_owner')
                ->count();

            if ($ownerCount <= 1) {
                return response()->json([
                    'message' => 'Cannot delete the last owner of the project.',
                ], 409);
            }
        }

        // 未完了タスクチェック
        $hasIncompleteTasks = $project->tasks()
            ->where('created_by', $membership->user_id)
            ->whereIn('status', ['todo', 'doing'])
            ->exists();

        if ($hasIncompleteTasks) {
            return response()->json([
                'message' => 'Cannot delete member with incomplete tasks (todo or doing).',
            ], 409);
        }

        // 削除実行
        $membership->delete();

        return response()->json([
            'message' => 'Membership deleted successfully.',
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
