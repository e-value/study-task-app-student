# 📘 study-task-app システム機能仕様書

---

## 📌 1. システム概要

### 1.1 システム名

study-task-app（学習用タスク管理アプリケーション）

### 1.2 目的

複数のユーザーがプロジェクトを作成・共有し、プロジェクト内でタスクを管理できるWebアプリケーション

### 1.3 技術スタック

| 分類      | 技術                    | バージョン  |
| ------- | --------------------- | ------ |
| バックエンド  | Laravel               | 12.x   |
| フロントエンド | Vue 3 SPA             | 3.4    |
| 認証      | Laravel Sanctum       | 4.0    |
| データベース  | MySQL                 | 8.4    |
| キャッシュ   | Redis                 | Alpine |
| スタイリング  | Tailwind CSS          | 3.2    |
| ビルドツール  | Vite                  | 6.0    |
| 開発環境    | Docker (Laravel Sail) | -      |

### 1.4 アクセスURL

開発環境: [http://localhost](http://localhost)

---

## 📊 2. データベース設計

### 2.1 ER図（Entity Relationship Diagram）

```
┌─────────────────┐
│     users       │
├─────────────────┤
│ id              │
│ name            │
│ email (unique)  │
│ password        │
│ created_at      │
│ updated_at      │
└────────┬────────┘
         │
         │ 1
         │
         │ N
    ┌────▼─────────────┐
    │  memberships     │
    ├──────────────────┤
    │ id               │
    │ project_id (FK)  │
    │ user_id (FK)     │
    │ role (enum)      │
    │ created_at       │
    │ updated_at       │
    └────┬─────────────┘
         │
         │ N
         │
         │ 1
┌────────▼────────┐         ┌──────────────┐
│    projects     │    1    │    tasks     │
├─────────────────┤◄───N────┤──────────────┤
│ id              │         │ id           │
│ name            │         │ project_id   │
│ is_archived     │         │ title        │
│ created_at      │         │ description  │
│ updated_at      │         │ status       │
└─────────────────┘         │ created_by   │
                            │ created_at   │
                            │ updated_at   │
                            └──────────────┘
```

### 2.2 テーブル定義

#### 2.2.1 users テーブル（ユーザー）

| カラム名                | 型            | 制約                  | 説明           |
| ------------------- | ------------ | ------------------- | ------------ |
| id                  | BIGINT       | PK, AUTO\_INCREMENT | ユーザーID       |
| name                | VARCHAR(255) | NOT NULL            | ユーザー名        |
| email               | VARCHAR(255) | NOT NULL, UNIQUE    | メールアドレス      |
| password            | VARCHAR(255) | NOT NULL            | パスワード（ハッシュ化） |
| email\_verified\_at | TIMESTAMP    | NULLABLE            | メール確認日時      |
| remember\_token     | VARCHAR(100) | NULLABLE            | ログイン継続用トークン  |
| created\_at         | TIMESTAMP    | -                   | 作成日時         |
| updated\_at         | TIMESTAMP    | -                   | 更新日時         |

#### 2.2.2 projects テーブル（プロジェクト）

| カラム名         | 型            | 制約                      | 説明       |
| ------------ | ------------ | ----------------------- | -------- |
| id           | BIGINT       | PK, AUTO\_INCREMENT     | プロジェクトID |
| name         | VARCHAR(255) | NOT NULL                | プロジェクト名  |
| is\_archived | BOOLEAN      | NOT NULL, DEFAULT false | アーカイブフラグ |
| created\_at  | TIMESTAMP    | -                       | 作成日時     |
| updated\_at  | TIMESTAMP    | -                       | 更新日時     |

#### 2.2.3 memberships テーブル（プロジェクトメンバーシップ）

| カラム名        | 型         | 制約                         | 説明        |
| ----------- | --------- | -------------------------- | --------- |
| id          | BIGINT    | PK, AUTO\_INCREMENT        | メンバーシップID |
| project\_id | BIGINT    | FK (projects.id), NOT NULL | プロジェクトID  |
| user\_id    | BIGINT    | FK (users.id), NOT NULL    | ユーザーID    |
| role        | ENUM      | NOT NULL                   | 権限ロール     |
| created\_at | TIMESTAMP | -                          | 作成日時      |
| updated\_at | TIMESTAMP | -                          | 更新日時      |

**制約:**

- UNIQUE(project\_id, user\_id) - 同じプロジェクトに同じユーザーは1回だけ参加可能
- ON DELETE CASCADE - プロジェクトまたはユーザー削除時に連動削除

**roleの値:**

- project\_owner: プロジェクトオーナー（最高権限）
- project\_admin: プロジェクト管理者
- project\_member: プロジェクトメンバー

#### 2.2.4 tasks テーブル（タスク）

| カラム名        | 型            | 制約                         | 説明       |
| ----------- | ------------ | -------------------------- | -------- |
| id          | BIGINT       | PK, AUTO\_INCREMENT        | タスクID    |
| project\_id | BIGINT       | FK (projects.id), NOT NULL | プロジェクトID |
| title       | VARCHAR(255) | NOT NULL                   | タスクタイトル  |
| description | TEXT         | NULLABLE                   | タスク詳細説明  |
| status      | ENUM         | NOT NULL, DEFAULT 'todo'   | ステータス    |
| created\_by | BIGINT       | FK (users.id), NOT NULL    | 作成者ID    |
| created\_at | TIMESTAMP    | -                          | 作成日時     |
| updated\_at | TIMESTAMP    | -                          | 更新日時     |

**制約:**

- ON DELETE CASCADE - プロジェクトまたはユーザー削除時に連動削除
- INDEX(project\_id, created\_by, status) - 検索パフォーマンス向上

**statusの値:**

- todo: 未着手
- doing: 作業中
- done: 完了

---

## 🔐 3. 認証機能

### 3.1 認証方式

Laravel Sanctum を使用したSPA認証 セッションベースの認証（Cookie認証）

### 3.2 認証機能一覧

| 機能        | エンドポイント                       | 説明             |
| --------- | ----------------------------- | -------------- |
| ユーザー登録    | POST /register                | 新規ユーザー登録       |
| ログイン      | POST /login                   | 認証情報でログイン      |
| ログアウト     | POST /logout                  | セッション破棄        |
| パスワードリセット | POST /forgot-password         | パスワードリセットメール送信 |
| メール確認     | GET /verify-email/{id}/{hash} | メールアドレス確認      |

### 3.3 パスワード要件

- 最小8文字
- bcryptでハッシュ化して保存

### 3.4 セキュリティ対策

- CSRF保護（全フォーム）
- XSS対策（自動エスケープ）
- SQLインジェクション対策（Eloquent ORM）
- レート制限（ログイン試行回数制限）
- セキュアセッションクッキー（HttpOnly）

---

## 🎯 4. 機能仕様

### 4.1 プロジェクト管理機能

#### 4.1.1 プロジェクト一覧取得

- API: GET /api/projects
- 認証: 必須
- 説明: 自分が所属している全プロジェクトを取得

**レスポンス:**

```json
{
  "data": [
    {
      "id": 1,
      "name": "プロジェクトA",
      "is_archived": false,
      "members": [
        {
          "id": 1,
          "name": "山田太郎",
          "email": "yamada@example.com",
          "role": "project_owner"
        }
      ],
      "created_at": "2024-12-21T10:00:00Z",
      "updated_at": "2024-12-21T10:00:00Z"
    }
  ]
}
```

#### 4.1.2 プロジェクト作成

- API: POST /api/projects
- 認証: 必須

**リクエスト:**

```json
{
  "name": "新規プロジェクト",
  "is_archived": false
}
```

**バリデーション:**

- name: 必須、文字列、最大255文字
- is\_archived: 真偽値（省略可）

**処理:**

- プロジェクトを作成
- 作成者を自動的にproject\_ownerとして追加

**レスポンス: 201 Created**

```json
{
  "data": {
    "id": 2,
    "name": "新規プロジェクト",
    "is_archived": false,
    "...": "..."
  },
  "message": "プロジェクトを作成しました"
}
```

#### 4.1.3 プロジェクト詳細取得

- API: GET /api/projects/{project}
- 認証: 必須
- 権限チェック: プロジェクトメンバーであること

**レスポンス:**

```json
{
  "data": {
    "id": 1,
    "name": "プロジェクトA",
    "is_archived": false,
    "members": ["..."],
    "tasks": ["..."],
    "created_at": "2024-12-21T10:00:00Z",
    "updated_at": "2024-12-21T10:00:00Z"
  }
}
```

#### 4.1.4 プロジェクト更新

- API: PUT /api/projects/{project}
- 認証: 必須
- 権限チェック: project\_owner または project\_admin

**リクエスト:**

```json
{
  "name": "更新後のプロジェクト名",
  "is_archived": true
}
```

**バリデーション:**

- name: 文字列、最大255文字（省略可）
- is\_archived: 真偽値（省略可）

**レスポンス:**

```json
{
  "data": {"...": "..."},
  "message": "プロジェクトを更新しました"
}
```

#### 4.1.5 プロジェクト削除

- API: DELETE /api/projects/{project}
- 認証: 必須
- 権限チェック: project\_owner のみ

**処理:**

- プロジェクトを削除
- 関連するmembershipsとtasksも連動削除（CASCADE）

**レスポンス:**

```json
{
  "message": "プロジェクトを削除しました"
}
```

### 4.2 タスク管理機能

#### 4.2.1 タスク一覧取得

- API: GET /api/projects/{project}/tasks
- 認証: 必須
- 権限チェック: プロジェクトメンバーであること

**レスポンス:**

```json
{
  "data": [
    {
      "id": 1,
      "project_id": 1,
      "title": "タスク1",
      "description": "タスク詳細",
      "status": "todo",
      "created_by": {
        "id": 1,
        "name": "山田太郎",
        "email": "yamada@example.com"
      },
      "created_at": "2024-12-21T10:00:00Z",
      "updated_at": "2024-12-21T10:00:00Z"
    }
  ]
}
```

#### 4.2.2 タスク作成

- API: POST /api/projects/{project}/tasks
- 認証: 必須
- 権限チェック: プロジェクトメンバーであること

**リクエスト:**

```json
{
  "title": "新しいタスク",
  "description": "タスクの詳細説明"
}
```

**バリデーション:**

- title: 必須、文字列、最大255文字
- description: 文字列（省略可）

**処理:**

- タスクを作成
- statusは自動的にtodoに設定
- created\_byは認証ユーザーのIDを設定

**レスポンス: 201 Created**

#### 4.2.3 タスク詳細取得

- API: GET /api/tasks/{task}
- 認証: 必須
- 権限チェック: タスクが属するプロジェクトのメンバーであること

#### 4.2.4 タスク更新

- API: PUT /api/tasks/{task}
- 認証: 必須
- 権限チェック: タスクが属するプロジェクトのメンバーであること

**リクエスト:**

```json
{
  "title": "更新後のタイトル",
  "description": "更新後の説明",
  "status": "doing"
}
```

**バリデーション:**

- title: 文字列、最大255文字（省略可）
- description: 文字列（省略可）
- status: todo, doing, done のいずれか（省略可）

#### 4.2.5 タスク削除

- API: DELETE /api/tasks/{task}
- 認証: 必須
- 権限チェック: タスクが属するプロジェクトのメンバーであること

#### 4.2.6 タスク開始（ステータス変更）

- API: POST /api/tasks/{task}/start
- 認証: 必須
- 権限チェック: タスクが属するプロジェクトのメンバーであること

**処理:**

- statusをtodoからdoingに変更
- todo以外の場合は409エラー

**レスポンス:**

```json
{
  "data": {
    "id": 1,
    "status": "doing",
    "...": "..."
  }
}
```

**エラーレスポンス（409 Conflict）:**

```json
{
  "message": "未着手のタスクのみ開始できます"
}
```

#### 4.2.7 タスク完了（ステータス変更）

- API: POST /api/tasks/{task}/complete
- 認証: 必須
- 権限チェック: タスクが属するプロジェクトのメンバーであること

**処理:**

- statusをdoingからdoneに変更
- doing以外の場合は409エラー

**エラーレスポンス（409 Conflict）:**

```json
{
  "message": "作業中のタスクのみ完了できます"
}
```

### 4.3 メンバーシップ管理機能

#### 4.3.1 メンバー一覧取得

- API: GET /api/projects/{project}/members
- 認証: 必須
- 権限チェック: プロジェクトメンバーであること

**レスポンス:**

```json
{
  "data": [
    {
      "id": 1,
      "project_id": 1,
      "user": {
        "id": 1,
        "name": "山田太郎",
        "email": "yamada@example.com"
      },
      "role": "project_owner",
      "created_at": "2024-12-21T10:00:00Z",
      "updated_at": "2024-12-21T10:00:00Z"
    }
  ]
}
```

#### 4.3.2 メンバー追加

- API: POST /api/projects/{project}/members
- 認証: 必須
- 権限チェック: project\_owner または project\_admin

**リクエスト:**

```json
{
  "user_id": 2,
  "role": "project_member"
}
```

**バリデーション:**

- user\_id: 必須、usersテーブルに存在すること
- role: project\_owner, project\_admin, project\_member のいずれか（省略可、デフォルト: project\_member）

**処理:**

- プロジェクトに新しいメンバーを追加
- 既にメンバーの場合は409エラー
- 自分自身を追加しようとした場合は409エラー
- roleが指定されない場合は自動的にproject\_memberに設定

**レスポンス: 201 Created**

```json
{
  "message": "メンバーを追加しました",
  "membership": {
    "id": 7,
    "project_id": 1,
    "user_id": 2,
    "role": "project_member",
    "user": {
      "id": 2,
      "name": "山田花子",
      "email": "hanako@example.com"
    },
    "created_at": "2024-12-21T10:00:00Z",
    "updated_at": "2024-12-21T10:00:00Z"
  }
}
```

**エラーレスポンス（403 Forbidden）:**

```json
{
  "message": "メンバーを追加する権限がありません（オーナーまたは管理者のみ）"
}
```

**エラーレスポンス（409 Conflict）:**

```json
{
  "message": "このユーザーは既にプロジェクトのメンバーです"
}
```

```json
{
  "message": "あなたは既にこのプロジェクトのメンバーです"
}
```

#### 4.3.3 メンバー削除

- API: DELETE /api/projects/{project}/members/{user}
- 認証: 必須
- パスパラメータ:
  - project: プロジェクトID
  - user: 削除するユーザーID

**権限チェック:**

- project\_owner または project\_adminのみ実行可能

**ビジネスルール:**

- 最後のオーナー保護: プロジェクトに最低1人のオーナーが必要
- オーナーが1人しかいない場合は削除不可（409エラー）
- 未完了タスク保護: 削除対象ユーザーに未完了タスクがある場合は削除不可
- statusがtodoまたはdoingのタスクを持つ場合は削除不可（409エラー）

**エラーレスポンス例:**

```json
{ "message": "Cannot delete the last owner of the project." }
```

```json
{ "message": "Cannot delete member with incomplete tasks (todo or doing)." }
```

- API: DELETE /api/memberships/{membership}
- 認証: 必須

**権限チェック:**

- project\_owner または project\_adminのみ実行可能

**ビジネスルール:**

- 最後のオーナー保護: プロジェクトに最低1人のオーナーが必要
- オーナーが1人しかいない場合は削除不可（409エラー）
- 未完了タスク保護: 削除対象ユーザーに未完了タスクがある場合は削除不可
- statusがtodoまたはdoingのタスクを持つ場合は削除不可（409エラー）

**エラーレスポンス例:**

```json
{ "message": "Cannot delete the last owner of the project." }
```

```json
{ "message": "Cannot delete member with incomplete tasks (todo or doing)." }
```

---

## 🔑 5. 権限設計

### 5.1 ロール定義

| ロール             | 権限                              |
| --------------- | ------------------------------- |
| project\_owner  | プロジェクト削除、全メンバー管理、プロジェクト編集、タスク管理 |
| project\_admin  | メンバー管理、プロジェクト編集、タスク管理           |
| project\_member | タスク管理のみ                         |

### 5.2 機能別権限マトリクス

| 機能       | owner | admin | member |
| -------- | ----- | ----- | ------ |
| プロジェクト閲覧 | ✅     | ✅     | ✅      |
| プロジェクト編集 | ✅     | ✅     | ❌      |
| プロジェクト削除 | ✅     | ❌     | ❌      |
| メンバー閲覧   | ✅     | ✅     | ✅      |
| メンバー削除   | ✅     | ✅     | ❌      |
| タスク閲覧    | ✅     | ✅     | ✅      |
| タスク作成    | ✅     | ✅     | ✅      |
| タスク編集    | ✅     | ✅     | ✅      |
| タスク削除    | ✅     | ✅     | ✅      |

---

## 📡 6. API仕様まとめ

### 6.1 認証API

| メソッド | エンドポイント   | 説明           |
| ---- | --------- | ------------ |
| GET  | /api/user | 認証済みユーザー情報取得 |

### 6.2 プロジェクトAPI

| メソッド   | エンドポイント                 | 説明       | 権限          |
| ------ | ----------------------- | -------- | ----------- |
| GET    | /api/projects           | プロジェクト一覧 | メンバー        |
| POST   | /api/projects           | プロジェクト作成 | 認証済み        |
| GET    | /api/projects/{project} | プロジェクト詳細 | メンバー        |
| PUT    | /api/projects/{project} | プロジェクト更新 | owner/admin |
| DELETE | /api/projects/{project} | プロジェクト削除 | owner       |

### 6.3 タスクAPI

| メソッド   | エンドポイント                       | 説明    | 権限   |
| ------ | ----------------------------- | ----- | ---- |
| GET    | /api/projects/{project}/tasks | タスク一覧 | メンバー |
| POST   | /api/projects/{project}/tasks | タスク作成 | メンバー |
| GET    | /api/tasks/{task}             | タスク詳細 | メンバー |
| PUT    | /api/tasks/{task}             | タスク更新 | メンバー |
| DELETE | /api/tasks/{task}             | タスク削除 | メンバー |
| POST   | /api/tasks/{task}/start       | タスク開始 | メンバー |
| POST   | /api/tasks/{task}/complete    | タスク完了 | メンバー |

### 6.4 メンバーシップAPI

| メソッド   | エンドポイント                                | 説明     | 権限          |
| ------ | -------------------------------------- | ------ | ----------- |
| GET    | /api/projects/{project}/members        | メンバー一覧 | メンバー        |
| POST   | /api/projects/{project}/members        | メンバー追加 | owner/admin |
| DELETE | /api/projects/{project}/members/{user} | メンバー削除 | owner/admin |

\------ | ------------------------------- | ------ | ----------- | | GET    | /api/projects/{project}/members | メンバー一覧 | メンバー        | | DELETE | /api/memberships/{membership}   | メンバー削除 | owner/admin |

---

## 🎨 7. フロントエンド構成

### 7.1 主要ページ

| ページ      | パス                  | 説明       | 認証 |
| -------- | ------------------- | -------- | -- |
| ウェルカム    | /                   | トップページ   | 不要 |
| ダッシュボード  | /dashboard          | ダッシュボード  | 必須 |
| ログイン     | /login              | ログインページ  | 不要 |
| ユーザー登録   | /register           | 新規登録ページ  | 不要 |
| プロジェクト一覧 | /projects           | プロジェクト一覧 | 必須 |
| プロジェクト作成 | /projects/create    | プロジェクト作成 | 必須 |
| プロジェクト詳細 | /projects/{id}      | プロジェクト詳細 | 必須 |
| プロジェクト編集 | /projects/{id}/edit | プロジェクト編集 | 必須 |
| タスク詳細    | /tasks/{id}         | タスク詳細    | 必須 |

### 7.2 コンポーネント構成

```
resources/js/
├── Components/          # 再利用可能なコンポーネント
│   ├── ApplicationLogo.vue
│   ├── Checkbox.vue
│   ├── DangerButton.vue
│   ├── Dropdown.vue
│   ├── InputError.vue
│   ├── InputLabel.vue
│   ├── Modal.vue
│   ├── NavLink.vue
│   ├── PrimaryButton.vue
│   ├── ResponsiveNavLink.vue
│   ├── SecondaryButton.vue
│   ├── TextInput.vue
│   └── Textarea.vue
├── Layouts/            # レイアウト
│   ├── AuthenticatedLayout.vue   # 認証済みユーザー用
│   └── GuestLayout.vue           # ゲストユーザー用
└── Pages/              # ページコンポーネント
    ├── Auth/           # 認証関連
    ├── Dashboard.vue   # ダッシュボード
    ├── Projects/       # プロジェクト関連
    │   ├── Index.vue
    │   ├── Create.vue
    │   ├── Edit.vue
    │   └── Show.vue
    └── Tasks/          # タスク関連
        └── Show.vue
```

---

## 🔄 8. データフロー

### 8.1 プロジェクト作成フロー

1. ユーザーが /projects/create にアクセス
2. Create.vue が表示
3. フォーム入力・送信
4. POST /api/projects
5. ProjectController\@store が処理
   - プロジェクト作成
   - 作成者をproject\_ownerとして登録
6. レスポンス返却（201 Created）
7. フロントエンドで /projects にリダイレクト

### 8.2 タスクステータス変更フロー

1. ユーザーがタスク詳細画面で「開始」ボタンクリック
2. POST /api/tasks/{task}/start
3. TaskController\@start が処理
   - メンバーシップチェック
   - ステータスチェック（todoのみ許可）
   - status を doing に更新
4. レスポンス返却
5. フロントエンド画面更新

---

## 🛡️ 9. エラーハンドリング

### 9.1 HTTPステータスコード

| コード | 意味                    | 使用例        |
| --- | --------------------- | ---------- |
| 200 | OK                    | 正常レスポンス    |
| 201 | Created               | リソース作成成功   |
| 400 | Bad Request           | 不正なリクエスト   |
| 401 | Unauthorized          | 認証エラー      |
| 403 | Forbidden             | 権限不足       |
| 404 | Not Found             | リソース未存在    |
| 409 | Conflict              | ビジネスルール違反  |
| 422 | Unprocessable Entity  | バリデーションエラー |
| 500 | Internal Server Error | サーバーエラー    |

### 9.2 エラーレスポンス形式

**バリデーションエラー（422）:**

```json
{
  "message": "バリデーションエラー",
  "errors": {
    "name": ["プロジェクト名は必須です"],
    "title": ["タイトルは255文字以内で入力してください"]
  }
}
```

**権限エラー（403）:**

```json
{
  "message": "このプロジェクトにアクセスする権限がありません"
}
```

**ビジネスルール違反（409）:**

```json
{
  "message": "未着手のタスクのみ開始できます"
}
```

---

## 🚀 10. 開発・デプロイ

### 10.1 環境構築

```bash
# リポジトリクローン

git clone <repository-url>

study-task-app

cd study-task-app

# 環境変数設定

cp .env.example .env

# 依存関係インストール（初回のみ）

docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php85-composer:latest \
    composer install --ignore-platform-reqs

# コンテナ起動

sail up -d

# アプリケーションキー生成

sail artisan key:generate

# マイグレーション実行

sail artisan migrate

# フロントエンド開発サーバー起動

sail npm install

sail npm run dev
```

### 10.2 使用ポート

| サービス       | ポート  |
| ---------- | ---- |
| Laravel    | 80   |
| MySQL      | 3306 |
| Redis      | 6379 |
| Vite (HMR) | 5173 |

---

## 📋 11. テスト

### 11.1 テストコマンド

```bash
# 全テスト実行
sail artisan test

# 認証テストのみ
sail artisan test --filter=Auth

# カバレッジ付き
sail artisan test --coverage
```

### 11.2 テストカバレッジ

- 認証機能（登録、ログイン、パスワードリセットなど）
- プロフィール管理

---

## 🎯 12. 今後の拡張予定

-

---

## 📞 13. 問い合わせ

質問や提案がある場合は、GitHubのIssueを作成してください。

---

最終更新日: 2024年12月22日\
バージョン: 1.0.0\
ライセンス: MIT

この仕様書は、他の開発者がシステムを理解し、機能追加や保守作業を行うための包括的なドキュメントです。新しいメンバーがジョインした際は、この仕様書を読むことでシステム全体の理解が可能になります。

