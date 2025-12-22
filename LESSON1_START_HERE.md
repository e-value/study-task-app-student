# 🎓 Lesson1: API エンドポイント設計と Controller 責務分離

## 👋 ようこそ！

この Lesson1 では、**あなた自身で**以下を設計します：

1. ✅ **API エンドポイント設計** - RESTful な URL と HTTP メソッドを決める
2. ✅ **Controller 責務分離** - どの Controller に何を実装すべきか決める
3. ✅ **ルーティング定義** - `routes/api.php` にエンドポイントを定義する
4. ✅ **ApiResource 設計** - フロントエンドに返す JSON 構造を考える

---

## 🚀 まず最初に読むべきファイル

📖 **[LESSON1_README.md](./LESSON1_README.md)** を開いてください

このファイルに、Lesson1 の詳細な手順とヒントが書かれています。

---

## 🎯 あなたのミッション

以下の機能を実現する API を設計してください：

### プロジェクト管理機能

-   プロジェクト一覧取得
-   プロジェクト作成
-   プロジェクト詳細取得
-   プロジェクト更新
-   プロジェクト削除

### タスク管理機能

-   プロジェクト内のタスク一覧取得
-   プロジェクト内にタスク作成
-   タスク詳細取得
-   タスク更新
-   タスク削除

### ユーザー機能

-   自分のタスク一覧取得

---

## 📁 現在のファイル構成（空の状態）

```
app/Http/
├── Controllers/Api/
│   └── ApiController.php           （ベースコントローラーのみ）
└── Resources/
    （空 - 受講者が自分で作成）

routes/
└── api.php                         （認証エンドポイントのみ）
```

**あなたがやること：**

1. Controller を作成する
2. `routes/api.php` にエンドポイントを定義する
3. ApiResource を作成する
4. ApiResource の構造を設計する

---

## 📝 作業の流れ

### Step 1: README を読む

📖 **[LESSON1_README.md](./LESSON1_README.md)** を読んで、RESTful 設計の基本を理解してください。

### Step 2: エンドポイント設計

紙やドキュメントに、以下を書き出してください：

| HTTP メソッド | URL             | 説明             | Controller |
| ------------- | --------------- | ---------------- | ---------- |
| GET           | `/api/projects` | プロジェクト一覧 | ？         |
| ...           | ...             | ...              | ...        |

### Step 3: Controller 作成

`app/Http/Controllers/Api/` に Controller を作成：

```bash
# 例
php artisan make:controller Api/ProjectsController
```

各メソッドは `abort(501, 'Not Implemented');` で OK。

### Step 4: ルーティング定義

`routes/api.php` にエンドポイントを定義：

```php
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/projects', [ProjectsController::class, 'index']);
    // ...
});
```

### Step 5: ApiResource 作成と設計

必要な ApiResource を作成して、返すべき JSON 構造を考えてください。

```bash
php artisan make:resource ProjectResource
php artisan make:resource TaskResource
```

### Step 6: 動作確認

```bash
# ルート一覧で確認
php artisan route:list --path=api

# API を叩いて 501 エラーが返ることを確認
php artisan serve
curl http://localhost:8000/api/projects
```

---

## ✅ 完了チェックリスト

-   [ ] `LESSON1_README.md` を読んだ
-   [ ] エンドポイント設計を紙に書き出した
-   [ ] Controller を作成した
-   [ ] `routes/api.php` にルーティングを定義した
-   [ ] `ProjectResource` を作成した
-   [ ] `ProjectResource` の構造を考えた
-   [ ] `TaskResource` を作成した
-   [ ] `TaskResource` の構造を考えた
-   [ ] `php artisan route:list --path=api` で確認した
-   [ ] API を叩いて 501 エラーが返ることを確認した

---

## 💡 ヒント

### RESTful 設計の基本

**良い例：**

```
GET    /api/projects           プロジェクト一覧
POST   /api/projects           プロジェクト作成
GET    /api/projects/{id}      プロジェクト詳細
PATCH  /api/projects/{id}      プロジェクト更新
DELETE /api/projects/{id}      プロジェクト削除
```

**悪い例：**

```
GET    /api/getProjects        ← 動詞が入っている
POST   /api/createProject      ← 動詞が入っている
GET    /api/projects/list      ← 不要な単語
```

### Controller 責務分離

**Option A: シンプル（2 つ）**

```
ProjectsController  → プロジェクト操作
TasksController     → タスク操作
```

**Option B: 明確（3 つ）**

```
ProjectsController  → プロジェクト操作
TasksController     → タスク操作
MeTasksController   → 自分のタスク一覧
```

**どちらを選びますか？理由は？**

---

## ❓ 質問があれば

-   RESTful 設計がわからない → `LESSON1_README.md` の「RESTful 設計の基本原則」を読む
-   Controller の責務分離がわからない → `LESSON1_README.md` の「Controller 責務分離を考える」を読む
-   エンドポイントの階層がわからない → `LESSON1_README.md` の「よくある質問」を読む

---

## 🚀 次のステップ

Lesson1 が完了したら、Lesson2 で以下を実装します：

1. Controller 内のロジック
2. バリデーション処理
3. 権限チェック
4. 業務ルール

**まずは設計をしっかり考えてください！頑張ってください！** 💪
