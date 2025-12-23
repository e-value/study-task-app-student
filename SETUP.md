# study-task-app 🚀

**Laravel × Sail × Vue3 (SPA)** で構築されたプロジェクトです。  
**ログイン機能付きの初期画面が実装済み**で、この README に従えば、誰でも環境構築なしでプロジェクトを立ち上げることができます。

---

## ✨ このプロジェクトの特徴

-   ✅ **認証機能実装済み**（ログイン・ログアウト・ユーザー登録）
-   ✅ **Docker 完全対応**（PHP・Node.js・Composer のインストール不要）
-   ✅ **モダンなフロントエンド**（Vue 3 SPA + Vue Router + Pinia）
-   ✅ **Laravel API**（Sanctum による認証）
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

> **💡 ヒント**  
> 以降のドキュメントでは `sail` と記載します。エイリアス未設定の場合は `./vendor/bin/sail` に読み替えてください。

---

## 🚀 プロジェクト立ち上げ手順

### 1️⃣ プロジェクトをクローン

```bash
cd ~/Desktop
git clone <リポジトリURL> study-task-app
cd study-task-app
```

### 2️⃣ 環境変数ファイルを準備

```bash
cp .env.example .env
```

### 3️⃣ 依存関係のインストール（初回のみ）

```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php84-composer:latest \
    composer install --ignore-platform-reqs
```

### 4️⃣ コンテナを起動

```bash
sail up -d
```

### 5️⃣ パッケージのインストール

```bash
# Composer (既にインストール済みの場合はスキップ)
sail composer install

# NPM
sail npm install
```

### 6️⃣ アプリケーションキーの生成

```bash
sail artisan key:generate
```

### 7️⃣ データベースのセットアップ

```bash
# データベースを作成
sail exec mysql mysql -uroot -e "CREATE DATABASE IF NOT EXISTS study_task_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# マイグレーションとシーダーを実行
sail artisan migrate:fresh --seed
```

> **📝 シーダーについて**  
> `--seed` オプションを付けることで、以下のダミーデータが自動的に作成されます：
>
> -   3 人のユーザー（owner@example.com, admin@example.com, member@example.com）
> -   複数のプロジェクト
> -   サンプルタスク
> -   メンバーシップ（ロール設定）
>
> パスワードは全て `password` です。

### 8️⃣ フロントエンドのビルド（開発モード）

**別のターミナルを開いて**以下を実行：

```bash
sail npm run dev
```

---

## 🌐 アプリケーションにアクセス

ブラウザで以下の URL を開いてください：

-   **アプリケーション**: http://localhost
-   **phpMyAdmin**: http://localhost:8080

### 🎉 初回アクセス時

ウェルカム画面が表示されます！

1. **「Register」** をクリックして新規ユーザーを作成
2. 必要な情報を入力して登録
3. 自動的にログインされ、ダッシュボードが表示されます

### 🗄️ データベース管理（phpMyAdmin）

phpMyAdmin にはログイン不要で直接アクセスできます：

-   **URL**: http://localhost:8080
-   **データベース名**: `study_task_app`
-   **ユーザー名**: `root`
-   **パスワード**: なし（空）

左側のサイドバーから `study_task_app` を選択すると、テーブル一覧が表示されます。

---

## 🛠 よく使うコマンド

### コンテナの起動・停止

```bash
# バックグラウンドで起動
sail up -d

# コンテナを停止
sail down
```

### Artisan コマンド

```bash
# マイグレーション
sail artisan migrate

# キャッシュクリア
sail artisan cache:clear
```

### NPM コマンド

```bash
# 開発サーバーの起動
sail npm run dev

# 本番用ビルド
sail npm run build
```

---

## 📦 技術スタック

| 分類               | 技術                           |
| ------------------ | ------------------------------ |
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

## 🎉 これで準備完了です！

Happy Coding! 🚀✨
