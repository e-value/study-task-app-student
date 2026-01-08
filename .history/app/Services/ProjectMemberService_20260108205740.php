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
    public function store(array $data, Project $project, int $userId): array
    {
        try {
            $this->checkOwnerOrAdmin($project, $userId);
        } catch (AuthorizationException $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
        
        $role = $data['role'] ?? 'project_member';
        
        $existingUser = $project->users()
            ->where('users.id', $data['user_id'])
            ->first();

        if ($existingUser) {
            return [
                'success' => false,
                'message' => 'このユーザーは既にプロジェクトのメンバーです',
            ];
        }

        if ($data['user_id'] == $userId) {
            return [
                'success' => false,
                'message' => 'あなたは既にこのプロジェクトのメンバーです',
            ];
        }

        $project->users()->attach($data['user_id'], [
            'role' => $role,
        ]);

        $user = $project->users()->find($data['user_id']);

        return [
            'success' => true,
            'data' => $user,
        ];
    }

    // メンバー削除
    public function destroy(Project $project, int $targetUserId, int $currentUserId): array
    {
        try {
            $this->checkProjectMembership($project, $currentUserId);
        } catch (AuthorizationException $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
        
        $targetUser = $project->users()
            ->where('users.id', $targetUserId)
            ->first();

        if (!$targetUser) {
            return [
                'success' => false,
                'message' => 'User is not a member of this project.',
            ];
        }

        try {
            $this->checkOwnerOrAdmin($project, $currentUserId);
        } catch (AuthorizationException $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }

        if ($targetUser->pivot->role === 'project_owner') {
            $ownerCount = $project->users()
                ->wherePivot('role', 'project_owner')
                ->count();

            if ($ownerCount <= 1) {
                return [
                    'success' => false,
                    'message' => 'プロジェクトの最後のオーナーは削除できません',
                ];
            }
        }

        $hasIncompleteTasks = $project->tasks()
            ->where('created_by', $targetUserId)
            ->whereIn('status', ['todo', 'doing'])
            ->exists();

        if ($hasIncompleteTasks) {
            return [
                'success' => false,
                'message' => '未完了のタスクがあるメンバーは削除できません',
            ];
        }

        $project->users()->detach($targetUserId);

        return [
            'success' => true,
        ];
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

