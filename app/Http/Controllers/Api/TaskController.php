<?php

namespace App\Http\Controllers\Api;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    #自分のタスク一覧取得（自分が所属しているプロジェクトの全タスク）
    public function index(Request $request)
    {
        abort(501, 'Not Implemented');
    }

}