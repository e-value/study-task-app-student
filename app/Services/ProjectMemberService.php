<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Http\Requests\ProjectMemberRequest;


class ProjectMemberService
{
    /**
     * プロジェクトを作成する
     *
     * @param array $data プロジェクト作成データ（name, is_archived）
     * @param User $user 作成者
     */

     public function indexProjectMember(Project $project, User $user)
     {
        $this->isMemberProjectMember($project, $user);

        // 2. メンバー一覧の取得
        // withPivot に 'id' を含めることで、Membership の ID も取得できます
        $members = $project->users()
            ->withPivot('id', 'role')
            ->get();

        return $members;
 
     }

    public function createProjectMember(Project $project,array $data, User $user)
    {

    $this->myUserProjectMember($project, $user);

    $this->existingUser($project, $data);
    
    $this->alreadyMember($data ,$user);

    // デフォルトロール設定
    $role = $validated['role'] ?? 'project_member';

   // メンバーシップ作成（users()リレーションのattach()を使用）
    $project->users()->attach($data['user_id'], [
        'role' => $role,
    ]);

    // ユーザー情報を含めて返す
    $user = $project->users()->find($data['user_id']);

    return $user;

    }

    public function deleteProjectMember(Project $project, User $user, $userId)
    {
        $this->hasIncompleteTasks($project, $userId, $user);
        $project->users()->detach($userId);
    }

    private function  hasIncompleteTasks(Project $project, $userId, User $user) 
    {

        $this->isMemberProjectMember($project, $user);

        $this->targetUser($project, $userId);

        $this->myUserProjectMember($project, $user);

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

       return $hasIncompleteTasks;
    }


     // 自分が所属しているかチェック
    private function isMemberProjectMember(Project $project, User $user) {

        $isMember = $project->users()
        ->where('users.id', $user->id)
        ->exists();

    if (!$isMember) {
        return response()->json([
            'message' => 'このプロジェクトにアクセスする権限がありません',
        ], 403);
    }

    return $isMember;

    }

    private function myUserProjectMember(Project $project, User $user) {

        $myUser = $project->users()
        ->where('users.id', $user->id)
        ->first();

    if (!$myUser || !in_array($myUser->pivot->role, ['project_owner', 'project_admin'])) {
        return response()->json([
            'message' => 'メンバーを追加する権限がありません（オーナーまたは管理者のみ）',
        ], 403);
    }

    return $myUser;
    }

    // 既にメンバーかチェック（users()リレーションを使用）
    private function existingUser(Project $project, array $data) {

        // 既にメンバーかチェック（users()リレーションを使用）
         $existingUser = $project->users()
               ->where('users.id', $data['user_id'])
               ->first();
   
           if ($existingUser) {
               return response()->json([
                   'message' => 'このユーザーは既にプロジェクトのメンバーです',
               ], 409);
           }
           return $existingUser;
    }


    // 削除対象のユーザーを取得
    // Owner維持チェック（Owner削除後に0人になる場合は不可）
    private function  targetUser(Project $project, $userId) 
    {
        $targetUser = $project->users()
        ->where('users.id', $userId)
        ->first();

    if (!$targetUser) {
        return response()->json([
            'message' => 'User is not a member of this project.',
        ], 404);
    }

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

    return  $targetUser;

    }

    // 自分自身を追加しようとしていないかチェック
     private function alreadyMember(array $data , User $user) 
     {
        // 自分自身を追加しようとしていないかチェック
        if ($data['user_id'] == $user->id) {
            return response()->json([
                'message' => 'あなたは既にこのプロジェクトのメンバーです',
            ], 409);
        }
     }

    // privateメソッドも定義
}
