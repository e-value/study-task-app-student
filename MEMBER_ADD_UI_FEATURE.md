# メンバー追加機能UI実装完了 🎉

## 📋 実装概要

プロジェクト詳細画面（`Projects/Show.vue`）の「メンバータブ」にメンバー追加機能のUIを実装しました。

## 🎨 実装した画面・機能

### 1. **メンバータブにメンバー追加ボタン**
- プロジェクト詳細画面の「メンバータブ」に「メンバーを追加」ボタンを配置
- クリックするとメンバー追加フォームが表示される

### 2. **メンバー追加フォーム**
以下のフィールドを持つフォームを実装：

| フィールド | 説明 |
|----------|------|
| **ユーザー選択** | ドロップダウンで追加可能なユーザーを選択 |
| **役割選択** | メンバー / 管理者 / オーナーから選択（デフォルト: メンバー） |

### 3. **スマートなユーザー絞り込み**
- 全ユーザーから**既存メンバーを自動的に除外**
- `availableUsers` computed propertyで実装
- 追加済みユーザーは選択肢に表示されない

### 4. **エラーハンドリング**
- 既存メンバーを追加しようとした場合: 409エラーを表示
- 権限がない場合: 403エラーを表示
- バリデーションエラー: 422エラーを表示

## 🔧 実装ファイル

### 1. バックエンド

#### `app/Http/Controllers/Api/UserController.php` (新規作成)
```php
class UserController extends ApiController
{
    public function index(): AnonymousResourceCollection
    {
        $users = User::orderBy('name')->get();
        return UserResource::collection($users);
    }
}
```

#### `routes/api.php`
```php
// Users
Route::get('/users', [UserController::class, 'index']);
```

### 2. フロントエンド

#### `resources/js/Pages/Projects/Show.vue`

**追加した主要機能：**

1. **状態管理**
```javascript
const addingMember = ref(false);
const showAddMemberForm = ref(false);
const allUsers = ref([]);
const newMember = ref({
  user_id: "",
  role: "project_member",
});
```

2. **利用可能ユーザーの計算**
```javascript
const availableUsers = computed(() => {
  const memberUserIds = members.value.map((m) => m.user_id);
  return allUsers.value.filter((user) => !memberUserIds.includes(user.id));
});
```

3. **メンバー追加関数**
```javascript
const addMember = async () => {
  if (!newMember.value.user_id) {
    alert("ユーザーを選択してください");
    return;
  }

  try {
    addingMember.value = true;
    memberError.value = null;
    const response = await axios.post(
      `/api/projects/${projectId}/members`,
      newMember.value
    );
    members.value.push(response.data.membership);
    newMember.value = { user_id: "", role: "project_member" };
    showAddMemberForm.value = false;
  } catch (err) {
    memberError.value =
      err.response?.data?.message || "メンバーの追加に失敗しました";
  } finally {
    addingMember.value = false;
  }
};
```

4. **ユーザー一覧取得**
```javascript
const fetchUsers = async () => {
  try {
    const response = await axios.get("/api/users");
    allUsers.value = response.data.data || [];
  } catch (err) {
    console.error("Failed to fetch users:", err);
  }
};

onMounted(() => {
  fetchProject();
  fetchUsers(); // 追加
});
```

## 🎨 UI デザイン

### メンバー追加ボタン
- グラデーション背景（青→紫）
- アイコン付き
- ホバーエフェクト

### メンバー追加フォーム
- ガラスモーフィズムデザイン（背景ぼかし）
- 2つのドロップダウン：
  1. **ユーザー選択**: 名前とメールアドレスを表示
  2. **役割選択**: メンバー / 管理者 / オーナー
- ボタン：
  - **メンバーを追加**: グラデーション（青→紫）、ローディングスピナー付き
  - **キャンセル**: 白背景、フォームを閉じる

## 📸 画面構成

```
プロジェクト詳細画面
├── タスクタブ
│   └── (既存機能)
└── メンバータブ
    ├── [メンバーを追加] ボタン
    ├── メンバー追加フォーム（表示/非表示切り替え）
    │   ├── ユーザー選択ドロップダウン
    │   ├── 役割選択ドロップダウン
    │   └── [メンバーを追加] [キャンセル] ボタン
    └── メンバー一覧テーブル
        └── 各メンバーに [削除] ボタン
```

## ✅ テスト結果

### API テスト
| テストケース | 結果 |
|------------|------|
| ユーザー一覧取得 (`GET /api/users`) | ✅ 成功 |
| メンバー追加 (`POST /api/projects/3/members`) | ✅ 成功 |
| 既存メンバー除外 (availableUsers) | ✅ 正常動作 |
| 役割指定でのメンバー追加 | ✅ 成功 |

### 実際のテストデータ
プロジェクト3（旧システム保守）に以下のメンバーを追加：
1. ✅ 山田太郎（project_owner）
2. ✅ ユーザー5（project_member）
3. ✅ 佐藤花子（project_admin）

## 🎯 使い方

### ユーザー視点での操作手順

1. **プロジェクト一覧からプロジェクトを選択**
2. **「メンバー」タブをクリック**
3. **「メンバーを追加」ボタンをクリック**
4. **追加するユーザーを選択**
   - ドロップダウンに既存メンバー以外のユーザーが表示される
5. **役割を選択**（任意、デフォルト: メンバー）
6. **「メンバーを追加」ボタンをクリック**
7. **成功したらメンバー一覧に即座に反映**

キャンセルする場合は「キャンセル」ボタンでフォームを閉じる。

## 🌟 実装のポイント

### 1. リアルタイムフィルタリング
`availableUsers` computed propertyにより、既存メンバーを除外したユーザーリストを動的に生成。

### 2. 楽観的UI更新
メンバー追加成功後、APIレスポンスのデータを即座に `members` 配列に追加。ページ再読み込み不要。

### 3. エラー表示
`memberError` を使って、フォーム上部にエラーメッセージを表示。

### 4. ローディング状態
`addingMember` フラグで、送信中はボタンを無効化しスピナーを表示。

### 5. フォーム初期化
追加成功またはキャンセル時に、フォームをリセット：
```javascript
newMember.value = { user_id: "", role: "project_member" };
showAddMemberForm.value = false;
```

## 🔗 関連API

| エンドポイント | メソッド | 説明 |
|--------------|--------|------|
| `/api/users` | GET | 全ユーザー一覧取得 |
| `/api/projects/{project}/members` | GET | プロジェクトのメンバー一覧 |
| `/api/projects/{project}/members` | POST | メンバー追加 |
| `/api/memberships/{membership}` | DELETE | メンバー削除 |

## 📚 完全な機能一覧（メンバー管理）

### 画面機能
- ✅ メンバー一覧表示
- ✅ メンバー追加フォーム
- ✅ メンバー削除
- ✅ 既存メンバー除外フィルター
- ✅ エラーメッセージ表示

### API機能
- ✅ ユーザー一覧取得
- ✅ メンバー一覧取得
- ✅ メンバー追加（権限チェック付き）
- ✅ メンバー削除（権限チェック、業務制約チェック付き）

## 🎊 まとめ

**プロジェクト詳細画面の「メンバータブ」で、直感的にメンバーを追加できる機能を実装しました！**

- 🎨 美しいUIデザイン（ガラスモーフィズム）
- 🔐 権限チェック（owner/adminのみ追加可能）
- 🎯 スマートなユーザー絞り込み（既存メンバー除外）
- ⚡ リアルタイムUI更新
- 🛡️ エラーハンドリング

すべての機能が正常に動作することをテストで確認済みです！

