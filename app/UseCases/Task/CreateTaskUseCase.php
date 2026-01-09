<?php

namespace App\UseCases\Task;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Services\Domain\Project\ProjectRuleService;

/**
 * タスク作成UseCase
 * 
 * 【UseCaseの責務】
 * - 「誰を」「どの順番で」呼ぶか決める司令塔
 * - 自分ではビジネスルールを判断しない
 * - Domain Serviceに判断を任せる
 */
class CreateTaskUseCase
{
    public function __construct(
        private ProjectRuleService $projectRule,
    ) {}

    /**
     * タスク作成の流れを組み立てる
     * 
     * @param array $data タスク作成データ（title, description）
     * @param Project $project プロジェクト
     * @param User $user 作成者
     * @return Task
     */
    public function execute(array $data, Project $project, User $user): Task
    {
        // ========================================
        // 1. ビジネスルール検証（Domain Service）
        //    → UseCaseは「呼ぶだけ」で判断しない
        // ========================================
        $this->projectRule->ensureMember($project, $user);

        // ========================================
        // 2. データ作成（Eloquent直接）
        // ========================================
        $task = Task::create([
            'project_id' => $project->id,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'status' => 'todo',
            'created_by' => $user->id,
        ]);

        // ========================================
        // 3. リレーションロード
        // ========================================
        $task->load('createdBy');

        return $task;
    }
}
