<?php

namespace App\Services;

use App\Models\Project;
use App\Models\User;

class ProjectMemberService
{
    /**
     * プロジェクトにメンバーを追加する
     *
     * @param Project $project プロジェクト
     * @param array $data メンバー追加データ（user_id, role）
     * @param User $currentUser 現在のユーザー
     * @return User
     */
    public function addMember(Project $project, array $data, User $currentUser): User
    {
        $userId = $data['user_id'];
        $role = $data['role'] ?? 'project_member';

        // 既にメンバーかチェック
        $this->checkExistingMember($project, $userId);

        // 自分自身を追加しようとしていないかチェック
        $this->checkSelfAddition($userId, $currentUser->id);

        // メンバーシップ作成
        $project->users()->attach($userId, [
            'role' => $role,
        ]);

        // ユーザー情報を取得（pivot情報を含める）
        return $project->users()
            ->withPivot('id', 'role')
            ->find($userId);
    }

    /**
     * プロジェクトからメンバーを削除する
     *
     * @param Project $project プロジェクト
     * @param int $userId 削除対象のユーザーID
     * @return void
     */
    public function removeMember(Project $project, int $userId): void
    {
        // 削除対象のユーザーを取得
        $targetUser = $project->users()
            ->where('users.id', $userId)
            ->first();

        if (!$targetUser) {
            throw new \Exception('User is not a member of this project.');
        }

        // Owner維持チェック
        $this->checkOwnerMaintenance($project, $targetUser);

        // 未完了タスクチェック
        $this->checkIncompleteTasks($project, $userId);

        // 削除実行
        $project->users()->detach($userId);
    }

    /**
     * プロジェクトのメンバーかチェック
     *
     * @param Project $project プロジェクト
     * @param User $user ユーザー
     * @return bool
     */
    public function isProjectMember(Project $project, User $user): bool
    {
        return $project->users()
            ->where('users.id', $user->id)
            ->exists();
    }

    /**
     * プロジェクトのオーナーまたは管理者かチェック
     *
     * @param Project $project プロジェクト
     * @param User $user ユーザー
     * @return bool
     */
    public function isProjectOwnerOrAdmin(Project $project, User $user): bool
    {
        $myUser = $project->users()
            ->where('users.id', $user->id)
            ->first();

        if (!$myUser) {
            return false;
        }

        return in_array($myUser->pivot->role, ['project_owner', 'project_admin']);
    }

    /**
     * 既にメンバーかチェック
     *
     * @param Project $project プロジェクト
     * @param int $userId ユーザーID
     * @return void
     * @throws \Exception
     */
    private function checkExistingMember(Project $project, int $userId): void
    {
        $existingUser = $project->users()
            ->where('users.id', $userId)
            ->first();

        if ($existingUser) {
            throw new \Exception('このユーザーは既にプロジェクトのメンバーです');
        }
    }

    /**
     * 自分自身を追加しようとしていないかチェック
     *
     * @param int $userId 追加しようとするユーザーID
     * @param int $currentUserId 現在のユーザーID
     * @return void
     * @throws \Exception
     */
    private function checkSelfAddition(int $userId, int $currentUserId): void
    {
        if ($userId == $currentUserId) {
            throw new \Exception('あなたは既にこのプロジェクトのメンバーです');
        }
    }

    /**
     * Owner維持チェック（Owner削除後に0人になる場合は不可）
     *
     * @param Project $project プロジェクト
     * @param User $targetUser 削除対象のユーザー
     * @return void
     * @throws \Exception
     */
    private function checkOwnerMaintenance(Project $project, User $targetUser): void
    {
        if ($targetUser->pivot->role === 'project_owner') {
            $ownerCount = $project->users()
                ->wherePivot('role', 'project_owner')
                ->count();

            if ($ownerCount <= 1) {
                throw new \Exception('プロジェクトの最後のオーナーは削除できません');
            }
        }
    }

    /**
     * 未完了タスクチェック
     *
     * @param Project $project プロジェクト
     * @param int $userId ユーザーID
     * @return void
     * @throws \Exception
     */
    private function checkIncompleteTasks(Project $project, int $userId): void
    {
        $hasIncompleteTasks = $project->tasks()
            ->where('created_by', $userId)
            ->whereIn('status', ['todo', 'doing'])
            ->exists();

        if ($hasIncompleteTasks) {
            throw new \Exception('未完了のタスクがあるメンバーは削除できません');
        }
    }
}

