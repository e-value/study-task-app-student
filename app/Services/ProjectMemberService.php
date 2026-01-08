<?php

namespace App\Services;

use App\Models\ProjectMember;
use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;

class ProjectMemberService
{

    public function getMembers(Project $project, User $user)
    {
        // 自分が所属しているかチェック
        $this->checkBelongsProject($project, $user);

        // 2. メンバー一覧の取得
        // withPivot に 'id' を含めることで、Membership の ID も取得できます
        $members = $project->users()
            ->withPivot('id', 'role')
            ->get();

        return $members;
    }

    public function createMember(Project $project, array $data, User $user)
    {
        // 自分がowner/adminかチェック（users()リレーションを使用）
        $this->checkOwnerOrAdmin($project, $user);

        // デフォルトロール設定
        $role = $validated['role'] ?? 'project_member';

        // 既にメンバーかチェック（users()リレーションを使用）
        $this->checkAlreadyProjectMember($project, $data['user_id']);

        // 自分自身を追加しようとしていないかチェック
        if ($data['user_id'] == $user->id) {
            return response()->json([
                'message' => 'あなたは既にこのプロジェクトのメンバーです',
            ], 409);
        }

        // メンバーシップ作成（users()リレーションのattach()を使用）
        $project->users()->attach($data['user_id'], [
            'role' => $role,
        ]);

        // ユーザー情報を含めて返す
        $user = $project->users()->find($data['user_id']);

        return $user;
    }

    public function deleteMember(Project $project, User $user, int $userId)
    {
        // 自分が所属しているかチェック
        $this->checkBelongsProject($project, $user);

        // 削除対象のユーザーを取得
        $targetUser = $project->users()
            ->where('users.id', $userId)
            ->first();

        if (!$targetUser) {
            return response()->json([
                'message' => 'User is not a member of this project.',
            ], 404);
        }

        // 自分がowner/adminかチェック（users()リレーションを使用）
        $this->checkOwnerOrAdmin($project, $user);

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
    }


    // 自分がオーナーまたは管理者かチェック
    private function checkOwnerOrAdmin(Project $project, User  $user)
    {
        $myUser = $project->users()
            ->where('users.id', $user->id)
            ->first();

        if (!$myUser || !in_array($myUser->pivot->role, ['project_owner', 'project_admin'])) {
            return response()->json([
                'message' => 'プロジェクトを編集する権限がありません',
            ], 403);
        }
    }

    //自分が操作対象のプロジェクトに所属しているかチェック
    private function checkBelongsProject(Project $project, User $user)
    {
        $isMember = $project->users()
            ->where('users.id', $user->id)
            ->exists();

        if (!$isMember) {
            return response()->json([
                'message' => 'このプロジェクトにアクセスする権限がありません',
            ], 403);
        }
    }

    // 既にメンバーかチェック（users()リレーションを使用）
    private function checkAlreadyProjectMember(Project $project, int $userId)
    {
        $existingUser = $project->users()
            ->where('users.id', $userId)
            ->first();

        if ($existingUser) {
            return response()->json([
                'message' => 'このユーザーは既にプロジェクトのメンバーです',
            ], 409);
        }
    }
}
