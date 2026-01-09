<?php

namespace App\UseCases\Task;

use App\Models\Task;
use App\Models\User;
use App\Services\Domain\Project\ProjectRuleService;

/**
 * タスク詳細取得UseCase
 */
class GetTaskUseCase
{
    public function __construct(
        private ProjectRuleService $projectRule,
    ) {}

    /**
     * タスク詳細取得の流れを組み立てる
     * 
     * @param Task $task タスク
     * @param User $user ユーザー
     * @return Task
     */
    public function execute(Task $task, User $user): Task
    {
        // 権限チェック
        $this->projectRule->ensureMember($task->project, $user);

        // リレーションをロード
        $task->load(['createdBy', 'project']);

        return $task;
    }
}
