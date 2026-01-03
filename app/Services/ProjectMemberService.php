<?php

namespace App\Services;

use App\Models\Project;
use App\Models\User;

class ProjectMemberService
{
    /**
     * メンバーシップ作成
     *
     * @param array $data プロジェクト作成データ（name, is_archived）
     * @param User $user 作成者
     * @return Project
     */
    public function addMember(array $data, User $user): User
    {
        $project = Project::findOrFail($data['project_id']);
        $userId = $data['user_id'];
        $role = 'project_member'; 

        // メンバーシップ作成（users()リレーションのattach()を使用）
        $project->users()->attach($userId, [
            'role' => $role,
        ]);

        // リレーションをロード
        $project->load(['users']);

        // ユーザー情報を含めて返す
        $user = $project->users()->find($userId);
        
        return $user;
    }
    
    // 他のメソッドもここに記載
    /**
     * プロジェクトメンバーを削除する
     *
     * @param Project $project 削除するプロジェクト
     * @return bool 削除成功時true
     */
    public function deleteProjectMember(Project $project): bool
    {
        return $project->delete();
    }

    // privateメソッドも定義
    // 共通処理がないため、privateメソッドは不要
}
