<?php

namespace App\UseCases\Project;

use App\Models\Project;
use App\Models\User;
use App\Services\Project\ProjectRules;

/**
 * プロジェクト更新UseCase
 */
class UpdateProjectUseCase
{
    public function __construct(
        private ProjectRules $projectRules,
    ) {}

    /**
     * プロジェクト更新の流れを組み立てる
     * 
     * @param Project $project プロジェクト
     * @param array $data 更新データ（name, is_archived）
     * @param User $user ユーザー
     * @return Project
     */
    public function execute(Project $project, array $data, User $user): Project
    {
        // オーナーまたは管理者かチェック（システム全体ルール）
        $this->projectRules->ensureOwnerOrAdmin($project, $user);

        // プロジェクト更新
        $project->update($data);
        $project->load(['users', 'tasks.createdBy']);

        return $project;
    }
}
