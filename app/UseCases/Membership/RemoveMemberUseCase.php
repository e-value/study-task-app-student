<?php

namespace App\UseCases\Membership;

use App\Models\Project;
use App\Models\User;
use App\Services\Project\ProjectRules;
use App\Exceptions\ConflictException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * プロジェクトメンバー削除UseCase
 */
class RemoveMemberUseCase
{
    public function __construct(
        private ProjectRules $projectRules,
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
        // 1. ビジネスルール検証
        // ========================================
        // 権限チェック（システム全体ルール - オーナーまたは管理者か）
        $this->projectRules->ensureOwnerOrAdmin($project, $currentUser);

        // 削除対象のユーザーを取得
        $targetUser = $this->ensureMemberExists($project, $userId);

        // Owner維持チェック（UseCase固有ルール）
        $this->ensureOwnerMaintenance($project, $targetUser);

        // 未完了タスクチェック（UseCase固有ルール）
        $this->ensureNoIncompleteTasks($project, $userId);

        // ========================================
        // 2. 削除実行
        // ========================================
        $project->users()->detach($userId);
    }


    /**
     * 削除対象のユーザーに未完了タスクがないか検証
     * 
     * 【なぜprivateメソッドに置くか】
     * - RemoveMemberUseCaseでしか使わない
     * - Taskドメインを参照するが、Task側のルールではない
     * - メンバー削除という文脈でのみ必要な制約
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
     * 【なぜprivateメソッドに置くか】
     * - RemoveMemberUseCaseでしか使わない
     * - メンバー削除という文脈でのみ必要な制約
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
