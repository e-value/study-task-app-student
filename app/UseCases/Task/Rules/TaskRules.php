<?php

namespace App\UseCases\Task\Rules;

use App\Models\Task;
use App\Exceptions\ConflictException;

/**
 * Taskドメイン内共通ルール
 * 
 * 配置理由：
 * - Task内の複数UseCaseで使用するルール（現在は2箇所で使用）
 *   - UpdateTaskUseCase
 *   - DeleteTaskUseCase
 * 
 * 注意：
 * - 1箇所でしか使わないルールはここに置かない
 * - 各UseCaseのprivateメソッドで十分な場合はそちらを優先
 */
class TaskRules
{
    /**
     * タスクが完了済みか判定
     * 
     * @param Task $task
     * @return bool
     */
    public function isDone(Task $task): bool
    {
        return $task->isDone();
    }

    /**
     * タスクが未完了か判定
     * 
     * @param Task $task
     * @return bool
     */
    public function isNotDone(Task $task): bool
    {
        return !$task->isDone();
    }

    /**
     * タスクが未完了であることを保証（完了済みなら例外）
     * 
     * 使用箇所：
     * - UpdateTaskUseCase: 完了済みタスクは更新不可
     * - DeleteTaskUseCase: 完了済みタスクは削除不可
     * 
     * @param Task $task
     * @return void
     * @throws ConflictException
     */
    public function ensureNotDone(Task $task): void
    {
        if ($task->isDone()) {
            throw new ConflictException('完了したタスクは操作できません');
        }
    }
}
