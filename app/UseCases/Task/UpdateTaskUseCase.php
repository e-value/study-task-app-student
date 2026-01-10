<?php

namespace App\UseCases\Task;

use App\Models\Task;
use App\Models\User;
use App\Services\Project\ProjectRules;
use App\UseCases\Task\Rules\TaskRules;

/**
 * タスク更新UseCase
 */
class UpdateTaskUseCase
{
    public function __construct(
        private ProjectRules $projectRules,
        private TaskRules $taskRules,
    ) {}

    /**
     * タスク更新の流れを組み立てる
     * 
     * @param Task $task タスク
     * @param array $data 更新データ（title, description, status）
     * @param User $user ユーザー
     * @return Task
     */
    public function execute(Task $task, array $data, User $user): Task
    {
        // 権限チェック（システム全体ルール）
        $this->projectRules->ensureMember($task->project, $user);

        // タスクが完了していないか検証（ドメイン内ルール）
        $this->taskRules->ensureNotDone($task);

        // タスク更新
        $task->update($data);
        $task->load('createdBy');

        return $task;
    }
}
