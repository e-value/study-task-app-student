# エラーハンドリング リファクタリング完了 ✅

## 📋 概要

各コントローラーのエラーハンドリングを統一し、保守性と可読性を大幅に向上させました。

---

## 🎯 実施内容

### 1. ✨ カスタム例外クラスの作成

適切な例外クラスを作成し、エラーの種類を明確にしました。

#### 作成したファイル

| ファイル | 用途 | HTTPステータス |
|---------|------|---------------|
| `app/Exceptions/ForbiddenException.php` | 権限エラー | 403 |
| `app/Exceptions/ConflictException.php` | 競合エラー（重複登録、不正な状態遷移など） | 409 |
| `app/Exceptions/NotFoundException.php` | リソース未検出 | 404 |

**使用例：**
```php
// 権限エラー
throw new ForbiddenException('このプロジェクトにアクセスする権限がありません');

// 競合エラー
throw new ConflictException('このユーザーは既にプロジェクトのメンバーです');

// 未検出エラー
throw new NotFoundException('指定されたユーザーが見つかりません');
```

---

### 2. 🔧 ApiExceptionHandlerの拡張

`app/Exceptions/ApiExceptionHandler.php` にカスタム例外のハンドリングを追加しました。

**追加した処理：**
- `ForbiddenException` → 403レスポンス
- `ConflictException` → 409レスポンス
- `NotFoundException` → 404レスポンス

**メリット：**
- コントローラーで個別にエラー処理を書く必要がなくなった
- エラーレスポンスの形式が統一された
- 保守性が向上した

---

### 3. 🛠 Service層の修正

全てのServiceクラスで、適切なカスタム例外を投げるように修正しました。

#### 修正したファイル

| ファイル | 主な変更内容 |
|---------|-------------|
| `app/Services/ProjectService.php` | `\Exception` → `ForbiddenException` |
| `app/Services/ProjectMemberService.php` | `\Exception` → `ForbiddenException`, `ConflictException`, `NotFoundException` |
| `app/Services/TaskService.php` | `\Exception` → `ForbiddenException`, `ConflictException` |

**修正例：**

```php
// ❌ 修正前
throw new \Exception('このプロジェクトにアクセスする権限がありません');

// ✅ 修正後
throw new ForbiddenException('このプロジェクトにアクセスする権限がありません');
```

---

### 4. 🎨 Controller層の修正

全てのAPIコントローラーから不要な`try-catch`を削除し、コードをシンプルにしました。

#### 修正したファイル

| ファイル | 主な変更内容 |
|---------|-------------|
| `app/Http/Controllers/Api/ProjectController.php` | try-catch削除、ApiResponse使用 |
| `app/Http/Controllers/Api/ProjectMemberController.php` | try-catch削除、ApiResponse使用 |
| `app/Http/Controllers/Api/TaskController.php` | try-catch削除、返り値の型を簡略化 |

**修正例（ProjectController::show）：**

```php
// ❌ 修正前
public function show(Request $request, Project $project): ProjectResource|JsonResponse
{
    try {
        $project = $this->projectService->getProject($project, $request->user());
        return new ProjectResource($project);
    } catch (\Exception $e) {
        return response()->json([
            'message' => $e->getMessage(),
        ], 403);
    }
}

// ✅ 修正後
public function show(Request $request, Project $project): ProjectResource
{
    $project = $this->projectService->getProject($project, $request->user());
    return new ProjectResource($project);
}
```

---

## 📊 Before / After 比較

### ❌ Before（問題点）

1. **全ての例外を汎用的に`\Exception`でキャッチ**
   - エラーの種類が不明確
   - 適切なステータスコードが返せない

2. **エラーメッセージの文字列でステータスコードを判断**
   ```php
   $statusCode = str_contains($e->getMessage(), '権限がありません') ? 403 : 409;
   ```
   - 脆弱で保守しづらい
   - メッセージ変更でステータスコードが変わる危険性

3. **ApiResponseを使わず直接`response()->json()`を呼び出し**
   - レスポンス形式が統一されていない
   - エラー時に`request_id`が付与されない

4. **コントローラーが肥大化**
   - try-catchだらけで読みづらい
   - エラー処理のロジックが重複

---

### ✅ After（改善後）

1. **明示的なカスタム例外を使用**
   ```php
   throw new ForbiddenException('権限がありません');
   throw new ConflictException('既に存在します');
   throw new NotFoundException('見つかりません');
   ```

2. **ApiExceptionHandlerが例外の型で判断**
   ```php
   return match (true) {
       $exception instanceof ForbiddenException => $this->handleForbidden($exception),
       $exception instanceof ConflictException => $this->handleConflict($exception),
       // ...
   };
   ```

3. **統一されたエラーレスポンス**
   - 全てのエラーで`request_id`が付与される
   - レスポンス形式が統一される

4. **コントローラーがシンプルに**
   - try-catchがほぼ不要
   - ビジネスロジックに集中できる

---

## 🔍 エラーフロー

### 現在のエラー処理の流れ

```
1. Controller
   ↓ メソッド呼び出し
   
2. Service
   ↓ カスタム例外をthrow
   
3. ApiExceptionHandler (bootstrap/app.php)
   ↓ 例外の種類を判定
   
4. ApiResponse
   ↓ 統一されたJSONレスポンス
   
5. クライアント
   ✅ 適切なエラーメッセージとステータスコードを受信
```

---

## 🎓 Laravel的なベストプラクティス

今回の実装は、Laravel推奨のエラーハンドリングパターンに準拠しています：

### Laravelでの対応

| 今回の実装 | Laravel標準 |
|----------|------------|
| `ForbiddenException` | `Illuminate\Auth\Access\AuthorizationException` |
| `ConflictException` | （カスタム例外） |
| `NotFoundException` | `Illuminate\Database\Eloquent\ModelNotFoundException` |
| `ApiExceptionHandler` | `app/Exceptions/Handler.php`の`render()`メソッド |

### 比較：Laravelの標準機能

Laravelには以下の機能もあります：

1. **Policy（ポリシー）**
   - 権限チェックを自動化
   - `$this->authorize('update', $project);`
   - 今回は研修用にシンプルな実装を採用

2. **FormRequest**
   - バリデーションとエラーレスポンスを自動化
   - 既に使用中（`ProjectRequest`, `TaskRequest`など）

3. **Resource**
   - レスポンスの形式を統一
   - 既に使用中（`ProjectResource`, `TaskResource`など）

---

## 📚 今後の拡張案

### 1. Policyの導入

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
public function show(Request $request, Project $project): ProjectResource
{
    $this->authorize('view', $project); // 自動で403が返る
    return new ProjectResource($project);
}
```

### 2. 例外メッセージの多言語対応

```php
throw new ForbiddenException(__('errors.project.forbidden'));
```

### 3. Sentryとの連携強化

```php
// 例外に追加のコンテキストを付与
Sentry\withScope(function (Scope $scope) use ($project) {
    $scope->setContext('project', [
        'id' => $project->id,
        'name' => $project->name,
    ]);
    throw new ForbiddenException('...');
});
```

---

## ✅ メリット

1. **可読性の向上** 🎯
   - コントローラーがシンプルに
   - try-catchの乱立がなくなった

2. **保守性の向上** 🛠
   - エラー処理が一箇所に集約
   - エラーメッセージの変更が容易

3. **安全性の向上** 🔒
   - 適切なステータスコードが返る
   - エラー時も統一された形式

4. **テスタビリティの向上** 🧪
   - 例外のテストが書きやすい
   - モックが容易

---

## 🚀 使い方

### 開発者向けガイド

#### 新しいエラーを追加する場合

1. **カスタム例外を作成**
   ```bash
   php artisan make:exception UnauthorizedException
   ```

2. **ApiExceptionHandlerに処理を追加**
   ```php
   return match (true) {
       // ...
       $exception instanceof UnauthorizedException => $this->handleUnauthorized($exception),
       // ...
   };
   ```

3. **Serviceで投げる**
   ```php
   throw new UnauthorizedException('認証が必要です');
   ```

#### エラーをテストする場合

```php
public function test_forbidden_error()
{
    $response = $this->getJson('/api/projects/999');
    
    $response->assertStatus(403);
    $response->assertJson([
        'success' => false,
        'message' => 'このプロジェクトにアクセスする権限がありません',
    ]);
}
```

---

## 🎉 まとめ

今回のリファクタリングにより、エラーハンドリングが**明確**で**保守しやすく**、**Laravelのベストプラクティス**に沿った実装になりました。

**主な成果：**
- ✅ カスタム例外クラス3つを作成
- ✅ ApiExceptionHandlerを拡張
- ✅ Service層を全て修正（3ファイル）
- ✅ Controller層を全て修正（3ファイル）
- ✅ Lintエラー0

**コード削減：**
- try-catchブロック：**14箇所削除**
- 条件分岐（status code判定）：**3箇所削除**
- 重複コード：**大幅に削減**

これで、今後の開発がよりスムーズに進められます！ 🚀
