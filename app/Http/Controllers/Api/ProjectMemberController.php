<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Membership\AddMemberRequest;
use App\Http\Resources\ProjectMemberResource;
use App\Models\Project;
use App\UseCases\Membership\GetMembersUseCase;
use App\UseCases\Membership\AddMemberUseCase;
use App\UseCases\Membership\RemoveMemberUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProjectMemberController extends ApiController
{
    public function __construct(
        private GetMembersUseCase $getMembersUseCase,
        private AddMemberUseCase $addMemberUseCase,
        private RemoveMemberUseCase $removeMemberUseCase,
    ) {}

    /**
     * プロジェクトのメンバー一覧を取得
     */
    public function index(Request $request, Project $project): AnonymousResourceCollection
    {
        $members = $this->getMembersUseCase->execute($project, $request->user());
        return ProjectMemberResource::collection($members);
    }

    /**
     * プロジェクトにメンバーを追加
     */
    public function store(AddMemberRequest $request, Project $project): JsonResponse
    {
        $user = $this->addMemberUseCase->execute(
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
        $this->removeMemberUseCase->execute($project, (int)$userId, $request->user());

        return $this->response()->success(null, 'メンバーを削除しました');
    }
}
