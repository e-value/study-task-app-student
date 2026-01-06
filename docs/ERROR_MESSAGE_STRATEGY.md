# エラーメッセージ管理戦略

## 📋 基本方針

**バックエンドが主導権を持ち、フロントエンドはそれを信頼する**

- バックエンドが環境に応じて適切なメッセージを返す
- フロントエンドは基本的にバックエンドのメッセージを表示する
- フロントエンドのデフォルトメッセージは「万が一」のフォールバック

## 🎯 エラーの分類と管理方針

### 1. 技術的エラー（500系）

**定義**: システムのインフラ層で発生するエラー（DB接続失敗、メモリ不足など）

**管理**: バックエンド（環境による切り替え）

```php
// bootstrap/app.php
$exceptions->render(function (Throwable $e, Request $request) use ($response) {
    if ($request->is('api/*')) {
        app('sentry')->captureException($e); // Sentryでログ監視
        
        // 本番: 固定メッセージ（内部情報を隠す）
        // 開発: 詳細メッセージ（デバッグ用）
        $message = config('app.debug') ? $e->getMessage() : 'サーバーエラー';
        return $response->serverError($message);
    }
});
```

**レスポンス例**:
```json
// 本番環境
{
  "success": false,
  "message": "サーバーエラー"
}

// 開発環境
{
  "success": false,
  "message": "SQLSTATE[HY000] [2002] Connection refused"
}
```

**フロントエンド**: バックエンドのメッセージをそのまま表示

---

### 2. ビジネスロジックエラー（400系）

**定義**: ビジネスルールや権限チェックで発生するエラー

**管理**: バックエンド（ビジネスロジック層）

```php
// TaskService.php
if (!$this->isProjectMember($project, $user)) {
    throw new \Exception('このプロジェクトにアクセスする権限がありません');
}
```

**レスポンス例**:
```json
{
  "message": "このプロジェクトにアクセスする権限がありません"
}
```

**フロントエンド**: バックエンドのメッセージをそのまま表示

---

### 3. バリデーションエラー（422）

**定義**: ユーザー入力の検証で発生するエラー

**管理**: バックエンド（バリデーション層）

```php
// TaskRequest.php
public function rules(): array
{
    return [
        'title' => 'required|max:255',
        'description' => 'nullable|max:1000',
    ];
}
```

**レスポンス例**:
```json
{
  "message": "バリデーションエラー",
  "errors": {
    "title": ["タイトルは必須です"],
    "description": ["説明は1000文字以内で入力してください"]
  }
}
```

**フロントエンド**: バックエンドのメッセージをそのまま表示

---

### 4. ネットワークエラー

**定義**: インターネット接続切断、DNSエラー、タイムアウトなど

**管理**: フロントエンド

```javascript
// utils/apiError.js
if (!error.response) {
  // ネットワークエラーは明確なメッセージを返す
  if (error.message && error.message.toLowerCase().includes('network')) {
    return 'ネットワークエラーが発生しました。インターネット接続を確認してください。';
  }
  return 'サーバーに接続できませんでした。';
}
```

**レスポンス**: なし（バックエンドに到達していない）

**フロントエンド**: フロントエンド側で固定メッセージを表示

---

## 📊 優先順位

```
1. ネットワークエラー → フロントエンドの固定メッセージ
2. バックエンドのメッセージ → バックエンドが返すメッセージ（信頼する）
3. デフォルトメッセージ → フォールバック（万が一のため）
```

## 💡 実装例

### バックエンド

```php
// bootstrap/app.php

// 【1】技術的エラー（500系）
$exceptions->render(function (Throwable $e, Request $request) use ($response) {
    if ($request->is('api/*')) {
        app('sentry')->captureException($e);
        $message = config('app.debug') ? $e->getMessage() : 'サーバーエラー';
        return $response->serverError($message);
    }
});

// 【2】ビジネスロジックエラー（403, 404, 409など）
// → コントローラーやサービス層で throw new \Exception('メッセージ');

// 【3】バリデーションエラー（422）
$exceptions->render(function (ValidationException $e, Request $request) use ($response) {
    if ($request->is('api/*')) {
        return $response->validationError('バリデーションエラー', $e->errors());
    }
});
```

### フロントエンド

```javascript
// utils/apiError.js
export function extractErrorMessage(error, defaultMessage = 'エラーが発生しました') {
  // 【1】ネットワークエラー
  if (!error.response) {
    return 'ネットワークエラーが発生しました。インターネット接続を確認してください。';
  }

  // 【2】バックエンドのメッセージを信頼
  if (error.response.data?.message) {
    return error.response.data.message;
  }

  // 【3】フォールバック（通常は到達しない）
  return defaultMessage;
}
```

```vue
<!-- コンポーネント -->
<script setup>
import { useApiError } from '@/composables/useApiError';

const { error, handleError, clearError } = useApiError();

const fetchData = async () => {
  try {
    clearError();
    const response = await axios.get('/api/data');
    // 成功処理
  } catch (err) {
    // 第2引数はフォールバック。ほとんどの場合、バックエンドのメッセージが使われる
    handleError(err, 'データの読み込みに失敗しました');
  }
};
</script>
```

## 🔐 セキュリティ考慮事項

### ❌ 悪い例

```json
{
  "message": "SQLSTATE[HY000] [2002] Connection to database failed at localhost:3306"
}
```

→ 内部情報（ホスト名、ポート番号）を漏洩

### ✅ 良い例

```json
{
  "message": "サーバーエラー"
}
```

→ ユーザーには必要最小限の情報のみ。管理者はSentryで詳細確認

## 🌍 多言語対応への拡張

将来的に多言語対応が必要な場合：

```javascript
// i18n/ja.json
{
  "errors": {
    "network": "ネットワークエラーが発生しました",
    "server": "サーバーエラー",
    "fallback": "エラーが発生しました"
  }
}

// utils/apiError.js
import { i18n } from '@/i18n';

export function extractErrorMessage(error, defaultMessage) {
  if (!error.response) {
    return i18n.t('errors.network');
  }
  
  // バックエンドのメッセージは多言語対応が必要な場合、
  // エラーコードベースに変更することを検討
  return error.response.data?.message || defaultMessage;
}
```

## 📚 関連ファイル

- `bootstrap/app.php` - グローバル例外ハンドラー
- `app/Http/Responses/ApiResponse.php` - APIレスポンス共通化
- `resources/js/utils/apiError.js` - エラーメッセージ抽出
- `resources/js/composables/useApiError.js` - エラーハンドリングComposable
- `resources/js/Components/ApiError.vue` - エラー表示コンポーネント

## 🎓 まとめ

| エラー種類 | 管理 | 本番メッセージ | 開発メッセージ | Sentryログ |
|-----------|------|---------------|---------------|-----------|
| 技術的エラー（500） | バックエンド | "サーバーエラー" | 詳細 | ✅ |
| ビジネスロジック（400系） | バックエンド | メッセージ | メッセージ | - |
| バリデーション（422） | バックエンド | メッセージ | メッセージ | - |
| ネットワークエラー | フロントエンド | "ネットワークエラー" | "ネットワークエラー" | - |

**原則**: バックエンドが主導権を持ち、フロントエンドはそれを信頼する。
