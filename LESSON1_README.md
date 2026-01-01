# Lesson1: API エンドポイント設計と Controller 責務分離

## 🎯 この Lesson の目的

この Lesson1 では、**あなた自身で**以下を設計します：

1. **API エンドポイント設計** - RESTful な URL と HTTP メソッドを決める
2. **Controller 責務分離** - どの Controller に何を実装すべきか決める
3. **ルーティング定義** - `routes/api.php` にエンドポイントを定義する
4. **ApiResource 設計** - フロントエンドに返す JSON 構造を考える

---

## 🌿 ブランチ作成

課題に取り組む前に、Lesson 用のブランチを作成してください：

```bash
# 現在のブランチを確認
git branch

# メインブランチに切り替え
git checkout main

# 最新の状態に更新（重要：必ずpullすること）
git pull origin main

# Lesson用のブランチを作成（例：lesson1, lesson01 など）
git checkout -b lesson1
```

**推奨：** 各 Lesson ごとに専用のブランチを作成することで、作業を整理しやすくなります。

---

## 📋 要件：実現すべき機能

-   プロジェクトの一覧取得
-   プロジェクトの作成
-   プロジェクトの詳細取得
-   プロジェクトの更新
-   プロジェクトの削除
-   プロジェクトのメンバー追加
-   プロジェクトのタスク一覧取得
-   プロジェクトのメンバーを削除
-   プロジェクトにタスク作成
-   プロジェクトのタスク詳細取得
-   プロジェクトのタスクを削除
-   プロジェクトのタスクを更新
-   プロジェクトのタスクを開始
-   プロジェクトのタスクを完了
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

## 🔗 リレーション（Eloquent 関係）

設計の参考に、`app/Models/` ディレクトリ内のモデルファイルを確認して、モデル間のリレーションを把握してください。

以下の表は、各モデル間のリレーションをまとめたものです：

| モデル         | リレーションメソッド | 関係の種類   | 関連モデル | 備考                                                                                     |
| -------------- | -------------------- | ------------ | ---------- | ---------------------------------------------------------------------------------------- |
| **Project**    | `users()`            | 多対多       | User       | プロジェクトのメンバー（ユーザー）一覧を取得。中間テーブル: `memberships`, pivot: `role` |
| **Project**    | `tasks()`            | 1 対多       | Task       | プロジェクトのタスク一覧                                                                 |
| **Task**       | `project()`          | 多対 1       | Project    | タスクが属するプロジェクト                                                               |
| **Task**       | `createdBy()`        | 多対 1       | User       | タスクを作成したユーザー（外部キー: `created_by`）                                       |
| **Membership** | （リレーションなし） | 中間テーブル | -          | Pivot モデル。`$project->users()`や`$user->projects()`経由でアクセス                     |
| **User**       | `projects()`         | 多対多       | Project    | ユーザーが所属するプロジェクト一覧を取得。中間テーブル: `memberships`, pivot: `role`     |
| **User**       | `createdTasks()`     | 1 対多       | Task       | ユーザーが作成したタスク一覧（外部キー: `created_by`）                                   |

---

## 📝 Step 1: エンドポイント設計

RESTful な API エンドポイントを設計してください。

**設計すべきエンドポイント：**

-   プロジェクトの一覧取得
-   プロジェクトの作成
-   プロジェクトの詳細取得
-   プロジェクトの更新
-   プロジェクトの削除
-   プロジェクトのメンバー追加
-   プロジェクトのタスク一覧取得
-   プロジェクトのメンバーを削除
-   プロジェクトにタスク作成
-   プロジェクトのタスク詳細取得
-   プロジェクトのタスクを削除
-   プロジェクトのタスクを更新
-   プロジェクトのタスクを開始
-   プロジェクトのタスクを完了
-   自分のタスク一覧取得（自分が所属しているプロジェクトの全タスク）

---

## 🎯 Step 2: Controller 作成

必要な Controller を `app/Http/Controllers/Api/` に作成してください。

```bash
# 例：Controllerの作成
sail artisan make:controller Api/YourController
```

**要件：**

-   各 Controller にメソッドを定義してください
-   各メソッドの中身は `abort(501, 'Not Implemented');` としてください

**記載例：**

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HogeController extends Controller
{
    public function hoge()
    {
        abort(501, 'Not Implemented');
    }
}
```

---

## 🎨 Step 3: ApiResource 作成

フロントエンドに返す JSON の構造を定義する ApiResource を作成してください。

> **📚 事前学習：** ApiResource について知らない方は、まず Laravel の公式ドキュメントや参考資料で ApiResource の基本的な使い方を調べてから取り組んでください。
>
> 以下のキーワードで検索することをおすすめします：
>
> -   Laravel API Resources
> -   Laravel Resource クラス
> -   `php artisan make:resource`
> -   Resource の `toArray()` メソッド

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

### 演習 1: エンドポイント設計とルーティング定義

設計したエンドポイントを `routes/api.php` に記載してください。

**記載内容：**

-   HTTP メソッド
-   URL
-   対応する Controller とメソッド

### 演習 2: Controller 作成とメソッド定義

1. 必要な Controller を `app/Http/Controllers/Api/` に作成
2. 各メソッドの中身は `abort(501, 'Not Implemented');` で OK

### 演習 3: ApiResource 作成と定義

1. 必要な ApiResource を作成
2. 各 Resource の `toArray()` メソッドを定義

---

## 🚀 実装完了後の作業

実装が完了したら、ブランチの内容をpushしてからプルリクエストを作成してください：

**1. ブランチの内容をpushする：**

```bash
# 変更をステージング
git add .

# コミット
git commit -m "Lesson01 実装"

# ブランチをpush
git push origin lesson1
```

**2. プルリクエストを作成する：**

以下の形式でプルリクエストを作成してください：

**プルリクエストのタイトル形式：**

```
【名前】Lesson01 実装
```

**例：**

-   【宮田】Lesson01 実装

---

## 📚 参考リンク

-   [RESTful API 設計ガイド](https://restfulapi.net/)
-   [Laravel Resource Controllers](https://laravel.com/docs/controllers#resource-controllers)
-   [Laravel API Resources](https://laravel.com/docs/eloquent-resources)
