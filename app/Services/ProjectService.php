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
    /**
     * プロジェクトを更新する
     *
     * @param Project $project 更新対象のプロジェクト
     * @param array $data プロジェクト更新データ（name, is_archived）
     * @param User $user 実行者
     * @return Project
     */
    public function updateProject(Project $project, array $data, User $user): Project
    {
        // 自分がオーナーまたは管理者かチェック（users()リレーションを使用）
        $myUser = $project->users()
            ->where('users.id', $user->id)
            ->first();

        if (!$myUser || !in_array($myUser->pivot->role, ['project_owner', 'project_admin'])) {
            abort(403, 'このプロジェクトにアクセスする権限がありません');
        }

        $project->update($data);
        $project->load(['users', 'tasks.createdBy']);

        return $project;
    }

    // 他のメソッドもここに記載
    /**
     * プロジェクトを削除する
     *
     * @param Project $project
     * @param User $user 実行者
     * @return void
     */
    public function deleteProject(Project $project, User $user): void
    {
        // 自分がオーナーかチェック
        $this->ensureUserIsOwner($project, $user);

        // プロジェクト削除
        $project->delete();
    }

    /**
     * ユーザーがプロジェクトのメンバーかチェック
     *
     * @param Project $project
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    private function getProjectMember(Project $project, User $user)
    {
        return $project->users()
            ->where('users.id', $user->id)
            ->first();
    }

    /**
     * ユーザーがプロジェクトのオーナーまたは管理者かチェック
     * 権限がない場合は403エラーを投げる
     *
     * @param Project $project
     * @param User $user
     * @return void
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    private function ensureUserIsOwnerOrAdmin(Project $project, User $user): void
    {
        $myUser = $this->getProjectMember($project, $user);

        if (!$myUser || !in_array($myUser->pivot->role, ['project_owner', 'project_admin'])) {
            abort(403, 'このプロジェクトにアクセスする権限がありません');
        }
    }

    // privateメソッドも定義
    /**
     * ユーザーがプロジェクトのオーナーかチェック
     * 権限がない場合は403エラーを投げる
     *
     * @param Project $project
     * @param User $user
     * @return void
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    private function ensureUserIsOwner(Project $project, User $user): void
    {
        $myUser = $this->getProjectMember($project, $user);

        if (!$myUser || $myUser->pivot->role !== 'project_owner') {
            abort(403, 'プロジェクトを削除する権限がありません（オーナーのみ）');
        }
    }
}
