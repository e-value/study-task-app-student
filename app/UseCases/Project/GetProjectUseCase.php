<?php

namespace App\UseCases\Project;

use App\Models\Project;
use App\Models\User;
use App\Services\Project\ProjectRules;

/**
 * プロジェクト詳細取得UseCase
 */
class GetProjectUseCase
{
    public function __construct(
        private ProjectRules $projectRules,
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
        // メンバーかチェック（システム全体ルール）
        $this->projectRules->ensureMember($project, $user);

        // リレーションをロード
        $project->load(['users', 'tasks.createdBy']);

        return $project;
    }
}
