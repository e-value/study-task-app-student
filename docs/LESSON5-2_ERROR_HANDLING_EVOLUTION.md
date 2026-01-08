# 🐘 エラーハンドリング進化論

## 〜ハードコーディングから美しい設計へ〜

---

## 🎬 プロローグ：開発初日、とりあえず動かす

👩‍💻「ガネーシャ先生！タスク管理アプリの開発を始めるんですけど、エラーハンドリングってどうすればいいんですか？」

🐘「おお、ええ質問やな！まずは**とりあえず動かす**ことから始めるで」

👩‍💻「とりあえず...ですか？」

🐘「せや。最初から完璧を目指すんやない。**動くものを作って、それを改善していく**んや」

👩‍💻「でも、エラーハンドリングって難しそう...」

🐘「大丈夫や。ワシの教え子のザッカーバーグくんも言うとったで。『Done is better than perfect』ってな」

👩‍💻「完璧を目指すより、まず完成させる...ですね！」

🐘「その通りや！ほな、始めよか」

---

## 📚 この教材で学べること

✅ エラーの種類（400 系・500 系）を理解する  
✅ Laravel のデフォルトエラーを隠す方法  
✅ まずはハードコーディングで実装  
✅ ApiResponse クラスで統一する（既存）  
✅ グローバル例外ハンドラーで一元管理（既存）  
✅ フロントエンドでも一箇所にまとめる（既存）  
✅ コメントアウトを解除して段階的に有効化

---

# 第 1 章：エラーの種類を理解する

🐘「まずはな、エラーにも**種類**があるんや」

👩‍💻「種類...ですか？」

---

## 📊 HTTP ステータスコードの基本

🐘「HTTP 通信ではな、ステータスコードっちゅうもんで**誰の責任か**を表すんや」

### 🎯 ステータスコードの分類

```
┌─────────────────────────────────────────────────────┐
│            HTTPステータスコードの分類               │
├─────────────────────────────────────────────────────┤
│                                                     │
│  2xx 成功                                           │
│  ├─ 200 OK          → 成功                          │
│  └─ 201 Created     → 作成成功                      │
│                                                     │
│  4xx クライアントエラー（リクエスト側の問題）       │
│  ├─ 400 Bad Request        → リクエストがおかしい   │
│  ├─ 401 Unauthorized       → 認証が必要             │
│  ├─ 403 Forbidden          → 権限がない             │
│  ├─ 404 Not Found          → リソースがない         │
│  ├─ 422 Unprocessable Entity → バリデーションエラー │
│  └─ 409 Conflict           → 状態の競合             │
│                                                     │
│  5xx サーバーエラー（サーバー側の問題）             │
│  ├─ 500 Internal Server Error → サーバー内部エラー  │
│  └─ 503 Service Unavailable   → サービス停止中      │
│                                                     │
└─────────────────────────────────────────────────────┘
```

👩‍💻「4xx はクライアント、5xx はサーバーの責任ってことですね」

🐘「せや！この違いを意識するのが大事や」

---

## 🚨 Laravel のデフォルトエラー表示の問題

👩‍💻「ところで、Laravel って何もしなくてもエラー表示してくれますよね？」

🐘「せや。でもな、**そのままやと問題がある**んや」

### ❌ 問題 1：エラーメッセージが英語

```json
{
    "message": "Call to undefined relationship [usrs] on model [App\\Models\\Project].",
    "exception": "BadMethodCallException",
    "file": "/var/www/html/app/Services/ProjectService.php",
    "line": 53
}
```

👩‍💻「これじゃユーザーに優しくないですね...」

---

### ❌ 問題 2：APP_DEBUG=true だと内部情報が丸見え

```json
{
  "message": "...",
  "exception": "BadMethodCallException",
  "file": "/var/www/html/app/Services/ProjectService.php",  ← 危険！
  "line": 53,  ← 危険！
  "trace": [...]  ← 危険！
}
```

🐘「これは前の教材（ERROR_EXPOSURE_PRACTICE.md）で学んだな？**セキュリティリスク**や」

---

### ❌ 問題 3：フロントで毎回 catch 内にコードを書く必要がある

```javascript
// ProjectsのShow.vue
catch (err) {
    console.error("❌ エラー:", err);
    if (err.response?.status === 403) {
        toast.error("権限がありません");
    } else if (err.response?.status === 404) {
        toast.error("プロジェクトが見つかりません");
    } else {
        toast.error("エラーが発生しました");
    }
}

// TasksのShow.vue
catch (err) {
    console.error("❌ エラー:", err);
    if (err.response?.status === 403) {
        toast.error("権限がありません");
    } else if (err.response?.status === 404) {
        toast.error("タスクが見つかりません");
    } else {
        toast.error("エラーが発生しました");
    }
}
```

👩‍💻「同じようなコードを何度も書いてます...」

🐘「せやろ？これを**DRY 原則（Don't Repeat Yourself）**違反って言うんや」

---

## 🎯 理想的なエラーハンドリング

🐘「理想は、こんな感じや」

```
✅ 理想的なエラーハンドリング

【バックエンド】
├─ 統一されたレスポンス形式
├─ 日本語のエラーメッセージ
├─ 適切なHTTPステータスコード
├─ 内部情報は隠す
└─ request_idで追跡可能

【フロントエンド】
├─ 一箇所でエラーハンドリング
├─ 各コンポーネントではシンプルに
├─ トースト表示も自動化
└─ エラーログも自動記録
```

👩‍💻「でも、どうやって実装すればいいんですか？」

🐘「実はな、お前のプロジェクトには**既に全部実装されとる**んや。ただ、**コメントアウトされてるだけ**やねん」

👩‍💻「え！そうなんですか？」

🐘「せや。これから段階的にコメントアウトを解除していくで」

---

# 第 2 章：まずはデフォルト状態を確認する

🐘「まずは、**何も設定してない状態**がどうなっとるか確認するで」

👩‍💻「はい！」

---

## 📝 現状確認：デフォルトのエラーハンドリング

### Step 1：bootstrap/app.php を確認

**開くファイル：** `bootstrap/app.php`

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use App\Exceptions\ApiExceptionHandler;
use Sentry\Laravel\Integration;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');

        $middleware->web(append: [
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Sentry統合
        // Integration::handles($exceptions);

        // 研修用に必要に応じて以下のコメントアウトの切り替え
        // （コメントアウト時はLaravelのデフォルトハンドラーが使用される）

        // // API例外ハンドラーを登録
        // $apiHandler = new ApiExceptionHandler();

        // $exceptions->render(function (\Throwable $e, Request $request) use ($apiHandler) {
        //     return $apiHandler->handle($e, $request);
        // });
    })->create();
```

🐘「見てみ？`withExceptions`の中が全部コメントアウトされとるやろ？」

👩‍💻「本当だ！これだと**Laravel のデフォルトハンドラー**が使われるんですね」

🐘「その通りや」

---

### Step 2：実際に確認してみよう

**ブラウザで以下の URL にアクセス：**

```
http://localhost/projects/1
```

**デベロッパーツールを開いて Network タブを確認**

🐘「ProjectService.php には意図的にタイポが仕込んであるから、エラーが出るで」

**ProjectService.php（53 行目）：**

```php
// ⚠️ ERROR_HANDLING_LESSON用：タイポを意図的に作成
// タスク機能の修正中に誤って users → usrs に変更してしまった想定
$project->load(['usrs', 'tasks.createdBy']);
```

**Network タブ > Response：**

```json
{
  "message": "Call to undefined relationship [usrs] on model [App\\Models\\Project].",
  "exception": "BadMethodCallException",
  "file": "/var/www/html/app/Services/ProjectService.php",
  "line": 53,
  "trace": [...]
}
```

👩‍💻「わぁ...ファイルパスとか行番号とか、全部見えてますね...」

🐘「せやろ？これが**デフォルト状態の危険性**や」

---

# 第 3 章：ApiResponse クラスで統一する

🐘「次は、**ApiResponse クラス**を見てみるで」

👩‍💻「これも既にあるんですか？」

---

## 📝 既存の ApiResponse クラスを確認

**開くファイル：** `app/Http/Responses/ApiResponse.php`

```php
<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    /**
     * 成功レスポンス
     */
    public function success($data = null, string $message = '成功'): JsonResponse
    {
        return $this->respond(true, $message, $data, 200);
    }

    /**
     * 作成成功レスポンス
     */
    public function created($data = null, string $message = '作成しました'): JsonResponse
    {
        return $this->respond(true, $message, $data, 201);
    }

    /**
     * 認証エラー
     */
    public function unauthorized(string $message = '認証が必要です', ?string $requestId = null): JsonResponse
    {
        return $this->respond(false, $message, null, 401, null, $requestId);
    }

    /**
     * 権限エラー
     */
    public function forbidden(string $message = '権限がありません', ?string $requestId = null): JsonResponse
    {
        return $this->respond(false, $message, null, 403, null, $requestId);
    }

    /**
     * 未検出エラー
     */
    public function notFound(string $message = '指定されたデータが見つかりません', ?string $requestId = null): JsonResponse
    {
        return $this->respond(false, $message, null, 404, null, $requestId);
    }

    /**
     * バリデーションエラー
     */
    public function validationError(string $message = 'バリデーションエラー', $errors = null, ?string $requestId = null): JsonResponse
    {
        return $this->respond(false, $message, null, 422, $errors, $requestId);
    }

    /**
     * サーバーエラー
     */
    public function serverError(string $message = 'サーバーエラー', ?string $requestId = null): JsonResponse
    {
        return $this->respond(false, $message, null, 500, null, $requestId);
    }

    // ... その他のメソッド
}
```

👩‍💻「わぁ！エラーの種類ごとにメソッドが用意されてますね！」

🐘「せやろ？このクラスを使えば、**統一されたレスポンス形式**で返せるんや」

---

### 📊 ApiResponse の特徴

```
✅ ApiResponseクラスの特徴

1️⃣ 統一されたレスポンス形式
   └─ success, message, data の形式

2️⃣ 日本語のデフォルトメッセージ
   └─ ユーザーに優しい

3️⃣ 適切なHTTPステータスコード
   └─ 401, 403, 404, 422, 500

4️⃣ request_idで追跡可能
   └─ エラーログと紐づけられる
```

---

## 🎯 ApiResponse の使い方（参考）

🐘「本来は、コントローラーでこんな風に使うんや」

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Responses\ApiResponse;

class TaskController extends Controller
{
    public function __construct(
        private TaskService $taskService,
        private ApiResponse $response  // 注入
    ) {}

    public function show(Request $request, Task $task)
    {
        try {
            $task = $this->taskService->getTask($task, $request->user());
            return new TaskResource($task);

        } catch (\Exception $e) {
            // ApiResponseを使う
            return $this->response->serverError('タスクの取得に失敗しました');
        }
    }
}
```

👩‍💻「でも、まだ try-catch が必要なんですね...」

🐘「せや。せやから次は**グローバル例外ハンドラー**や」

---

# 第 4 章：グローバル例外ハンドラーで一元管理

🐘「次は、**ApiExceptionHandler**を見るで」

👩‍💻「これも既にあるんですか？」

---

## 📝 既存の ApiExceptionHandler を確認

**開くファイル：** `app/Exceptions/ApiExceptionHandler.php`

```php
<?php

namespace App\Exceptions;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Responses\ApiResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Throwable;

class ApiExceptionHandler
{
    private ApiResponse $response;

    public function __construct()
    {
        $this->response = new ApiResponse();
    }

    /**
     * API例外を処理する
     */
    public function handle(Throwable $exception, Request $request): ?JsonResponse
    {
        // API以外のリクエストは処理しない
        if (!$request->is('api/*')) {
            return null;
        }

        // リクエストIDを生成（エラー追跡用）
        $requestId = $request->header('X-Request-ID') ?? uniqid('req_', true);

        // 例外タイプに応じて処理を振り分け
        return match (true) {
            $exception instanceof NotFoundHttpException => $this->handleNotFound($requestId),
            $exception instanceof ValidationException => $this->handleValidation($exception, $requestId),
            $exception instanceof AuthenticationException => $this->handleAuthentication($requestId),
            default => $this->handleServerError($exception, $request, $requestId),
        };
    }

    /**
     * 404エラー
     */
    private function handleNotFound(string $requestId): JsonResponse
    {
        return $this->response->notFound(requestId: $requestId);
    }

    /**
     * バリデーションエラー
     */
    private function handleValidation(ValidationException $exception, string $requestId): JsonResponse
    {
        return $this->response->validationError('バリデーションエラー', $exception->errors(), $requestId);
    }

    /**
     * 認証エラー
     */
    private function handleAuthentication(string $requestId): JsonResponse
    {
        return $this->response->unauthorized(requestId: $requestId);
    }

    /**
     * サーバーエラー（500）
     */
    private function handleServerError(Throwable $exception, Request $request, string $requestId): JsonResponse
    {
        // Sentryにコンテキスト情報を追加してエラーを送信
        // ...（省略）

        // 本番では固定メッセージ、開発中は詳細表示
        $message = config('app.debug') ? $exception->getMessage() : 'サーバーエラー';

        return $this->response->serverError($message, $requestId);
    }
}
```

👩‍💻「すごい！全ての例外を一箇所で処理してますね！」

🐘「せやろ？これがあれば、**コントローラーから try-catch を削除**できるんや」

---

### 🎯 グローバル例外ハンドラーの仕組み

```
┌─────────────────────────────────────────────────────┐
│         グローバル例外ハンドラーの流れ             │
├─────────────────────────────────────────────────────┤
│                                                     │
│  1. Controller                                      │
│     └─ サービス層を呼ぶだけ（try-catch不要）       │
│                                                     │
│  2. Service                                         │
│     └─ 例外が発生                                   │
│                                                     │
│  3. ApiExceptionHandler                             │
│     └─ 全ての例外を自動的にキャッチ                 │
│     └─ 例外の型に応じて適切なレスポンスを返す       │
│                                                     │
│  4. ApiResponse                                     │
│     └─ 統一されたJSON形式でレスポンス               │
│                                                     │
└─────────────────────────────────────────────────────┘
```

---

## 🚀 演習：グローバル例外ハンドラーを有効化する

🐘「ほな、実際に有効化してみよか」

---

### Step 1：bootstrap/app.php のコメントアウトを解除

**開くファイル：** `bootstrap/app.php`

**28〜42 行目を修正：**

```php
// 修正前（コメントアウトされている）
->withExceptions(function (Exceptions $exceptions): void {
    // Sentry統合
    // Integration::handles($exceptions);

    // 研修用に必要に応じて以下のコメントアウトの切り替え
    // （コメントアウト時はLaravelのデフォルトハンドラーが使用される）

    // // API例外ハンドラーを登録
    // $apiHandler = new ApiExceptionHandler();

    // $exceptions->render(function (\Throwable $e, Request $request) use ($apiHandler) {
    //     return $apiHandler->handle($e, $request);
    // });
})
```

**修正後（コメントアウトを解除）：**

```php
->withExceptions(function (Exceptions $exceptions): void {
    // Sentry統合
    // Integration::handles($exceptions);

    // 研修用に必要に応じて以下のコメントアウトの切り替え
    // （コメントアウト時はLaravelのデフォルトハンドラーが使用される）

    // API例外ハンドラーを登録
    $apiHandler = new ApiExceptionHandler();

    $exceptions->render(function (\Throwable $e, Request $request) use ($apiHandler) {
        return $apiHandler->handle($e, $request);
    });
})
```

**保存：** `Cmd + S` (Mac) / `Ctrl + S` (Windows)

---

### Step 2：ブラウザで確認

**ブラウザをリロード：**

```
強制リロード: Cmd + Shift + R (Mac) / Ctrl + Shift + R (Windows)
```

**Network タブ > Response を確認：**

```json
{
    "success": false,
    "message": "サーバーエラー",
    "request_id": "req_65a1b2c3d4e5f6a7b8c9"
}
```

👩‍💻「あ！今度はシンプルなメッセージになりました！」

🐘「せやろ？**内部情報が一切表示されてない**やろ？」

---

### ✅ 何が変わったのか？

```
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Before（デフォルト）
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
{
  "message": "Call to undefined relationship [usrs]...",
  "exception": "BadMethodCallException",
  "file": "/var/www/html/app/Services/ProjectService.php",  ← 危険
  "line": 53,  ← 危険
  "trace": [...]  ← 危険
}

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
After（グローバルハンドラー有効化）
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
{
  "success": false,
  "message": "サーバーエラー",  ← 安全
  "request_id": "req_65a1b2c3d4e5f6a7b8c9"  ← 追跡可能
}
```

---

### Step 3：エラーの詳細はログで確認

🐘「でもな、エラーの詳細はどこで確認するんや？」

👩‍💻「ログファイルですか？」

🐘「せや！」

**ターミナルで実行：**

```bash
tail -f storage/logs/laravel.log
```

**表示内容：**

```
[2024-01-15 10:30:45] local.ERROR: Call to undefined relationship [usrs] on model [App\Models\Project].
{
  "exception": "BadMethodCallException",
  "file": "/var/www/html/app/Services/ProjectService.php",
  "line": 53,
  "trace": [...]
}
[ErrorID: req_65a1b2c3d4e5f6a7b8c9]
```

👩‍💻「ログには詳細な情報が残ってますね！しかも`request_id`で紐づけできる！」

---

### Step 4：タイポを修正してみる

🐘「原因が分かったから、修正してみよか」

**開くファイル：** `app/Services/ProjectService.php`

**53 行目を修正：**

```php
// 修正前（タイポあり）
$project->load(['usrs', 'tasks.createdBy']);

// 修正後（タイポ修正）
$project->load(['users', 'tasks.createdBy']);
```

**保存してブラウザをリロード**

👩‍💻「やりました！今度はエラーが出ずに、プロジェクト詳細が正しく表示されました！」

🐘「よっしゃ！**修正完了や！**」

---

# 第 5 章：フロントエンドでも一箇所にまとめる

🐘「次は、**フロントエンド**のエラーハンドリングや」

👩‍💻「フロントエンドも既にあるんですか？」

---

## 📝 既存の useApiError を確認

**開くファイル：** `resources/js/composables/useApiError.js`

```javascript
import { ref } from "vue";
import { extractErrorMessage, extractValidationErrors } from "@/utils/apiError";

/**
 * APIエラーハンドリング用のComposable
 */
export function useApiError() {
    const error = ref(null);
    const validationErrors = ref({});
    const requestId = ref(null);
    const statusCode = ref(null);

    /**
     * エラーを処理する
     */
    const handleError = (err, defaultMessage = "エラーが発生しました") => {
        // APP_DEBUG が true の場合のみコンソールに詳細ログを出力
        if (import.meta.env.APP_DEBUG) {
            console.group("🚨 API Error");
            console.error("Error:", err);
            if (err.response) {
                console.error("Status:", err.response.status);
                console.error("Data:", err.response.data);
                console.error("URL:", err.config?.url);
            } else {
                console.error("Network Error:", err.message);
            }
            console.groupEnd();
        }

        // エラーメッセージを抽出
        error.value = extractErrorMessage(err, defaultMessage);

        // リクエストIDとステータスコードを取得
        if (err.response?.data) {
            requestId.value = err.response.data.request_id || null;
            statusCode.value = err.response.status || null;
        }

        // バリデーションエラーを抽出
        const validation = extractValidationErrors(err);
        if (validation) {
            validationErrors.value = validation;
        } else {
            validationErrors.value = {};
        }
    };

    /**
     * エラーをクリアする
     */
    const clearError = () => {
        error.value = null;
        validationErrors.value = {};
        requestId.value = null;
        statusCode.value = null;
    };

    return {
        error,
        validationErrors,
        requestId,
        statusCode,
        handleError,
        clearError,
    };
}
```

👩‍💻「おお！エラーハンドリングが一箇所にまとまってますね！」

---

### 🎯 useApiError の特徴

```
✅ useApiErrorの特徴

1️⃣ 統一されたエラーハンドリング
   └─ どのコンポーネントでも同じ方法で

2️⃣ APP_DEBUGに応じたログ出力
   └─ 開発中は詳細ログ、本番では非表示

3️⃣ バックエンドのメッセージを信頼
   └─ extractErrorMessage でバックエンドのメッセージを優先

4️⃣ バリデーションエラーも自動抽出
   └─ フォームで使いやすい

5️⃣ request_idとステータスコードを保持
   └─ エラー追跡に便利
```

---

## 📝 useApiError の使い方

🐘「実際の使い方を見てみよか」

**例：Projects/Show.vue**

```javascript
<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { useToast } from 'vue-toastification';
import { useApiError } from '@/composables/useApiError';  // インポート
import axios from 'axios';

const route = useRoute();
const toast = useToast();
const { error, handleError, clearError } = useApiError();  // 使用

const projectId = route.params.id;
const project = ref(null);
const loading = ref(false);

/**
 * プロジェクト詳細を取得
 */
const fetchProject = async () => {
    try {
        loading.value = true;
        clearError();  // 既存のエラーをクリア

        const response = await axios.get(`/api/projects/${projectId}`);
        project.value = response.data.data;

    } catch (err) {
        // シンプル！一行で完結！
        handleError(err, 'プロジェクト詳細の読み込みに失敗しました');

        // エラーメッセージをトースト表示
        if (error.value) {
            toast.error(error.value);
        }

    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchProject();
});
</script>
```

👩‍💻「catch ブロックがシンプルになりましたね！」

🐘「せやろ？**handleError 一行で全部やってくれる**んや」

---

### 📊 フロントエンドの進化

```
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Before（毎回catchブロックを書く）
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
catch (err) {
    console.error("❌ エラー:", err);
    if (err.response?.status === 403) {
        toast.error("権限がありません");
    } else if (err.response?.status === 404) {
        toast.error("プロジェクトが見つかりません");
    } else {
        toast.error("エラーが発生しました");
    }
}

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
After（useApiErrorで統一）
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
catch (err) {
    handleError(err, 'プロジェクト詳細の読み込みに失敗しました');
    if (error.value) {
        toast.error(error.value);
    }
}

✅ シンプル
✅ 統一されたエラーハンドリング
✅ バックエンドのメッセージを信頼
```

---

# 第 6 章：全体の流れを確認する

🐘「さて、ここまでの全体像を確認するで」

---

## 🎯 エラーハンドリングの全体像

```
┌─────────────────────────────────────────────────────────────┐
│                エラーハンドリングの全体像                   │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  【フロントエンド】                                         │
│  ┌────────────────────────────────────────────────┐         │
│  │ 1. Vue Component                                │         │
│  │    └─ axios.get('/api/projects/1')             │         │
│  │    └─ catch (err) { handleError(err) }         │         │
│  │                                                 │         │
│  │ 2. useApiError (Composable)                     │         │
│  │    └─ エラーメッセージを抽出                   │         │
│  │    └─ バックエンドのメッセージを信頼           │         │
│  │    └─ APP_DEBUGに応じてログ出力                │         │
│  └────────────────────────────────────────────────┘         │
│                      ↓                                       │
│  ━━━━━━━━━━━━━━━━ HTTP通信 ━━━━━━━━━━━━━━━━                │
│                      ↓                                       │
│  【バックエンド】                                            │
│  ┌────────────────────────────────────────────────┐         │
│  │ 1. Controller                                   │         │
│  │    └─ サービス層を呼ぶだけ（try-catch不要）    │         │
│  │                                                 │         │
│  │ 2. Service                                      │
│  │    └─ ビジネスロジック実行                     │         │
│  │    └─ 問題があれば例外をthrow                  │         │
│  │                                                 │         │
│  │ 3. ApiExceptionHandler                          │         │
│  │    └─ 全ての例外を自動的にキャッチ             │         │
│  │    └─ 例外の型に応じて適切なレスポンスを返す   │         │
│  │                                                 │         │
│  │ 4. ApiResponse                                  │         │
│  │    └─ 統一されたJSON形式でレスポンス            │         │
│  │    └─ request_idで追跡可能                      │         │
│  └────────────────────────────────────────────────┘         │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

---

## 📊 最終的なファイル構成

```
study-task-app/
├─ app/
│  ├─ Exceptions/
│  │  └─ ApiExceptionHandler.php        【グローバルハンドラー】✅既存
│  │
│  ├─ Http/
│  │  ├─ Controllers/
│  │  │  └─ Api/
│  │  │     ├─ TaskController.php       【try-catch不要】
│  │  │     └─ ProjectController.php    【try-catch不要】
│  │  │
│  │  └─ Responses/
│  │     └─ ApiResponse.php             【統一レスポンス】✅既存
│  │
│  └─ Services/
│     ├─ TaskService.php                【例外をthrow】
│     └─ ProjectService.php             【例外をthrow】
│
├─ bootstrap/
│  └─ app.php                           【ハンドラー登録】✅コメント解除
│
└─ resources/
   └─ js/
      ├─ composables/
      │  └─ useApiError.js              【フロント統一処理】✅既存
      │
      └─ utils/
         └─ apiError.js                 【ヘルパー関数】✅既存
```

---

## ✅ やったこと・やらなかったこと

```
✅ やったこと

1️⃣ デフォルト状態を確認
   └─ Laravelのデフォルトエラーの危険性を理解

2️⃣ ApiResponseクラスの確認
   └─ 既に実装済み

3️⃣ ApiExceptionHandlerの確認
   └─ 既に実装済み

4️⃣ bootstrap/app.phpでコメント解除
   └─ グローバルハンドラーを有効化

5️⃣ useApiErrorの確認
   └─ 既に実装済み

6️⃣ ProjectService.phpのタイポ修正
   └─ usrs → users

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

❌ やらなかったこと

1️⃣ 新規ファイルの作成
   └─ 全て既存のファイルを使用

2️⃣ カスタム例外クラスの作成
   └─ 今回は標準例外のみ使用

3️⃣ axiosインターセプターの実装
   └─ useApiErrorで十分
```

---

# 第 7 章：まとめと復習

🐘「お疲れさまやで！ここまでの復習をするで」

---

## 🎓 重要なポイント

```
💡 エラーハンドリングで覚えておくべきこと

1️⃣ デフォルトは危険
   └─ APP_DEBUG=true + デフォルトハンドラーは危険

2️⃣ グローバルハンドラーで一元管理
   └─ ApiExceptionHandlerで全ての例外をキャッチ

3️⃣ ApiResponseで統一
   └─ 全てのAPIレスポンスが同じ形式

4️⃣ フロントも統一
   └─ useApiErrorで一箇所にまとめる

5️⃣ バックエンドのメッセージを信頼
   └─ フロントは基本的にそのまま表示

6️⃣ request_idで追跡
   └─ ログとレスポンスを紐づけられる

7️⃣ APP_DEBUGで挙動を切り替え
   └─ 開発中は詳細ログ、本番では隠す
```

---

## 📝 設定チェックリスト

```
✅ 設定チェックリスト

【bootstrap/app.php】
□ ApiExceptionHandlerのコメントアウトを解除
□ $apiHandlerが正しくインスタンス化されている
□ $exceptions->renderが正しく設定されている

【.env】
□ APP_DEBUG=false（本番環境）
□ APP_DEBUG=true（開発環境）

【ProjectService.php】
□ usrs → users に修正されている

【フロントエンド】
□ useApiErrorを使用している
□ バックエンドのメッセージを信頼している
```

---

## 🐘 ガネーシャからの最終メッセージ

🐘「お疲れさまやで！」

👩‍💻「既存のコードがしっかりしてたので、コメント解除するだけでした！」

🐘「せやろ？**良いコードは、既に準備されとる**んや」

👩‍💻「でも、段階的に学べてよく分かりました」

🐘「**その通り！**ワシの教え子のスティーブ・ジョブズくんも言うとったで」

```
💬 スティーブ・ジョブズの名言

"Simple can be harder than complex."
（シンプルは複雑よりも難しい）

でもな、
"Simplicity is the ultimate sophistication."
（シンプルさこそ究極の洗練）

なんや。
```

🐘「複雑なコードを書くのは簡単や。でも、**シンプルで分かりやすいコード**を書くのは難しいんや」

👩‍💻「今回のエラーハンドリングも、結果的にとてもシンプルになりましたね」

🐘「せや。**コメント解除するだけで全てが動く**。これが**良い設計**や」

---

### 🎯 次のステップ

```
✅ やったこと
├─ デフォルトエラーの確認
├─ ApiResponseクラスの理解
├─ グローバルハンドラーの有効化
├─ useApiErrorの理解
└─ 全体の流れの把握

🚀 次にやること
├─ 他のページでもuseApiErrorを使ってみる
├─ カスタム例外クラスを追加してみる
├─ Sentryでエラー監視を設定する
└─ パフォーマンスの最適化
```

---

🐘「ほな、最後に一つだけ...あんみつ買ってきてや！🍨」

👩‍💻「...はい 😅（やっぱり）」

🐘「はい、Oh, My God!!」

---

## 🎓 おめでとうございます！

この教材を完了しました 🎉

既存のコードを活用して、
エラーハンドリングの仕組みを理解しました！

```
       🐘
      /||\
     / || \
    🍨    🍨

ガネーシャより愛を込めて
Simple is better!
```

---

## 📚 関連教材

-   `ERROR_HANDLING_LESSON.md` - エラーハンドリングの理論
-   `ERROR_EXPOSURE_PRACTICE.md` - エラー露出の実践演習
-   `CONSOLE_LOG_LESSON.md` - コンソールログの実践
