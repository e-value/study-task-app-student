<?php

namespace App\UseCases\Task;

use App\Models\Task;
use App\Models\User;
use App\UseCases\Task\Shared\Rules\EnsureTaskNotDone;
use App\Services\Domain\Project\ProjectRuleService;

/**
 * タスク削除UseCase
 */
class DeleteTaskUseCase
{
    public function __construct(
        private ProjectRuleService $projectRule,
        private EnsureTaskNotDone $ensureTaskNotDone,
    ) {}

    /**
     * タスク削除の流れを組み立てる
     * 
     * @param Task $task タスク
     * @param User $user ユーザー
     * @return void
     */
    public function execute(Task $task, User $user): void
    {
        // 権限チェック
        $this->projectRule->ensureMember($task->project, $user);

        // タスクが完了していないか検証
        ($this->ensureTaskNotDone)($task);

        // タスク削除
        $task->delete();
    }
}
