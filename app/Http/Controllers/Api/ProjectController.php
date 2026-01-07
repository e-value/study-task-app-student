<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Services\ProjectService;
use App\Http\Requests\ProjectRequest;

class ProjectController extends ApiController
{
    public function __construct(
        private ProjectService $projectService
    ) {
        $this->projectService = $projectService;
    }

    /**
     * 自分が所属しているプロジェクト一覧を返す
     */
    public function index(ProjectRequest $request): AnonymousResourceCollection
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
    public function show(Project $project, ProjectRequest $request): ProjectResource|JsonResponse
    {

        $project = $this->projectService->showProject(
            $project,
            $request->user()
        );

        return new ProjectResource($project);
    }

    /**
     * プロジェクト更新
     */
    public function update(Project $project, ProjectRequest $request): ProjectResource|JsonResponse
    {
        // 自分がオーナーまたは管理者かチェック（users()リレーションを使用）
        // プロジェクトの更新
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
    public function destroy(Project $project, ProjectRequest $request): JsonResponse
    {
        $this->projectService->deleteProject(
            $project,
            $request->user(),
        );

        return response()->json([
            'message' => 'プロジェクトを削除しました',
        ]);
    }
}
