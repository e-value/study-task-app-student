<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ProjectMemberResource;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\ProjectMemberRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
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
        $members = $this->projectMemberService->getMembers($project, $request->users());

        return ProjectMemberResource::collection($members);
    }

    /**
     * プロジェクトにメンバーを追加
     */
    public function store(ProjectMemberRequest $request, Project $project): JsonResponse
    {
        $user = $this->projectMemberService->createMember($project, $request->validated(), $request->user());

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
        $this->projectMemberService->deleteMember($project, $request->user(), $userId);

        return response()->json([
            'message' => 'メンバーを削除しました',
        ]);
    }
}
