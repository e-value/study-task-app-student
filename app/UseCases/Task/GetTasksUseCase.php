<?php

namespace App\UseCases\Task;

use App\Models\Project;
use App\Models\User;
use App\Services\Domain\Project\ProjectRuleService;
use Illuminate\Database\Eloquent\Collection;

/**
 * タスク一覧取得UseCase
 */
class GetTasksUseCase
{
    public function __construct(
        private ProjectRuleService $projectRule,
    ) {}

    /**
     * タスク一覧取得の流れを組み立てる
     * 
     * @param Project $project プロジェクト
     * @param User $user ユーザー
     * @return Collection
     */
    public function execute(Project $project, User $user): Collection
    {
        // ビジネスルール検証
        $this->projectRule->ensureMember($project, $user);

        // タスク一覧取得
        return $project->tasks()
            ->with('createdBy')
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
