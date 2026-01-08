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
     * プロジェクト詳細を取得
     *
     * @param Project $project プロジェクト
     * @param User $user ユーザー
     * @return Project
     */
    public function getProject(Project $project, User $user): Project
    {
        // メンバーかチェック
        if (!$this->isProjectMember($project, $user)) {
            throw new \Exception('このプロジェクトにアクセスする権限がありません');
        }

        // リレーションをロード
        // ⚠️ ERROR_HANDLING_LESSON用：タイポを意図的に作成
        // タスク機能の修正中に誤って users → usrs に変更してしまった想定
        $project->load(['usrs', 'tasks.createdBy']);

        return $project;
    }

    /**
     * プロジェクトを更新する
     *
     * @param Project $project プロジェクト
     * @param array $data 更新データ（name, is_archived）
     * @param User $user ユーザー
     * @return Project
     */
    public function updateProject(Project $project, array $data, User $user): Project
    {
        // オーナーまたは管理者かチェック
        if (!$this->isProjectOwnerOrAdmin($project, $user)) {
            throw new \Exception('プロジェクトを更新する権限がありません（オーナー・管理者のみ）');
        }

        $project->update($data);
        $project->load(['users', 'tasks.createdBy']);

        return $project;
    }

    /**
     * プロジェクトを削除する
     *
     * @param Project $project プロジェクト
     * @param User $user ユーザー
     * @return void
     */
    public function deleteProject(Project $project, User $user): void
    {
        // 権限チェック（オーナーのみ）
        if (!$this->isProjectOwner($project, $user)) {
            throw new \Exception('プロジェクトを削除する権限がありません（オーナーのみ）');
        }

        $project->delete();
    }

    /**
     * プロジェクトのメンバーかチェック
     *
     * @param Project $project プロジェクト
     * @param User $user ユーザー
     * @return bool
     */
    private function isProjectMember(Project $project, User $user): bool
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
    private function isProjectOwnerOrAdmin(Project $project, User $user): bool
    {
        $myUser = $this->getProjectUser($project, $user);

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
    private function isProjectOwner(Project $project, User $user): bool
    {
        $myUser = $this->getProjectUser($project, $user);

        if (!$myUser) {
            return false;
        }

        return $myUser->pivot->role === 'project_owner';
    }

    /**
     * プロジェクトのユーザー情報を取得
     *
     * @param Project $project プロジェクト
     * @param User $user ユーザー
     * @return User|null
     */
    private function getProjectUser(Project $project, User $user): ?User
    {
        return $project->users()
            ->where('users.id', $user->id)
            ->first();
    }
}
