<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ProjectMemberResource;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class ProjectMemberController extends ApiController
{
    /**
     * プロジェクトのメンバー一覧を取得
     */
    public function index(Request $request, Project $project): AnonymousResourceCollection|JsonResponse
    {
        // 自分がプロジェクトのメンバーかチェック
        $id = Auth::id();
        $login_user = $project->users()
        ->wherePivot('user_id', $id )
        ->exists();

        // メンバーでなければエラーを返す
        // エラーコード: 403, エラーメッセージ: このプロジェクトにアクセスする権限がありません
        if ($login_user == false)  {
            return response()->json([
                'message' => 'このプロジェクトにアクセスする権限がありません'
            ], 403);
         }

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
        $id = Auth::id();
        $login_user = $project->users()
        ->wherePivot('user_id', $id)
        ->exists();

        $user_role = $project->users()
        ->wherePivot('user_id', $id)
        ->wherePivotIn('role', ['project_owner', 'project_admin'])
        ->exists();

        // メンバーでないかつ、owner/adminでない場合はエラーを返す
        // エラーコード: 403, エラーメッセージ: メンバーを追加する権限がありません
        if (!($login_user && $user_role))  {
            return response()->json([
                'message' => 'メンバーを追加する権限がありません'
            ], 403);
         }

        // バリデーション
        // デフォルトロール設定
        $role = $validated['role'] ?? 'project_member';

        // 既にメンバーかチェック（users()リレーションを使用）
        $registered_member = $project->users()
        ->wherePivot('user_id', $id)->first();


        // 既にメンバーならエラーを返す
        if($registered_member) {
                        return response()->json([
                'message' => 'あなたは既にこのプロジェクトのメンバーです'
           ]);
        }

        // 自分自身を追加しようとしていないかチェック
        // 自分自身を追加しようとしていればエラーを返す
        // エラーコード: 409, エラーメッセージ: あなたは既にこのプロジェクトのメンバーです
        $validated =  Validator::make($request->all(),[
            'user_id' => 'required|exists:memberships,id,deleted_at,NULL',
        ]);

        if ($validated->fails()) {
            return response()->json([
                'message' => 'あなたは既にこのプロジェクトのメンバーです'
            ], 409);
        }

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
        $login_user = $project->users()
        ->wherePivot('user_id', $userId)
        ->exists();

        // メンバーでなければエラーを返す
        // エラーコード: 403, エラーメッセージ: このプロジェクトにアクセスする権限がありません
        if ($login_user == false)  {
            return response()->json([
                'message' => 'このプロジェクトにアクセスする権限がありません'
            ], 403);
         }

        // 削除対象のユーザーを取得
        $targetUser = $project->users()
            ->where('users.id', $userId)
            ->first();

        // 削除対象のユーザーが存在しない場合はエラーを返す
        // エラーコード: 404, エラーメッセージ: ユーザーはこのプロジェクトのメンバーではありません。
        if ($targetUser == false)  {
            return response()->json([
                'message' => 'ユーザーはこのプロジェクトのメンバーではありません。'
            ], 404);
         }

        // 自分がowner/adminかチェック（users()リレーションを使用）
        $user_role = $project->users()
        ->where('id', $userId)
        ->wherePivot('role', ['project_owner', 'project_admin'])
        ->exists();


        // メンバーでないかつ、owner / adminでない場合はエラーを返す
        // エラーコード: 403, エラーメッセージ: メンバーを削除する権限がありません（オーナーまたは管理者のみ）
        if(!($login_user && $user_role)) {
            return response()->json([
                'message' => 'メンバーを削除する権限がありません（オーナーまたは管理者のみ）'
            ], 404);
        }

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
