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

    public function showProject(Project $project, User $user)
    {
        $this->isMemberProject(
            $project,
            $user
        );
        $project = $project->load(['users', 'tasks.createdBy']);
        return $project;
    }

    /**
     * プロジェクトをする
     *
     * @param array $data プロジェクト作成データ（name, is_archived）
     * @param User $user 作成者
     * @return Project
     */
    public function updateProject(Project $project, array $data, User $user)
    {

        $this->myUserProject($project, $user);
        
        $project->update(
            $data(['name', 'is_archived'])
        );
        $project->load(['users', 'tasks.createdBy']);

        return $project;
    }

    public function deleteProject(Project $project, User $user)
    {
        $this->myUserProject($project, $user);
        $project->delete();

    }


    // privateメソッドも定義


    // ログイン中のユーザーがプロジェクトに所属しているかチェック（users()リレーションを使用）
    private function isMemberProject(Project $project, User $user)
    {
        $isMember = $project->users()
            ->where('users.id', $user->id)
            ->exists();

            if (!$isMember) {
                return response()->json([
                    'message' => 'このプロジェクトにアクセスする権限がありません',
                ], 403);
            }

        return $isMember;
    }


    // ログイン中のユーザーの権限チェック（users()リレーションを使用）
    private function myUserProject(Project $project, User $user) 
    {

        $myUser = $project->users()
        ->where('users.id', $user->id)
        ->first();

       // 自分がオーナーかチェック
        if (!$myUser || $myUser->pivot->role !== 'project_owner') {
            return response()->json([
                'message' => 'プロジェクトを削除する権限がありません（オーナーのみ）',
            ], 403);
        }

        // 自分がオーナーまたは管理者かチェック
        elseif (!$myUser || !in_array($myUser->pivot->role, ['project_owner', 'project_admin'])) {
            return response()->json([
                'message' => 'プロジェクトを編集する権限がありません',
            ], 403);
        }

        return $myUser;
    }




}
