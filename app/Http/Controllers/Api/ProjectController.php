<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\Auth;

class ProjectController extends ApiController
{
     protected $currentUser;

     public function __construct()
     {
         $this->currentUser = Auth::user();
     }

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
    public function store(Request $request): JsonResponse
    {
        # バリデーションを記載
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string'],
            'is_archived' => ['required', 'boolean']
        ]);

        // プロジェクト作成
        $project = Project::create([
            'name' => $request->name,
            'is_archived' => $request->is_archived ?? false,
        ]);

        // 作成者を自動的にオーナーとして追加（users()リレーションのattach()を使用）
        $project->users()->attach($this->currentUser->id, ['role' => 'project_owner']);

        $project->load(['users']);

        return (new ProjectResource($project))
            ->additional(['message' => 'プロジェクトを作成しました'])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * プロジェクト詳細を返す
     */
    public function show(Request $request, Project $project): ProjectResource|JsonResponse
    {
        // 自分がプロジェクトのメンバーかチェック（users()リレーションを使用）
        if(!$project->users()->where('user_id', $this->currentUser->id)->exists())
        {
            // メンバーでなければエラーを返す
            // エラーコード: 403, エラーメッセージ: このプロジェクトにアクセスする権限がありません
            return response()->json(['message' => "このプロジェクトにアクセスする権限がありません"], 403);
        }

        // 読み込み（N+1問題を防ぐため）
        $project->load(['users', 'tasks.createdBy']);

        return new ProjectResource($project);
    }

    /**
     * プロジェクト更新
     */
    public function update(Request $request, Project $project): ProjectResource|JsonResponse
    {
        // 自分がオーナーまたは管理者かチェック（users()リレーションを使用）
        if($project->users()->where('user_id', $this->currentUser->id)->exists()){
            $currentUserRole = $project->users()->where('user_id', $this->currentUser->id)->role;
            if(!$currentUserRole == 'project_owner' &&  !$currentUserRole == 'project_admin' )
            {
                // 権限がなければエラーを返す
                // エラーコード: 403, エラーメッセージ: プロジェクトを更新する権限がありません
                return response()->json(['message' => "プロジェクトを更新する権限がありません"], 403);
            }
        }

        // バリデーションを記載
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string'],
            'is_archived' => ['required', 'boolean']
        ]);

        // プロジェクトを更新
        $project->update($request->only(['name', 'is_archived']));

        // 読み込み（N+1問題を防ぐため）
        $project->load(['users', 'tasks.createdBy']);

        return (new ProjectResource($project))
            ->additional(['message' => 'プロジェクトを更新しました']);
    }

    /**
     * プロジェクト削除
     */
    public function destroy(Request $request, Project $project): JsonResponse
    {
        // 自分がオーナーかチェック（users()リレーションを使用
        $currentUserRole = $project->users()->where('user_id', $this->currentUser->id)->role;
        if(!$currentUserRole == 'project_owner')
        {
            // 権限がなければエラーを返す
            // エラーコード: 403, エラーメッセージ: プロジェクトを削除する権限がありません
            return response()->json(['message' => "プロジェクトを削除する権限がありません"], 403);
        }

        $project->delete();

        return response()->json([
            'message' => 'プロジェクトを削除しました',
        ]);
    }
}
