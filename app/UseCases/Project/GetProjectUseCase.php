<?php

namespace App\UseCases\Project;

use App\Models\Project;
use App\Models\User;
use App\Services\Domain\Project\ProjectRuleService;

/**
 * プロジェクト詳細取得UseCase
 */
class GetProjectUseCase
{
    public function __construct(
        private ProjectRuleService $projectRule,
    ) {}

    /**
     * プロジェクト詳細取得の流れを組み立てる
     * 
     * @param Project $project プロジェクト
     * @param User $user ユーザー
     * @return Project
     */
    public function execute(Project $project, User $user): Project
    {
        // メンバーかチェック
        $this->projectRule->ensureMember($project, $user);

        // リレーションをロード
        $project->load(['users', 'tasks.createdBy']);

        return $project;
    }
}
