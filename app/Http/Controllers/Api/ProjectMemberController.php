<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ProjectMemberResource;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProjectMemberController extends ApiController
{
    /**
     * プロジェクトのメンバー一覧を取得
     */
    public function index(Request $request, Project $project): AnonymousResourceCollection|JsonResponse
    {
        // 自分がプロジェクトのメンバーかチェック
        $user_id = auth()->user()->id;
        if (!$project->users()->find($user_id)){  
            // メンバーでなければエラーを返す
            // エラーコード: 403, エラーメッセージ: このプロジェクトにアクセスする権限がありません
            return response()->json(['message'=> 'このプロジェクトにアクセスする権限がありません'],403);
        };

        // メンバー一覧の取得
        $members = $project->users()
            ->withPivot('id', 'role')
            ->get();

        return ProjectMemberResource::collection($members);
    }

    /**
     * プロジェクトにメンバーを追加
     */
    public function store(Request $request, Project $project): JsonResponse
    {
        // 自分がプロジェクトのメンバーかチェック
        $user_id = auth()->user()->id;
        // メンバーでないかつ、owner/adminでない場合はエラーを返す
        if (!$project->users()->find($user_id)){
            return response()->json(['message'=> 'このプロジェクトにアクセスする権限がありません'],403);
        }
        else{
            $role = $project->users()->find($user_id)->pivot->role;

            if ($role !== "project_owner" or $role !== "project_admin"){
                // エラーコード: 403, エラーメッセージ: メンバーを追加する権限がありません
                return response()->json(['message'=> 'メンバーを追加する権限がありません'],403);
            }
        };

        // バリデーション
        $validated = $request->validate([
            'project_id' => [
                'required',
                'exists:projects,id',
                Rule::unique('memberships')->where(function($query) use($request) {
                    $query->where('user_id', $request->input('user_id'));
                }) // 同一の`user_id`との組み合わせで、すでに登録のあるレコードがないかを確認
            ],
            'user_id' => [
                'required',
                'exists:users,id',
                Rule::unique('memberships')->where(function($query) use($request) {
                    $query->where('project_id', $request->input('project_id'));
                }) // 同一の`project_id`との組み合わせで、すでに登録のあるレコードがないかを確認
            ],
            'roles' => ['nullable',Rule::in(['project_owner', 'project_admin', 'project_member']),],
        ]);

        // デフォルトロール設定
        $role = $validated['role'] ?? 'project_member';

        // 既にメンバーかチェック（users()リレーションを使用）
        if (in_array($validated['user_id'], $project->users()->pluck('id')->toArray())){
            // 既にメンバーならエラーを返す
            return response()->json(['message'=> '既にこのプロジェクトのメンバーです'],409);
        }
        // 自分自身を追加しようとしていないかチェック
        else{
            if ($validated['user_id'] === auth()->user()->id){
                // 自分自身を追加しようとしていればエラーを返す
                // エラーコード: 409, エラーメッセージ: あなたは既にこのプロジェクトのメンバーです
                return response()->json(['message'=> 'あなたは既にこのプロジェクトのメンバーです'],409);
            }
        };

        // メンバーシップ作成（users()リレーションのattach()を使用）
        $project->users()->attach($validated['user_id'], [
            'role' => $role,
        ]);

        // ユーザー情報を含めて返す
        $user = $project->users()->find($validated['user_id']);

        return response()->json([
            'message' => 'メンバーを追加しました',
            'membership' => new ProjectMemberResource($user),
        ], 201);
    }

    /**
     * プロジェクトからメンバーを削除（users()リレーションを使用）
     */
    public function destroy(Request $request, Project $project, $userId): JsonResponse
    {
        // 自分がプロジェクトのメンバーかチェック
        $user_id = auth()->user()->id;
        if (!$project->users()->find($user_id)){
            // メンバーでなければエラーを返す
            // エラーコード: 403, エラーメッセージ: このプロジェクトにアクセスする権限がありません
            return response()->json(['message'=> 'このプロジェクトにアクセスする権限がありません'],403);
        };

        // 削除対象のユーザーを取得
        $targetUser = $project->users()
            ->where('users.id', $userId)
            ->first();

        // 削除対象のユーザーが存在しない場合はエラーを返す
        if (!$targetUser){
            // エラーコード: 404, エラーメッセージ: ユーザーはこのプロジェクトのメンバーではありません。
            return response()->json(['message'=> 'ユーザーはこのプロジェクトのメンバーではありません。'],404);
        };

        // 自分がowner/adminかチェック（users()リレーションを使用）
        $role = $project->users()->find($user_id)->pivot->role;

        // メンバーでないかつ、owner / adminでない場合はエラーを返す
        if ($role!=="project_owner" or $role!=="project_admin"){
            // エラーコード: 403, エラーメッセージ: メンバーを削除する権限がありません（オーナーまたは管理者のみ）
            return response()->json(['message'=> 'メンバーを削除する権限がありません（オーナーまたは管理者のみ）'],403);
        };

        // Owner維持チェック（Owner削除後に0人になる場合は不可）
        if ($targetUser->pivot->role === 'project_owner') {
            $ownerCount = $project->users()
                ->wherePivot('role', 'project_owner')
                ->count();

            if ($ownerCount <= 1) {
                return response()->json([
                    'message' => 'プロジェクトの最後のオーナーは削除できません',
                ], 409);
            }
        }

        // 未完了タスクチェック
        $hasIncompleteTasks = $project->tasks()
            ->where('created_by', $userId)
            ->whereIn('status', ['todo', 'doing'])
            ->exists();

        if ($hasIncompleteTasks) {
            return response()->json([
                'message' => '未完了のタスクがあるメンバーは削除できません',
            ], 409);
        }

        // 削除実行（users()リレーションのdetach()を使用）
        $project->users()->detach($userId);

        return response()->json([
            'message' => 'メンバーを削除しました',
        ]);
    }
}
