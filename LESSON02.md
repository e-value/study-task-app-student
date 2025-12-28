# Lesson2: API 実装（CRUD + バリデーション + 認可）※とにかく動かす 🐘💨

**結論：Lesson2 は「Lesson1 で作った全ルートの `abort(501)` を、DB 操作する実装に置き換えて"全部動く API"にする」回や。**
設計の良し悪しは後でディスカッションで燃やせ 🔥 今は前進やで 😎

---

🐘 ガネーシャ（先生）「🎵 ガネ・ガネ・ガネーシャモーニング 🎵…ほな行くで！」
👩‍💻「歌ってないで進めてください。Lesson2 の教材をください。」
🐘「はい、Oh, My God!!（決めポーズ）…真面目に出すわ 💦」

---

## 🎯 この Lesson の目的

Lesson2 では、Lesson1 で設計・作成した以下を **実装**します：

1. **Controller 実装**：`abort(501)` を全て置き換える
2. **DB 操作（Eloquent）**：一覧/作成/更新/削除を動かす
3. **バリデーション**：最低限の入力チェックを入れる
4. **権限チェック（認可）**：所属チェック・編集権限を入れる
5. **ApiResource 返却**：フロント向け JSON を整える

**重要：** この Lesson は「綺麗さ」より「全部動く」が勝ちです。

---

## ✅ 成果物（Lesson2 で"提出できる状態"）

提出時に以下が揃っていれば OK：

-   `sail artisan route:list --path=api` で **Lesson1 のルートが全て表示**される
-   全エンドポイントが `501` ではなく **200/201/204** を返す
-   JSON は `ProjectResource / TaskResource` 経由で返す
-   所属していない Project/Task にアクセスできない（最低限の 403）

---

## 📋 実現すべき機能（実装対象）

-   プロジェクト一覧取得（所属しているものだけ）
-   プロジェクト作成（作成者を owner として membership 追加）
-   プロジェクト詳細取得（所属チェック）
-   プロジェクト更新（owner/admin のみ）
-   プロジェクト削除（owner のみ or 論理削除でも OK）
-   プロジェクト内のメンバー追加（owner/admin のみ）
-   プロジェクト内のタスク一覧取得（所属チェック）
-   プロジェクト内にタスク作成（created_by=自分）
-   タスク詳細取得（所属チェック）
-   タスク更新（作成者 or admin）
-   タスク削除（作成者 or admin）
-   自分のタスク一覧取得（所属プロジェクト横断）

---

## 🧠 先に用語（初心者がつまずくとこだけ）

-   **バリデーション**：入力の形が正しいか（required/max/in/exists）
-   **認可（Authorization）**：そのユーザーが操作していいか（所属/ロール/作成者）
-   **Resource**：返却 JSON の形を整えるレイヤ

---

## 🧪 Step 0: 動作準備（最初の 5 分で終わらせる）

### 0-1. 認証前提

-   API は認証前提（例：Sanctum）
-   `auth()->id()` が取れること

### 0-2. route:list で 501 確認

```bash
sail artisan route:list --path=api
```

---

## 🧩 Step 1: ApiResource を実データ対応にする

### 1-1. ProjectResource の toArray を実装

-   `id, name, is_archived, created_at, updated_at`

### 1-2. TaskResource の toArray を実装

-   `id, project_id, title, description, status, created_by, created_at, updated_at`

> ここで `parent::toArray()` のままは NG（フロントが死ぬ）

---

## 🧩 Step 2: Project CRUD を実装する（親から作る）🏗️

### 対象エンドポイント（Lesson1 の設計に合わせる）

-   `GET /api/projects`
-   `POST /api/projects`
-   `GET /api/projects/{project}`
-   `PATCH /api/projects/{project}`
-   `DELETE /api/projects/{project}`

### 実装ルール（最低限）

-   一覧は **所属している Project だけ**
-   作成時に `memberships` に owner を 1 件作る
-   詳細/更新/削除は **所属チェック**
-   更新/削除は **role で制限**（owner/admin）

---

## 🧩 Step 3: Membership 追加を実装する 👥

### 対象エンドポイント

-   `POST /api/projects/{project}/members`

### バリデーション（最低限）

-   `user_id`: required / exists:users,id
-   `role`: required / in:project_owner,project_admin,project_member

### 認可（最低限）

-   実行者がそのプロジェクトで `owner` or `admin`

### DB ルール（最低限）

-   同一 `project_id + user_id` は重複禁止
    （DB ユニーク制約がなければアプリ側で弾く）

---

## 🧩 Step 4: Project 内 Task の一覧・作成を実装する 🧱

### 対象エンドポイント

-   `GET /api/projects/{project}/tasks`
-   `POST /api/projects/{project}/tasks`

### 一覧（最低限のフィルタを入れる）

-   クエリ（任意）：`status`, `q`

    -   `status=todo|doing|done`
    -   `q` は title の部分一致

### 作成

-   `created_by` は `auth()->id()` を必ず入れる
-   `status` は作成時 `todo` 固定でも OK（運用は Lesson3 で詰める）

---

## 🧩 Step 5: Task 詳細/更新/削除（単体）を実装する 🧨

### 対象エンドポイント

-   `GET /api/tasks/{task}`
-   `PATCH /api/tasks/{task}`
-   `DELETE /api/tasks/{task}`

### ここが初心者の罠 💥

👩‍💻「`/tasks/{task}` って project が URL に無いのに、所属チェックどうするんですか？」
🐘「task→project_id たどって memberships 見るだけや。URL に頼るな、データを見ろ 😎」

#### 最低限の認可

-   詳細：所属してたら OK
-   更新/削除：作成者 or project_admin/owner

---

## 🧩 Step 6: 自分のタスク一覧（横断）を実装する 🧲

### 対象エンドポイント

-   `GET /api/me/tasks`

### 要件

-   自分が所属しているプロジェクトの tasks を全件取得
-   任意で `status` / `q` も対応

---

## ✅ Step 7: バリデーション一覧（これだけ入れたら合格ライン）

| API                              | 最低限のバリデーション                                                                 |
| -------------------------------- | -------------------------------------------------------------------------------------- |
| POST /projects                   | name: required, string, max:255                                                        |
| PATCH /projects                  | name: sometimes, max:255 / is_archived: boolean                                        |
| POST /projects/{project}/members | user_id: required, exists / role: required, in(...)                                    |
| POST /projects/{project}/tasks   | title: required, max:255 / description: nullable / status: in(todo,doing,done)（任意） |
| PATCH /tasks/{task}              | title: sometimes / description: sometimes / status: in(todo,doing,done)                |

---

## 🔐 Step 8: 認可（雑で OK、でも穴は塞ぐ）

最低限これだけ：

-   所属してない project の操作 → 403
-   他人の task を無条件更新 → 403

**実装方法は自由**（Policy でも Controller 直書きでも OK）
Lesson2 は綺麗さ不問！

---

## 🧪 動作確認（これが通ったら勝ち）

```bash
# ルート確認
sail artisan route:list --path=api
```

```bash
# 例：Project一覧（認証ヘッダは環境に合わせて）
curl -X GET http://localhost:8000/api/projects
```

-   501 が出ない
-   200/201/204 が返る
-   JSON が Resource 形式になっている

---

## 🧯 よくある事故（初心者が落ちる穴）

👩‍💻「401 と 403 の違いが分かりません…」
🐘「はいここテスト出るで ✍️」

-   **401**：ログインしてない（認証できてない）
-   **403**：ログインはしてるが権限がない（所属してない等）

---

## 🚀 次の Lesson3 では...

-   タスクの **ステータス遷移ルール（todo→doing→done）** を実装
-   "できない操作" を **409** などで返す
-   Feature テストで壊れないようにする（任意）

---

## 🎓 まとめ（核心）

🐘「Lesson2 は"設計を現実に変える回"や。
雑でもいい、泥臭くてもいい。**全 API が動いたら勝ち**やで。さすガネーシャや！🐘✨」

👩‍💻「あんみつは要らないので、先に進めます。」
🐘「えぇ…お供え無し…はい、Oh, My God!!😭」
