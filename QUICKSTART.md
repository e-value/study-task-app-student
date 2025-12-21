# ⚡ クイックスタートガイド

**5 分でアプリを立ち上げる！**

---

## 🎯 前提条件

-   ✅ Docker Desktop がインストール済み
-   ✅ Docker Desktop が起動中

---

## 🚀 セットアップ（3 ステップ）

### 1️⃣ プロジェクトに移動

```bash
cd ~/Desktop/sample-project
```

### 2️⃣ セットアップスクリプトを実行

```bash
./setup.sh
```

このスクリプトが自動で以下を実行します：
-   環境変数の準備
-   Docker コンテナの起動
-   パッケージのインストール
-   データベースのセットアップ

⏱️ **所要時間**: 5〜10 分

### 3️⃣ フロントエンドを起動

**別のターミナルを開いて**以下を実行：

```bash
cd ~/Desktop/sample-project
sail npm run dev
```

---

## 🌐 アクセス

ブラウザで以下を開く：

-   **アプリケーション**: http://localhost
-   **phpMyAdmin（データベース管理）**: http://localhost:8080

---

## 🎉 初回ログイン

1. **「Register」** をクリック
2. 以下を入力：
    - Name: `Test User`
    - Email: `test@example.com`
    - Password: `password`
3. **「Register」** ボタンをクリック
4. ダッシュボードが表示されれば成功！ 🎊

---

## 🗄️ phpMyAdmin の使い方

データベースを直接確認・編集したい場合：

1. http://localhost:8080 にアクセス
2. 自動ログイン済み（認証情報は `.env` から取得）
3. 左側から `study-task-app` データベースを選択
4. テーブルを確認・編集できます

### よく使う機能
-   📊 **SQL タブ**: クエリを直接実行
-   📝 **テーブル表示**: データの閲覧・編集
-   🔍 **検索**: データの検索
-   📤 **エクスポート**: データのバックアップ

---

## 🛑 停止方法

### フロントエンド開発サーバーを停止

`npm run dev` を実行しているターミナルで **`Ctrl + C`**

### Docker コンテナを停止

```bash
sail down
```

---

## 🔄 再起動方法

### コンテナを起動

```bash
sail up -d
```

### フロントエンドを起動

```bash
sail npm run dev
```

---

## ❓ トラブルシューティング

### ポートが使用中のエラー

`.env` ファイルを編集：

```env
APP_PORT=8080
VITE_PORT=5174
```

その後、コンテナを再起動：

```bash
sail down
sail up -d
```

### その他の問題

詳細は **[README.md](./README.md)** の「トラブルシューティング」セクションを参照してください。

---

## 📖 次のステップ

-   📘 [README.md](./README.md) - 詳細なドキュメント
-   🔐 [docs/BREEZE_SETUP.md](./docs/BREEZE_SETUP.md) - 認証機能の詳細
-   🎨 [Tailwind CSS](https://tailwindcss.com/) - スタイリング
-   🖥️ [Vue 3](https://vuejs.org/) - フロントエンド開発

---

Happy Coding! 🚀✨

