<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * プロジェクト一覧取得
     */
    public function index()
    {
        abort(501, 'Not Implemented');
    }

    /**
     * プロジェクト作成
     */
    public function store(Request $request)
    {
        abort(501, 'Not Implemented');
    }

    /**
     * プロジェクト詳細取得
     */
    public function show(Project $project)
    {
        abort(501, 'Not Implemented');
    }

    /**
     * プロジェクト更新
     */
    public function update(Project $project, Request $request)
    {
        abort(501, 'Not Implemented');
    }

    /**
     * プロジェクト削除
     */
    public function destroy(Project $project)
    {
        abort(501, 'Not Implemented');
    }

    /**
     * プロジェクトのメンバー追加
     */
    public function storeProjectMember(Project $project, User $user)
    {
        abort(501, 'Not Implemented');
    }

    /**
     * プロジェクトのメンバー削除
     */
    public function deleteProjectMember(Project $project, User $user)
    {
        abort(501, 'Not Implemented');
    }
}
