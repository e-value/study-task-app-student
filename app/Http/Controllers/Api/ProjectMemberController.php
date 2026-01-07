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
    public function index(Request $request, Project $project): AnonymousResourceCollection
    {
        $members = $this->projectMemberService->getMembers($project, $request->user());
        return ProjectMemberResource::collection($members);
    }

    /**
     * プロジェクトにメンバーを追加
     */
    public function store(ProjectMemberRequest $request, Project $project): JsonResponse
    {
        $user = $this->projectMemberService->addMember(
            $project,
            $request->validated(),
            $request->user()
        );

        return $this->response()->created([
            'membership' => new ProjectMemberResource($user),
        ], 'メンバーを追加しました');
    }

    /**
     * プロジェクトからメンバーを削除
     */
    public function destroy(Request $request, Project $project, $userId): JsonResponse
    {
        $this->projectMemberService->removeMember($project, (int)$userId, $request->user());

        return $this->response()->success(null, 'メンバーを削除しました');
    }
}
