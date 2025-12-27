<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * プロジェクト一覧取得
     */
    public function index(Request $request)
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
    public function show(Request $request, Project $project)
    {
        abort(501, 'Not Implemented');
    }

    /**
     * プロジェクト更新
     */
    public function update(Request $request, Project $project)
    {
        abort(501, 'Not Implemented');
    }

    /**
     * プロジェクト削除
     */
    public function destroy(Request $request, Project $project)
    {
        abort(501, 'Not Implemented');
    }
}
