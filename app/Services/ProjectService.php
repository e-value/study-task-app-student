<?php

namespace App\Services;

use App\Models\Project;
use App\Models\User;

class ProjectService
{
    /**
     * プロジェクトを作成する
     *
     * @param array $data プロジェクト作成データ（name, is_archived）
     * @param User $user 作成者
     * @return Project
     */
    public function createProject(array $data, User $user): Project
    {
        // プロジェクト作成
        $project = Project::create([
            'name' => $data['name'],
            'is_archived' => $data['is_archived'] ?? false,
        ]);

        // 作成者を自動的にオーナーとして追加
        $project->users()->attach($user->id, [
            'role' => 'project_owner',
        ]);

        // リレーションをロード
        $project->load(['users']);

        return $project;
    }

    // 他のメソッドもここに記載
    public function updateProject() {}

    // 他のメソッドもここに記載


    // privateメソッドも定義
}
