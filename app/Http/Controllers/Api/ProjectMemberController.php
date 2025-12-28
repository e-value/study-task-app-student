<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectMemberController extends Controller
{
    /**
     * プロジェクト内のメンバー追加
     */
    public function store(Request $request, Project $project)
    {
        abort(501, 'Not Implemented');
    }

    /**
     * プロジェクト内のメンバー削除
     */
    public function destroy(Request $request, Project $project, User $user)
    {
        abort(501, 'Not Implemented');
    }
}
