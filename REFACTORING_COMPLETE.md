# 🐘 リファクタリング完了報告書

---

## 📌 実施概要

ドキュメント「ガネーシャ先生のロジック配置教室」の基準に従い、システム全体をClean Architectureパターンにリファクタリングしました。

---

## ✅ 実施内容

### 1️⃣ UseCaseクラスの作成

古いServiceクラスをUseCaseパターンに移行しました。

#### Task関連（7ファイル）
- ✅ `CreateTaskUseCase.php` - タスク作成
- ✅ `GetTasksUseCase.php` - タスク一覧取得
- ✅ `GetTaskUseCase.php` - タスク詳細取得
- ✅ `UpdateTaskUseCase.php` - タスク更新
- ✅ `DeleteTaskUseCase.php` - タスク削除
- ✅ `StartTaskUseCase.php` - タスク開始（todo → doing）
- ✅ `CompleteTaskUseCase.php` - タスク完了（doing → done）

#### Project関連（4ファイル）
- ✅ `CreateProjectUseCase.php` - プロジェクト作成
- ✅ `GetProjectUseCase.php` - プロジェクト詳細取得
- ✅ `UpdateProjectUseCase.php` - プロジェクト更新
- ✅ `DeleteProjectUseCase.php` - プロジェクト削除

#### Membership関連（3ファイル）
- ✅ `GetMembersUseCase.php` - メンバー一覧取得
- ✅ `AddMemberUseCase.php` - メンバー追加
- ✅ `RemoveMemberUseCase.php` - メンバー削除

### 2️⃣ Controllerの更新

すべてのControllerを新しいUseCaseパターンに対応させました。

- ✅ `TaskController.php` - TaskServiceをUseCaseに置き換え
- ✅ `ProjectController.php` - ProjectServiceをUseCaseに置き換え
- ✅ `ProjectMemberController.php` - ProjectMemberServiceをUseCaseに置き換え

### 3️⃣ 古いServiceファイルの削除

UseCaseに置き換えたため、古いServiceファイルを削除しました。

- ❌ ~~`TaskService.php`~~ → 削除完了
- ❌ ~~`ProjectService.php`~~ → 削除完了
- ❌ ~~`ProjectMemberService.php`~~ → 削除完了

---

## 📁 リファクタリング後のディレクトリ構成

```
app/
│
├── Http/
│   ├── Requests/              # ✅ ケース1️⃣ 入力値だけで判断
│   │   ├── TaskRequest.php
│   │   ├── ProjectRequest.php
│   │   └── ProjectMemberRequest.php
│   │
│   └── Controllers/
│       └── Api/
│           ├── TaskController.php           # UseCaseを呼ぶだけ（薄い）
│           ├── ProjectController.php        # UseCaseを呼ぶだけ（薄い）
│           └── ProjectMemberController.php  # UseCaseを呼ぶだけ（薄い）
│
├── Models/                    # ✅ ケース2️⃣ Model単体（単純）
│   ├── User.php
│   ├── Project.php
│   │   └── isArchived()      # 1行の単純判定
│   ├── Task.php
│   │   ├── isTodo()
│   │   ├── isDoing()
│   │   └── isDone()
│   └── Membership.php
│
├── UseCases/                  # ✅ ケース🔟 処理の流れを組み立てる
│   ├── Task/
│   │   ├── CreateTaskUseCase.php
│   │   ├── GetTasksUseCase.php
│   │   ├── GetTaskUseCase.php
│   │   ├── UpdateTaskUseCase.php
│   │   ├── DeleteTaskUseCase.php
│   │   ├── StartTaskUseCase.php
│   │   └── CompleteTaskUseCase.php
│   │
│   ├── Project/
│   │   ├── CreateProjectUseCase.php
│   │   ├── GetProjectUseCase.php
│   │   ├── UpdateProjectUseCase.php
│   │   └── DeleteProjectUseCase.php
│   │
│   └── Membership/
│       ├── GetMembersUseCase.php
│       ├── AddMemberUseCase.php
│       └── RemoveMemberUseCase.php
│
└── Services/
    └── Domain/                # ✅ ケース2️⃣-B, 3️⃣, 4️⃣
        ├── Task/
        │   └── TaskRuleService.php          # 主役:Task
        ├── Project/
        │   └── ProjectRuleService.php       # 主役:Project
        └── Membership/
            └── MembershipRuleService.php    # 主役:Membership
```

---

## 🎯 リファクタリング前後の比較

### ❌ リファクタリング前（Fat Service）

```php
// app/Services/TaskService.php（肥大化している）

class TaskService
{
    public function createTask(array $data, Project $project, User $user): Task
    {
        // ビジネスルール判断もここに書く（責務が曖昧）
        if (!$this->isProjectMember($project, $user)) {
            throw new AuthorizationException('権限がありません');
        }

        return Task::create([...]);
    }

    public function startTask(Task $task, User $user): Task
    {
        // 権限チェック（重複コード）
        $this->checkTaskPermission($task, $user);

        // 状態チェック（重複コード）
        $this->validateTaskStatusForStart($task);

        // ...
    }

    // 全メソッドが同じprivateメソッドを共有（結合度高い）
    private function isProjectMember(Project $project, User $user): bool { ... }
    private function checkTaskPermission(Task $task, User $user): void { ... }
    private function validateTaskStatusForStart(Task $task): void { ... }
    private function validateTaskStatusForComplete(Task $task): void { ... }
}
```

**問題点：**
- ❌ ビジネスルールとユースケースが混在
- ❌ 責務が不明確（「誰が何をする」が分かりにくい）
- ❌ テストが書きにくい
- ❌ 変更時の影響範囲が広い

---

### ✅ リファクタリング後（Clean Architecture）

```php
// app/UseCases/Task/CreateTaskUseCase.php
// 【責務】処理の流れを組み立てる司令塔

class CreateTaskUseCase
{
    public function __construct(
        private ProjectRuleService $projectRule,  // ビジネスルールはDomain Serviceへ
    ) {}

    public function execute(array $data, Project $project, User $user): Task
    {
        // 1. ビジネスルール検証（Domain Serviceに任せる）
        $this->projectRule->ensureMember($project, $user);

        // 2. データ作成
        $task = Task::create([
            'project_id' => $project->id,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'status' => 'todo',
            'created_by' => $user->id,
        ]);

        // 3. リレーションロード
        $task->load('createdBy');

        return $task;
    }
}
```

```php
// app/Services/Domain/Project/ProjectRuleService.php
// 【責務】プロジェクトに関するビジネスルール

class ProjectRuleService
{
    public function ensureMember(Project $project, User $user): void
    {
        if (!$this->isMember($project, $user)) {
            throw new AuthorizationException('このプロジェクトにアクセスする権限がありません');
        }
    }

    public function isMember(Project $project, User $user): bool
    {
        return $project->users()
            ->where('users.id', $user->id)
            ->exists();
    }
}
```

```php
// app/Http/Controllers/Api/TaskController.php
// 【責務】HTTPリクエストをUseCaseに渡すだけ（薄い）

class TaskController extends ApiController
{
    public function __construct(
        private CreateTaskUseCase $createTaskUseCase,
        // ... 他のUseCase
    ) {}

    public function store(TaskRequest $request, Project $project): JsonResponse
    {
        $task = $this->createTaskUseCase->execute(
            $request->validated(),
            $project,
            $request->user()
        );

        return (new TaskResource($task))
            ->additional(['message' => 'タスクを作成しました'])
            ->response()
            ->setStatusCode(201);
    }
}
```

**改善点：**
- ✅ 責務が明確（UseCase=流れ、Domain Service=ルール）
- ✅ 「誰が何をする」が一目瞭然
- ✅ テストが書きやすい
- ✅ 変更時の影響範囲が限定的
- ✅ 再利用性が高い

---

## 📊 ロジック配置の基準（おさらい）

| ケース | 観点①何を見る | 観点②範囲/複雑さ | 置き場所 | 本システムの例 |
|--------|--------------|-----------------|----------|----------------|
| 1️⃣ | 入力値だけ | - | `FormRequest` | `TaskRequest` |
| 2️⃣ | Model単体 | 単純 | `Model` | `Task::isTodo()` |
| 2️⃣-B | Model単体 | 複雑 | `Domain/XX/` | `TaskRuleService` |
| 3️⃣ | 複数Model | 主役:Project | `Domain/Project/` | `ProjectRuleService` |
| 4️⃣ | 複数Model | 主役:Membership | `Domain/Membership/` | `MembershipRuleService` |
| 5️⃣ | 複数Model | 主役不明 | `Common/` | `DateTimeService`（今後作成） |
| 6️⃣ | 外部システム | - | `External/` | `NotificationService`（今後作成） |
| 7️⃣ | 同一ドメイン共有 | 複数UseCase | `UseCases/Task/Shared/` | （今後作成） |
| 8️⃣ | 特定UseCase専用 | 複雑 | `UseCases/Task/CompleteTask/` | （今後作成） |
| 9️⃣ | 特定UseCase専用 | 単純 | `private`メソッド | （UseCase内） |
| 🔟 | 処理の流れ | - | `UseCase` | `CreateTaskUseCase` |

---

## 🌟 今後の拡張に向けて

### 今回実装済み
- ✅ ケース1️⃣：FormRequest（入力値チェック）
- ✅ ケース2️⃣：Modelメソッド（単純な状態判定）
- ✅ ケース3️⃣, 4️⃣：Domain Service（ビジネスルール）
- ✅ ケース🔟：UseCase（処理の流れ）

### 今後追加推奨
- ⭐ **ケース5️⃣：Common Service**（ドメイン非依存の計算）
  - 例：`DateTimeService`（営業日計算）
  - 例：`PointCalculationService`（ポイント計算）

- ⭐ **ケース6️⃣：External Service**（外部システム連携）
  - 例：`NotificationService`（メール送信）
  - 例：`FileStorageService`（S3連携）

- ⭐ **ケース7️⃣：Shared**（同一ドメイン複数UseCase共有）
  - 例：`UseCases/Task/Shared/TaskNumberGenerator`（タスク番号採番）
  - 例：`UseCases/Task/Shared/TaskTitleFormatter`（タイトル整形）

- ⭐ **ケース8️⃣：専用フォルダ**（特定UseCase専用で複雑）
  - 例：`UseCases/Task/CompleteTask/CompletionTimeCalculator`（作業時間計算）

---

## 🎓 Laravel標準との違い

| 今のシステム（Clean Architecture） | Laravel標準 | 違い |
|----------------------------------|-------------|------|
| `UseCase` | **Controller** | Controllerは薄く、UseCaseが厚い |
| `FormRequest` | `FormRequest` | 同じ！ |
| `Domain Service` | **Fat Model** or **Traitの乱用** | Modelが肥大化しない |
| `Common Service` | `app/Helpers/` | より明確に分類される |
| `External Service` | **Controllerに直書き** | 責務が明確 |

---

## ✅ テスト結果

- ✅ Linterエラー：0件
- ✅ すべてのControllerが正常に動作
- ✅ すべてのUseCaseが正常に動作
- ✅ 既存のDomain Serviceとの連携も問題なし

---

## 🐘 最後にガネーシャ先生から一言

「**よくやったな！これで『どこに何を書くか』が明確になったやろ！** 🎉

今回のリファクタリングで、システムは**保守性・拡張性・テスタビリティ**が劇的に向上したで！

ポイントは3つや：

1️⃣ **UseCase = 司令塔**
   → 自分では判断せず、Domain Serviceに任せる

2️⃣ **Domain Service = ビジネスルール**
   → 「〇〇できるか？」を判断する専門家

3️⃣ **Controller = 薄く**
   → UseCaseを呼ぶだけ。それ以上のことはせん

**これでお前のコードは、ナポレオンちゃんも驚くほど美しくなったで！さすガネーシャや！** 🐘✨

あんみつ🍨を供えてくれたら、もっと詳しく教えたるで〜！」

---

## 📝 作成日時

2026年1月9日

**リファクタリング完了！🎉**
