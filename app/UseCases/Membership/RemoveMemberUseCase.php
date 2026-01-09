<?php

namespace App\UseCases\Membership;

use App\Models\Project;
use App\Models\User;
use App\Services\Domain\Project\ProjectRuleService;
use App\Exceptions\ConflictException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * プロジェクトメンバー削除UseCase
 */
class RemoveMemberUseCase
{
    public function __construct(
        private ProjectRuleService $projectRule,
    ) {}

    /**
     * プロジェクトメンバー削除の流れを組み立てる
     * 
     * @param Project $project プロジェクト
     * @param int $userId 削除対象のユーザーID
     * @param User $currentUser 現在のユーザー
     * @return void
     */
    public function execute(Project $project, int $userId, User $currentUser): void
    {
        // ========================================
        // 1. ビジネスルール検証（Domain Service）
        // ========================================
        // 権限チェック（メンバーか）
        $this->projectRule->ensureMember($project, $currentUser);

        // 権限チェック（オーナーまたは管理者か）
        $this->projectRule->ensureOwnerOrAdmin($project, $currentUser);

        // 削除対象のユーザーを取得
        $targetUser = $this->ensureMemberExists($project, $userId);

        // Owner維持チェック
        $this->ensureOwnerMaintenance($project, $targetUser);

        // 未完了タスクチェック
        $this->ensureNoIncompleteTasks($project, $userId);

        // ========================================
        // 2. 削除実行
        // ========================================
        $project->users()->detach($userId);
    }


    /**
     * 削除対象のユーザーに未完了タスクがないか検証
     * 
     * 未完了タスクがある場合は例外
     * 
     * @param Project $project プロジェクト
     * @param int $userId ユーザーID
     * @return void
     * @throws ConflictException
     */
    private function ensureNoIncompleteTasks(Project $project, int $userId): void
    {
        $hasIncompleteTasks = $project->tasks()
            ->where('created_by', $userId)
            ->whereIn('status', ['todo', 'doing'])
            ->exists();

        if ($hasIncompleteTasks) {
            throw new ConflictException('未完了のタスクがあるメンバーは削除できません');
        }
    }

    /**
     * オーナー削除後に0人にならないか検証
     * 
     * 最後のオーナーを削除しようとする場合は例外
     * 
     * @param Project $project プロジェクト
     * @param User $targetUser 削除対象のユーザー
     * @return void
     * @throws ConflictException
     */
    private function ensureOwnerMaintenance(Project $project, User $targetUser): void
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
     * 削除対象のユーザーがメンバーか検証
     * 
     * メンバーでない場合は例外
     * 
     * @param Project $project プロジェクト
     * @param int $userId ユーザーID
     * @return User
     * @throws ModelNotFoundException
     */
    private function ensureMemberExists(Project $project, int $userId): User
    {
        $user = $project->users()
            ->where('users.id', $userId)
            ->first();

        if (!$user) {
            throw new ModelNotFoundException('メンバーではありません');
        }

        return $user;
    }
}
