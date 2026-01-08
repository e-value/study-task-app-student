<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Validator;

class ProjectController extends ApiController
{
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
        // バリデーションルールを定義
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'is_archived' => 'boolean',
        ]);

        // プロジェクト作成
        $project = Project::create([
            'name' => $validatedData['name'],
            'is_archived' => $validatedData['is_archived'] ?? false,
        ]);

        // 作成者を自動的にオーナーとして追加（users()リレーションのattach()を使用）
        $user = $request->user();
        $project->users()->attach($request->user()->id, ['role' => 'project_owner']);

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
        $isMember = $project->users()
            ->where('users.id', $request->user()->id)
            ->exists();

        // メンバーでなければエラーを返す
        // エラーコード: 403, エラーメッセージ: このプロジェクトにアクセスする権限がありません
        if(!$isMember) {
            return response()->json([
                'message'=>'このプロジェクトにアクセスする権限がありません'
            ], 403);
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
        $isAuthorized = $project->users()
            ->where('users.id', $request->user()->id)
            ->wherePivotIn('role', ['project_owner', 'project_admin'])
            ->exists();

        // 権限がなければエラーを返す
        // エラーコード: 403, エラーメッセージ: プロジェクトを更新する権限がありません
        if(!$isAuthorized) {
            return response()->json([
                'message'=>'このプロジェクトにアクセスする権限がありません'
            ], 403);
        }

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
        $isOwner = $project->users()
            ->where('users.id', $request->user()->id)
            ->wherePivotIn('role', ['project_owner'])
            ->exists();

        // 権限がなければエラーを返す
        // エラーコード: 403, エラーメッセージ: プロジェクトを削除する権限がありません
        if(!$isOwner) {
            return response()->json([
                'message'=>'プロジェクトを削除する権限がありません'
            ], 403);
        }

        $project->delete();

        return response()->json([
            'message' => 'プロジェクトを削除しました',
        ]);
    }
}
