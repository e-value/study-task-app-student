<?php

namespace App\UseCases\Membership;

use App\Models\Project;
use App\Models\User;
use App\Services\Domain\Project\ProjectRuleService;
use Illuminate\Database\Eloquent\Collection;

/**
 * プロジェクトメンバー一覧取得UseCase
 */
class GetMembersUseCase
{
    public function __construct(
        private ProjectRuleService $projectRule,
    ) {}

    /**
     * メンバー一覧取得の流れを組み立てる
     * 
     * @param Project $project プロジェクト
     * @param User $user ユーザー
     * @return Collection
     */
    public function execute(Project $project, User $user): Collection
    {
        // 権限チェック
        $this->projectRule->ensureMember($project, $user);

        // メンバー一覧取得
        return $project->users()
            ->withPivot('id', 'role')
            ->get();
    }
}
