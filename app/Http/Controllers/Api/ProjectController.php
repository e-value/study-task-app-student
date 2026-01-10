<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Project\StoreProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\UseCases\Project\CreateProjectUseCase;
use App\UseCases\Project\GetProjectUseCase;
use App\UseCases\Project\GetProjectsUseCase;
use App\UseCases\Project\UpdateProjectUseCase;
use App\UseCases\Project\DeleteProjectUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;


class ProjectController extends ApiController
{
    public function __construct(
        private CreateProjectUseCase $createProjectUseCase,
        private GetProjectUseCase $getProjectUseCase,
        private UpdateProjectUseCase $updateProjectUseCase,
        private DeleteProjectUseCase $deleteProjectUseCase,
        private GetProjectsUseCase $getProjectsUseCase,
    ) {}

    /**
     * 自分が所属しているプロジェクト一覧を返す
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $projects = $this->getProjectsUseCase->execute($request->user());

        return ProjectResource::collection($projects);
    }

    /**
     * プロジェクト新規作成
     */
    public function store(StoreProjectRequest $request): JsonResponse
    {
        $project = $this->createProjectUseCase->execute(
            $request->validated(),
            $request->user()
        );

        return $this->response()->createdWithResource(
            new ProjectResource($project),
            'プロジェクトを作成しました'
        );
    }

    /**
     * プロジェクト詳細を返す
     */
    public function show(Request $request, Project $project): ProjectResource
    {
        $project = $this->getProjectUseCase->execute($project, $request->user());
        return new ProjectResource($project);
    }

    /**
     * プロジェクト更新
     */
    public function update(UpdateProjectRequest $request, Project $project): JsonResponse
    {
        $project = $this->updateProjectUseCase->execute(
            $project,
            $request->validated(),
            $request->user()
        );

        return $this->response()->successWithResource(
            new ProjectResource($project),
            'プロジェクトを更新しました'
        );
    }

    /**
     * プロジェクト削除
     */
    public function destroy(Request $request, Project $project): JsonResponse
    {
        $this->deleteProjectUseCase->execute($project, $request->user());

        return $this->response()->success(null, 'プロジェクトを削除しました');
    }
}
