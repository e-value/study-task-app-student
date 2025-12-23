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

## 📝 Step 1: エンドポイント設計

RESTful な API エンドポイントを設計してください。

**設計すべきエンドポイント：**

-   プロジェクト一覧取得
-   プロジェクト作成
-   プロジェクト詳細取得
-   プロジェクト更新
-   プロジェクト削除
-   プロジェクト内のタスク一覧取得
-   プロジェクト内にタスク作成
-   タスク詳細取得
-   タスク更新
-   タスク削除
-   自分のタスク一覧取得

---

## 🎯 Step 2: Controller 作成

必要な Controller を `app/Http/Controllers/Api/` に作成してください。

```bash
# 例：Controllerの作成
./vendor/bin/sail artisan make:controller Api/YourController
```

**要件：**

-   各 Controller にメソッドを定義してください
-   各メソッドの中身は `abort(501, 'Not Implemented');` としてください

---

## 🎨 Step 3: ApiResource 作成

フロントエンドに返す JSON の構造を定義する ApiResource を作成してください。

```bash
# ApiResourceの作成
./vendor/bin/sail artisan make:resource ProjectResource
./vendor/bin/sail artisan make:resource TaskResource
```

**要件：**

-   `app/Http/Resources/` に作成された各 Resource ファイルの `toArray()` メソッドを定義してください

---

## ✍️ 演習：あなたのターン！

> **📚 事前学習：** エンドポイント設計を始める前に、RESTful API の基本原則について調べてください。
>
> 以下のキーワードで検索することをおすすめします：
>
> -   RESTful API とは
> -   HTTP メソッド（GET, POST, PUT, PATCH, DELETE）の使い分け
> -   リソース指向の URL 設計
> -   RESTful API 階層構造

### 演習 1: エンドポイント設計を文書化

全てのエンドポイントを紙やドキュメントに書き出してください。

**記載内容：**

-   HTTP メソッド
-   URL
-   説明

### 演習 2: Controller 作成とメソッド定義

1. 必要な Controller を `app/Http/Controllers/Api/` に作成
2. 各メソッドの中身は `abort(501, 'Not Implemented');` で OK

### 演習 3: ルーティング定義

`routes/api.php` に全てのエンドポイントを定義してください。

### 演習 4: ApiResource 作成と定義

1. 必要な ApiResource を作成
2. 各 Resource の `toArray()` メソッドを定義

---

## 🧪 動作確認

設計が完了したら、以下で確認してください：

```bash
# ルート一覧を表示
./vendor/bin/sail artisan route:list --path=api
```

```bash
# 動作確認（501エラーが返ればOK）
./vendor/bin/sail artisan serve

# 別ターミナルで
curl -X GET http://localhost:8000/api/projects
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
