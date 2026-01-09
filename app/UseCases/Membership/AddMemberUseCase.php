<?php

namespace App\UseCases\Membership;

use App\Models\Project;
use App\Models\User;
use App\Exceptions\ConflictException;

/**
 * プロジェクトメンバー追加UseCase
 */
class AddMemberUseCase
{

    /**
     * プロジェクトメンバー追加の流れを組み立てる
     * 
     * @param Project $project プロジェクト
     * @param array $data メンバー追加データ（user_id, role）
     * @param User $currentUser 現在のユーザー
     * @return User
     */
    public function execute(Project $project, array $data, User $currentUser): User
    {
        $userId = $data['user_id'];
        $role = $data['role'] ?? 'project_member';

        // ========================================
        // 1. ビジネスルール検証（Domain Service）
        // ========================================
        // 既にメンバーかチェック
        $this->ensureNotMember($project, $userId);

        // 自分自身を追加しようとしていないかチェック
        $this->ensureNotSelf($userId, $currentUser->id);

        // ========================================
        // 2. メンバーシップ作成
        // ========================================
        $project->users()->attach($userId, [
            'role' => $role,
        ]);

        // ========================================
        // 3. ユーザー情報を取得（pivot情報を含める）
        // ========================================
        return $project->users()
            ->withPivot('id', 'role')
            ->find($userId);
    }

    /**
     * 自分自身を追加しようとしていないか検証
     * 
     * @param int $userId 追加しようとするユーザーID
     * @param int $currentUserId 現在のユーザーID
     * @return void
     * @throws ConflictException
     */
    private function ensureNotSelf(int $userId, int $currentUserId): void
    {
        if ($userId == $currentUserId) {
            throw new ConflictException('あなたは既にこのプロジェクトのメンバーです');
        }
    }

    /**
     * 既にメンバーか検証（メンバーなら例外）
     * 
     * @param Project $project プロジェクト
     * @param int $userId ユーザーID
     * @return void
     * @throws ConflictException
     */
    private function ensureNotMember(Project $project, int $userId): void
    {
        $exists = $project->users()
            ->where('users.id', $userId)
            ->exists();

        if ($exists) {
            throw new ConflictException('既にメンバーです');
        }
    }
}
