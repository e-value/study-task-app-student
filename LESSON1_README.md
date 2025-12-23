# Lesson1: API エンドポイント設計と Controller 責務分離

## 🎯 この Lesson の目的

この Lesson1 では、**あなた自身で**以下を設計します：

1. **API エンドポイント設計** - RESTful な URL と HTTP メソッドを決める
2. **Controller 責務分離** - どの Controller に何を実装すべきか決める
3. **ルーティング定義** - `routes/api.php` にエンドポイントを定義する
4. **ApiResource 設計** - フロントエンドに返す JSON 構造を考える

**重要：** 実装はしません。構造を設計するだけです。

---

## 📋 要件：実現すべき機能

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

-   自分のタスク一覧取得（自分が所属しているプロジェクトの全タスク）

---

## 💡 データベース構造（参考）

設計の参考に、現在のテーブル構造を確認してください：

### projects テーブル

```
id              bigint
name            string      プロジェクト名
is_archived     boolean     アーカイブ済みか
created_at      timestamp
updated_at      timestamp
```

### tasks テーブル

```
id              bigint
project_id      bigint      どのプロジェクトのタスクか
title           string      タスクタイトル
description     text        タスク詳細（NULL可）
status          string      'todo' | 'doing' | 'done'
created_by      bigint      作成者のユーザーID
created_at      timestamp
updated_at      timestamp
```

### memberships テーブル（プロジェクトメンバー）

```
id              bigint
project_id      bigint      どのプロジェクトか
user_id         bigint      どのユーザーか
role            string      'project_owner' | 'project_admin' | 'project_member'
created_at      timestamp
updated_at      timestamp
```

### users テーブル

```
id              bigint
name            string
email           string
created_at      timestamp
updated_at      timestamp
```

---

## 🤔 Step 1: エンドポイント設計を考える

### 考えるべき質問

#### Q1: プロジェクト操作のエンドポイントは？

**プロジェクト一覧取得** はどうする？

-   `GET /api/projects` ？
-   `GET /api/project/list` ？
-   どちらが RESTful？

**プロジェクト作成** はどうする？

-   `POST /api/projects` ？
-   `POST /api/project/create` ？
-   どちらが RESTful？

**プロジェクト詳細取得** はどうする？

-   `GET /api/projects/{project}` ？
-   `GET /api/project/{id}` ？
-   `GET /api/projects/{id}/show` ？
-   どれが RESTful？

**プロジェクト更新** はどうする？

-   `PUT /api/projects/{project}` ？
-   `PATCH /api/projects/{project}` ？
-   `POST /api/projects/{project}/update` ？
-   PUT と PATCH の違いは？

**プロジェクト削除** はどうする？

-   `DELETE /api/projects/{project}` ？
-   `POST /api/projects/{project}/delete` ？
-   どちらが RESTful？

#### Q2: タスク操作のエンドポイントは？

**プロジェクト内のタスク一覧取得** はどうする？

-   `GET /api/projects/{project}/tasks` ？
-   `GET /api/tasks?project_id={project}` ？
-   どちらが階層構造を表現している？

**プロジェクト内にタスク作成** はどうする？

-   `POST /api/projects/{project}/tasks` ？
-   `POST /api/tasks` （body に project_id を入れる）？
-   どちらがわかりやすい？

**タスク詳細取得** はどうする？

-   `GET /api/tasks/{task}` ？
-   `GET /api/projects/{project}/tasks/{task}` ？
-   どちらがシンプル？

**タスク更新** はどうする？

-   `PATCH /api/tasks/{task}` ？
-   `PATCH /api/projects/{project}/tasks/{task}` ？

**タスク削除** はどうする？

-   `DELETE /api/tasks/{task}` ？
-   `DELETE /api/projects/{project}/tasks/{task}` ？

#### Q3: 自分のタスク一覧取得は？

**自分のタスク一覧** はどうする？

-   `GET /api/me/tasks` ？
-   `GET /api/tasks/my` ？
-   `GET /api/tasks?user=me` ？
-   どれが「自分のタスク」を明確に表現している？

---

## 🧠 Step 2: Controller 責務分離を考える

### 考えるべき質問

#### Q1: Controller をいくつ作るべき？

**Option A: 1 つの Controller にまとめる**

```
ProjectController
  - プロジェクト操作
  - タスク操作
  - 全部入り
```

**メリット・デメリットは？**

**Option B: リソースごとに分ける**

```
ProjectsController
  - プロジェクト操作のみ

TasksController
  - タスク操作のみ
```

**メリット・デメリットは？**

**Option C: さらに細かく分ける**

```
ProjectsController
  - プロジェクト操作のみ

TasksController
  - タスク操作のみ

MeTasksController
  - 自分のタスク一覧取得のみ
```

**メリット・デメリットは？**

#### Q2: どの Controller にどのメソッドを実装する？

あなたが設計した Controller に対して：

-   プロジェクト一覧取得 → どの Controller の何というメソッド？
-   プロジェクト作成 → どの Controller の何というメソッド？
-   プロジェクト詳細取得 → どの Controller の何というメソッド？
-   プロジェクト更新 → どの Controller の何というメソッド？
-   プロジェクト削除 → どの Controller の何というメソッド？

-   プロジェクト内のタスク一覧取得 → どの Controller の何というメソッド？
-   プロジェクト内にタスク作成 → どの Controller の何というメソッド？
-   タスク詳細取得 → どの Controller の何というメソッド？
-   タスク更新 → どの Controller の何というメソッド？
-   タスク削除 → どの Controller の何というメソッド？

-   自分のタスク一覧取得 → どの Controller の何というメソッド？

---

## 🎨 Step 3: ApiResource 設計を考える

### ProjectResource の設計

フロントエンドに返す JSON 構造を考えてください：

```bash
# まず ApiResource を作成
php artisan make:resource ProjectResource
```

```php
// 作成後、app/Http/Resources/ProjectResource.php を編集
public function toArray(Request $request): array
{
    return [
        // TODO: 何を返すべき？

        // id は必要？
        // name だけでいい？
        // is_archived を boolean で返す？ それとも status: "active" | "archived" にする？
        // メンバー情報を含める？
        // タスク数を含める？
        // 作成日時・更新日時は？
        //
        // 一覧表示用と詳細表示用で分けるべき？
    ];
}
```

### TaskResource の設計

```bash
# まず ApiResource を作成
php artisan make:resource TaskResource
```

```php
// 作成後、app/Http/Resources/TaskResource.php を編集
public function toArray(Request $request): array
{
    return [
        // TODO: 何を返すべき？

        // id, title, description は必要？
        // status を "todo" | "doing" | "done" で返す？
        // 作成者の情報をどこまで含める？（id だけ？名前も？）
        // プロジェクト情報を含める？
        // 作成日時・更新日時は？
        //
        // 一覧表示用と詳細表示用で分けるべき？
    ];
}
```

---

## ✍️ 演習：あなたのターン！

> **📚 事前学習：** エンドポイント設計を始める前に、RESTful API の基本原則について調べてください。
> 
> 以下のキーワードで検索することをおすすめします：
> - RESTful API とは
> - HTTP メソッド（GET, POST, PUT, PATCH, DELETE）の使い分け
> - リソース指向の URL 設計
> - RESTful API 階層構造

### 演習 1: エンドポイント設計

1. 紙やドキュメントに、全てのエンドポイントを書き出してください
2. 各エンドポイントについて、以下を記載：
    - HTTP メソッド
    - URL
    - 説明
    - どの Controller に実装するか

### 演習 2: Controller 作成

1. 必要な Controller を `app/Http/Controllers/Api/` に作成
2. 各メソッドは `abort(501, 'Not Implemented');` で OK
3. クラス名とメソッド名だけ決めてください

### 演習 3: ルーティング定義

1. `routes/api.php` にエンドポイントを定義
2. 各エンドポイントを適切な Controller に紐づける

### 演習 4: ApiResource 作成と設計

1. 必要な ApiResource を作成する
    ```bash
    php artisan make:resource ProjectResource
    php artisan make:resource TaskResource
    ```
2. `ProjectResource.php` の `toArray()` に返すべき構造をコメントで書く
3. `TaskResource.php` の `toArray()` に返すべき構造をコメントで書く

---

## 🧪 動作確認

設計が完了したら、以下で確認：

```bash
# ルート一覧を表示
php artisan route:list --path=api

# 期待される出力：
# あなたが設計した全エンドポイントが表示されるはず
```

```bash
# アプリを起動
php artisan serve

# 別ターミナルで API を叩いてみる
curl -X GET http://localhost:8000/api/projects \
  -H "Authorization: Bearer YOUR_TOKEN"

# レスポンス（501エラーが返ればOK）
{
  "message": "Not Implemented"
}
```

---

## 🚀 次の Lesson2 では...

-   あなたが設計したエンドポイントと ApiResource を**実装**します
-   バリデーション処理を追加します
-   権限チェックを実装します
-   業務ルール（ステータス遷移など）を実装します

---

## 📚 参考リンク

-   [RESTful API 設計ガイド](https://restfulapi.net/)
-   [Laravel Resource Controllers](https://laravel.com/docs/controllers#resource-controllers)
-   [Laravel API Resources](https://laravel.com/docs/eloquent-resources)

---

## 🎓 まとめ

Lesson1 では、**「設計を考える力」** を養います。

-   ✅ RESTful なエンドポイント設計
-   ✅ Controller 責務分離の判断
-   ✅ ルーティング定義
-   ✅ ApiResource 設計

実装は Lesson2 で行います。まずは設計をしっかり考えてください！

**頑張ってください！** 🚀
