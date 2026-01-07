<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;


class TaskService
{

    private $taskProject;
    private $projectMember;
    /**
     * プロジェクトを作成する
     *
     * @param array $data プロジェクト作成データ（name, is_archived）
     * @param User $user 作成者
     * @return Task
     */
    public function createTask(array $data, Project $project, User $user): Task
    {
        // 自分が所属しているかチェック
        $this->projectMember = $project;
        $this->isMemberTask($user);

        // プロジェクト作成
        $task = Task::create([
            'project_id' => $project->id,
            'title' => $data['title'],
            'description' => $data['description'] ?? false,
            'status' => 'todo',
            'created_by' => $user->id,
        ]);

        // リレーションをロード
        // $project->load(['users']);
        $task->load('createdBy');

        return $task;
    }

    public function listTask(User $user,Project $project): Task
    {
        // 自分が所属しているかチェック
        $this->projectMember = $project;
        $this->isMemberTask($user);

        $tasks = $project->tasks()
        ->with('createdBy')
        ->orderBy('created_at', 'desc')
        ->get();

        return $tasks;
    }

    

    // 他のメソッドもここに記載
    public function updateTask(Task $task, User $user, array $data) {

        $this->taskProject = $task->project;
        $this->isMemberTask($user);

        $task->update($data(['title', 'description', 'status']));
        $task->load('createdBy');

        return $task;
    }

    public function showTask(Task $task, User $user) 
    {
        $this->taskProject = $task->project;
        $this->isMemberTask($user);
        $task = $task->load(['createdBy', 'project']);
        return $task;
    }

    public function deleteTask(Task $task, User $user) {
        $this->taskProject = $task->project;
        $this->isMemberTask($user);
        $task->delete();
    }


    public function startTask(Task $task, User $user) {

        $this->taskProject = $task->project;
        $this->isMemberTask($user);

        // 状態チェック
        if ($task->status !== 'todo') {
            return response()->json([
                'message' => '未着手のタスクのみ開始できます',
            ], 409);
        }

        $task->update(['status' => 'doing']);
        $task->load('createdBy');

        return $task;
    }


    public function completeTask(Task $task, User $user) {

        $this->taskProject = $task->project;

        $this->isMemberTask($user);

        if ($task->status !== 'doing') {
            return response()->json([
                'message' => '作業中のタスクのみ完了できます',
            ], 409);
        }
        $task->update(['status' => 'done']);
        $task->load('createdBy');

        return $task;
    }


     // privateメソッドも定義


    private function isMemberTask(User $user) {

        $taskProject = $this->taskProject;
        $projectMember = $this->projectMember;

        if($taskProject) {
            $project = $taskProject;
        } else {
            $project =  $projectMember;
        }
        
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
    



   
}
