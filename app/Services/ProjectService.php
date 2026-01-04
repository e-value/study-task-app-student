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

    /**
     * プロジェクトを更新する
     *
     * @param Project $project 
     * @param array $data プロジェクト作成データ（name, is_archived）
     * @param User $user 作成者
     * @return Project
     */
    public function updateProject(Project $project, array $data, User $user): Project
    {
         // 自分がオーナーまたは管理者かチェック（users()リレーションを使用）
        $this->checkRole($project,$user, "update");

        $project->update($data);
        $project->load(['users', 'tasks.createdBy']);

        return $project;
    }

    /**
     * プロジェクトを削除する
     *
     * @param Project $project 
     * @param User $user 作成者
     * @return void
     */
     public function deleteProject(Project $project, User $user): void
     {
        // 自分がオーナーかチェック
        $this->checkRole($project,$user, "delete");

        $project->delete();
     }


    // privateメソッドも定義
    private function checkRole(Project $project, User $user, String $method)
    {
        $myUser = $project->users()
        ->where('users.id', $user->id)
        ->first();

        if ($method === "delete") {
            if (!$myUser || $myUser->pivot->role !== 'project_owner') {
                abort(403, 'プロジェクトを削除する権限がありません（オーナーのみ）');
            }
        }

        if ($method === "update") {
            if (!$myUser || !in_array($myUser->pivot->role, ['project_owner', 'project_admin'])) {
                abort(403, 'プロジェクトを編集する権限がありません');
            }
        }
    }

}
