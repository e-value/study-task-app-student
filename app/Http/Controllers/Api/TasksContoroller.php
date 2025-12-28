<?php

namespace App\Http\Controllers\Api;

use App\Models\Task;
use Illuminate\Http\Request;

class TasksController extends Controller
{
    public function index()
    {
        abort(501, 'Not Implemented');
    }

    public function show(Task $task)
    {
        abort(501, 'Not Implemented');
    }

    public function store(Request $request)
    {
        abort(501, 'Not Implemented');
    }

    public function update(Request $request, Task $task)
    {
        abort(501, 'Not Implemented');
    }

    public function destroy(Task $task)
    {
        abort(501, 'Not Implemented');
    }

    public function start(Task $task)
    {
        abort(501, 'Not Implemented');
    }

    public function complete(Task $task)
    {
        abort(501, 'Not Implemented');
    }
}

