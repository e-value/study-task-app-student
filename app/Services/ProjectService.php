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
        $this->loadProjectRelations($project);

        return $project;
    }
    /**
     * プロジェクトを更新する
     *
     * @param array $data プロジェクト作成データ（name, is_archived）
     * @param Project $Project プロジェクトそのもの
     * @return Project
     */
    // 他のメソッドもここに記載
    public function updateProject(Project $project, array $data): Project
     {
        $project->update([
            'name' => $data['name'],
            'is_archived' => $data['is_archived'] ?? false,
        ]);

        // リレーションをロード
        $this->loadProjectRelations($project);
    
        return $project;

     }

    // 他のメソッドもここに記載
    /**
     * プロジェクトを削除する
     *
     * @param Project $project 削除するプロジェクト
     * @return bool 削除成功時true
     */
    public function deleteProject(Project $project): bool
    {
        return $project->delete();
    }

    // privateメソッドも定義
    /**
     * プロジェクトのリレーションをロードする
     *
     * @param Project $project プロジェクト
     * @return void
     */
    private function loadProjectRelations(Project $project): void
    {
        $project->load(['users']);
    }
}
