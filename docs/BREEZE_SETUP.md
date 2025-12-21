# Laravel Breeze セットアップ手順 🔐

このドキュメントは、このプロジェクトに **Laravel Breeze (Vue + Inertia)** がどのようにインストールされたかを記録したものです。

> **⚠️ 注意**  
> このプロジェクトには既に Breeze が実装済みです。この手順は**参考資料**として残しています。

---

## 📋 Laravel Breeze とは？

Laravel Breeze は、Laravel の認証機能を素早く実装するためのスターターキットです。

### 実装される機能

-   ✅ ユーザー登録
-   ✅ ログイン / ログアウト
-   ✅ パスワードリセット
-   ✅ メール認証
-   ✅ プロフィール編集
-   ✅ パスワード確認

---

## 🚀 インストール手順（参考）

このプロジェクトは以下の手順で作成されました：

### 1. Laravel プロジェクトの作成

```bash
composer create-project laravel/laravel sample-project
cd sample-project
```

### 2. Laravel Sail のインストール

```bash
composer require laravel/sail --dev
php artisan sail:install
```

選択肢から以下を選択：

-   MySQL
-   Redis

### 3. Laravel Breeze のインストール

```bash
sail up -d
sail composer require laravel/breeze --dev
```

### 4. Breeze のセットアップ（Vue + Inertia）

```bash
sail artisan breeze:install vue
```

**質問への回答：**

-   `Would you like to install dark mode support?` → No (または Yes)
-   `Which testing framework do you prefer?` → PHPUnit (または Pest)

### 5. フロントエンド依存関係のインストール

```bash
sail npm install
sail npm run dev
```

### 6. データベースマイグレーション

```bash
sail artisan migrate
```

---

## 📁 Breeze によって生成されたファイル

### コントローラー

```
app/Http/Controllers/Auth/
├── AuthenticatedSessionController.php      # ログイン
├── ConfirmablePasswordController.php      # パスワード確認
├── EmailVerificationNotificationController.php
├── EmailVerificationPromptController.php
├── NewPasswordController.php              # 新パスワード設定
├── PasswordController.php                 # パスワード更新
├── PasswordResetLinkController.php        # パスワードリセットリンク
├── RegisteredUserController.php           # ユーザー登録
└── VerifyEmailController.php              # メール認証

app/Http/Controllers/
└── ProfileController.php                   # プロフィール編集
```

### ルート

```
routes/
├── auth.php                                # 認証関連ルート
└── web.php                                 # 一般ルート（ダッシュボード等）
```

### Vue コンポーネント

```
resources/js/Pages/
├── Auth/
│   ├── ConfirmPassword.vue
│   ├── ForgotPassword.vue
│   ├── Login.vue                          # ログイン画面
│   ├── Register.vue                       # ユーザー登録画面
│   ├── ResetPassword.vue
│   └── VerifyEmail.vue
├── Profile/
│   ├── Edit.vue                           # プロフィール編集
│   └── Partials/
│       ├── DeleteUserForm.vue
│       ├── UpdatePasswordForm.vue
│       └── UpdateProfileInformationForm.vue
├── Dashboard.vue                          # ダッシュボード
└── Welcome.vue                            # ウェルカムページ

resources/js/Layouts/
├── AuthenticatedLayout.vue                # 認証済みレイアウト
└── GuestLayout.vue                        # ゲストレイアウト

resources/js/Components/
├── ApplicationLogo.vue
├── Checkbox.vue
├── DangerButton.vue
├── Dropdown.vue
├── InputError.vue
├── InputLabel.vue
├── Modal.vue
├── NavLink.vue
├── PrimaryButton.vue
├── ResponsiveNavLink.vue
├── SecondaryButton.vue
├── TextInput.vue
└── ... その他の UI コンポーネント
```

### マイグレーション

```
database/migrations/
├── 0001_01_01_000000_create_users_table.php
├── 0001_01_01_000001_create_cache_table.php
└── 0001_01_01_000002_create_jobs_table.php
```

---

## 🎨 カスタマイズポイント

### 1. リダイレクト先の変更

`app/Providers/RouteServiceProvider.php` または Laravel 11 の場合は `bootstrap/app.php`：

```php
public const HOME = '/dashboard';  // ログイン後のリダイレクト先
```

### 2. ログイン画面のカスタマイズ

`resources/js/Pages/Auth/Login.vue` を編集します。

### 3. 認証ミドルウェアの追加

新しいルートに認証を追加：

```php
Route::middleware('auth')->group(function () {
    Route::get('/tasks', [TaskController::class, 'index']);
});
```

### 4. ユーザー情報の拡張

`database/migrations/xxxx_create_users_table.php` にカラムを追加し、
`app/Models/User.php` の `$fillable` を更新します。

---

## 🔒 セキュリティ機能

### Breeze が提供するセキュリティ機能

1. **CSRF 保護** - フォームに自動的に CSRF トークンが含まれる
2. **パスワードハッシュ化** - bcrypt で安全にハッシュ化
3. **レート制限** - ログイン試行回数の制限
4. **メール認証** - オプションで有効化可能
5. **パスワードリセット** - 安全なトークンベースのリセット

---

## 🧪 テスト

Breeze には認証機能のテストも含まれています：

```bash
sail artisan test --filter=Auth
```

---

## 📚 参考リンク

-   [Laravel Breeze 公式ドキュメント](https://laravel.com/docs/starter-kits#laravel-breeze)
-   [Inertia.js 公式ドキュメント](https://inertiajs.com/)
-   [Vue 3 公式ドキュメント](https://vuejs.org/)

---

## 💡 よくある質問

### Q. Breeze と Jetstream の違いは？

-   **Breeze**: シンプルで軽量。基本的な認証機能のみ。
-   **Jetstream**: より高機能。チーム管理、2FA などを含む。

このプロジェクトでは、学習や小〜中規模プロジェクト向けに **Breeze** を採用しています。

### Q. API 認証も可能？

はい。Laravel Sanctum が既にインストールされているため、API 認証も実装可能です。

```bash
sail artisan install:api
```

### Q. ソーシャルログインを追加したい

Laravel Socialite を使用することで、Google、GitHub などのソーシャルログインを追加できます：

```bash
sail composer require laravel/socialite
```

---

## 🎉 まとめ

このプロジェクトには **Laravel Breeze** が完全に実装されており、すぐに認証機能を使い始めることができます。

カスタマイズが必要な場合は、上記のファイルを編集してください。
