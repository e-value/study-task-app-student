<?php

namespace App\Http\Controllers\Api;

use App\Models\Membership;
use App\Models\Project;
use Illuminate\Http\Request;

class MembershipsController extends Controller
{
    #プロジェクトのメンバー追加
    public function add(Request $request, Project $project)
    {
        abort(501, 'Not Implemented');
    }

    #プロジェクトのメンバーを削除
    public function delete(Request $request,Project $project)
    {
        abort(501, 'Not Implemented');
    }

}