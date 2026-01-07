# エラーハンドリング最適化完了 ✅

## 📋 概要

Laravel標準例外を活用し、不要なカスタム例外クラスを削除して、よりシンプルで保守しやすいエラーハンドリングに最適化しました。

---

## 🎯 最適化内容

### 1. ❌ 削除したファイル

不要なカスタム例外クラスを削除し、Laravel標準の例外で代替しました。

| 削除ファイル | Laravel標準での代替 |
|------------|------------------|
| `app/Exceptions/ForbiddenException.php` | `Illuminate\Auth\Access\AuthorizationException` |
| `app/Exceptions/NotFoundException.php` | `Illuminate\Database\Eloquent\ModelNotFoundException` |

### 2. ✅ 残したファイル

Laravel標準にない例外のみ、カスタム例外として保持しています。

| 保持ファイル | 理由 |
|------------|------|
| `app/Exceptions/ConflictException.php` | Laravel標準に409エラー用の例外がないため |
| `app/Exceptions/ApiExceptionHandler.php` | グローバルエラーハンドラー（必須） |

---

## 📝 修正内容の詳細

### **app/Exceptions/ApiExceptionHandler.php**

Laravel標準の例外を処理するように変更しました。

```php
<?php

namespace App\Exceptions;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Responses\ApiResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;        // ✅ Laravel標準
use Illuminate\Database\Eloquent\ModelNotFoundException;  // ✅ Laravel標準
use App\Exceptions\ConflictException;                     // カスタム（409用）
use Throwable;

class ApiExceptionHandler
{
    public function handle(Throwable $exception, Request $request): ?JsonResponse
    {
        if (!$request->is('api/*')) {
            return null;
        }

        // Laravel標準の例外を優先的に処理
        return match (true) {
            $exception instanceof NotFoundHttpException => $this->handleNotFound(),
            $exception instanceof ModelNotFoundException => $this->handleNotFound($exception->getMessage()),
            $exception instanceof ValidationException => $this->handleValidation($exception),
            $exception instanceof AuthenticationException => $this->handleAuthentication(),
            $exception instanceof AuthorizationException => $this->handleForbidden($exception),  // ✅
            $exception instanceof ConflictException => $this->handleConflict($exception),
            default => $this->handleServerError($exception),
        };
    }

    private function handleForbidden(AuthorizationException $exception): JsonResponse
    {
        return $this->response->forbidden($exception->getMessage());
    }
}
```

---

### **app/Services/ProjectService.php**

カスタム例外からLaravel標準の`AuthorizationException`に変更しました。

```php
<?php

namespace App\Services;

use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;  // ✅ Laravel標準

class ProjectService
{
    public function getProject(Project $project, User $user): Project
    {
        if (!$this->isProjectMember($project, $user)) {
            // ✅ Laravel標準の例外を使用
            throw new AuthorizationException('このプロジェクトにアクセスする権限がありません');
        }
        
        $project->load(['users', 'tasks.createdBy']);
        return $project;
    }

    public function updateProject(Project $project, array $data, User $user): Project
    {
        if (!$this->isProjectOwnerOrAdmin($project, $user)) {
            throw new AuthorizationException('プロジェクトを更新する権限がありません（オーナー・管理者のみ）');
        }
        
        $project->update($data);
        $project->load(['users', 'tasks.createdBy']);
        return $project;
    }

    public function deleteProject(Project $project, User $user): void
    {
        if (!$this->isProjectOwner($project, $user)) {
            throw new AuthorizationException('プロジェクトを削除する権限がありません（オーナーのみ）');
        }
        
        $project->delete();
    }
}
```

---

### **app/Services/ProjectMemberService.php**

Laravel標準の例外とカスタム例外を適切に使い分けています。

```php
<?php

namespace App\Services;

use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;      // ✅ 権限エラー
use Illuminate\Database\Eloquent\ModelNotFoundException; // ✅ 未検出エラー
use App\Exceptions\ConflictException;                    // ✅ 競合エラー

class ProjectMemberService
{
    public function getMembers(Project $project, User $user)
    {
        if (!$this->isProjectMember($project, $user)) {
            throw new AuthorizationException('このプロジェクトにアクセスする権限がありません');
        }
        
        return $project->users()->withPivot('id', 'role')->get();
    }

    public function removeMember(Project $project, int $userId, User $currentUser): void
    {
        // 権限チェック
        if (!$this->isProjectMember($project, $currentUser)) {
            throw new AuthorizationException('このプロジェクトにアクセスする権限がありません');
        }
        
        if (!$this->isProjectOwnerOrAdmin($project, $currentUser)) {
            throw new AuthorizationException('メンバーを削除する権限がありません（オーナーまたは管理者のみ）');
        }
        
        // 存在チェック
        $targetUser = $project->users()->where('users.id', $userId)->first();
        if (!$targetUser) {
            throw new ModelNotFoundException('指定されたユーザーはこのプロジェクトのメンバーではありません');
        }
        
        // 競合チェック
        $this->checkOwnerMaintenance($project, $targetUser);
        $this->checkIncompleteTasks($project, $userId);
        
        $project->users()->detach($userId);
    }

    private function checkExistingMember(Project $project, int $userId): void
    {
        $existingUser = $project->users()->where('users.id', $userId)->first();
        
        if ($existingUser) {
            // ✅ Laravel標準にないため、カスタム例外を使用
            throw new ConflictException('このユーザーは既にプロジェクトのメンバーです');
        }
    }

    private function checkOwnerMaintenance(Project $project, User $targetUser): void
    {
        if ($targetUser->pivot->role === 'project_owner') {
            $ownerCount = $project->users()->wherePivot('role', 'project_owner')->count();
            
            if ($ownerCount <= 1) {
                throw new ConflictException('プロジェクトの最後のオーナーは削除できません');
            }
        }
    }

    private function checkIncompleteTasks(Project $project, int $userId): void
    {
        $hasIncompleteTasks = $project->tasks()
            ->where('created_by', $userId)
            ->whereIn('status', ['todo', 'doing'])
            ->exists();
        
        if ($hasIncompleteTasks) {
            throw new ConflictException('未完了のタスクがあるメンバーは削除できません');
        }
    }
}
```

---

### **app/Services/TaskService.php**

同様にLaravel標準の例外を使用しています。

```php
<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;  // ✅ 権限エラー
use App\Exceptions\ConflictException;               // ✅ 競合エラー

class TaskService
{
    public function getTasks(Project $project, User $user)
    {
        if (!$this->isProjectMember($project, $user)) {
            throw new AuthorizationException('このプロジェクトにアクセスする権限がありません');
        }
        
        return $project->tasks()->with('createdBy')->orderBy('created_at', 'desc')->get();
    }

    public function createTask(array $data, Project $project, User $user): Task
    {
        if (!$this->isProjectMember($project, $user)) {
            throw new AuthorizationException('このプロジェクトのタスクを作成する権限がありません');
        }
        
        $task = Task::create([
            'project_id' => $project->id,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'status' => 'todo',
            'created_by' => $user->id,
        ]);
        
        $task->load('createdBy');
        return $task;
    }

    private function checkTaskPermission(Task $task, User $user): void
    {
        $project = $task->project;
        if (!$this->isProjectMember($project, $user)) {
            throw new AuthorizationException('このプロジェクトにアクセスする権限がありません');
        }
    }

    private function validateTaskStatusForStart(Task $task): void
    {
        if ($task->status !== 'todo') {
            throw new ConflictException('未着手のタスクのみ開始できます');
        }
    }

    private function validateTaskStatusForComplete(Task $task): void
    {
        if ($task->status !== 'doing') {
            throw new ConflictException('作業中のタスクのみ完了できます');
        }
    }
}
```

---

## 📊 Before / After

### ❌ **Before（過剰設計）**

```
app/Exceptions/
├── ApiExceptionHandler.php
├── ForbiddenException.php      ← 不要（Laravel標準で代替可能）
├── ConflictException.php       ← 必要
└── NotFoundException.php        ← 不要（Laravel標準で代替可能）
```

**問題点：**
- カスタム例外を3つも作成
- Laravel標準で代替できるものまで作成
- ファイルが増えて複雑化

---

### ✅ **After（最適化）**

```
app/Exceptions/
├── ApiExceptionHandler.php     ← グローバルハンドラー
└── ConflictException.php       ← Laravel標準にないため必要
```

**改善点：**
- ✅ ファイル数を3→2に削減
- ✅ Laravel標準の例外を活用
- ✅ シンプルで保守しやすい
- ✅ Laravel Policyへの移行が容易

---

## 🎯 **例外の使い分けルール**

| エラー種類 | HTTPステータス | 使用する例外 |
|-----------|---------------|-------------|
| **権限エラー** | 403 | `AuthorizationException` ← Laravel標準 |
| **認証エラー** | 401 | `AuthenticationException` ← Laravel標準 |
| **未検出エラー** | 404 | `ModelNotFoundException` ← Laravel標準 |
| **バリデーションエラー** | 422 | `ValidationException` ← Laravel標準 |
| **競合エラー** | 409 | `ConflictException` ← カスタム |
| **サーバーエラー** | 500 | `Exception` ← PHP標準 |

---

## 🔍 **エラー処理フロー**

```
┌─────────────┐
│  Controller │ ← シンプル！try-catch不要
└──────┬──────┘
       │ メソッド呼び出し
       ↓
┌─────────────┐
│   Service   │ ← Laravel標準例外 or カスタム例外をthrow
└──────┬──────┘
       │ AuthorizationException / ConflictException / etc.
       ↓
┌─────────────────────┐
│ ApiExceptionHandler │ ← 例外の型で自動判定
│  (bootstrap/app.php) │
└──────┬──────────────┘
       │ 適切なHTTPステータス
       ↓
┌─────────────┐
│ ApiResponse │ ← 統一されたJSON形式
└──────┬──────┘
       │
       ↓
{
  "success": false,
  "message": "このプロジェクトにアクセスする権限がありません",
  "request_id": "req_xxx"
}
```

---

## 📚 **Laravel標準例外の一覧**

### **権限・認証系**

| 例外クラス | 用途 | HTTPステータス |
|-----------|------|---------------|
| `AuthorizationException` | 権限エラー | 403 |
| `AuthenticationException` | 認証エラー | 401 |

### **データ系**

| 例外クラス | 用途 | HTTPステータス |
|-----------|------|---------------|
| `ModelNotFoundException` | モデル未検出 | 404 |
| `ValidationException` | バリデーションエラー | 422 |

### **HTTP系**

| 例外クラス | 用途 | HTTPステータス |
|-----------|------|---------------|
| `NotFoundHttpException` | ルート未検出 | 404 |
| `MethodNotAllowedHttpException` | HTTPメソッド不許可 | 405 |

---

## 🎓 **なぜLaravel標準を使うべきか**

### **1. Policy連携**

```php
// app/Policies/ProjectPolicy.php
class ProjectPolicy
{
    public function view(User $user, Project $project): bool
    {
        return $project->users()->where('users.id', $user->id)->exists();
    }
}

// Controller
public function show(Project $project)
{
    // ✅ authorize()が失敗すると AuthorizationException を自動で投げる
    $this->authorize('view', $project);
    
    return new ProjectResource($project);
}
```

### **2. 統一性**

- ✅ Laravelコミュニティで標準的
- ✅ 他の開発者が理解しやすい
- ✅ ドキュメントが豊富

### **3. 保守性**

- ✅ Laravelのアップデートに追従
- ✅ フレームワークが自動でハンドリング
- ✅ カスタムコードが少ない

---

## ✅ **メリット**

| 項目 | 効果 |
|-----|------|
| **ファイル数削減** | 3→2（33%削減） |
| **保守性向上** | Laravel標準に準拠 |
| **可読性向上** | 他の開発者が理解しやすい |
| **拡張性向上** | Policyへの移行が容易 |
| **学習コスト低減** | Laravel標準を学べば理解できる |

---

## 🚀 **今後の拡張案**

### **1. Policyへの移行**

```php
// 現在：Service層で権限チェック
if (!$this->isProjectMember($project, $user)) {
    throw new AuthorizationException('...');
}

// 将来：Policyで権限チェック（推奨）
$this->authorize('view', $project);
```

### **2. カスタム例外メソッドの追加**

必要に応じて、ApiResponseにconflict()メソッドを追加できます。

```php
// app/Http/Responses/ApiResponse.php
public function conflict(string $message = '競合が発生しました', ?string $requestId = null): JsonResponse
{
    return $this->respond(false, $message, null, 409, null, $requestId);
}

// ApiExceptionHandler
private function handleConflict(ConflictException $exception): JsonResponse
{
    return $this->response->conflict($exception->getMessage());
}
```

---

## 🎉 **まとめ**

### **最適化の成果**

- ✅ 不要なカスタム例外を2つ削除
- ✅ Laravel標準の例外を活用
- ✅ シンプルで保守しやすいコードに改善
- ✅ Laravelベストプラクティスに準拠

### **ファイル構成（最終）**

```
app/Exceptions/
├── ApiExceptionHandler.php     ← グローバルエラーハンドラー
└── ConflictException.php       ← カスタム（Laravel標準にないため）

app/Services/
├── ProjectService.php          ← AuthorizationException使用
├── ProjectMemberService.php    ← AuthorizationException, ConflictException使用
└── TaskService.php             ← AuthorizationException, ConflictException使用

app/Http/Controllers/Api/
├── ProjectController.php       ← try-catchなし
├── ProjectMemberController.php ← try-catchなし
└── TaskController.php          ← try-catchなし
```

これで、**Laravel標準に準拠した、シンプルで保守しやすいエラーハンドリング**が完成しました！🚀
