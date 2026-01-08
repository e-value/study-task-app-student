<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;

class ProjectMembershipService
{
    public function fetchMembers(Project $project) // 返り値の宣言の宣言として適切なものわからず（Userの配列を返す意図を示したかった）
    {
        // 今後検索や絞り込みが生じることを想定して切り出す

        $members = $project->users()
            ->withPivot('id', 'role')
            ->get();

        return $members;
    }

    public function addMember(Project $project, int $userId, string $role)
    {
        $project->users()->attach($userId, [
            'role' => $role,
        ]);
    }

    public function isOwnerOrAdmin(Project $project, User $user): bool
    {

        $myUser = $project->users()
            ->where('users.id', $user->id)
            ->first();
        
        return myUser and in_array($myUser->pivot->role, ['project_owner', 'project_admin']);
    }

    public function checkCanDestroy(Project $project, User $user, int $targetUserId): array // ProjectMembershipの型宣言は適切？
    {
        // 削除対象のユーザーを取得
        $targetUser = $project->users()
            ->where('users.id', $targetUserId)
            ->first();

        if (!$targetUser) {
            return ['User is not a member of this project.',404];
        }

        // 自分がowner/adminかチェック
        $isOwnerOrAdmin = $this->isOwnerOrAdmin([
            $project, 
            $user
        ]);

        if (!$isOwnerOrAdmin) {
            return ['メンバーを削除する権限がありません（オーナーまたは管理者のみ）',403];
        }

        // Owner維持チェック（Owner削除後に0人になる場合は不可）
        if ($targetUser->pivot->role === 'project_owner') {
            $ownerCount = $project->users()
                ->wherePivot('role', 'project_owner')
                ->count();

            if ($ownerCount <= 1) {
                return ['プロジェクトの最後のオーナーは削除できません',409];
            }
        }

        // 未完了タスクチェック
        $hasIncompleteTasks = $project->tasks()
            ->where('created_by', $targetUserId)
            ->whereIn('status', ['todo', 'doing'])
            ->exists();

        if ($hasIncompleteTasks) {
            return ['未完了のタスクがあるメンバーは削除できません', 409];
        }
    }
}
