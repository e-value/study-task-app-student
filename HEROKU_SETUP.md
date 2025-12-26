# 🚀 Heroku デプロイ設定ガイド

## ⚠️ エラー: MissingAppKeyException の解決方法

`APP_KEY`が設定されていないため、以下のエラーが発生しています：

```
MissingAppKeyException: No application encryption key has been specified.
```

## 📋 Heroku Config Vars の設定手順

### 1️⃣ APP_KEY の生成と取得

ローカル環境で以下のコマンドを実行して、新しい APP_KEY を生成：

```bash
php artisan key:generate --show
```

出力された値をコピーします（例：`base64:xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx=`）

### 2️⃣ Heroku Config Vars の設定

#### 方法 A: Heroku CLI を使用（推奨）

```bash
# APP_KEY を設定
heroku config:set APP_KEY="base64:xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx=" -a your-app-name

# その他の必要な環境変数も設定
heroku config:set APP_ENV=production -a your-app-name
heroku config:set APP_DEBUG=false -a your-app-name
heroku config:set APP_URL="https://your-app-name.herokuapp.com" -a your-app-name
```

#### 方法 B: Heroku Dashboard を使用

1. [Heroku Dashboard](https://dashboard.heroku.com/) にアクセス
2. アプリを選択
3. **Settings** タブをクリック
4. **Config Vars** セクションで **Reveal Config Vars** をクリック
5. 以下の環境変数を追加：

| Key         | Value                                                  |
| ----------- | ------------------------------------------------------ |
| `APP_KEY`   | `base64:xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx=` |
| `APP_ENV`   | `production`                                           |
| `APP_DEBUG` | `false`                                                |
| `APP_URL`   | `https://your-app-name.herokuapp.com`                  |

### 3️⃣ Sanctum 設定（SPA 認証用）

SPA で Sanctum を使用する場合、以下の環境変数を設定する必要があります：

```bash
# HerokuのドメインをSANCTUM_STATEFUL_DOMAINSに設定
heroku config:set SANCTUM_STATEFUL_DOMAINS="study-task-app-fc5bab4787a9.herokuapp.com" -a your-app-name

# セッション設定（HTTPSを使用するため）
heroku config:set SESSION_DRIVER=cookie -a your-app-name
heroku config:set SESSION_SECURE_COOKIE=true -a your-app-name
heroku config:set SESSION_DOMAIN=null -a your-app-name
```

**重要：** `SANCTUM_STATEFUL_DOMAINS` には、フロントエンドが動作するドメインを設定します。カスタムドメインを使用する場合は、そのドメインも追加してください（カンマ区切り）。

例：

```bash
heroku config:set SANCTUM_STATEFUL_DOMAINS="study-task-app-fc5bab4787a9.herokuapp.com,yourdomain.com" -a your-app-name
```

### 4️⃣ データベース設定（必要な場合）

データベースを使用する場合は、以下も設定：

```bash
# JawsDB MySQL または ClearDB MySQL を使用する場合
heroku config:set DB_CONNECTION=mysql -a your-app-name
heroku config:set DB_HOST=xxxxx.xxxxx.us-east-1.rds.amazonaws.com -a your-app-name
heroku config:set DB_PORT=3306 -a your-app-name
heroku config:set DB_DATABASE=xxxxxxxxxxxxxx -a your-app-name
heroku config:set DB_USERNAME=xxxxxxxxxxxxxx -a your-app-name
heroku config:set DB_PASSWORD=xxxxxxxxxxxxxx -a your-app-name
```

または、Heroku Postgres を使用する場合：

```bash
# Postgres アドオンを追加（まだの場合）
heroku addons:create heroku-postgresql:mini -a your-app-name

# DATABASE_URL が自動的に設定されます
# .env で DATABASE_URL を使用するように設定してください
```

### 5️⃣ 設定の確認

設定した環境変数を確認：

```bash
heroku config -a your-app-name
```

### 6️⃣ アプリケーションの再起動

環境変数を変更した後は、アプリを再起動：

```bash
heroku restart -a your-app-name
```

## 📝 必要な環境変数の一覧

最低限必要な環境変数：

-   ✅ `APP_KEY` - Laravel の暗号化キー（必須）
-   ✅ `APP_ENV` - `production`
-   ✅ `APP_DEBUG` - `false`
-   ✅ `APP_URL` - Heroku アプリの URL

Sanctum SPA 認証を使用する場合：

-   ✅ `SANCTUM_STATEFUL_DOMAINS` - フロントエンドのドメイン（例：`study-task-app-fc5bab4787a9.herokuapp.com`）
-   ✅ `SESSION_DRIVER` - `cookie`
-   ✅ `SESSION_SECURE_COOKIE` - `true`（HTTPS を使用するため）
-   ✅ `SESSION_DOMAIN` - `null`

データベースを使用する場合：

-   ✅ `DB_CONNECTION` - `mysql` または `pgsql`
-   ✅ `DB_HOST` - データベースホスト
-   ✅ `DB_PORT` - データベースポート
-   ✅ `DB_DATABASE` - データベース名
-   ✅ `DB_USERNAME` - データベースユーザー名
-   ✅ `DB_PASSWORD` - データベースパスワード

### ⚠️ エラー: 401 Unauthorized on /api/user

`/api/user` エンドポイントで 401 エラーが発生する場合、Sanctum の設定が正しく行われていない可能性があります。

**原因：**

-   `SANCTUM_STATEFUL_DOMAINS` が設定されていない
-   セッションクッキーが正しく送信されていない
-   HTTPS 設定が正しくない

**解決方法：**

1. **Heroku のドメインを確認**

    ```bash
    heroku info -a your-app-name
    ```

2. **SANCTUM_STATEFUL_DOMAINS を設定**

    ```bash
    heroku config:set SANCTUM_STATEFUL_DOMAINS="study-task-app-fc5bab4787a9.herokuapp.com" -a your-app-name
    ```

3. **セッション設定を確認**

    ```bash
    heroku config:set SESSION_DRIVER=cookie -a your-app-name
    heroku config:set SESSION_SECURE_COOKIE=true -a your-app-name
    heroku config:set SESSION_DOMAIN=null -a your-app-name
    ```

4. **APP_URL を確認**

    ```bash
    heroku config:set APP_URL="https://study-task-app-fc5bab4787a9.herokuapp.com" -a your-app-name
    ```

5. **アプリを再起動**
    ```bash
    heroku restart -a your-app-name
    ```

**確認方法：**
ブラウザの開発者ツールで、`/api/user` リクエストに `laravel_session` クッキーが含まれているか確認してください。

### ⚠️ エラー: 422 Unprocessable Entity on /login

ログイン時に 422 エラーが発生する場合、以下の原因が考えられます：

**原因：**

-   データベースにユーザーが存在しない（シーダーが実行されていない）
-   リクエストのバリデーションエラー

**解決方法：**

1. **シーダーが実行されているか確認**

    ```bash
    heroku run php artisan tinker -a your-app-name
    ```

    ターミナルで以下を実行：

    ```php
    User::count();
    ```

    0 が返された場合、シーダーが実行されていません。

2. **シーダーを実行**

    ```bash
    heroku run php artisan db:seed -a your-app-name
    ```

3. **テストユーザーでログイン**
    - メール: `owner@example.com`
    - パスワード: `password`

## 🎯 トラブルシューティング

### ⚠️ エラー: Access denied (using password: NO)

以下のエラーが発生している場合：

```
SQLSTATE[HY000] [1045] Access denied for user 'xxx'@'xxx' (using password: NO)
```

**原因：** `DB_PASSWORD` が設定されていない、または空文字列になっています。

**解決方法：**

#### 1. Heroku Dashboard でデータベース接続情報を確認

1. [Heroku Dashboard](https://dashboard.heroku.com/) にアクセス
2. アプリを選択
3. **Resources** タブを確認
4. 使用しているデータベースアドオン（JawsDB MySQL、ClearDB MySQL、Heroku Postgres など）を確認

#### 2. JawsDB MySQL / ClearDB MySQL の場合

**方法 A: Heroku CLI で接続情報を確認**

```bash
# データベースアドオンの設定を確認
heroku config -a your-app-name | grep -i jaw
# または
heroku config -a your-app-name | grep -i clear
```

**方法 B: Heroku Dashboard で確認**

1. **Resources** タブでデータベースアドオンをクリック
2. 接続情報（Host、Database、Username、Password）をコピー
3. 以下の環境変数を設定：

```bash
heroku config:set DB_CONNECTION=mysql -a your-app-name
heroku config:set DB_HOST="接続情報のHost" -a your-app-name
heroku config:set DB_PORT=3306 -a your-app-name
heroku config:set DB_DATABASE="接続情報のDatabase" -a your-app-name
heroku config:set DB_USERNAME="接続情報のUsername" -a your-app-name
heroku config:set DB_PASSWORD="接続情報のPassword" -a your-app-name
```

**⚠️ 重要：** `DB_PASSWORD` に特殊文字が含まれている場合は、引用符で囲んでください：

```bash
heroku config:set DB_PASSWORD="your-password-with-special-chars" -a your-app-name
```

#### 3. Heroku Postgres の場合

Heroku Postgres を使用している場合、`DATABASE_URL` が自動的に設定されます。Laravel は `DATABASE_URL` を自動的に解析しますが、明示的に設定する場合は：

```bash
# DATABASE_URL が既に設定されているか確認
heroku config:get DATABASE_URL -a your-app-name

# DATABASE_URL から接続情報を抽出して個別に設定（必要に応じて）
heroku config:set DB_CONNECTION=pgsql -a your-app-name
```

#### 4. 設定後の確認

```bash
# すべての環境変数を確認
heroku config -a your-app-name

# DB_PASSWORD が正しく設定されているか確認
heroku config:get DB_PASSWORD -a your-app-name
```

#### 5. アプリを再起動

```bash
heroku restart -a your-app-name
```

#### 6. マイグレーションとシーダーを実行

```bash
# マイグレーションを実行
heroku run php artisan migrate -a your-app-name

# シーダーを実行（テストユーザーとサンプルデータを作成）
heroku run php artisan db:seed -a your-app-name
```

**シーダーで作成されるテストユーザー：**

-   `owner@example.com` / パスワード: `password`
-   `admin@example.com` / パスワード: `password`
-   `member@example.com` / パスワード: `password`

### APP_KEY が既に存在する場合

ローカルの `.env` ファイルから既存の `APP_KEY` を確認：

```bash
grep "^APP_KEY=" .env
```

その値をそのまま Heroku の Config Vars に設定してください。

### 新しい APP_KEY を生成したい場合

```bash
# ローカルで新しいキーを生成して表示
php artisan key:generate --show

# その値を Heroku に設定
heroku config:set APP_KEY="生成された値" -a your-app-name
```

## 📚 参考リンク

-   [Heroku Config Vars](https://devcenter.heroku.com/articles/config-vars)
-   [Laravel Deployment on Heroku](https://laravel.com/docs/deployment#heroku)
