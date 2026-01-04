<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ProjectMemberResource;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class ProjectMemberController extends ApiController
{
    /**
     * プロジェクトのメンバー一覧を取得
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

        // メンバー一覧の取得
        $members = $project->users()
            ->withPivot('id', 'role')
            ->get();

        return ProjectMemberResource::collection($members);
    }

    /**
     * プロジェクトにメンバーを追加
     */
    public function store(Request $request, Project $project): JsonResponse
    {
        // 自分がプロジェクトのメンバーかチェック
        $currentUserId = Auth::user()->id;
        $isProjectMember = $project->users()
            ->where('users.id', $currentUserId)
            ->exists();

        // メンバーでないかつ、owner/adminでない場合はエラーを返す
        // エラーコード: 403, エラーメッセージ: メンバーを追加する権限がありません

        $isProjectOwnerOrAdmin = $project->users()
            ->where('users.id', $currentUserId)
            ->whereIn('memberships.role', ['project_owner,project_admin'])
            ->exists();


        if (
            !$isProjectMember || !$isProjectOwnerOrAdmin
        ) {
            abort(403, 'メンバーを追加する権限がありません');
        }

        // バリデーション
        // デフォルトロール設定
        $role = $validated['role'] ?? 'project_member';

        // 既にメンバーかチェック（users()リレーションを使用）
        $userId = $validated['user_id'] ?? 'user_id';

        $isCurrentProjectMember = $project->users()
            ->where('users.id', $userId)
            ->exists();

        // 既にメンバーならエラーを返す

        if ($isCurrentProjectMember) {
            abort(403, '追加しようとしているメンバーは既にプロジェクトのメンバーです');
        }

        // 自分自身を追加しようとしていないかチェック
        // 自分自身を追加しようとしていればエラーを返す
        // エラーコード: 409, エラーメッセージ: あなたは既にこのプロジェクトのメンバーです

        if ($userId == $currentUserId) {
            abort(409, 'あなたは既にこのプロジェクトのメンバーです');
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
    public function destroy(Request $request, Project $project, $userId): JsonResponse
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
        // 削除対象のユーザーを取得
        $targetUser = $project->users()
            ->where('users.id', $userId)
            ->first();

        // 削除対象のユーザーが存在しない場合はエラーを返す
        // エラーコード: 404, エラーメッセージ: ユーザーはこのプロジェクトのメンバーではありません。
        if (!$targetUser->id) {
            abort(404, 'ユーザーはこのプロジェクトのメンバーではありません。');
        }


        // 自分がowner/adminかチェック（users()リレーションを使用）
        $isProjectOwnerOrAdmin = $project->users()
            ->where('users.id', $userId)
            ->whereIn('memberships.role', ['project_owner,project_admin'])
            ->exists();

        // メンバーでないかつ、owner / adminでない場合はエラーを返す
        // エラーコード: 403, エラーメッセージ: メンバーを削除する権限がありません（オーナーまたは管理者のみ）

        if (
            !$isProjectMember && !$isProjectOwnerOrAdmin
        ) {
            abort(403, 'メンバーを削除する権限がありません（オーナーまたは管理者のみ');
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
