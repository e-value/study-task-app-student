<?php

namespace App\Http\Controllers\Api;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectsController extends Controller
{
    public function index()
    {
        abort(501, 'Not Implemented');
    }


    public function show(Project $project)
    {
        abort(501, 'Not Implemented');
    }

    public function store(Request $request)
    {
        abort(501, 'Not Implemented');
    }


    public function update(Request $request, Project $project)
    {
        abort(501, 'Not Implemented');
    }


    public function destroy(Project $project)
    {
        abort(501, 'Not Implemented');
    }

}

