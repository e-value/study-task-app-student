<?php

namespace App\Services;

use App\Models\Project;
use Illuminate\Support\Collection;
use Illuminate\Auth\Access\AuthorizationException;

class ProjectMemberService
{
    // 一覧取得
    public function index(Project $project, int $userId): Collection
    {
        $this->checkProjectMembership($project, $userId);
        
        return $project->users()
            ->withPivot('id', 'role')
            ->get();
    }

    // メンバー追加
    public function store(array $data, Project $project, int $userId)
    {
        $this->checkOwnerOrAdmin($project, $userId);
        
        $role = $data['role'] ?? 'project_member';
        
        $existingUser = $project->users()
            ->where('users.id', $data['user_id'])
            ->first();

        if ($existingUser) {
            throw new \Exception('このユーザーは既にプロジェクトのメンバーです');
        }

        if ($data['user_id'] == $userId) {
            throw new \Exception('あなたは既にこのプロジェクトのメンバーです');
        }

        $project->users()->attach($data['user_id'], [
            'role' => $role,
        ]);

        return $project->users()->find($data['user_id']);
    }

    // メンバー削除
    public function destroy(Project $project, int $targetUserId, int $currentUserId): void
    {

        $this->checkProjectMembership($project, $currentUserId);
        
        $targetUser = $project->users()
            ->where('users.id', $targetUserId)
            ->first();

        if (!$targetUser) {
            throw new \Exception('User is not a member of this project.');
        }

        $this->checkOwnerOrAdmin($project, $currentUserId);

        if ($targetUser->pivot->role === 'project_owner') {
            $ownerCount = $project->users()
                ->wherePivot('role', 'project_owner')
                ->count();

            if ($ownerCount <= 1) {
                throw new \Exception('プロジェクトの最後のオーナーは削除できません');
            }
        }

        $hasIncompleteTasks = $project->tasks()
            ->where('created_by', $targetUserId)
            ->whereIn('status', ['todo', 'doing'])
            ->exists();

        if ($hasIncompleteTasks) {
            throw new \Exception('未完了のタスクがあるメンバーは削除できません');
        }

        $project->users()->detach($targetUserId);
    }

    // メンバーシップのチェック
    private function checkProjectMembership(Project $project, int $userId): void
    {
        $isMember = $project->users()
            ->where('users.id', $userId)
            ->exists();

        if (!$isMember) {
            throw new AuthorizationException('このプロジェクトにアクセスする権限がありません');
        }
    }

    // オーナーor管理者チェック
    private function checkOwnerOrAdmin(Project $project, int $userId): void
    {
        $myUser = $project->users()
            ->where('users.id', $userId)
            ->first();

        if (!$myUser || !in_array($myUser->pivot->role, ['project_owner', 'project_admin'])) {
            throw new AuthorizationException('メンバーを追加する権限がありません（オーナーまたは管理者のみ）');
        }
    }
}

