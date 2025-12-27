<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
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
}
