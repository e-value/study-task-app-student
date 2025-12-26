# 📚 Laravel API 設計 実践講座

> **Laravel × Vue3 SPA で学ぶ REST API 設計とレビュープロセス**

---

## 🎯 この講座について

この講座では、**実際の開発現場で使われる PR レビュープロセス**を通じて、  
**REST API の設計力**を実践的に身につけることができます。

### ✨ 学べること

-   ✅ **REST API の設計思想**（リソース思考、ステータスコード、命名規則）
-   ✅ **Laravel の API 実装**（Controller、Resource、Request Validation）
-   ✅ **PR レビューの受け方**（指摘の受け止め方、修正の進め方）
-   ✅ **実務レベルのコード品質**（保守性、拡張性、可読性）

---

## 📖 学習の流れ

```
1️⃣ このリポジトリを Fork
     ↓
2️⃣ ローカルに Clone
     ↓
3️⃣ 課題を実装（API エンドポイントの追加）
     ↓
4️⃣ Pull Request を作成
     ↓
5️⃣ レビューを受ける（エンドポイント設計、責務分離など）
     ↓
6️⃣ 指摘を修正 → 再レビュー
     ↓
7️⃣ 承認 → マージ → 次のレッスンへ 🎉
```

---

## 🚀 はじめ方

### 1️⃣ このリポジトリを Fork

右上の **「Fork」** ボタンをクリックして、あなたのアカウントにコピーしてください。

### 2️⃣ ローカルに Clone

```bash
cd ~/Desktop
git clone https://github.com/e-value/study-task-app-student.git
cd study-task-app-student
```

### 3️⃣ 環境構築

> **💡 最短 3 ステップでセットアップしたい方へ**  
> **[QUICKSTART.md](./QUICKSTART.md)** をご覧ください！

#### 手順 1: 環境変数ファイルの準備

```bash
cp .env.example .env
```

#### 手順 2: Composer パッケージのインストール

**ローカルに Composer がインストールされている場合（推奨）**:

```bash
composer install --ignore-platform-reqs
```

**ローカルに Composer がない場合（Docker 経由）**:

```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php84-composer:latest \
    composer install --ignore-platform-reqs
```

#### 手順 3: Docker コンテナの起動

```bash
./vendor/bin/sail up -d
```

> **📝 Note**: コンテナの起動には少し時間がかかります（初回は特に）

#### 手順 4: Node.js パッケージのインストール

```bash
./vendor/bin/sail npm install
```

#### 手順 5: アプリケーションキーの生成

```bash
./vendor/bin/sail artisan key:generate
```

#### 手順 6: データベースのセットアップ

```bash
# MySQL の起動を待ってから実行（15秒待機）
sleep 15 && ./vendor/bin/sail artisan migrate:fresh --seed
```

> **💡 Tip**: シーダーにより以下のテストユーザーが作成されます
>
> -   owner@example.com (password: `password`)
> -   admin@example.com (password: `password`)
> -   member@example.com (password: `password`)

#### 手順 7: フロントエンドの起動

**別のターミナルを開いて**以下を実行：

```bash
cd study-task-app-student
./vendor/bin/sail npm run dev
```

### 4️⃣ ブラウザでアクセス

セットアップが完了したら、以下の URL にアクセスしてください：

-   **アプリケーション**: http://localhost
-   **phpMyAdmin**: http://localhost:8080

#### 🔑 テストユーザーでログイン

以下のアカウントでログインできます：

| メールアドレス     | パスワード | 役割     |
| :----------------- | :--------- | :------- |
| owner@example.com  | password   | オーナー |
| admin@example.com  | password   | 管理者   |
| member@example.com | password   | メンバー |

---

## 📝 課題について

### Lesson 1: タスク API の実装

**目標**: プロジェクトに紐づくタスク（Task）の CRUD API を実装する

#### 実装するエンドポイント

| メソッド | URL                             | 説明           |
| :------- | :------------------------------ | :------------- |
| `GET`    | `/api/projects/{project}/tasks` | タスク一覧取得 |
| `POST`   | `/api/projects/{project}/tasks` | タスク作成     |
| `GET`    | `/api/tasks/{task}`             | タスク詳細取得 |
| `PUT`    | `/api/tasks/{task}`             | タスク更新     |
| `DELETE` | `/api/tasks/{task}`             | タスク削除     |

#### 実装の流れ

1. **ブランチを切る**

    ```bash
    git checkout -b feature/task-api
    ```

2. **Controller を作成**

    ```bash
    ./vendor/bin/sail artisan make:controller Api/TaskController
    ```

3. **実装する**

    - `routes/api.php` にルーティングを追加
    - `TaskController` に各メソッドを実装
    - `TaskResource` を作成してレスポンス形式を定義
    - `TaskRequest` を作成してバリデーションを実装

4. **動作確認**

    - Postman や curl でエンドポイントをテスト
    - フロントエンドから実際に動作するか確認

5. **コミット & プッシュ**

    ```bash
    git add .
    git commit -m "feat: タスクAPIの実装"
    git push origin feature/task-api
    ```

6. **Pull Request を作成**
    - GitHub で PR を作成
    - レビュー依頼を送る

---

## 🔍 レビューで見られるポイント

PR レビューでは、以下の観点でコードがチェックされます。

### 1. **エンドポイント設計** 🎯

-   ✅ RESTful な URL 設計になっているか？
-   ✅ 適切な HTTP メソッドを使っているか？
-   ✅ ステータスコードは正しいか？

**よくある指摘例**

```php
// ❌ 悪い例：動詞を使っている
POST /api/tasks/create

// ✅ 良い例：リソース思考
POST /api/tasks
```

### 2. **Resource の責務** 📦

-   ✅ API レスポンスの形式が統一されているか？
-   ✅ Resource クラスでレスポンスを整形しているか？
-   ✅ 不要な情報を返していないか？

**よくある指摘例**

```php
// ❌ 悪い例：Controller で直接配列を返す
return response()->json($task);

// ✅ 良い例：Resource を使う
return new TaskResource($task);
```

### 3. **バリデーション** ✅

-   ✅ FormRequest でバリデーションしているか？
-   ✅ エラーメッセージは分かりやすいか？
-   ✅ 必須/任意が適切か？

### 4. **ステータスコード** 🔢

-   ✅ 作成時に `201 Created` を返しているか？
-   ✅ 更新時に `200 OK` を返しているか？
-   ✅ 削除時に `204 No Content` を返しているか？
-   ✅ エラー時に適切なコードを返しているか？

### 5. **コードの品質** 🎨

-   ✅ 責務が明確に分離されているか？
-   ✅ 変数名・メソッド名が分かりやすいか？
-   ✅ 冗長なコードがないか？

---

## 💡 参考資料

### 📚 Laravel 公式ドキュメント

-   [API Resources](https://laravel.com/docs/12.x/eloquent-resources)
-   [Form Requests](https://laravel.com/docs/12.x/validation#form-request-validation)
-   [Routing](https://laravel.com/docs/12.x/routing)

### 🎓 REST API 設計

-   [RESTful API 設計のベストプラクティス](https://restfulapi.net/)
-   [HTTP ステータスコード一覧](https://developer.mozilla.org/ja/docs/Web/HTTP/Status)

---

## ❓ よくある質問

### Q1. コンテナが起動しない

**A.** 以下を確認してください：

1. **Docker Desktop が起動しているか確認**

```bash
docker info
```

2. **ポートが使用されていないか確認**

```bash
# ポート 80 の使用状況
lsof -i :80

# ポート 3306 の使用状況
lsof -i :3306
```

3. **コンテナの状態を確認**

```bash
# コンテナの状態を確認
./vendor/bin/sail ps

# ログを確認
./vendor/bin/sail logs

# 特定のコンテナのログを確認
./vendor/bin/sail logs mysql
```

4. **コンテナを再起動**

```bash
./vendor/bin/sail down
./vendor/bin/sail up -d
```

### Q2. マイグレーションでエラーが出る

#### 💡 エラー 1: `Unknown database 'study_task_app'`

```
SQLSTATE[HY000] [1049] Unknown database 'study_task_app'
```

**原因**: データベースが作成されていない、または古いボリュームが残っている

**解決方法**:

1. **`.env` ファイルの確認**

    ```bash
    # .env ファイルが存在するか確認
    ls -la .env
    ```

    存在しない場合は作成：

    ```bash
    cp .env.example .env
    ```

    **重要**: `.env` ファイルに以下の設定が正しく記述されているか確認してください：

    ```env
    DB_CONNECTION=mysql
    DB_HOST=mysql
    DB_PORT=3306
    DB_DATABASE=study_task_app
    DB_USERNAME=sail
    DB_PASSWORD=password
    ```

    > **💡 注意**: `DB_USERNAME` と `DB_PASSWORD` が設定されていないと、データベース接続エラーが発生します。

2. **Docker ボリュームを含めて再起動**

    ```bash
    # コンテナとボリュームを完全削除
    ./vendor/bin/sail down -v

    # 再起動
    ./vendor/bin/sail up -d

    # MySQL の起動を待つ（重要！）
    sleep 15

    # マイグレーション実行
    ./vendor/bin/sail artisan migrate:fresh --seed
    ```

3. **それでもダメな場合：手動でデータベースを作成**

    ```bash
    # MySQL コンテナに接続
    ./vendor/bin/sail mysql

    # データベースを作成
    CREATE DATABASE IF NOT EXISTS study_task_app;

    # 確認
    SHOW DATABASES;

    # 終了
    exit
    ```

    または、phpMyAdmin (http://localhost:8080) からデータベース `study_task_app` を作成してください。

#### 💡 エラー 2: その他のマイグレーションエラー

**A.** MySQL コンテナの起動を待ってから実行してください。

```bash
# MySQL の起動を待つ（15秒）
sleep 15

# マイグレーションを実行
./vendor/bin/sail artisan migrate:fresh --seed
```

**それでもエラーが出る場合**:

```bash
# コンテナの状態を確認
./vendor/bin/sail ps

# MySQL のログを確認
./vendor/bin/sail logs mysql

# コンテナを再起動
./vendor/bin/sail down
./vendor/bin/sail up -d
```

### Q3. npm install でエラーが出る

**A.** 依存関係の競合エラーが出た場合は、以下を試してください。

**エラー例**:

```
npm error ERESOLVE could not resolve
npm error Conflicting peer dependency: vite@7.3.0
```

**原因**:

-   `laravel-vite-plugin@2.x` は Vite 7.x を要求しますが、このプロジェクトは Vite 6.x を使用しています

**解決方法**:

このプロジェクトは Vite 6.x ベースで設計されているため、以下の手順で解決してください：

1. **package.json の確認**

    `laravel-vite-plugin` が `^1.0.0` になっているか確認してください。  
    もし `^2.0.0` になっている場合は、以下のように修正してください：

    ```json
    "laravel-vite-plugin": "^1.0.0"
    ```

2. **キャッシュをクリア**

    ```bash
    ./vendor/bin/sail exec laravel.test rm -rf node_modules package-lock.json
    ```

3. **再インストール**
    ```bash
    ./vendor/bin/sail npm install
    ```

### Q4. フロントエンドが表示されない

**A.** Vite の開発サーバーが起動しているか確認してください。

```bash
# 別ターミナルで実行
./vendor/bin/sail npm run dev
```

### Q5. PR レビューで何を書けばいい？

**A.** 以下のテンプレートを参考にしてください。

```markdown
## 実装内容

-   タスクの CRUD API を実装しました

## 動作確認

-   ✅ 一覧取得: GET /api/projects/1/tasks
-   ✅ 詳細取得: GET /api/tasks/1
-   ✅ 作成: POST /api/projects/1/tasks
-   ✅ 更新: PUT /api/tasks/1
-   ✅ 削除: DELETE /api/tasks/1

## 質問・相談

-   ステータスの更新 API は別エンドポイントにすべきでしょうか？
```

---

## 📞 サポート

質問や問題があれば、以下の方法でサポートを受けられます：

1. **Issue を作成**（バグ報告・質問）
2. **PR コメント**（実装に関する相談）
3. **Discord / Slack**（講師と直接やり取り）

---

## 🎉 Let's Code!

**実装 → レビュー → 修正** のサイクルを回すことで、  
確実に API 設計力が身につきます！

頑張ってください！💪✨

---

## 📦 技術スタック

| 分類               | 技術                           |
| :----------------- | :----------------------------- |
| **バックエンド**   | Laravel 12.x                   |
| **フロントエンド** | Vue 3 SPA + Vue Router + Pinia |
| **認証**           | Laravel Sanctum                |
| **スタイリング**   | Tailwind CSS                   |
| **データベース**   | MySQL 8.4                      |
| **DB 管理ツール**  | phpMyAdmin                     |
| **キャッシュ**     | Redis                          |
| **開発環境**       | Docker (Laravel Sail)          |
| **ビルドツール**   | Vite 6                         |

---

## 📚 関連ドキュメント

-   **[QUICKSTART.md](./QUICKSTART.md)** - 最短 3 ステップのセットアップガイド
-   **[SETUP.md](./SETUP.md)** - 詳細なセットアップ手順
-   **[docs/PROJECT_OVERVIEW.md](./docs/PROJECT_OVERVIEW.md)** - アーキテクチャ設計
