<?php

namespace App\UseCases\Project;

use App\Models\Project;
use App\Models\User;
use App\Services\Domain\Project\ProjectRuleService;

/**
 * プロジェクト削除UseCase
 */
class DeleteProjectUseCase
{
    public function __construct(
        private ProjectRuleService $projectRule,
    ) {}

    /**
     * プロジェクト削除の流れを組み立てる
     * 
     * @param Project $project プロジェクト
     * @param User $user ユーザー
     * @return void
     */
    public function execute(Project $project, User $user): void
    {
        // 権限チェック（オーナーのみ）
        $this->projectRule->ensureOwner($project, $user);

        // プロジェクト削除
        $project->delete();
    }
}
