<?php

namespace App\Http\Controllers\Api;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function myTask(Request $request)
    {
        abort(501, 'Not Implemented');
    }
}
