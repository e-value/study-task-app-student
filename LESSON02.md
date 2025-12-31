# Lesson2: Controller 実装（コメント穴埋め）で“肥大化”を体験する

## 🎯 この Lesson の目的

この Lesson2 では、**あなた自身で**以下を実装します：

1. **Lesson2 ブランチを取り込み、課題ブランチで作業する**
2. **Controller 実装** - コメントに従って処理を実装する（Controller に直接書いて OK）
3. **認可チェック** - プロジェクトメンバー判定／owner・admin 判定を実装する
4. **バリデーション実装** - バリデーションを実装して入力チェックを実装する
5. **エラーハンドリング** - 403 / 409 / 422 を使い分けて返す

### 💡 このレッスンで体験すること

実際のシステムでは、各エンドポイントで以下のような処理を実装する必要があります：

-   **権限チェック** - ユーザーがその操作を実行する権限があるか
-   **バリデーション** - 入力データが正しい形式・値か
-   **エラーハンドリング** - 適切なエラーコードとメッセージを返す
-   **ビジネスロジック** - ステータス遷移の制約など

これらをすべて Controller に直接実装すると、**Controller が肥大化**してしまいます。このレッスンでは、そのような状況を実際に体験することで、なぜ Controller をスリムに保つ必要があるのか、どのような問題が発生するのかを理解することが目的です。

**重要：** このレッスンでは、実装したコードが正しく動作しなくても問題ありません。**一旦すべての処理を書き切ることを優先**してください。完璧に動作させることよりも、Controller に多くの処理を書くことでどのような問題が発生するかを体験することが重要です。

---

## 🌿 ブランチ作成

課題に取り組む前に、Lesson2 のブランチを取り込み、作業ブランチを作成してください：

```bash
# 現在のブランチを確認
git branch

# mainブランチに切り替え
git checkout main

# 最新の状態に更新（必ずpullすること）
git pull origin main

# lesson2ブランチを取り込む（講師リポジトリ or upstream）
git fetch origin lesson2
git checkout lesson2
git pull origin lesson2

# 作業用ブランチを作成（例：yourname_lesson2 など）
git checkout -b lesson2_メンバーの名字
```

**推奨：** 各 Lesson ごとに専用ブランチで作業することで、差分が見やすくなります。

---

## 📋 要件：実現すべき機能

Lesson2 では、Controller に記載されているコメントを元に、必要な処理を実装します。

対象は以下の Controller です：

-   `app/Http/Controllers/Api/ProjectController.php`
-   `app/Http/Controllers/Api/TaskController.php`
-   `app/Http/Controllers/Api/ProjectMemberController.php`

### 🔍 エンドポイントとメソッドの確認

実装を始める前に、`routes/api.php` と各コントローラーファイルを確認し、以下のエンドポイントとメソッドを把握してください：

#### ProjectController

-   `GET /api/projects` → `index()` - プロジェクト一覧取得
-   `POST /api/projects` → `store()` - プロジェクト作成
-   `GET /api/projects/{project}` → `show()` - プロジェクト詳細取得
-   `PUT /api/projects/{project}` → `update()` - プロジェクト更新
-   `DELETE /api/projects/{project}` → `destroy()` - プロジェクト削除

#### TaskController

-   `GET /api/projects/{project}/tasks` → `index()` - タスク一覧取得
-   `POST /api/projects/{project}/tasks` → `store()` - タスク作成
-   `GET /api/tasks/{task}` → `show()` - タスク詳細取得
-   `PUT /api/tasks/{task}` → `update()` - タスク更新
-   `DELETE /api/tasks/{task}` → `destroy()` - タスク削除
-   `POST /api/tasks/{task}/start` → `start()` - タスク開始
-   `POST /api/tasks/{task}/complete` → `complete()` - タスク完了

#### ProjectMemberController

-   `GET /api/projects/{project}/members` → `index()` - メンバー一覧取得
-   `POST /api/projects/{project}/members` → `store()` - メンバー追加
-   `DELETE /api/projects/{project}/members/{user}` → `destroy()` - メンバー削除

---

## 📝 Step 1: Controller 実装（コメントの下に実装する）

Lesson2 では、**Controller の各メソッド内に書かれているコメントの直下に処理を実装**してください。

### 📌 実装時の注意点

-   **完璧を求めすぎない** - 実装したコードが正しく動作しなくても構いません。まずは**すべてのコメントの下に処理を書き切る**ことを優先してください。
-   **エンドポイントとメソッドの確認** - 実装前に `routes/api.php` と各コントローラーファイルを確認し、どのエンドポイントでどのメソッドが呼ばれるかを把握してください。
-   **Controller の肥大化を体験する** - 権限チェック、バリデーション、エラーハンドリングなど、多くの処理を Controller に直接書くことで、コードが複雑になり、読みにくくなることを実感してください。
-   **N+1 問題について調べる** - コード内に「N+1 問題を防ぐため」というコメントがあります。実装前に N+1 問題とは何か、なぜ発生するのか、どのように防ぐのか（`load()` や `with()` の使い方）を調べて理解してください。これはパフォーマンスに大きく影響する重要な概念です。

### ✅ 実装ルール

```
 /**
     * プロジェクト詳細を返す
     */
    public function show(Request $request, Project $project): ProjectResource|JsonResponse
    {
        // 自分がプロジェクトのメンバーかチェック（users()リレーションを使用）
        （ここに記載）

        // メンバーでなければエラーを返す
        // エラーコード: 403, エラーメッセージ: このプロジェクトにアクセスする権限がありません
        （ここに記載）

        // 読み込み（N+1問題を防ぐため）
        $project->load(['users', 'tasks.createdBy']);

        return new ProjectResource($project);
    }
```

---

## 🚀 実装完了後の作業

実装が完了したら、以下の形式でプルリクエストを作成してください：

**プルリクエストのタイトル形式：**

```
【名前】Lesson02 実装
```

**例：**

-   【宮田】Lesson02 実装
