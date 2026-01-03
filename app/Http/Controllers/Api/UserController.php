<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Services\UserService;

class UserController extends ApiController
{

    public function __construct(
        private UserService $userService
    ) {}
    /**
     * ユーザー一覧を取得
     * 
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $users = $this->userService->getUsers([
            'per_page' => $request->input('per_page'),
            'search' => $request->input('search'),
            'sort_by' => $request->input('sort_by'),
            'sort_order' => $request->input('sort_order'),
        ]);

        return UserResource::collection($users);
    }

    /**
     * ドロップダウン用のユーザー一覧を取得
     * 
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function dropdown(Request $request): AnonymousResourceCollection
    {
        $users = $this->userService->getUsersDropdown([
            'search' => $request->input('search'),
        ]);

        return UserResource::collection($users);
    }
}
