<?php

namespace App\Http\Controllers\Api;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectsController extends Controller
{
    #プロジェクトの一覧取得
    public function index()
    {
        abort(501, 'Not Implemented');
    }

    #プロジェクトの作成
    public function create(Request $request)
    {
        abort(501, 'Not Implemented');
    }

    #プロジェクトの詳細取得
    public function store(Request $request,Project $project)
    {
        abort(501, 'Not Implemented');
    }

    #プロジェクトの更新
    public function update(Request $request, Project $project)
    {
        abort(501, 'Not Implemented');
    }

    #プロジェクトの削除
    public function delete(Request $request,Project $project)
    {
        abort(501, 'Not Implemented');
    }

}
