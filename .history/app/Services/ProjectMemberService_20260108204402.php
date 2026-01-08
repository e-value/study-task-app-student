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
        // オーナー/管理者チェック
        $this->checkOwnerOrAdmin($project, $userId);
        
        // デフォルトロール設定
        $role = $data['role'] ?? 'project_member';
        
        // 既にメンバーかチェック
        $existingUser = $project->users()
            ->where('users.id', $data['user_id'])
            ->first();

        if ($existingUser) {
            throw new \Exception('このユーザーは既にプロジェクトのメンバーです');
        }

        // 自分自身を追加しようとしていないかチェック
        if ($data['user_id'] == $userId) {
            throw new \Exception('あなたは既にこのプロジェクトのメンバーです');
        }

        // メンバーシップ作成
        $project->users()->attach($data['user_id'], [
            'role' => $role,
        ]);

        // ユーザー情報を含めて返す
        return $project->users()->find($data['user_id']);
    }

    // メンバー削除
    public function destroy(Project $project, int $targetUserId, int $currentUserId): void
    {
        // 自分が所属しているかチェック
        $this->checkProjectMembership($project, $currentUserId);
        
        // 削除対象のユーザーを取得
        $targetUser = $project->users()
            ->where('users.id', $targetUserId)
            ->first();

        if (!$targetUser) {
            throw new \Exception('User is not a member of this project.');
        }

        // オーナー/管理者チェック
        $this->checkOwnerOrAdmin($project, $currentUserId);

        // Owner維持チェック
        if ($targetUser->pivot->role === 'project_owner') {
            $ownerCount = $project->users()
                ->wherePivot('role', 'project_owner')
                ->count();

            if ($ownerCount <= 1) {
                throw new \Exception('プロジェクトの最後のオーナーは削除できません');
            }
        }

        // 未完了タスクチェック
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

    // オーナー/管理者チェック
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

