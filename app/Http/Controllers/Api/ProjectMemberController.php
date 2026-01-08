<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ProjectMemberResource;
use App\Http\Requests\ProjectMemberRequest;
use App\Services\ProjectMembershipService;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProjectMemberController extends ApiController
{
    public function __construct(
        private ProjectMembershipService $projectMembershipService
    ){}
    /**
     * プロジェクトのメンバー一覧を取得
     */
    public function index(ProjectMemberRequest $request, Project $project): AnonymousResourceCollection|JsonResponse
    {
        // 2. メンバー一覧の取得
        $members = $this->projectMembershipService->fetchMembers($project);

        // 3. Resourceに渡すだけ
        return ProjectMemberResource::collection($members);
    }

    /**
     * プロジェクトにメンバーを追加
     */
    public function store(ProjectMemberRequest $request, Project $project): JsonResponse
    {
        // 自分がowner/adminかチェック
        $isOwnerOrAdmin = $this->projectMembershipService->isOwnerOrAdmin($project, $request->user());

        if (!$isOwnerOrAdmin) {
            return response()->json([
                'message' => 'メンバーを追加する権限がありません（オーナーまたは管理者のみ）',
            ], 403);
        }

        // バリデーション
        $validated = $request->validate();

        // デフォルトロール設定
        $role = $validated['role'] ?? 'project_member';

        // 既にメンバーかチェック（users()リレーションを使用）
        $existingUser = $project->users()
            ->where('users.id', $validated['user_id'])
            ->first();

        if ($existingUser) {
            return response()->json([
                'message' => 'このユーザーは既にプロジェクトのメンバーです',
            ], 409);
        }

        // 自分自身を追加しようとしていないかチェック
        if ($validated['user_id'] == $request->user()->id) {
            return response()->json([
                'message' => 'あなたは既にこのプロジェクトのメンバーです',
            ], 409);
        }

        // メンバーシップ作成（users()リレーションのattach()を使用）
        $userId = $validate['user_id'];
        $this->projectMembershipService->addMember($project, $userId);

        // ユーザー情報を含めて返す
        $user = $project->users()->find($userId);

        return response()->json([
            'message' => 'メンバーを追加しました',
            'membership' => new ProjectMemberResource($user),
        ], 201);
    }

    /**
     * プロジェクトからメンバーを削除（users()リレーションを使用）
     */
    public function destroy(Request $request, Project $project, $userId): JsonResponse
    {
        // 削除可能かチェック。不可の場合エラーコードとメッセージを受け取る
        list($errorMessage, $errorCode) = $this->projectMembershipService->checkCanDestroy([
            $project, 
            $targetUser
        ]);

        // エラーがある場合は返す
        if ($errorMessage and $errorCode){
            return response()->json([
                'message' => $errorMessage,
            ], $errorCode);
        }

        // 削除実行（users()リレーションのdetach()を使用）
        $project->users()->detach($userId);

        return response()->json([
            'message' => 'メンバーを削除しました',
        ]);
    }
}
