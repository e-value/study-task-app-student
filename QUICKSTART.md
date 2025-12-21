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

👉 **http://localhost**

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

