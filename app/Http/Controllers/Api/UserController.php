<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends ApiController
{
    /**
     * ユーザー一覧を取得（メンバー追加用）
     */
    public function index(): AnonymousResourceCollection
    {
        $users = User::orderBy('name')->get();
        return UserResource::collection($users);
    }
}
