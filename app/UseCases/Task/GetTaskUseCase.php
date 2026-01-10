<?php

namespace App\UseCases\Task;

use App\Models\Task;
use App\Models\User;
use App\Services\Project\ProjectRules;

/**
 * タスク詳細取得UseCase
 */
class GetTaskUseCase
{
    public function __construct(
        private ProjectRules $projectRules,
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
        // 権限チェック（システム全体ルール）
        $this->projectRules->ensureMember($task->project, $user);

        // リレーションをロード
        $task->load(['createdBy', 'project']);

        return $task;
    }
}
