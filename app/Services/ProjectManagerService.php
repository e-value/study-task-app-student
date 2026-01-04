<?php

namespace App\Services;

use App\Models\Project;
use App\Models\User;
use App\Models\Task;

class TaskService
{  /**
    * プロジェクトメンバーの一覧取得
    *
    * @param Project $project
    * @param User $user 作成者
    */
   public function indexProjectMember(Project $project, User $user)
   {
        // 自分が所属しているかチェック
        $this->checkBelongsToProject($project, $user, "index");

        // 2. メンバー一覧の取得
        // withPivot に 'id' を含めることで、Membership の ID も取得できます
        $members = $project->users()
            ->withPivot('id', 'role')
            ->get();

        //3. $membersをreturn
        return $members;
   }

    /**
     * プロジェクトメンバーを作成する
     *
     * @param Project $project
     * @param array $data プロジェクト作成データ（name, is_archived）
     * @param User $user 作成者
     * @return User
     */
    public function createProjectMember(Project $project,array $data, User $user): User
    {
        // 自分がowner/adminかチェック（users()リレーションを使用）
        $this->checkBelongsToProject($project, $user, "create");
      
        // デフォルトロール設定
        $role = $data['role'] ?? 'project_member';

        // 既にメンバーかチェック（users()リレーションを使用）
        $existingUser = $project->users()
            ->where('users.id', $data['user_id'])
            ->first();

        if ($existingUser) {
            abort(409, 'このユーザーは既にプロジェクトのメンバーです');
        }

        // 自分自身を追加しようとしていないかチェック
        if ($data['user_id'] == $user->id) {
            abort(409, 'あなたは既にこのプロジェクトのメンバーです');
        }

        // メンバーシップ作成（users()リレーションのattach()を使用）
        $project->users()->attach($data['user_id'], [
            'role' => $role,
        ]);

        // ユーザー情報を含めて返す
        $user = $project->users()->find($data['user_id']);

        return $user;
    }

    /**
     * プロジェクトメンバーんpの削除
     *
     * @param Project $project 
     * @param User $user 作成者
     * @param int $userId 削除するユーザーID
     * @return void
     */
    public function deleteProjectMember(Project $project, User $user, int $userId): void
    {
       $this->checkBelongsToProject($project, $user, "delete");

        // 削除対象のユーザーを取得
        $targetUser = $project->users()
            ->where('users.id', $userId)
            ->first();

        if (!$targetUser) {
            abort(404, 'User is not a member of this project.');
        }

        // 自分がowner/adminかチェック（users()リレーションを使用）
        $myUser = $project->users()
            ->where('users.id', $user->id)
            ->first();

        if (!$myUser || !in_array($myUser->pivot->role, ['project_owner', 'project_admin'])) {
            abort(403, 'メンバーを削除する権限がありません（オーナーまたは管理者のみ）');
        }

        // Owner維持チェック（Owner削除後に0人になる場合は不可）
        if ($targetUser->pivot->role === 'project_owner') {
            $ownerCount = $project->users()
                ->wherePivot('role', 'project_owner')
                ->count();

            if ($ownerCount <= 1) {
                abort(409, 'プロジェクトの最後のオーナーは削除できません');
            }
        }

        // 未完了タスクチェック
        $hasIncompleteTasks = $project->tasks()
            ->where('created_by', $userId)
            ->whereIn('status', ['todo', 'doing'])
            ->exists();

        if ($hasIncompleteTasks) {
            abort(409, '未完了のタスクがあるメンバーは削除できません');

        }

        // 削除実行（users()リレーションのdetach()を使用）
        $project->users()->detach($userId);
    }

    private function checkBelongsToProject(Project $project, User $user, $method): void
    {
          // 自分が所属しているかチェック
          $myUser = $project->users()
          ->where('users.id', $user->id)
          ->fist();
  
          if($method === "index" || $method === "delete") {
            if (!$myUser) {
                abort(403, 'このプロジェクトにアクセスする権限がありません');
            }
        }

        if($method === "create") {
            if (!$myUser || !$myUser->pivot->role || !in_array($myUser->pivot->role, ['project_owner', 'project_admin'])) {
                abort(403, 'メンバーを追加する権限がありません（オーナーまたは管理者のみ）');
            }
        }

    }
}
