# プロジェクト概要

sample-project の構成と技術選定について説明します。

---

## 📊 プロジェクト情報

| 項目             | 内容                                    |
| ---------------- | --------------------------------------- |
| **プロジェクト名** | sample-project                          |
| **目的**         | Laravel × Vue3 の学習用テンプレート     |
| **開発環境**     | Docker (Laravel Sail)                   |
| **開発開始日**   | 2024 年                                 |
| **ステータス**   | ✅ 開発環境構築済み・ログイン機能実装済み |

---

## 🏗️ アーキテクチャ

### システム構成図

```
┌─────────────────────────────────────────────┐
│           ユーザー（ブラウザ）              │
└─────────────────┬───────────────────────────┘
                  │ HTTP/HTTPS
┌─────────────────┼───────────────────────────┐
│     Docker Compose Environment              │
│                 │                            │
│  ┌──────────────▼──────────────┐            │
│  │   Laravel Container         │            │
│  │   ┌────────────────────┐    │            │
│  │   │  Laravel 11.x      │    │            │
│  │   │  + Inertia.js      │    │            │
│  │   │  + Breeze          │    │            │
│  │   └────────────────────┘    │            │
│  │   ┌────────────────────┐    │            │
│  │   │  Vite (HMR)        │    │            │
│  │   │  + Vue 3           │    │            │
│  │   │  + Tailwind CSS    │    │            │
│  │   └────────────────────┘    │            │
│  │   Port: 80, 5173            │            │
│  └──────────────┬───────────────┘            │
│                 │                            │
│  ┌──────────────▼──────────────┐            │
│  │   MySQL Container           │            │
│  │   MySQL 8.4                 │            │
│  │   Port: 3306                │            │
│  └─────────────────────────────┘            │
│                                              │
│  ┌───────────────────────────────┐          │
│  │   Redis Container             │          │
│  │   Redis (Alpine)              │          │
│  │   Port: 6379                  │          │
│  └───────────────────────────────┘          │
└──────────────────────────────────────────────┘
```

---

## 🛠️ 技術スタック詳細

### バックエンド

| 技術               | バージョン | 用途                        |
| ------------------ | ---------- | --------------------------- |
| **Laravel**        | 11.x       | Web フレームワーク          |
| **PHP**            | 8.2+       | プログラミング言語          |
| **Inertia.js**     | 2.0        | SPA 用アダプター            |
| **Laravel Breeze** | 2.3        | 認証スターターキット        |
| **Laravel Sanctum**| 4.0        | API トークン認証            |
| **Ziggy**          | 2.0        | JS でのルート名前解決       |

### フロントエンド

| 技術               | バージョン | 用途                        |
| ------------------ | ---------- | --------------------------- |
| **Vue**            | 3.4        | フロントエンドフレームワーク|
| **Vite**           | 6.0        | ビルドツール                |
| **Tailwind CSS**   | 3.2        | CSS フレームワーク          |
| **Axios**          | 1.11       | HTTP クライアント           |

### インフラ・開発環境

| 技術               | バージョン | 用途                        |
| ------------------ | ---------- | --------------------------- |
| **Docker**         | -          | コンテナ仮想化              |
| **Laravel Sail**   | 1.41       | Docker 開発環境             |
| **MySQL**          | 8.4        | リレーショナルデータベース  |
| **Redis**          | Alpine     | キャッシュ・セッション      |

---

## 📁 ディレクトリ構造の説明

### バックエンド（Laravel）

```
app/
├── Http/
│   ├── Controllers/          # コントローラー層
│   │   ├── Auth/            # 認証関連コントローラー
│   │   └── ProfileController.php
│   ├── Middleware/          # ミドルウェア
│   └── Requests/            # フォームリクエスト
├── Models/                  # Eloquent モデル
│   └── User.php
└── Providers/               # サービスプロバイダー

database/
├── migrations/              # データベーススキーマ
├── factories/               # モデルファクトリー
└── seeders/                 # シーダー

routes/
├── web.php                  # Web ルート
├── auth.php                 # 認証ルート
└── api.php                  # API ルート
```

### フロントエンド（Vue）

```
resources/
├── js/
│   ├── app.js               # エントリーポイント
│   ├── Components/          # 再利用可能なコンポーネント
│   │   ├── ApplicationLogo.vue
│   │   ├── PrimaryButton.vue
│   │   ├── TextInput.vue
│   │   └── ...
│   ├── Layouts/             # レイアウトコンポーネント
│   │   ├── AuthenticatedLayout.vue  # 認証済み
│   │   └── GuestLayout.vue          # ゲスト
│   └── Pages/               # ページコンポーネント
│       ├── Auth/            # 認証ページ
│       │   ├── Login.vue
│       │   ├── Register.vue
│       │   └── ...
│       ├── Profile/         # プロフィールページ
│       ├── Dashboard.vue    # ダッシュボード
│       └── Welcome.vue      # ウェルカムページ
└── css/
    └── app.css              # Tailwind CSS
```

---

## 🔄 データフロー

### Inertia.js を使用したデータフロー

```
1. ユーザーがブラウザでアクション
   ↓
2. Vue コンポーネントが Inertia リクエストを送信
   ↓
3. Laravel のルーターが受信
   ↓
4. コントローラーが処理
   ↓
5. Inertia::render() でデータとコンポーネント名を返す
   ↓
6. Inertia.js が JSON レスポンスを受け取る
   ↓
7. Vue コンポーネントが再レンダリング
   ↓
8. ブラウザに表示（ページリロードなし！）
```

### 認証フロー

```
1. ユーザーが /register にアクセス
   ↓
2. Register.vue が表示される
   ↓
3. フォーム送信
   ↓
4. RegisteredUserController@store が処理
   ↓
5. ユーザーを DB に保存
   ↓
6. Auth::login() でログイン
   ↓
7. /dashboard にリダイレクト
   ↓
8. Dashboard.vue が表示される
```

---

## 🔐 セキュリティ対策

### 実装済みのセキュリティ機能

1. **CSRF 保護**
    - 全てのフォームに CSRF トークンを自動付与
    - `@csrf` ディレクティブ使用

2. **XSS 対策**
    - Blade のエスケープ機能
    - Vue の自動エスケープ

3. **SQL インジェクション対策**
    - Eloquent ORM 使用
    - プリペアドステートメント

4. **パスワード保護**
    - bcrypt によるハッシュ化
    - 最小 8 文字の制限

5. **レート制限**
    - ログイン試行回数の制限
    - API リクエスト数の制限

6. **セッション管理**
    - セキュアなセッションクッキー
    - HttpOnly フラグ

---

## 🧪 テスト戦略

### テストの種類

```
tests/
├── Feature/                 # フィーチャーテスト
│   ├── Auth/               # 認証機能のテスト
│   │   ├── AuthenticationTest.php
│   │   ├── RegistrationTest.php
│   │   └── ...
│   └── ProfileTest.php
└── Unit/                    # ユニットテスト
    └── ExampleTest.php
```

### テスト実行

```bash
# 全テスト実行
sail artisan test

# 認証テストのみ
sail artisan test --filter=Auth

# カバレッジ付き
sail artisan test --coverage
```

---

## 📈 パフォーマンス最適化

### 実装済みの最適化

1. **キャッシュ戦略**
    - Redis によるセッション管理
    - クエリ結果のキャッシュ

2. **アセット最適化**
    - Vite による高速ビルド
    - コード分割（Code Splitting）
    - Tree Shaking

3. **データベース最適化**
    - Eager Loading で N+1 問題を回避
    - インデックスの適切な設定

---

## 🚀 デプロイメント

### 本番環境へのデプロイ手順（概要）

1. **環境変数の設定**

```bash
APP_ENV=production
APP_DEBUG=false
```

2. **依存関係のインストール**

```bash
composer install --optimize-autoloader --no-dev
npm ci
npm run build
```

3. **キャッシュの生成**

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

4. **マイグレーション**

```bash
php artisan migrate --force
```

---

## 🔮 今後の拡張予定

-   [ ] タスク管理機能
-   [ ] API エンドポイント
-   [ ] ファイルアップロード機能
-   [ ] 通知機能
-   [ ] チーム/組織機能
-   [ ] CI/CD パイプライン

---

## 📚 参考資料

### 公式ドキュメント

-   [Laravel 公式ドキュメント](https://laravel.com/docs)
-   [Vue 3 公式ドキュメント](https://vuejs.org/)
-   [Inertia.js 公式ドキュメント](https://inertiajs.com/)
-   [Tailwind CSS 公式ドキュメント](https://tailwindcss.com/)

### コミュニティ

-   [Laravel Japan User Group](https://laravel.jp/)
-   [Vue.js 日本ユーザーグループ](https://jp.vuejs.org/)

---

## 🤝 コントリビューション

このプロジェクトへの貢献を歓迎します！

1. Fork する
2. Feature ブランチを作成 (`git checkout -b feature/amazing-feature`)
3. コミット (`git commit -m 'Add some amazing feature'`)
4. Push (`git push origin feature/amazing-feature`)
5. Pull Request を作成

---

## 📝 ライセンス

このプロジェクトは MIT ライセンスの下で公開されています。

---

## 📧 お問い合わせ

質問や提案がある場合は、Issue を作成してください。

Happy Coding! 🚀✨

