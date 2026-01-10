<?php

namespace App\UseCases\Membership;

use App\Models\Project;
use App\Models\User;
use App\Services\Project\ProjectRules;
use App\Exceptions\ConflictException;

/**
 * プロジェクトメンバー追加UseCase
 */
class AddMemberUseCase
{
    public function __construct(
        private ProjectRules $projectRules,
    ) {}

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
        // 1. ビジネスルール検証
        // ========================================
        // 既にメンバーかチェック（システム全体ルール - Projectドメイン）
        $this->projectRules->ensureNotMember($project, $userId);

        // 自分自身を追加しようとしていないかチェック（UseCase固有ルール）
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
     * 【なぜprivateメソッドに置くか】
     * - AddMemberUseCaseでしか使わない
     * - 他のUseCaseで使う予定がない（RemoveMemberは別の制約があるため）
     * - ロジックが単純
     * 
     * @param int $targetUserId 対象ユーザーID
     * @param int $currentUserId 現在のユーザーID
     * @return void
     * @throws ConflictException
     */
    private function ensureNotSelf(int $targetUserId, int $currentUserId): void
    {
        if ($targetUserId === $currentUserId) {
            throw new ConflictException('自分自身を追加することはできません');
        }
    }
}
