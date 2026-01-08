<?php

namespace App\Services;

use App\Models\Project;
use App\Models\User;

class ProjectMemberService
{
    /**
     * プロジェクトにメンバーを追加する
     *
     * @param Project $project メンバーを追加するプロジェクト
     * @param array $data メンバー追加データ（user_id, role）
     * @param User $user 実行者
     * @return User 追加されたユーザー
     */
    public function addMember(Project $project, array $data, User $user): User
    {
        // 自分がowner/adminかチェック（users()リレーションを使用）
        $this->ensureUserIsOwnerOrAdmin($project, $user);

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
        return $project->users()->find($data['user_id']);
    }

    // 他のメソッドもここに記載
    /**
     * プロジェクトのメンバーを削除する
     *
     * @param Project $project メンバーを削除するプロジェクト
     * @param int $userId 削除対象のユーザーID
     * @param User $user 実行者
     * @return User
     */
    public function removeMember(Project $project, int $userId, User $user): void
    {
        // 自分が所属しているかチェック
        $isMember = $project->users()
            ->where('users.id', $user->id)
            ->exists();

        if (!$isMember) {
            abort(403, 'このプロジェクトにアクセスする権限がありません');
        }

        // 削除対象のユーザーを取得
        $targetUser = $project->users()
            ->where('users.id', $userId)
            ->first();

        if (!$targetUser) {
            abort(404, 'User is not a member of this project.');
        }

        // 自分がowner/adminかチェック（users()リレーションを使用）
        $this->ensureUserIsOwnerOrAdmin($project, $user);

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


    // privateメソッドも定義
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
    private function ensureUserIsOwnerOrAdmin(Project $project, User $user): void
    {
        $myUser = $this->getProjectMember($project, $user);

        if (!$myUser || !in_array($myUser->pivot->role, ['project_owner', 'project_admin'])) {
            abort(403, 'メンバーを削除する権限がありません（オーナーまたは管理者のみ）');
        }
    }
}
