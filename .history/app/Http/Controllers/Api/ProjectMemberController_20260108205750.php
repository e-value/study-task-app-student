<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ProjectMemberResource;
use App\Http\Requests\AddProjectMemberRequest;
use App\Services\ProjectMemberService;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProjectMemberController extends ApiController
{
    // コンストラクタ
    public function __construct(
        private ProjectMemberService $projectMemberService
    ) {
    }

    /**
     * プロジェクトのメンバー一覧を取得
     */
    public function index(Request $request, Project $project): AnonymousResourceCollection
    {
        $members = $this->projectMemberService->index($project, $request->user()->id);
        
        return ProjectMemberResource::collection($members);
    }

    /**
     * プロジェクトにメンバーを追加
     */
    public function store(AddProjectMemberRequest $request, Project $project): JsonResponse
    {
        $result = $this->projectMemberService->store(
            $request->validated(),
            $project,
            $request->user()->id
        );

        if (!$result['success']) {
            return response()->json([
                'message' => $result['message'],
            ], 409);
        }

        return response()->json([
            'message' => 'メンバーを追加しました',
            'membership' => new ProjectMemberResource($result['data']),
        ], 201);
    }

    /**
     * プロジェクトからメンバーを削除（users()リレーションを使用）
     */
    public function destroy(Request $request, Project $project, $userId): JsonResponse
    {
        try {
            $this->projectMemberService->destroy($project, $userId, $request->user()->id);

            return response()->json([
                'message' => 'メンバーを削除しました',
            ]);
        } catch (\Exception $e) {
            $statusCode = $e->getMessage() === 'User is not a member of this project.' ? 404 : 409;
            
            return response()->json([
                'message' => $e->getMessage(),
            ], $statusCode);
        }
    }
}
