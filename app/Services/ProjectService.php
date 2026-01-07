<?php

namespace App\Services;

use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;

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
    public function updateProject(Project $project, array $data, User $user)
    {

        // 自分がオーナーまたは管理者かチェック
        $this->checkOwnerOrAdmin($project, $user);

        $project->update([
            'name' => $data['name'],
            'is_archived' => $data['is_archived'],
        ]);

        $project->load(['users']);

        return $project;
    }

    public function getProject(Project $project, User $user)
    {
        //自分が操作対象のプロジェクトに所属しているかチェック
        $this->checkBelongsProject($project, $user);

        $project->load(['users', 'tasks.createdBy']);

        return $project;
    }

    public function deleteProject(Project $project, User $user)
    {
        // 自分がオーナーかチェック
        $this->checkOwner($project, $user);

        $project->delete();
    }

    // 自分がオーナーかチェック
    private function checkOwner(Project $project, User  $user)
    {
        $myUser = $project->users()
            ->where('users.id', $user->id)
            ->first();

        if (!$myUser || $myUser->pivot->role !== 'project_owner') {
            return response()->json([
                'message' => 'プロジェクトを削除する権限がありません（オーナーのみ）',
            ], 403);
        }
    }

    // 自分がオーナーまたは管理者かチェック
    private function checkOwnerOrAdmin(Project $project, User  $user)
    {
        $myUser = $project->users()
            ->where('users.id', $user->id)
            ->first();

        if (!$myUser || !in_array($myUser->pivot->role, ['project_owner', 'project_admin'])) {
            return response()->json([
                'message' => 'プロジェクトを編集する権限がありません',
            ], 403);
        }
    }

    //自分が操作対象のプロジェクトに所属しているかチェック
    private function checkBelongsProject(Project $project, User $user)
    {
        $isMember = $project->users()
            ->where('users.id', $user->id)
            ->exists();

        if (!$isMember) {
            throw new AuthorizationException('プロジェクトを編集する権限がありません');
        }
    }
}
