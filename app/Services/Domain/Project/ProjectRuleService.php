<?php

namespace App\Services\Domain\Project;

use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;

/**
 * プロジェクトのビジネスルールService
 * 
 * 【なぜDomain/Project/に置くか】
 * - 「プロジェクトに〇〇できるか」を判断する = Projectが主役
 * - 複数Model（Project, User）を見る
 * - 権限判定のルールを管理
 */
class ProjectRuleService
{
    /**
     * プロジェクトメンバーか検証（メンバーでなければ例外）
     * 
     * @param Project $project プロジェクト
     * @param User $user ユーザー
     * @return void
     * @throws AuthorizationException
     */
    public function ensureMember(Project $project, User $user): void
    {
        $isMember = $project->users()
            ->where('users.id', $user->id)
            ->exists();

        if (!$isMember) {
            throw new AuthorizationException('このプロジェクトにアクセスする権限がありません');
        }
    }

    /**
     * プロジェクトオーナーか検証（オーナーでなければ例外）
     * 
     * @param Project $project プロジェクト
     * @param User $user ユーザー
     * @return void
     * @throws AuthorizationException
     */
    public function ensureOwner(Project $project, User $user): void
    {
        $role = $this->getRole($project, $user);

        if ($role !== 'project_owner') {
            throw new AuthorizationException('プロジェクトを削除する権限がありません（オーナーのみ）');
        }
    }

    /**
     * プロジェクトオーナーまたは管理者か検証（どちらでもなければ例外）
     * 
     * @param Project $project プロジェクト
     * @param User $user ユーザー
     * @return void
     * @throws AuthorizationException
     */
    public function ensureOwnerOrAdmin(Project $project, User $user): void
    {
        $role = $this->getRole($project, $user);

        if (!in_array($role, ['project_owner', 'project_admin'])) {
            throw new AuthorizationException('プロジェクトを更新する権限がありません（オーナー・管理者のみ）');
        }
    }

    /**
     * プロジェクト内でのユーザーのロールを取得
     * 
     * @param Project $project プロジェクト
     * @param User $user ユーザー
     * @return string|null ロール（メンバーでない場合はnull）
     */
    private function getRole(Project $project, User $user): ?string
    {
        return $project->users()
            ->where('users.id', $user->id)
            ->first()
            ?->pivot->role;
    }
}
