<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ProjectMemberResource;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Requests\ProjectMemberStoreRequest;
use App\Services\ProjectMemberService;

class ProjectMemberController extends ApiController
{
    public function __construct(
        private ProjectMemberService $projectMemberService
    ) {}
    /**
     * プロジェクトのメンバー一覧を取得
     */
    public function index(Request $request, Project $project): AnonymousResourceCollection|JsonResponse
    {
        // 自分が所属しているかチェック
        $isMember = $project->users()
            ->where('users.id', $request->user()->id)
            ->exists();

        if (!$isMember) {
            return response()->json([
                'message' => 'このプロジェクトにアクセスする権限がありません',
            ], 403);
        }

        // 2. メンバー一覧の取得
        // withPivot に 'id' を含めることで、Membership の ID も取得できます
        $members = $project->users()
            ->withPivot('id', 'role')
            ->get();

        // 3. Resourceに渡すだけ
        return ProjectMemberResource::collection($members);
    }

    /**
     * プロジェクトにメンバーを追加
     */
    public function store(ProjectMemberStoreRequest $request, Project $project): JsonResponse
    {
        $user = $this->projectMemberService->addMember(
            $project,
            $request->validated(),
            $request->user()
        );

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
        $this->projectMemberService->removeMember(
            $project,
            (int)$userId,
            $request->user()
        );

        return response()->json([
            'message' => 'メンバーを削除しました',
        ]);
    }
}
