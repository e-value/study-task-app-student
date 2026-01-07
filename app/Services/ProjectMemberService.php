<?php

namespace App\Services;

use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Exceptions\ConflictException;

class ProjectMemberService
{
    /**
     * プロジェクトのメンバー一覧を取得
     *
     * @param Project $project プロジェクト
     * @param User $user ユーザー
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getMembers(Project $project, User $user): \Illuminate\Database\Eloquent\Collection
    {
        // 権限チェック
        if (!$this->isProjectMember($project, $user)) {
            throw new AuthorizationException('このプロジェクトにアクセスする権限がありません');
        }

        return $project->users()
            ->withPivot('id', 'role')
            ->get();
    }

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
     * @param User $currentUser 現在のユーザー
     * @return void
     */
    public function removeMember(Project $project, int $userId, User $currentUser): void
    {
        // 権限チェック（メンバーか）
        if (!$this->isProjectMember($project, $currentUser)) {
            throw new AuthorizationException('このプロジェクトにアクセスする権限がありません');
        }

        // 権限チェック（オーナーまたは管理者か）
        if (!$this->isProjectOwnerOrAdmin($project, $currentUser)) {
            throw new AuthorizationException('メンバーを削除する権限がありません（オーナーまたは管理者のみ）');
        }

        // 削除対象のユーザーを取得
        $targetUser = $project->users()
            ->where('users.id', $userId)
            ->first();

        if (!$targetUser) {
            throw new ModelNotFoundException('指定されたユーザーはこのプロジェクトのメンバーではありません');
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
    private function isProjectMember(Project $project, User $user): bool
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
    private function isProjectOwnerOrAdmin(Project $project, User $user): bool
    {
        $myUser = $this->getProjectUser($project, $user);

        if (!$myUser) {
            return false;
        }

        return in_array($myUser->pivot->role, ['project_owner', 'project_admin']);
    }

    /**
     * プロジェクトのユーザー情報を取得
     *
     * @param Project $project プロジェクト
     * @param User $user ユーザー
     * @return User|null
     */
    private function getProjectUser(Project $project, User $user): ?User
    {
        return $project->users()
            ->where('users.id', $user->id)
            ->first();
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
            throw new ConflictException('このユーザーは既にプロジェクトのメンバーです');
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
            throw new ConflictException('あなたは既にこのプロジェクトのメンバーです');
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
                throw new ConflictException('プロジェクトの最後のオーナーは削除できません');
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
            throw new ConflictException('未完了のタスクがあるメンバーは削除できません');
        }
    }
}
