<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ProjectMemberRequest;
use App\Http\Resources\ProjectMemberResource;
use App\Models\Project;
use App\Services\ProjectMemberService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

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
        try {
            $members = $this->projectMemberService->getMembers($project, $request->user());
            return ProjectMemberResource::collection($members);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 403);
        }
    }

    /**
     * プロジェクトにメンバーを追加
     */
    public function store(ProjectMemberRequest $request, Project $project): JsonResponse
    {
        try {
            $user = $this->projectMemberService->addMember(
                $project,
                $request->validated(),
                $request->user()
            );

            return response()->json([
                'message' => 'メンバーを追加しました',
                'membership' => new ProjectMemberResource($user),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 409);
        }
    }

    /**
     * プロジェクトからメンバーを削除
     */
    public function destroy(Request $request, Project $project, $userId): JsonResponse
    {
        try {
            $this->projectMemberService->removeMember($project, (int)$userId, $request->user());

            return response()->json([
                'message' => 'メンバーを削除しました',
            ]);
        } catch (\Exception $e) {
            $statusCode = str_contains($e->getMessage(), 'not a member') ? 404 : 403;
            return response()->json([
                'message' => $e->getMessage(),
            ], $statusCode);
        }
    }
}
