<?php

namespace App\UseCases\Task;

use App\Models\Task;
use App\Models\User;
use App\UseCases\Task\Rules\TaskRules;
use App\Services\Project\ProjectRules;

/**
 * タスク削除UseCase
 */
class DeleteTaskUseCase
{
    public function __construct(
        private ProjectRules $projectRules,
        private TaskRules $taskRules,
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
        // 権限チェック（システム全体ルール）
        $this->projectRules->ensureMember($task->project, $user);

        // タスクが完了していないか検証（ドメイン内ルール）
        $this->taskRules->ensureNotDone($task);

        // タスク削除
        $task->delete();
    }
}
