# ✅ Lesson1 セットアップ完了

## 📝 実施した作業

### 1. ✅ 全 Controller を削除

**削除したファイル：**

-   ❌ `ProjectController.php`
-   ❌ `ProjectsController.php`
-   ❌ `TaskController.php`
-   ❌ `TasksController.php`
-   ❌ `ProjectTasksController.php`
-   ❌ `MeTasksController.php`
-   ❌ `MembershipController.php`
-   ❌ `UserController.php`

**残したファイル：**

-   ✅ `ApiController.php` - ベースコントローラーのみ残す

**理由：**
受講者に「どのような Controller を作るべきか」を自分で考えさせるため。

---

### 2. ✅ 全エンドポイントを削除

`routes/api.php` から、認証関連以外の全エンドポイントを削除しました。

**削除したエンドポイント：**

-   ❌ プロジェクト関連の全エンドポイント
-   ❌ タスク関連の全エンドポイント
-   ❌ メンバー関連の全エンドポイント
-   ❌ ユーザー関連の全エンドポイント

**残したエンドポイント：**

-   ✅ `GET /api/user` - 認証済みユーザー情報取得のみ

**理由：**
受講者に「どのようなエンドポイントを作るべきか」を自分で考えさせるため。

---

### 3. ✅ ApiResource を空にする

**修正したファイル：**

-   ✅ `ProjectResource.php` - `toArray()` を空配列に変更
-   ✅ `TaskResource.php` - `toArray()` を空配列に変更

**削除したファイル：**

-   ❌ `MembershipResource.php`
-   ❌ `UserResource.php`

**理由：**
受講者に「どのような JSON 構造を返すべきか」を自分で考えさせるため。

---

### 4. ✅ ドキュメント作成

**作成したドキュメント：**

1. **`LESSON1_README.md`** - 受講者向けの詳細ガイド

    - RESTful 設計の基本原則
    - エンドポイント設計の考え方
    - Controller 責務分離の考え方
    - ApiResource 設計のポイント
    - 演習課題

2. **`LESSON1_SETUP_COMPLETE.md`** - このファイル
    - セットアップ完了内容
    - 現在のファイル構成
    - 受講者への指示

---

## 🎯 現在の状態

### ファイル構成

```
app/Http/
├── Controllers/Api/
│   └── ApiController.php           （ベースコントローラーのみ）
└── Resources/
    ├── ProjectResource.php         （toArray() が空配列）
    └── TaskResource.php            （toArray() が空配列）

routes/
└── api.php                         （認証エンドポイントのみ）
```

### 動作確認

```bash
# ルート一覧を確認
php artisan route:list --path=api

# 結果（認証エンドポイントのみ）
✅ GET|HEAD   api/user
```

---

## 📖 Lesson1 で受講者が行うこと

### Step 1: 要件を理解する

`LESSON1_README.md` を読んで、実現すべき機能を理解する：

-   プロジェクト管理機能（CRUD）
-   タスク管理機能（CRUD）
-   自分のタスク一覧取得

### Step 2: エンドポイント設計

以下を考えて、紙やドキュメントに書き出す：

-   どのような URL にすべきか？
-   どの HTTP メソッドを使うべきか？
-   RESTful になっているか？
-   リソースの階層構造は適切か？

### Step 3: Controller 責務分離

以下を考える：

-   Controller をいくつ作るべきか？
-   どの Controller にどの操作を実装すべきか？
-   責務は明確に分かれているか？

### Step 4: Controller 作成

`app/Http/Controllers/Api/` に Controller を作成：

```php
<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

class YourController extends ApiController
{
    public function index(Request $request)
    {
        abort(501, 'Not Implemented');
    }

    // ... 他のメソッド
}
```

### Step 5: ルーティング定義

`routes/api.php` にエンドポイントを定義：

```php
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/your-endpoint', [YourController::class, 'index']);
    // ...
});
```

### Step 6: ApiResource 設計

`ProjectResource.php` と `TaskResource.php` に返すべき構造を考える：

```php
public function toArray(Request $request): array
{
    return [
        // TODO: どのような JSON 構造にするか考える
    ];
}
```

### Step 7: 動作確認

```bash
# ルート一覧で確認
php artisan route:list --path=api

# API を叩いて 501 エラーが返ることを確認
curl -X GET http://localhost:8000/api/your-endpoint
```

---

## 🎯 Lesson1 の目標

受講者が以下を習得すること：

### 1. RESTful 設計の理解

-   リソース指向の URL 設計
-   HTTP メソッドの適切な使い分け
-   リソースの階層構造の表現
-   エンドポイントの一貫性

### 2. Controller 責務分離の判断

-   Single Responsibility Principle
-   責務ごとに Controller を分ける理由
-   保守性・拡張性の向上
-   テストのしやすさ

### 3. 設計スキル

-   要件からエンドポイントを導き出す力
-   適切な Controller 構成を判断する力
-   フロントエンドが使いやすい JSON 構造を考える力

---

## ❌ Lesson1 では実装しないこと

-   Controller 内のロジック実装
-   バリデーション処理
-   権限チェック
-   業務ルール（ステータス遷移など）
-   データベース操作

**これらは Lesson2 以降で実装します。**

---

## 🧪 受講者の成果物チェックリスト

Lesson1 完了時に、以下が揃っているか確認：

### 必須項目

-   [ ] エンドポイント設計書（紙 or ドキュメント）

    -   [ ] HTTP メソッド
    -   [ ] URL
    -   [ ] 説明
    -   [ ] どの Controller に実装するか

-   [ ] Controller ファイルが作成されている

    -   [ ] クラス名が適切
    -   [ ] メソッド名が適切
    -   [ ] 各メソッドが `abort(501)` を返す

-   [ ] `routes/api.php` にルーティングが定義されている

    -   [ ] 全エンドポイントが定義されている
    -   [ ] Controller に正しく紐づいている

-   [ ] `ProjectResource.php` の設計

    -   [ ] 返すべき JSON 構造が決まっている（コメントで OK）

-   [ ] `TaskResource.php` の設計
    -   [ ] 返すべき JSON 構造が決まっている（コメントで OK）

### 動作確認

-   [ ] `php artisan route:list --path=api` で全エンドポイントが表示される
-   [ ] リンターエラーがない
-   [ ] アプリが起動する
-   [ ] API を叩くと 501 エラーが返る

---

## 💡 評価のポイント

### 良い設計の例

**エンドポイント：**

-   ✅ RESTful な URL 設計
-   ✅ HTTP メソッドの適切な使い分け
-   ✅ リソースの階層構造が明確
-   ✅ 一貫性のある命名規則

**Controller：**

-   ✅ 責務が明確に分かれている
-   ✅ クラス名・メソッド名がわかりやすい
-   ✅ 将来の拡張を考慮している

**ApiResource：**

-   ✅ フロントエンドが使いやすい構造
-   ✅ DB カラムをそのまま返していない
-   ✅ 一覧と詳細で使い分けを考えている

### 改善が必要な例

**エンドポイント：**

-   ❌ URL に動詞が入っている（`/api/getProjects`）
-   ❌ HTTP メソッドが不適切（GET で削除など）
-   ❌ 階層構造が深すぎる（3 階層以上）
-   ❌ 命名が不統一

**Controller：**

-   ❌ 1 つの Controller に全部詰め込んでいる
-   ❌ 責務が不明確
-   ❌ クラス名・メソッド名が抽象的

**ApiResource：**

-   ❌ DB カラムをそのまま返している
-   ❌ 構造が考えられていない

---

## 🚀 Lesson2 への準備

Lesson1 で設計を理解したら、Lesson2 で以下を実装します：

1. **Controller 内のロジック実装**

    - データ取得・作成・更新・削除

2. **ApiResource の実装**

    - `toArray()` の具体的な実装

3. **バリデーション処理**

    - Request クラスの作成
    - ルール定義

4. **権限チェック**

    - プロジェクトメンバーかどうか
    - オーナー・管理者かどうか

5. **エラーハンドリング**
    - 適切なステータスコードとメッセージ

---

## 📚 補足資料

### データベース構造の確認方法

```bash
# マイグレーションファイルを確認
ls database/migrations/

# 特に以下を確認：
# - 2024_12_21_000003_create_projects_table.php
# - 2024_12_21_000004_create_memberships_table.php
# - 2024_12_21_000005_create_tasks_table.php
```

### Model の確認方法

```bash
# Model ファイルを確認
ls app/Models/

# 以下が存在：
# - Project.php
# - Task.php
# - Membership.php
# - User.php
```

---

## ✅ チェックリスト（講師用）

-   [x] 古い Controller を全削除
-   [x] ApiController のみ残す
-   [x] routes/api.php から全エンドポイントを削除（認証除く）
-   [x] 不要な Resource を削除
-   [x] ProjectResource/TaskResource を空にする
-   [x] リンターエラーなし
-   [x] ドキュメント作成（LESSON1_README.md）
-   [x] セットアップ完了レポート作成

---

## 🎓 まとめ

✅ **Lesson1 セットアップ完了！**

受講者は以下を自分で設計できる状態になりました：

1. ✅ RESTful なエンドポイント設計
2. ✅ Controller 責務分離の判断
3. ✅ ルーティング定義
4. ✅ ApiResource 設計

**完全に空の状態から設計を考えることで、より深い理解が得られます。**

次の Lesson2 で実装に入る準備が整いました！🚀
