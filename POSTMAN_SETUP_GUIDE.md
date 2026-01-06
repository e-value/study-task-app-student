# 📮 Postman 導入＆API テスト完全ガイド

## 📋 目次

0. [事前準備（開発環境のセットアップ）](#0-事前準備開発環境のセットアップ)
1. [Postman のインストール](#1-postmanのインストール)
2. [環境変数の設定（固定トークン）](#2-環境変数の設定固定トークン)
3. [API リクエストの作成と実行](#3-apiリクエストの作成と実行)
4. [よく使うエンドポイント一覧](#4-よく使うエンドポイント一覧)
5. [トラブルシューティング](#5-トラブルシューティング)

---

## 0. 事前準備（開発環境のセットアップ）

Postman でテストする前に、Laravel アプリケーションとデータベースをセットアップしてください。

### ステップ 0-1: Laravel サーバーを起動

```bash
sail up -d
```

> **📝 Note**: コンテナの起動には少し時間がかかります（初回は特に）

### ステップ 0-2: データベースとテストデータを作成

```bash
sail artisan migrate:fresh --seed
```

実行すると、以下のように**固定トークン**が表示されます：

```
========================================
🔑 Postmanテスト用トークン（完全固定）
========================================

👤 オーナー (owner@example.com):
1|postman-owner-test-token-abc123def456ghi789jkl012mno345pqr678stu901vwx234

👤 管理者 (admin@example.com):
2|postman-admin-test-token-xyz789abc012def345ghi678jkl901mno234pqr567stu890

👤 メンバー (member@example.com):
3|postman-member-test-token-qwe456rty789uio012asd345fgh678jkl901zxc234vbn567

========================================
📝 Postmanの設定方法:
1. 環境変数に「token」を作成
2. 上記のトークンをコピーして設定（一度だけ！）
3. Authorization > Bearer Token > {{token}}

💡 このトークンは完全固定です
   何度 migrate:fresh --seed しても同じトークンです
========================================
```

### ステップ 0-3: オーナー用トークンをコピー

表示された**オーナー用トークン**をコピーしてください：

```
1|postman-owner-test-token-abc123def456ghi789jkl012mno345pqr678stu901vwx234
```

このトークンは次のステップ（環境変数の設定）で使用します。

✅ **これで API テストの準備が完了しました！**

---

## 1. Postman のセットアップ

### 推奨：Postman Web アプリを使用（インストール不要）

**Postman Web アプリ**を使用すれば、アプリのインストールなしですぐに API テストを開始できます。

### ステップ 1-1: Postman Web アプリにアクセス

1. ブラウザで以下の URL にアクセス: https://identity.getpostman.com/signup
2. アカウントを作成（または既存アカウントでログイン）
3. ログイン後、自動的に Postman Web アプリが起動します

> **💡 メリット**:
>
> -   ✅ インストール不要、ブラウザだけで使える
> -   ✅ どのパソコンからでもアクセス可能
> -   ✅ 設定が自動的にクラウドに保存される

✅ **これで Postman が使える状態です**

---

### オプション：デスクトップアプリを使用したい場合

> **📚 詳しいインストール方法はこちら**  
> Postman の詳細なダウンロード・インストール手順は以下の記事を参考にしてください：  
> [2026 最新：Postman をダウンロードしてインストールする](https://apidog.com/jp/blog/download-install-postman-jp/)

**手順**:

1. 公式サイトにアクセス: https://www.postman.com/downloads/
2. お使いの OS（Mac / Windows / Linux）に合わせてダウンロード
3. インストーラーを実行してインストール
4. アプリを起動
5. アカウント作成を求められた場合：
    - **「Skip and go to the app」**をクリック（アカウントなしでも使用可能）
    - または、アカウントを作成してログイン（推奨：設定が保存されます）

---

## 2. 環境変数の設定（固定トークン）

### ステップ 2-1: 新しい環境を作成

1. Postman 画面の**左側サイドバー**にある「**Environments**」をクリック
2. 右上の「**+**」ボタン（または「**Create Environment**」）をクリック
3. 環境名を入力: `Study Task App Local`
4. 「**Save**」をクリック

### ステップ 2-2: 固定トークンを設定

環境変数を以下のように追加します：

| VARIABLE | INITIAL VALUE | CURRENT VALUE                                                              |
| -------- | ------------- | -------------------------------------------------------------------------- | --- | -------------------------------------------------------------------------- |
| `token`  | `1            | postman-owner-test-token-abc123def456ghi789jkl012mno345pqr678stu901vwx234` | `1  | postman-owner-test-token-abc123def456ghi789jkl012mno345pqr678stu901vwx234` |

**入力手順：**

1. 「**VARIABLE**」列に `token` と入力
2. 「**INITIAL VALUE**」列に以下をコピペ：
    ```
    1|postman-owner-test-token-abc123def456ghi789jkl012mno345pqr678stu901vwx234
    ```
3. 「**CURRENT VALUE**」列にも同じ値をコピペ
4. 右上の「**Save**」ボタンをクリック

### ステップ 2-3: 環境を選択

1. Postman 画面**右上**のドロップダウン（「**No Environment**」と表示されている部分）をクリック
2. 「**Study Task App Local**」を選択

✅ **これで認証トークンの設定が完了しました**

---

## 3. API リクエストの作成と実行

### ステップ 3-1: 新しいリクエストを作成

1. Postman 画面の左側「**Collections**」の横にある「**+**」ボタンをクリック
2. 「**HTTP**」を選択（または画面中央の「**New**」→「**HTTP Request**」）

### ステップ 3-2: リクエストの設定

#### 3-2-1. メソッドと URL を設定

```
GET   http://localhost/api/projects
```

-   **メソッド**: `GET`（デフォルト）
-   **URL**: `http://localhost/api/projects`

#### 3-2-2. 認証設定（Bearer Token）

1. URL の下にある「**Authorization**」タブをクリック
2. 「**Type**」のドロップダウンで「**Bearer Token**」を選択
3. 「**Token**」フィールドに以下を入力：
    ```
    {{token}}
    ```
    ※ 波括弧 2 つずつで変数を参照します

### ステップ 3-3: リクエストを送信

1. 右上の「**Send**」ボタンをクリック
2. 下部にレスポンスが表示されます

#### ✅ 成功した場合のレスポンス例

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "ECサイトリニューアルプロジェクト",
            "is_archived": false,
            "created_at": "2024-12-21T12:00:00.000000Z",
            "updated_at": "2024-12-21T12:00:00.000000Z"
        },
        {
            "id": 2,
            "name": "新規モバイルアプリ開発",
            "is_archived": false,
            "created_at": "2024-12-21T12:00:00.000000Z",
            "updated_at": "2024-12-21T12:00:00.000000Z"
        }
    ]
}
```

#### ❌ 失敗した場合（認証エラー）

```json
{
    "message": "Unauthenticated."
}
```

→ [トラブルシューティング](#5-トラブルシューティング)を確認してください

### ステップ 3-4: リクエストを保存（オプション）

1. 「**Save**」ボタンをクリック
2. リクエスト名を入力: `プロジェクト一覧取得`
3. 新しい Collection を作成: `Study Task App API`
4. 「**Save**」をクリック

---

## 4. よく使うエンドポイント一覧

### 📁 プロジェクト関連

#### 1. プロジェクト一覧を取得

```
GET http://localhost/api/projects
Authorization: Bearer {{token}}
```

#### 2. プロジェクト詳細を取得

```
GET http://localhost/api/projects/1
Authorization: Bearer {{token}}
```

#### 3. プロジェクトを作成

```
POST http://localhost/api/projects
Authorization: Bearer {{token}}
Content-Type: application/json

Body (raw, JSON):
{
  "name": "新しいプロジェクト",
  "is_archived": false
}
```

**設定方法**:

1. メソッドを `POST` に変更
2. URL に `http://localhost/api/projects` を入力
3. `Authorization` タブで `Bearer Token` → `{{token}}`
4. `Body` タブをクリック
5. `raw` を選択
6. 右側のドロップダウンで `JSON` を選択
7. 上記の JSON をコピペ

#### 4. プロジェクトを更新

```
PUT http://localhost/api/projects/1
Authorization: Bearer {{token}}
Content-Type: application/json

Body (raw, JSON):
{
  "name": "更新されたプロジェクト名",
  "is_archived": false
}
```

#### 5. プロジェクトを削除

```
DELETE http://localhost/api/projects/1
Authorization: Bearer {{token}}
```

---

### ✅ タスク関連

#### 1. タスク一覧を取得（プロジェクト配下）

```
GET http://localhost/api/projects/1/tasks
Authorization: Bearer {{token}}
```

#### 2. タスク詳細を取得

```
GET http://localhost/api/tasks/1
Authorization: Bearer {{token}}
```

#### 3. タスクを作成

```
POST http://localhost/api/projects/1/tasks
Authorization: Bearer {{token}}
Content-Type: application/json

Body (raw, JSON):
{
  "title": "新しいタスク",
  "description": "タスクの説明",
  "status": "todo"
}
```

**status の値**:

-   `todo`: 未着手
-   `doing`: 作業中
-   `done`: 完了

#### 4. タスクを更新

```
PUT http://localhost/api/tasks/1
Authorization: Bearer {{token}}
Content-Type: application/json

Body (raw, JSON):
{
  "title": "更新されたタスク",
  "description": "更新された説明",
  "status": "doing"
}
```

#### 5. タスクを作業開始

```
POST http://localhost/api/tasks/1/start
Authorization: Bearer {{token}}
```

#### 6. タスクを完了

```
POST http://localhost/api/tasks/1/complete
Authorization: Bearer {{token}}
```

#### 7. タスクを削除

```
DELETE http://localhost/api/tasks/1
Authorization: Bearer {{token}}
```

---

### 👥 ユーザー関連

#### 1. ユーザー一覧を取得

```
GET http://localhost/api/users
Authorization: Bearer {{token}}
```

#### 2. ユーザードロップダウン用一覧を取得

```
GET http://localhost/api/users/dropdown
Authorization: Bearer {{token}}
```

---

### 👨‍👩‍👧‍👦 プロジェクトメンバー関連

#### 1. プロジェクトのメンバー一覧を取得

```
GET http://localhost/api/projects/1/members
Authorization: Bearer {{token}}
```

#### 2. プロジェクトにメンバーを追加

```
POST http://localhost/api/projects/1/members
Authorization: Bearer {{token}}
Content-Type: application/json

Body (raw, JSON):
{
  "user_id": 2,
  "role": "admin"
}
```

**role の値**:

-   `owner`: オーナー
-   `admin`: 管理者
-   `member`: メンバー

#### 3. プロジェクトからメンバーを削除

```
DELETE http://localhost/api/projects/1/members/2
Authorization: Bearer {{token}}
```

---

## 5. トラブルシューティング

### ❌ エラー: "Unauthenticated."

**原因と解決方法**:

#### 1. 環境が選択されていない

-   画面右上のドロップダウンで「**Study Task App Local**」が選択されているか確認

#### 2. トークンが正しく設定されていない

-   画面右上の **👁️（目のアイコン）** をクリック
-   `token` の値が以下と一致しているか確認：
    ```
    1|postman-owner-test-token-abc123def456ghi789jkl012mno345pqr678stu901vwx234
    ```

#### 3. Authorization 設定が間違っている

-   `Authorization` タブで `Bearer Token` が選択されているか確認
-   Token フィールドに `{{token}}` が入力されているか確認（波括弧 2 つずつ）

#### 4. トークンに余計なスペースが入っている

-   環境変数の値をコピーし直して、前後のスペースを削除

---

### ❌ エラー: "Connection refused" または "Could not get any response"

**原因**: Laravel サーバーが起動していない

**解決方法**:

ターミナルで以下を実行：

```bash
sail up -d
```

サーバーが起動したら、もう一度リクエストを送信してください。

**確認方法**:

```bash
sail ps
```

すべてのコンテナが「Up」状態になっているか確認してください。

---

### ❌ エラー: "404 Not Found"

**原因**: URL が間違っている

**チェックポイント**:

-   URL の先頭が `http://localhost/api/` になっているか
-   エンドポイントのスペルが正しいか
-   プロジェクト ID やタスク ID が存在するか

**確認方法**:
まず `GET http://localhost/api/projects` で一覧を取得して、存在する ID を確認してください。

---

### ❌ エラー: "422 Unprocessable Entity"

**原因**: リクエストボディのバリデーションエラー

**解決方法**:
レスポンスの `errors` フィールドを確認して、どのフィールドがエラーになっているか確認してください。

**例**:

```json
{
    "message": "The name field is required.",
    "errors": {
        "name": ["The name field is required."]
    }
}
```

→ `name` フィールドが必須なのに入力されていない

---

### ❌ レスポンスが文字化けする

**解決方法**:

1. `Headers` タブをクリック
2. 以下のヘッダーを追加：
    ```
    Key: Accept
    Value: application/json
    ```

---

## 📚 補足情報

### 複数ユーザーでテストする場合

環境変数に以下を追加：

| VARIABLE       | VALUE |
| -------------- | ----- | --------------------------------------------------------------------------- |
| `token_owner`  | `1    | postman-owner-test-token-abc123def456ghi789jkl012mno345pqr678stu901vwx234`  |
| `token_admin`  | `2    | postman-admin-test-token-xyz789abc012def345ghi678jkl901mno234pqr567stu890`  |
| `token_member` | `3    | postman-member-test-token-qwe456rty789uio012asd345fgh678jkl901zxc234vbn567` |

**使い分け**:

-   オーナー権限でテスト: `{{token_owner}}`
-   管理者権限でテスト: `{{token_admin}}`
-   メンバー権限でテスト: `{{token_member}}`

---

### テストユーザー情報

| 名前     | メールアドレス     | 役割     | トークン           |
| -------- | ------------------ | -------- | ------------------ |
| 山田太郎 | owner@example.com  | オーナー | `{{token_owner}}`  |
| 佐藤花子 | admin@example.com  | 管理者   | `{{token_admin}}`  |
| 鈴木一郎 | member@example.com | メンバー | `{{token_member}}` |

---

### データベースをリセットした場合

データベースをリセットする場合：

```bash
sail artisan migrate:fresh --seed
```

**トークンは変わりません**。同じトークンをそのまま使い続けることができます。

💡 何度リセットしても、常に同じ固定トークンが生成されます。

---

## 🎉 完了！

これで Postman を使って API テストができるようになりました。

質問がある場合は、開発チームまでお問い合わせください。

---

## 📖 参考リンク

-   Postman 公式ドキュメント: https://learning.postman.com/docs/getting-started/introduction/
-   Laravel Sanctum 認証: https://laravel.com/docs/11.x/sanctum
-   プロジェクト README: [README.md](./README.md)
