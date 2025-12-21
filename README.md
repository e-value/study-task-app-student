# sample-project 🚀

**Laravel × Sail × Vue3 × Inertia.js** で構築されたプロジェクトです。  
**ログイン機能付きの初期画面が実装済み**で、この README に従えば、誰でも環境構築なしでプロジェクトを立ち上げることができます。

> **⚡ 今すぐ始めたい方は [QUICKSTART.md](./QUICKSTART.md) をご覧ください！**

---

## ✨ このプロジェクトの特徴

-   ✅ **認証機能実装済み**（ログイン・ログアウト・ユーザー登録）
-   ✅ **Docker 完全対応**（PHP・Node.js・Composer のインストール不要）
-   ✅ **モダンなフロントエンド**（Vue 3 + Inertia.js + Tailwind CSS）
-   ✅ **開発環境即座に起動**（コマンド数回で動作する）

---

## 📋 必要な環境

-   **Docker Desktop** がインストールされていること
-   **Git** がインストールされていること

※ PHP、Composer、Node.js のインストールは不要です！

---

## ⚙️ Sail コマンドのエイリアス設定（推奨）

このプロジェクトでは `./vendor/bin/sail` の代わりに短く `sail` と入力できるように設定することを推奨します。

### macOS / Linux の場合

お使いのシェル設定ファイル（`~/.zshrc` または `~/.bashrc`）に以下を追加：

```bash
alias sail='[ -f sail ] && sh sail || sh vendor/bin/sail'
```

設定を反映：

```bash
source ~/.zshrc  # zsh の場合
# または
source ~/.bashrc  # bash の場合
```

### 設定後の確認

```bash
sail --version
```

> **💡 ヒント**  
> このエイリアスを設定すると、以降のドキュメントで `sail` と記載されているコマンドがすべて使えるようになります。  
> 設定しない場合は、`sail` を `./vendor/bin/sail` に読み替えてコマンドを実行してください。

---

## 🚀 プロジェクト立ち上げ手順

### 🎯 クイックスタート（推奨）

セットアップスクリプトを使えば、**1 コマンドで自動セットアップ**できます！

```bash
cd ~/Desktop/sample-project
./setup.sh
```

このスクリプトは以下を自動で実行します：

-   ✅ .env ファイルの作成
-   ✅ Composer パッケージのインストール
-   ✅ Docker コンテナの起動
-   ✅ Node.js パッケージのインストール
-   ✅ アプリケーションキーの生成
-   ✅ データベースのマイグレーション

> **💡 完了後の手順**  
> セットアップ完了後、別のターミナルで `sail npm run dev` を実行してください。

---

### 📝 手動セットアップ（詳細版）

セットアップスクリプトを使わない場合は、以下の手順を実行してください。

### 1️⃣ プロジェクトをクローン

```bash
cd ~/Desktop
git clone <リポジトリURL> sample-project
cd sample-project
```

> **💡 ヒント**  
> すでにプロジェクトをダウンロード済みの場合は `cd sample-project` だけで OK です。

### 2️⃣ Docker Desktop を起動

Docker Desktop が起動していることを確認してください。

### 3️⃣ 環境変数ファイルを準備

```bash
cp .env.example .env
```

### 4️⃣ 依存関係のインストール（初回のみ）

`vendor` ディレクトリが存在しない場合は以下を実行：

```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php85-composer:latest \
    composer install --ignore-platform-reqs
```

### 5️⃣ コンテナを起動

```bash
sail up -d
```

> **💡 初回起動時について**  
> 初回起動時は Docker イメージのビルドに時間がかかる場合があります（5〜10 分程度）。  
> コーヒーを淹れて待ちましょう ☕

### 6️⃣ Node.js のパッケージをインストール

```bash
sail npm install --legacy-peer-deps
```

### 7️⃣ アプリケーションキーの生成

```bash
sail artisan key:generate
```

### 8️⃣ データベースのマイグレーション

```bash
sail artisan migrate
```

このコマンドで、ユーザーテーブルなど必要なデータベースが作成されます。

### 9️⃣ フロントエンドのビルド（開発モード）

**別のターミナルを開いて**以下を実行：

```bash
cd ~/Desktop/sample-project
sail npm run dev
```

> **⚠️ 重要**  
> このコマンドは起動し続けます。停止したい場合は `Ctrl + C` を押してください。

---

## 🌐 アプリケーションにアクセス

ブラウザで以下の URL を開いてください：

-   **アプリケーション**: http://localhost
-   **Vite (HMR)**: http://localhost:5173

### 🎉 初回アクセス時

ログイン画面が表示されます！

1. **「Register」** をクリックして新規ユーザーを作成
2. 必要な情報を入力して登録
3. 自動的にログインされ、ダッシュボードが表示されます

---

## 📸 スクリーンショット

### ログイン画面

初回アクセス時に表示される認証画面です。

### ダッシュボード

ログイン後に表示されるメイン画面です。

---

## 🛠 よく使うコマンド

### コンテナの起動・停止

```bash
# バックグラウンドで起動
sail up -d

# フォアグラウンドで起動（ログを確認しながら）
sail up

# コンテナを停止
sail down

# コンテナを停止してボリュームも削除
sail down -v

# コンテナの状態確認
sail ps
```

### Artisan コマンド

```bash
# マイグレーション
sail artisan migrate

# マイグレーションのリセット（データが消えます！）
sail artisan migrate:fresh

# シーダーの実行
sail artisan db:seed

# マイグレーション＋シーダー実行
sail artisan migrate:fresh --seed

# キャッシュクリア
sail artisan cache:clear
sail artisan config:clear
sail artisan route:clear
sail artisan view:clear

# 全てのキャッシュをクリア
sail artisan optimize:clear
```

### Composer コマンド

```bash
# パッケージのインストール
sail composer install

# パッケージの追加
sail composer require パッケージ名

# 開発用パッケージの追加
sail composer require --dev パッケージ名
```

### NPM コマンド

```bash
# 開発サーバーの起動
sail npm run dev

# 本番用ビルド
sail npm run build

# パッケージの追加
sail npm install パッケージ名 --legacy-peer-deps
```

### データベース操作

```bash
# MySQLに接続
sail mysql

# Redisに接続
sail redis
```

### テスト実行

```bash
# 全テスト実行
sail artisan test

# 特定のテストを実行
sail artisan test --filter TestName
```

---

## 📦 技術スタック

| 分類               | 技術                        |
| ------------------ | --------------------------- |
| **バックエンド**   | Laravel 11.x                |
| **フロントエンド** | Vue 3 + Inertia.js          |
| **認証**           | Laravel Breeze (Inertia 版) |
| **スタイリング**   | Tailwind CSS                |
| **データベース**   | MySQL 8.4                   |
| **キャッシュ**     | Redis                       |
| **開発環境**       | Docker (Laravel Sail)       |
| **ビルドツール**   | Vite 6                      |

---

## 🗂 プロジェクト構造

```
sample-project/
├── app/                    # アプリケーションコア
│   ├── Http/
│   │   └── Controllers/   # コントローラー
│   │       └── Auth/      # 認証コントローラー
│   └── Models/            # Eloquent モデル
│       └── User.php       # ユーザーモデル
├── resources/
│   ├── js/                # Vue.js コンポーネント
│   │   ├── Components/   # 再利用可能なコンポーネント
│   │   ├── Layouts/      # レイアウトコンポーネント
│   │   │   └── AuthenticatedLayout.vue  # 認証済みレイアウト
│   │   └── Pages/        # ページコンポーネント (Inertia)
│   │       ├── Auth/     # 認証ページ（ログイン・登録）
│   │       ├── Dashboard.vue  # ダッシュボード
│   │       └── Welcome.vue    # ウェルカムページ
│   └── views/            # Blade テンプレート
│       └── app.blade.php # メインテンプレート
├── routes/
│   ├── web.php           # Web ルート
│   ├── auth.php          # 認証ルート
│   └── api.php           # API ルート
├── database/
│   ├── migrations/       # マイグレーションファイル
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   └── 0001_01_01_000001_create_cache_table.php
│   └── seeders/          # シーダー
├── tests/                # テストファイル
├── public/               # 公開ディレクトリ
├── compose.yaml          # Docker Compose 設定
├── .env.example          # 環境変数のサンプル
├── .env                  # 環境変数（コピーして作成）
└── package.json          # Node.js 依存関係
```

---

## ⚙️ 環境変数の設定

`.env` ファイルで以下の設定を確認・変更できます：

```env
# アプリケーション設定
APP_NAME=StudyTaskApp
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

# データベース設定
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=sail
DB_PASSWORD=password

# Redis 設定
REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379
```

---

## 🐛 トラブルシューティング

### ❌ ポートが既に使用されている場合

別のアプリケーションがポートを使用している場合、`.env` ファイルでポート番号を変更：

```env
APP_PORT=8080
FORWARD_DB_PORT=33060
FORWARD_REDIS_PORT=63790
VITE_PORT=5174
```

変更後、コンテナを再起動：

```bash
sail down
sail up -d
```

### ❌ パーミッションエラーが発生する場合

```bash
sudo chmod -R 777 storage bootstrap/cache
```

### ❌ npm install でエラーが出る場合

```bash
sail npm install --legacy-peer-deps
```

### ❌ コンテナが起動しない場合

```bash
# コンテナとボリュームをクリーンアップ
sail down -v

# 再度起動
sail up -d
```

### ❌ データベース接続エラー

```bash
# MySQLコンテナが起動しているか確認
sail ps

# MySQLコンテナのログを確認
docker logs sample-project-mysql-1

# マイグレーションを再実行
sail artisan migrate
```

### ❌ 「APP_KEY」が設定されていないエラー

```bash
sail artisan key:generate
```

### ❌ Vite がビルドエラーを出す場合

```bash
# node_modules を削除して再インストール
sail npm ci --legacy-peer-deps
```

### ❌ ログイン画面が表示されない場合

1. マイグレーションが実行されているか確認：

```bash
sail artisan migrate:status
```

2. ルートが正しく登録されているか確認：

```bash
sail artisan route:list
```

3. キャッシュをクリア：

```bash
sail artisan optimize:clear
```

---

## 📝 開発の進め方

### 新しい機能を追加する基本フロー

1. **ルートを定義** (`routes/web.php`)
2. **コントローラーを作成** (`app/Http/Controllers/`)
3. **Vue コンポーネントを作成** (`resources/js/Pages/`)
4. **必要に応じてモデル・マイグレーションを作成**

### データベースを変更する

```bash
# マイグレーションファイルを作成
sail artisan make:migration create_tasks_table

# マイグレーションを実行
sail artisan migrate
```

### モデルを作成する

```bash
# モデルとマイグレーションを同時に作成
sail artisan make:model Task -m

# モデル・マイグレーション・コントローラー・シーダーを一括作成
sail artisan make:model Task -mcrs
```

### コントローラーを作成する

```bash
# 基本的なコントローラー
sail artisan make:controller TaskController

# リソースコントローラー（CRUD用）
sail artisan make:controller TaskController --resource
```

### Vue コンポーネントを追加する

新しいページを作成する場合：

1. `resources/js/Pages/` に新しい `.vue` ファイルを作成
2. コントローラーから `Inertia::render()` で呼び出す

```php
// Controller 例
use Inertia\Inertia;

public function index()
{
    return Inertia::render('Tasks/Index', [
        'tasks' => Task::all()
    ]);
}
```

---

## 🔐 認証機能について

このプロジェクトには **Laravel Breeze (Inertia 版)** が実装されています。

### 実装されている機能

-   ✅ ユーザー登録
-   ✅ ログイン
-   ✅ ログアウト
-   ✅ パスワードリセット
-   ✅ メール認証
-   ✅ プロフィール編集

### 認証が必要なページを作る

`routes/web.php` で `auth` ミドルウェアを使用：

```php
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
});
```

### 現在のユーザー情報を取得

Vue コンポーネント内で：

```vue
<script setup>
import { usePage } from "@inertiajs/vue3";

const page = usePage();
const user = page.props.auth.user;

console.log(user.name); // ログイン中のユーザー名
</script>
```

---

## 🧪 動作確認

プロジェクトが正しくセットアップされたか確認するには：

### 1. コンテナの状態を確認

```bash
sail ps
```

以下のコンテナが `Up` 状態であることを確認：

-   `sample-project-laravel.test-1`
-   `sample-project-mysql-1`
-   `sample-project-redis-1`

### 2. ブラウザでアクセス

http://localhost を開いて、ログイン画面が表示されることを確認

### 3. ユーザー登録してログイン

1. 「Register」をクリック
2. 必要事項を入力して登録
3. ダッシュボードが表示されれば OK！ 🎉

### 4. データベースの確認

```bash
sail mysql

# MySQL内で
USE laravel;
SHOW TABLES;
SELECT * FROM users;
```

---

## 🎉 これで準備完了です！

この README に従えば、**5〜15 分程度**でログイン機能付きのアプリケーションを立ち上げることができます。

### 次のステップ

1. ✅ プロジェクトを立ち上げる（この README 参照）
2. 📝 独自の機能を追加する（タスク管理、ブログなど）
3. 🎨 デザインをカスタマイズする（Tailwind CSS）
4. 🚀 本番環境にデプロイする

### サポート

何か問題が発生した場合は、この README の**トラブルシューティング**セクションを参照してください。

Happy Coding! 🚀✨

---

## 📚 参考リンク

### 公式ドキュメント

-   [Laravel 公式ドキュメント](https://laravel.com/docs)
-   [Vue 3 公式ドキュメント](https://vuejs.org/)
-   [Inertia.js 公式ドキュメント](https://inertiajs.com/)
-   [Tailwind CSS 公式ドキュメント](https://tailwindcss.com/)
-   [Laravel Sail 公式ドキュメント](https://laravel.com/docs/sail)

### プロジェクト内ドキュメント

-   [⚡ クイックスタートガイド](./QUICKSTART.md) - 5 分で始める
-   [🔐 Laravel Breeze セットアップ](./docs/BREEZE_SETUP.md) - 認証機能の詳細
-   [📊 プロジェクト概要](./docs/PROJECT_OVERVIEW.md) - 技術選定とアーキテクチャ
