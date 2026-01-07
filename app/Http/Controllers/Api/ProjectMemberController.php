<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ProjectMemberResource;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Requests\ProjectMemberRequest;
use App\Services\ProjectMemberService;

class ProjectMemberController extends ApiController
{
    public function __construct(
        private ProjectMemberService $projectMemberService
    ) {
        $this->projectMemberService = $projectMemberService;
    }
    /**
     * プロジェクトのメンバー一覧を取得
     */
    public function index(ProjectMemberRequest $request, Project $project): AnonymousResourceCollection|JsonResponse
    {
        // 自分が所属しているかチェック
        // 2. メンバー一覧の取得
        // withPivot に 'id' を含めることで、Membership の ID も取得できます
        $members = $this->projectMemberService->indexProjectMember(
            $project,
            $request->user(),
        );

        // 3. Resourceに渡すだけ
        return ProjectMemberResource::collection($members);
    }

    /**
     * プロジェクトにメンバーを追加
     */
    public function store(ProjectMemberRequest $request, Project $project): JsonResponse
    {
        // 自分がowner/adminかチェック（users()リレーションを使用）
        // 既にメンバーかチェック（users()リレーションを使用）
        // 自分自身を追加しようとしていないかチェック
        // デフォルトロール設定
         // メンバーシップ作成（users()リレーションのattach()を使用）

        $user = $this->projectMemberService->createProjectMember(
            $project,
            $request->validated(),
            $request->user(),
        );

        return response()->json([
            'message' => 'メンバーを追加しました',
            'membership' => new ProjectMemberResource($user),
        ], 201);
    }

    /**
     * プロジェクトからメンバーを削除（users()リレーションを使用）
     */
    public function destroy(ProjectMemberRequest $request, Project $project, $userId): JsonResponse
    {
        
        // 自分が所属しているかチェック
        // 削除対象のユーザーを取得
        // Owner維持チェック（Owner削除後に0人になる場合は不可）
        // 自分がowner/adminかチェック（users()リレーションを使用）
        // 未完了タスクチェック
        // 削除実行（users()リレーションのdetach()を使用）
        $this->projectMemberService->deleteProjectMember(
            $project, 
            $userId,
            $request->user(),
        );

        return response()->json([
            'message' => 'メンバーを削除しました',
        ]);
    }
}
