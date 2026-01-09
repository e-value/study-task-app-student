<?php

namespace App\UseCases\Task\Shared\Rules;

use App\Models\Task;
use App\Exceptions\ConflictException;

class EnsureTaskNotDone
{
    public function __invoke(Task $task): void
    {
        if ($task->isDone()) {
            throw new ConflictException('完了したタスクは操作できません');
        }
    }
}
