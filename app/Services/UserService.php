<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserService
{
    /**
     *  ユーザー一覧を取得
     *
     * @param array 
     * @return AnonymousResourceCollection
     */
    public function getUsers(array $params): AnonymousResourceCollection
    {
        // ページネーション設定（デフォルト: 15件/ページ）
        $perPage = $params['per_page'] ?? 15;
        $perPage = min(max((int)$perPage, 5), 100); // 5〜100の範囲に制限

        // 検索クエリ
        $search = $params['search'] ?? null;

        // ソート
        $sortBy = $params['sort_by'] ?? 'name';
        $sortOrder = $params['sort_order'] ?? 'asc';

        // クエリビルダー
        $query = User::query();

        // 検索条件を適用
        $this->applySearchConditions($query, $search);

        // ソート条件
        $allowedSorts = ['name', 'email', 'created_at'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder === 'desc' ? 'desc' : 'asc');
        } else {
            $query->orderBy('name', 'asc');
        }

        // ページネーション実行
        return $query->paginate($perPage);
    }
    /**
     * ドロップダウン用のユーザー一覧を取得
     *
     * @param array
     * @return AnonymousResourceCollection
     */
    public function getUsersDropdown(array $params = []): AnonymousResourceCollection
    {
        // 検索クエリ
        $search = $params['search'] ?? null;

        // 必要最小限のカラムのみ選択
        $query = User::select('id', 'name', 'email')
            ->orderBy('name');

        // 検索条件を適用
        $this->applySearchConditions($query, $search);

        // DoS攻撃対策として上限を設定（100件）
        return $query->limit(100)->get();
    }

    // privateメソッドも定義
    /**
     * 検索条件
     * @param Illuminate\Database\Eloquent\Builder;
     * @param string
     * @return void
     */
    private function applySearchConditions($query, $search): void
    {
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }
    }
}
