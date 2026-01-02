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
     * @param Project $project プロジェクト
     * @param array $data 更新データ（name, is_archived）
     * @return Project
     */
    public function updateProject(Project $project, array $data): Project
    {
        $project->update($data);
        $project->load(['users', 'tasks.createdBy']);

        return $project;
    }

    /**
     * プロジェクトを削除する
     *
     * @param Project $project プロジェクト
     * @return void
     */
    public function deleteProject(Project $project): void
    {
        $project->delete();
    }

    /**
     * プロジェクトのメンバーかチェック
     *
     * @param Project $project プロジェクト
     * @param User $user ユーザー
     * @return bool
     */
    public function isProjectMember(Project $project, User $user): bool
    {
        return $project->users()
            ->where('users.id', $user->id)
            ->exists();
    }

    /**
     * プロジェクトのオーナーまたは管理者かチェック
     *
     * @param Project $project プロジェクト
     * @param User $user ユーザー
     * @return bool
     */
    public function isProjectOwnerOrAdmin(Project $project, User $user): bool
    {
        $myUser = $project->users()
            ->where('users.id', $user->id)
            ->first();

        if (!$myUser) {
            return false;
        }

        return in_array($myUser->pivot->role, ['project_owner', 'project_admin']);
    }

    /**
     * プロジェクトのオーナーかチェック
     *
     * @param Project $project プロジェクト
     * @param User $user ユーザー
     * @return bool
     */
    public function isProjectOwner(Project $project, User $user): bool
    {
        $myUser = $project->users()
            ->where('users.id', $user->id)
            ->first();

        if (!$myUser) {
            return false;
        }

        return $myUser->pivot->role === 'project_owner';
    }
}
