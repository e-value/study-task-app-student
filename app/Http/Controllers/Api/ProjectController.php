<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Validator;
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
    public function show(ProjectRequest $request, Project $project): ProjectResource|JsonResponse
    {
        $project->load(['users', 'tasks.createdBy']); // showの中身が多くなってきたら切り分けでOKか？CRUD全てサービスに入れた方がいいのか？

        return new ProjectResource($project);
    }

    /**
     * プロジェクト更新
     */
    public function update(ProjectRequest $request, Project $project): ProjectResource|JsonResponse
    {
        $canUpdate = $this->projectService->canUpdate($project, $request->user());

        if (!$canUpdate) {
            return response()->json([
                'message' => 'プロジェクトを編集する権限がありません',
            ], 403);
        }

        $updatedProject = $this->projectService->updateProject(
            $project,
            $request->validated()->only(['name', 'is_archived'])
        );

        return (new ProjectResource($updatedProject))
            ->additional(['message' => 'プロジェクトを更新しました']);
    }

    /**
     * プロジェクト削除
     */
    public function destroy(ProjectRequest $request, Project $project): JsonResponse
    {
        $canDestroy = $this->projectService->canDestroy($project, $request->user());
        
        if (!$canDestroy){
            return response()->json([
                'message' => 'プロジェクトを削除する権限がありません（オーナーのみ）',
            ], 403);
        }

        $project->delete();

        return response()->json([
            'message' => 'プロジェクトを削除しました',
        ]);
    }
}
