<?php

namespace App\Http\Controllers;

use App\Models\Membership;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MembershipController extends Controller
{
    /**
     * プロジェクトのメンバー一覧を取得
     */
    public function index(Request $request, Project $project): JsonResponse
    {
        // メンバーチェック
        if (!$this->isMember($request, $project)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $memberships = $project->memberships()
            ->with('user')
            ->get();

        return response()->json([
            'memberships' => $memberships,
        ]);
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
