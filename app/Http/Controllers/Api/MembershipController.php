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
            return response()->json(['message' => 'このプロジェクトにアクセスする権限がありません'], 403);
        }

        $users = $project->users()
            ->get();

        // MembershipResourceの形式に合わせて変換
        $memberships = $users->map(function ($user) use ($project) {
            // Membershipモデルのインスタンスを作成（Resourceクラスが期待する形式）
            $membership = new \App\Models\Membership();
            $membership->id = \App\Models\Membership::where('project_id', $project->id)
                ->where('user_id', $user->id)
                ->value('id');
            $membership->project_id = $project->id;
            $membership->user_id = $user->id;
            $membership->role = $user->pivot->role;
            $membership->created_at = $user->pivot->created_at;
            $membership->updated_at = $user->pivot->updated_at;
            $membership->setRelation('user', $user);

            return $membership;
        });

        return MembershipResource::collection($memberships);
    }

    /**
     * プロジェクトにメンバーを追加
     */
    public function store(Request $request, Project $project): JsonResponse
    {
        // 自分がowner/adminかチェック（users()リレーションを使用）
        $myUser = $project->users()
            ->where('users.id', $request->user()->id)
            ->first();

        if (!$myUser || !in_array($myUser->pivot->role, ['project_owner', 'project_admin'])) {
            return response()->json([
                'message' => 'メンバーを追加する権限がありません（オーナーまたは管理者のみ）',
            ], 403);
        }

        // バリデーション
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'nullable|in:project_owner,project_admin,project_member',
        ]);

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
        $membershipId = \App\Models\Membership::where('project_id', $project->id)
            ->where('user_id', $validated['user_id'])
            ->value('id');
        
        $membership = new \App\Models\Membership();
        $membership->id = $membershipId;
        $membership->project_id = $project->id;
        $membership->user_id = $user->id;
        $membership->role = $user->pivot->role;
        $membership->created_at = $user->pivot->created_at;
        $membership->updated_at = $user->pivot->updated_at;
        $membership->setRelation('user', $user);

        return response()->json([
            'message' => 'メンバーを追加しました',
            'membership' => new MembershipResource($membership),
        ], 201);
    }

    /**
     * メンバーシップ削除
     */
    public function destroy(Request $request, Membership $membership): JsonResponse
    {
        $project = $membership->project;

        // 自分がowner/adminかチェック（users()リレーションを使用）
        $myUser = $project->users()
            ->where('users.id', $request->user()->id)
            ->first();

        if (!$myUser || !in_array($myUser->pivot->role, ['project_owner', 'project_admin'])) {
            return response()->json([
                'message' => 'メンバーを削除する権限がありません（オーナーまたは管理者のみ）',
            ], 403);
        }

        // Owner維持チェック（Owner削除後に0人になる場合は不可）
        if ($membership->role === 'project_owner') {
            $ownerCount = $project->memberships()
                ->where('role', 'project_owner')
                ->count();

            if ($ownerCount <= 1) {
                return response()->json([
                    'message' => 'プロジェクトの最後のオーナーは削除できません',
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
                'message' => '未完了のタスクがあるメンバーは削除できません',
            ], 409);
        }

        // 削除実行
        $membership->delete();

        return response()->json([
            'message' => 'メンバーを削除しました',
        ]);
    }

    /**
     * ユーザーがプロジェクトのメンバーかチェック（users()リレーションを使用）
     */
    private function isMember(Request $request, Project $project): bool
    {
        return $project->users()
            ->where('users.id', $request->user()->id)
            ->exists();
    }
}
