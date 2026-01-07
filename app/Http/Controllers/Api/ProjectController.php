<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Services\ProjectService;
use App\Http\Requests\ProjectRequest;

class ProjectController extends ApiController
{
    public function __construct(
        private ProjectService $projectService
    ) {}

    /**
     * 自分が所属しているプロジェクト一覧を返す
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $projects = $request->user()
            ->projects()
            ->with('users')
            ->orderBy('created_at', 'desc')
            ->get();

        return ProjectResource::collection($projects);
    }

    /**
     * プロジェクト新規作成
     */
    public function store(ProjectRequest $request): JsonResponse
    {
        $project = $this->projectService->createProject(
            $request->validated(),
            $request->user()
        );

        return (new ProjectResource($project))
            ->additional(['message' => 'プロジェクトを作成しました'])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * プロジェクト詳細を返す
     */
    public function show(Request $request, Project $project): ProjectResource
    {
        $project = $this->projectService->getProject($project, $request->user());

        return new ProjectResource($project);
    }

    /**
     * プロジェクト更新
     */
    public function update(ProjectRequest $request, Project $project): ProjectResource|JsonResponse
    {
        $project = $this->projectService->updateProject(
            $project,
            $request->validated(),
            $request->user(),
        );

        return (new ProjectResource($project))
            ->additional(['message' => 'プロジェクトを更新しました']);
    }

    /**
     * プロジェクト削除
     */
    public function destroy(Request $request, Project $project): JsonResponse
    {

        $project = $this->projectService->deleteProject($project, $request->users());

        return response()->json([
            'message' => 'プロジェクトを削除しました',
        ]);
    }
}
