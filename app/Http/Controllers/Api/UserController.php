<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends ApiController
{
    /**
     * ユーザー一覧を取得
     * 
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        // ページネーション設定（デフォルト: 15件/ページ）
        $perPage = $request->input('per_page', 15);
        $perPage = min(max((int)$perPage, 5), 100); // 5〜100の範囲に制限

        // 検索クエリ
        $search = $request->input('search');

        // ソート
        $sortBy = $request->input('sort_by', 'name');
        $sortOrder = $request->input('sort_order', 'asc');

        // クエリビルダー
        $query = User::query();

        // 検索条件
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // ソート条件
        $allowedSorts = ['name', 'email', 'created_at'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder === 'desc' ? 'desc' : 'asc');
        } else {
            $query->orderBy('name', 'asc');
        }

        // ページネーション実行
        $users = $query->paginate($perPage);

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
        // 検索クエリ
        $search = $request->input('search');

        // 必要最小限のカラムのみ選択
        $query = User::select('id', 'name', 'email')
            ->orderBy('name');

        // 検索条件
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // DoS攻撃対策として上限を設定（100件）
        $users = $query->limit(100)->get();

        return UserResource::collection($users);
    }
}
