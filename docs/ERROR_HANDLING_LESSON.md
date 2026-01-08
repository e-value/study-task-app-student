# 🐘 エラーハンドリング完全攻略！〜例外を制する者はシステムを制す〜

---

## 🎬 プロローグ：納品から 2 週間後、突然の電話

👩‍💻「ふぅ...システム納品してから 2 週間。クライアントさんからも好評で順調です！🎉」

🐘「おお、ええことやな！CONSOLE_LOG_LESSON で学んだデバッグ技術も活かせたやろ？」

👩‍💻「はい！console.log でエラー追跡もバッチリできて、開発もスムーズでした！」

🐘「せやな。console.log をマスターしたら、もう怖いもんなしや。じゃあ、今日は打ち上げにあんみつでも...🍨」

📞 **プルルルル...プルルルル...** ☎️

👩‍💻「あ、クライアントさんから電話です」

🐘「おお、追加機能の相談かもしれんな。出てみ」

---

👩‍💻「お電話ありがとうございます！」

📞 **クライアント**：「あの...急ぎの用件なんですけど...さっきタスクの詳細を開いたら、変な英語の文字がいっぱい出てきて...😰」

👩‍💻「え...？でも、これまで 2 週間問題なく使えてたはずでは...」

📞 **クライアント**：「スクリーンショット送りますね...何かエラーって書いてあるんですけど...」

---

**📧 受信した画像**

```
┌─────────────────────────────────────────────────────────────────┐
│  🔴 クライアントに表示された画面                                │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  Exception:                                                     │
│  Call to undefined relationship [creatdBy] on model             │
│  [App\Models\Task].                                             │
│                                                                 │
│  in /var/www/html/app/Services/TaskService.php:72               │
│                                                                 │
│  Stack trace:                                                   │
│  #0 /var/www/html/app/Services/TaskService.php(72):            │
│     Illuminate\Database\Eloquent\Model->__call()                │
│  #1 /var/www/html/app/Http/Controllers/Api/TaskController.php  │
│  #2 /var/www/html/vendor/laravel/framework/src/Illuminate/...   │
│  #3 /var/www/html/vendor/laravel/framework/src/Illuminate/...   │
│  ...                                                            │
│                                                                 │
│  Database: sqlite                                               │
│  File: /var/www/html/database/database.sqlite                   │
│  User: www-data                                                 │
│  Environment: production                                        │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

---

👩‍💻「え...え...ええええ！？😱 2 週間も問題なかったのに、なんで今更...」

🐘「**あちゃー、これはアカンやつや！**DB のファイルパスとか丸見えやないか！しかも本番環境（production）やと！？」

👩‍💻「で、でも、開発中は console.log でちゃんとデバッグしてたのに...なんで急に...」

🐘「落ち着きや。エラー内容を見てみ。`creatdBy`って書いてあるやろ？」

👩‍💻「あ...本当だ...`createdBy`じゃなくて`creatdBy`...これって...」

🐘「せや。**たった一文字のタイポ**や。おそらく最近の更新で誰かが間違えて修正してもうたんやろな」

👩‍💻「console.log でデバッグはできても...エラーハンドリングしてないと、こんなことになるんですね...」

🐘「その通りや！**console.log はデバッグの武器。でもエラーハンドリングは防具や。**両方揃って初めて、プロの開発者なんや」

👩‍💻「クライアントさん『意味不明な英語がいっぱい出てきて怖い』って...😱」

🐘「まずいどころの話やない！ハッカーからしたら宝の山や。『どうぞ攻撃してください』って言うてるようなもんやで」

👩‍💻「ど、どうすれば...」

🐘「まぁ落ち着きや。これは**エラーハンドリング**をちゃんとしてへんから起きた問題や。ワシの教え子のグレース・ホッパーちゃんも言うとったで。『バグは避けられへん。大事なのはバグとどう向き合うかや』ってな」

👩‍💻「グレース・ホッパー？あの『バグ』って言葉を広めた人ですか？」

🐘「せや！実際に蛾（moth）がコンピュータに挟まっとったのを見つけて『First actual case of bug being found』って記録したんや。まぁ、今日はそのバグ…いや、**エラー**とどう付き合うかを教えたるわ！」

👩‍💻「お願いします...！」

---

## 📚 第 1 章：エラーハンドリングとは何か？

👩‍💻「そもそも『エラーハンドリング』って何ですか？」

🐘「ええ質問や！**エラーハンドリング**っちゅうのはな、**プログラムで起きる問題を予測して、適切に対処する仕組み**のことや」

```
┌─────────────────────────────────────────────────────────┐
│                    エラーハンドリングとは               │
├─────────────────────────────────────────────────────────┤
│  🎯 目的：エラーが起きても、システムが優雅に対処する    │
│                                                         │
│  【エラーなし】        【エラーあり・対処なし】          │
│   😊 正常動作    →     💀 システム停止・白画面          │
│                                                         │
│  【エラーあり・対処あり】                               │
│   😌 「申し訳ございません」メッセージ表示              │
│   📝 ログに記録 → 開発者が後で調査可能                 │
└─────────────────────────────────────────────────────────┘
```

👩‍💻「つまり、エラーが起きることを前提に準備しておくってことですね？」

🐘「その通りや！ワシの教え子のナポレオンちゃんも言うとったで。『戦いに備えて計画を立てるんやない。計画が崩れた時に備えて計画を立てるんや』ってな」

👩‍💻「さっきクライアントに表示されたあの画面は、まさに『対処なし』の状態だったんですね…」

🐘「せや！本来ユーザーには『申し訳ございません。しばらくお待ちください』みたいなメッセージだけ見せて、詳細なエラー情報は開発者だけが見られるようにせなアカンのや」

---

## 🚨 第 2 章：なぜエラーハンドリングが重要なのか？

👩‍💻「でも、ちゃんとコード書いてたらエラーって起きないんじゃ…」

🐘「**甘いわ！あんみつより甘いわ！**🍨」

```
┌──────────────────────────────────────────────────────────┐
│        エラーハンドリングがないと起きる悲劇              │
├──────────────────────────────────────────────────────────┤
│                                                          │
│  😱 ユーザー側                                           │
│  ├── 真っ白な画面（何が起きたかわからない）             │
│  ├── 謎のエラーコードだけ表示                           │
│  └── 個人情報やシステム情報が丸見え（セキュリティ事故）│
│                                                          │
│  😭 開発者側                                             │
│  ├── どこでエラーが起きたかわからない                   │
│  ├── 再現できない謎のバグに悩まされる                   │
│  └── 夜中に緊急コール（地獄）                           │
│                                                          │
│  💀 ビジネス側                                           │
│  ├── 信頼失墜                                           │
│  ├── 売上減少                                           │
│  └── 最悪、損害賠償                                     │
└──────────────────────────────────────────────────────────┘
```

👩‍💻「うわ…結構深刻ですね」

🐘「せやろ？エラーハンドリングは**保険**みたいなもんや。何も起きへん時は無駄に見えるけど、いざという時に会社を救うんや」

---

## 🎭 第 3 章：エラーの種類を知ろう

👩‍💻「エラーって一口に言っても色々ありますよね？」

🐘「ええとこに気づいたな！エラーには大きく分けて**3 つのカテゴリ**があるんや」

### 📊 エラーの分類表

| カテゴリ                             | 説明                       | 例                                | 対処の責任         |
| ------------------------------------ | -------------------------- | --------------------------------- | ------------------ |
| 🔴 **文法エラー（Syntax Error）**    | コードの書き方が間違ってる | セミコロン忘れ、括弧の対応ミス    | 開発者が直す       |
| 🟠 **実行時エラー（Runtime Error）** | 動かしてみたら起きる       | null 参照、0 除算、ファイルがない | try-catch で対処   |
| 🟡 **業務エラー（Business Error）**  | ビジネスルール違反         | 権限なし、不正なステータス遷移    | アプリで適切に処理 |

👩‍💻「HTTP のエラーコードとかもありますよね？404 とか 500 とか」

🐘「おお、それも大事やな！HTTP ステータスコードを整理したるわ」

### 📊 よく見る HTTP ステータスコード

| コード | 名前                  | 意味                 | 誰の責任？   |
| ------ | --------------------- | -------------------- | ------------ |
| `200`  | OK                    | 成功                 | -            |
| `201`  | Created               | 作成成功             | -            |
| `400`  | Bad Request           | リクエストがおかしい | クライアント |
| `401`  | Unauthorized          | 認証が必要           | クライアント |
| `403`  | Forbidden             | 権限がない           | クライアント |
| `404`  | Not Found             | リソースがない       | クライアント |
| `405`  | Method Not Allowed    | HTTP メソッドが違う  | クライアント |
| `409`  | Conflict              | 状態の競合           | クライアント |
| `422`  | Unprocessable Entity  | バリデーションエラー | クライアント |
| `500`  | Internal Server Error | サーバー側のエラー   | サーバー     |
| `503`  | Service Unavailable   | サービス停止中       | サーバー     |

👩‍💻「4xx はクライアント（リクエスト側）の問題で、5xx はサーバー側の問題ってことですね！」

🐘「さすがやな！ワシの教え子のアラン・チューリングくんも『問題を分類できれば、半分は解決したようなもんや』って言うとったわ」

👩‍💻「…それ本当にチューリングさんが言ったんですか？」

🐘「まぁ…細かいことはええやんけ！本質は合っとる！」

---

## 🏗️ 第 4 章：Laravel のデフォルトのエラーハンドリング

👩‍💻「Laravel って、何もしなくてもエラー画面出ますよね？」

🐘「せや！Laravel は優秀やから、最初から**例外ハンドラー**が用意されとるんや」

### 🔧 Laravel の例外処理の仕組み

```
┌─────────────────────────────────────────────────────────┐
│              Laravelの例外処理フロー                    │
├─────────────────────────────────────────────────────────┤
│                                                         │
│   📥 リクエスト                                         │
│        ↓                                                │
│   🎯 Controller / Service で例外発生！                  │
│        ↓                                                │
│   🚨 例外がthrowされる                                  │
│        ↓                                                │
│   📋 bootstrap/app.php の withExceptions() でキャッチ   │
│        ↓                                                │
│   🎨 レスポンス生成                                     │
│        ├── 🌐 Web → HTMLエラーページ                    │
│        └── 🔌 API → JSONエラーレスポンス                │
│                                                         │
└─────────────────────────────────────────────────────────┘
```

### 📝 Laravel 11+ の例外ハンドラー設定

```php
// bootstrap/app.php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // ミドルウェア設定
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // 🎯 ここで例外処理をカスタマイズできる！
    })
    ->create();
```

### 🌍 環境による表示の違い

👩‍💻「本番と開発で表示が違いますよね？」

🐘「せや！`.env`の`APP_DEBUG`で挙動が変わるんや」

| 環境 | APP_DEBUG | 表示内容                               |
| ---- | --------- | -------------------------------------- |
| 開発 | `true`    | 詳細なエラー情報（スタックトレース等） |
| 本番 | `false`   | シンプルなエラーページ（情報漏洩防止） |

```
【開発環境 APP_DEBUG=true】
┌─────────────────────────────────────┐
│  🔴 Exception                       │
│  このプロジェクトにアクセスする権限  │
│  がありません                       │
│                                     │
│  📍 app/Services/TaskService.php:22 │
│                                     │
│  Stack Trace:                       │
│  #0 app/Http/Controllers/...        │
│  #1 vendor/laravel/framework/...    │
│  ...（詳細情報ダダ漏れ）            │
└─────────────────────────────────────┘

【本番環境 APP_DEBUG=false】
┌─────────────────────────────────────┐
│                                     │
│       🚫 500                        │
│   Server Error                      │
│                                     │
│  （これだけ！安全！）               │
└─────────────────────────────────────┘
```

👩‍💻「あ！さっきクライアントに表示されたエラー画面、まさに`APP_DEBUG=true`の状態だったんですね！本番環境なのに！」

🐘「**せや！それが大問題やったんや！** 本番で`APP_DEBUG=true`にしたら DB の接続情報とかパスワードとか丸見えになるで。ワシの教え子のスノーデンくんも言うとったわ、『情報漏洩は内部から起きる』ってな」

👩‍💻「それ、ちょっと文脈違いません…？」

🐘「まぁええやんけ！本質は『情報は守れ』っちゅうことや！」

---

## 🎣 第 5 章：try-catch とは何か？

👩‍💻「よく出てくる`try-catch`って何ですか？」

🐘「よっしゃ、ここからが本番や！**try-catch**は**例外を捕まえる網**みたいなもんや」

### 🎪 例え話：サーカスの綱渡り

```
【try-catchのイメージ】

        🎪 サーカスの綱渡り

     try（挑戦するエリア）
    ━━━━━━━━━━━━━━━━━━━━━
          🏃 綱渡り中...
              ↓
           😱 落ちた！
              ↓
    ========================
     catch（セーフティネット）
    ========================
          😮‍💨 助かった！

    「落ちても大丈夫」という安心感
```

| サーカスの例え     | プログラムでは              |
| ------------------ | --------------------------- |
| 綱渡りに挑戦       | `try`ブロック内の処理を実行 |
| 落ちる（失敗）     | 例外（Exception）が発生     |
| セーフティネット   | `catch`ブロック             |
| ネットで受け止める | 例外をキャッチして処理      |
| 観客に謝る         | エラーメッセージを返す      |

### 📝 基本構文

```php
<?php

try {
    // 🎯 ここに「失敗するかもしれない処理」を書く
    $result = riskyOperation();

} catch (Exception $e) {
    // 🛡️ 例外が発生したらここに来る
    // $e には例外の情報が入ってる
    Log::error($e->getMessage());

    return response()->json([
        'message' => 'エラーが発生しました'
    ], 500);
}
```

👩‍💻「なるほど！`try`の中で問題が起きたら、`catch`に飛ぶんですね」

🐘「その通りや！ワシの教え子のエジソンくんも言うとったで。『失敗は成功の母や。でも失敗した時の備えがなかったら、ただの失敗や』ってな」

---

## ⚡ 第 6 章：try-catch がある時とない時の違い

👩‍💻「`try-catch`がないとどうなるんですか？」

🐘「ええ質問や！実際に比較してみよか」

### 🔴 try-catch がない場合

```php
<?php

namespace App\Http\Controllers\Api;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends ApiController
{
    public function show(Request $request, Task $task)
    {
        // ⚠️ try-catchなし
        $task = $this->taskService->getTask($task, $request->user());

        return new TaskResource($task);
    }
}
```

```
【権限のないユーザーがアクセス】

📥 GET /api/tasks/123

        ↓

😱 Exception発生！

        ↓

┌─────────────────────────────────────┐
│  Exception:                         │
│  このプロジェクトにアクセスする権限  │
│  がありません                       │
│                                     │
│  in TaskService.php:175             │
│                                     │
│  Status: 500                        │
└─────────────────────────────────────┘

※ Laravelのデフォルトハンドラーが処理してくれる
　 でも、HTTPステータスが500になってしまう
　 （本来は403 Forbiddenが適切）
```

### 🟢 try-catch がある場合

```php
<?php

namespace App\Http\Controllers\Api;

use App\Models\Task;
use Illuminate\Http\Request;
use App\Services\TaskService;
use App\Http\Resources\TaskResource;

class TaskController extends ApiController
{
    public function __construct(
        private TaskService $taskService
    ) {}

    public function show(Request $request, Task $task)
    {
        try {
            $task = $this->taskService->getTask($task, $request->user());

            return new TaskResource($task);

        } catch (\Exception $e) {
            // 🎯 権限エラーの場合は403を返す
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error_code' => 'FORBIDDEN'
            ], 403);
        }
    }
}
```

```
【権限のないユーザーがアクセス】

📥 GET /api/tasks/123

        ↓

🛡️ catchでキャッチ！

        ↓

┌─────────────────────────────────────┐
│  {                                  │
│    "success": false,                │
│    "message": "このプロジェクトに...",│
│    "error_code": "FORBIDDEN"        │
│  }                                  │
│                                     │
│  Status: 403                        │
└─────────────────────────────────────┘

※ 適切なステータスコード＆カスタムメッセージ
　 フロントエンドが処理しやすい！
```

### 📊 比較表

| 項目            | try-catch なし       | try-catch あり     |
| --------------- | -------------------- | ------------------ |
| エラー時の挙動  | Laravel のデフォルト | 自分で制御可能     |
| メッセージ      | 英語の定型文         | 日本語カスタム可能 |
| HTTP ステータス | 500（不適切）        | 403（適切）        |
| エラーコード    | なし                 | 独自コード設定可能 |
| ログ記録        | デフォルトのみ       | 詳細なログ可能     |
| 後続処理        | できない             | 可能               |

👩‍💻「なるほど！でも全部に`try-catch`書くのは大変そう…」

🐘「せやから、**適材適所**や！何でもかんでも書く必要はないんや」

### 🎯 try-catch を使うべき場面

```
┌────────────────────────────────────────────────┐
│         try-catchを使うべき場面                │
├────────────────────────────────────────────────┤
│                                                │
│  ✅ 使うべき                                   │
│  ├── 権限チェックで特別な処理が必要            │
│  ├── ステータス遷移で特別なエラーメッセージ    │
│  ├── ユーザーへのメッセージをカスタムしたい時  │
│  ├── エラー後に後続処理が必要な時              │
│  └── 複数の例外を区別して処理したい            │
│                                                │
│  ❌ 不要な場面                                 │
│  ├── 単純なCRUD（デフォルトで十分）            │
│  ├── バリデーション（FormRequestに任せる）     │
│  └── 認証（ミドルウェアに任せる）              │
│                                                │
└────────────────────────────────────────────────┘
```

---

## 🚀 第 7 章：throw new とサービス層

👩‍💻「`throw new`って何ですか？自分でエラーを起こすってこと？」

🐘「せや！**throw**は**例外を投げる**という意味や。自分から『これは問題やで！』って叫ぶようなもんや」

### 🎪 例え話：工場の品質検査

```
【throwのイメージ】

🏭 タスク管理工場での品質検査

    📦 タスクの状態遷移を検査
         ↓
    🔍 不正な遷移を発見！
         ↓
    📢 「これはルール違反です！」と報告（throw）
         ↓
    🚨 処理ストップ → 対処へ


💡 プログラムも同じ！
   問題を見つけたら「throw」で報告する
```

| 工場の例え             | プログラムでは           |
| ---------------------- | ------------------------ |
| 不良品を発見           | ビジネスルール違反を検知 |
| 「不良品です！」と報告 | `throw new Exception()`  |
| どんな不良か伝える     | 例外クラスとメッセージ   |
| 対処部門に回す         | catch ブロックで処理     |

### 📝 サービス層での throw

```php
<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;

class TaskService
{
    /**
     * タスクを開始する（todo → doing）
     */
    public function startTask(Task $task, User $user): Task
    {
        // 1️⃣ 権限チェック
        $this->checkTaskPermission($task, $user);

        // 2️⃣ 状態チェック → 問題あればthrow！
        if ($task->status !== 'todo') {
            // 🚨 問題発見！例外を投げる！
            throw new \Exception('未着手のタスクのみ開始できます');
        }

        // 3️⃣ すべてOK → 状態を更新
        $task->update(['status' => 'doing']);
        $task->load('createdBy');

        return $task;
    }

    /**
     * タスクを完了する（doing → done）
     */
    public function completeTask(Task $task, User $user): Task
    {
        // 1️⃣ 権限チェック
        $this->checkTaskPermission($task, $user);

        // 2️⃣ 状態チェック → 問題あればthrow！
        if ($task->status !== 'doing') {
            // 🚨 問題発見！例外を投げる！
            throw new \Exception('作業中のタスクのみ完了できます');
        }

        // 3️⃣ すべてOK → 状態を更新
        $task->update(['status' => 'done']);
        $task->load('createdBy');

        return $task;
    }

    /**
     * プロジェクトのメンバーかチェック
     */
    private function isProjectMember(Project $project, User $user): bool
    {
        return $project->users()
            ->where('users.id', $user->id)
            ->exists();
    }

    /**
     * タスクに対する権限チェック
     */
    private function checkTaskPermission(Task $task, User $user): void
    {
        $project = $task->project;
        if (!$this->isProjectMember($project, $user)) {
            // 🚨 権限なし！例外を投げる！
            throw new \Exception('このプロジェクトにアクセスする権限がありません');
        }
    }
}
```

👩‍💻「自分で例外を作るってことは、カスタム例外クラスも作れるんですか？」

🐘「その通りや！さすがやな！」

### 📝 カスタム例外クラスの作成

```php
<?php

namespace App\Exceptions;

use Exception;

class InvalidTaskStatusException extends Exception
{
    protected $code = 409; // Conflict

    public function __construct(
        string $message = 'タスクの状態遷移が不正です',
        public readonly string $currentStatus = '',
        public readonly string $expectedStatus = ''
    ) {
        parent::__construct($message, $this->code);
    }

    /**
     * HTTPレスポンスとしてレンダリング
     */
    public function render()
    {
        return response()->json([
            'success' => false,
            'message' => $this->getMessage(),
            'error_code' => 'INVALID_TASK_STATUS',
            'details' => [
                'current_status' => $this->currentStatus,
                'expected_status' => $this->expectedStatus,
            ]
        ], $this->code);
    }
}
```

```php
<?php

namespace App\Exceptions;

use Exception;

class TaskPermissionDeniedException extends Exception
{
    protected $code = 403; // Forbidden

    public function __construct(
        string $message = 'このプロジェクトにアクセスする権限がありません',
        public readonly int $projectId = 0,
        public readonly int $userId = 0
    ) {
        parent::__construct($message, $this->code);
    }

    /**
     * HTTPレスポンスとしてレンダリング
     */
    public function render()
    {
        return response()->json([
            'success' => false,
            'message' => $this->getMessage(),
            'error_code' => 'TASK_PERMISSION_DENIED',
            'details' => [
                'project_id' => $this->projectId,
                'user_id' => $this->userId,
            ]
        ], $this->code);
    }
}
```

👩‍💻「`render()`メソッドがあると、Laravel が自動でこのレスポンスを返してくれるんですね！」

🐘「せや！Laravel は賢いから、例外クラスに`render()`があれば、それを使ってレスポンスを生成してくれるんや」

---

## 🎭 第 8 章：throw new と try-catch の組み合わせ

👩‍💻「`throw`と`try-catch`を組み合わせると、どういう流れになるんですか？」

🐘「よっしゃ！これが本日のメインディッシュや！🍽️」

### 📊 処理の流れ図

```
┌─────────────────────────────────────────────────────────────┐
│              throw と try-catch の連携フロー                │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  📥 Controller                                              │
│  ┌─────────────────────────────────────────────┐           │
│  │ try {                                        │           │
│  │     $task = $taskService->startTask();      │ ←───┐    │
│  │ }                                            │     │    │
│  │ catch (InvalidTaskStatusException $e) {      │     │    │
│  │     // 状態遷移エラーの処理                 │ ←─┐ │    │
│  │ }                                            │   │ │    │
│  │ catch (TaskPermissionDeniedException $e) {   │   │ │    │
│  │     // 権限エラーの処理                     │   │ │    │
│  │ }                                            │   │ │    │
│  │ catch (Exception $e) {                       │   │ │    │
│  │     // その他のエラー                        │   │ │    │
│  │ }                                            │   │ │    │
│  └─────────────────────────────────────────────┘   │ │    │
│                                                     │ │    │
│  🏭 Service                                         │ │    │
│  ┌─────────────────────────────────────────────┐   │ │    │
│  │ public function startTask() {                │   │ │    │
│  │                                              │   │ │    │
│  │     if ($task->status !== 'todo') {          │   │ │    │
│  │         throw new InvalidTaskStatusException │ ──┘ │    │
│  │     }                  ↑                     │     │    │
│  │                        │                     │     │    │
│  │     return $task; ─────┼─────────────────────┼─────┘    │
│  │ }                      │                     │          │
│  └────────────────────────┼─────────────────────┘          │
│                           │                                 │
│                    例外が上に                               │
│                    「バブルアップ」                         │
│                    していく                                 │
└─────────────────────────────────────────────────────────────┘
```

### 📝 実際のコード例

```php
<?php
// ========================================
// 🎯 カスタム例外クラス
// ========================================

namespace App\Exceptions;

use Exception;

class InvalidTaskStatusException extends Exception
{
    public function __construct(
        string $message,
        public readonly string $currentStatus,
        public readonly string $expectedStatus
    ) {
        parent::__construct($message, 409);
    }
}

class TaskPermissionDeniedException extends Exception
{
    public function __construct(
        string $message,
        public readonly int $projectId
    ) {
        parent::__construct($message, 403);
    }
}
```

```php
<?php
// ========================================
// 🏭 Service層（例外を投げる側）
// ========================================

namespace App\Services;

use App\Exceptions\InvalidTaskStatusException;
use App\Exceptions\TaskPermissionDeniedException;
use App\Models\Task;
use App\Models\User;

class TaskService
{
    public function startTask(Task $task, User $user): Task
    {
        // 1️⃣ 権限チェック → 問題あればthrow！
        $project = $task->project;
        if (!$this->isProjectMember($project, $user)) {
            throw new TaskPermissionDeniedException(
                message: "このプロジェクトにアクセスする権限がありません",
                projectId: $project->id
            );
        }

        // 2️⃣ 状態チェック → 問題あればthrow！
        if ($task->status !== 'todo') {
            throw new InvalidTaskStatusException(
                message: "未着手のタスクのみ開始できます",
                currentStatus: $task->status,
                expectedStatus: 'todo'
            );
        }

        // 3️⃣ すべてOK → タスクを開始
        $task->update(['status' => 'doing']);
        $task->load('createdBy');

        return $task;
    }

    public function completeTask(Task $task, User $user): Task
    {
        // 1️⃣ 権限チェック → 問題あればthrow！
        $project = $task->project;
        if (!$this->isProjectMember($project, $user)) {
            throw new TaskPermissionDeniedException(
                message: "このプロジェクトにアクセスする権限がありません",
                projectId: $project->id
            );
        }

        // 2️⃣ 状態チェック → 問題あればthrow！
        if ($task->status !== 'doing') {
            throw new InvalidTaskStatusException(
                message: "作業中のタスクのみ完了できます",
                currentStatus: $task->status,
                expectedStatus: 'doing'
            );
        }

        // 3️⃣ すべてOK → タスクを完了
        $task->update(['status' => 'done']);
        $task->load('createdBy');

        return $task;
    }

    private function isProjectMember($project, $user): bool
    {
        return $project->users()
            ->where('users.id', $user->id)
            ->exists();
    }
}
```

```php
<?php
// ========================================
// 📥 Controller（例外をキャッチする側）
// ========================================

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidTaskStatusException;
use App\Exceptions\TaskPermissionDeniedException;
use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    public function __construct(
        private TaskService $taskService
    ) {}

    /**
     * タスクを開始（todo → doing）
     */
    public function start(Request $request, Task $task): TaskResource|JsonResponse
    {
        try {
            // 🎯 サービス層の処理を呼び出し
            $task = $this->taskService->startTask($task, $request->user());

            // ✅ 成功時
            return (new TaskResource($task))
                ->additional(['message' => 'タスクを開始しました']);

        } catch (TaskPermissionDeniedException $e) {
            // 🔴 権限エラー
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error_code' => 'TASK_PERMISSION_DENIED',
                'details' => [
                    'project_id' => $e->projectId,
                ]
            ], 403);

        } catch (InvalidTaskStatusException $e) {
            // 🟠 状態遷移エラー
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error_code' => 'INVALID_TASK_STATUS',
                'details' => [
                    'current_status' => $e->currentStatus,
                    'expected_status' => $e->expectedStatus,
                ]
            ], 409);

        } catch (ModelNotFoundException $e) {
            // 🟡 タスクが見つからない
            return response()->json([
                'success' => false,
                'message' => '指定されたタスクが見つかりません',
                'error_code' => 'TASK_NOT_FOUND'
            ], 404);

        } catch (\Exception $e) {
            // 🔵 その他の予期せぬエラー
            Log::error('タスク開始処理で予期せぬエラー', [
                'task_id' => $task->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'システムエラーが発生しました',
                'error_code' => 'INTERNAL_ERROR'
            ], 500);
        }
    }

    /**
     * タスクを完了（doing → done）
     */
    public function complete(Request $request, Task $task): TaskResource|JsonResponse
    {
        try {
            // 🎯 サービス層の処理を呼び出し
            $task = $this->taskService->completeTask($task, $request->user());

            // ✅ 成功時
            return (new TaskResource($task))
                ->additional(['message' => 'タスクを完了しました']);

        } catch (TaskPermissionDeniedException $e) {
            // 🔴 権限エラー
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error_code' => 'TASK_PERMISSION_DENIED',
                'details' => [
                    'project_id' => $e->projectId,
                ]
            ], 403);

        } catch (InvalidTaskStatusException $e) {
            // 🟠 状態遷移エラー
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error_code' => 'INVALID_TASK_STATUS',
                'details' => [
                    'current_status' => $e->currentStatus,
                    'expected_status' => $e->expectedStatus,
                ]
            ], 409);

        } catch (ModelNotFoundException $e) {
            // 🟡 タスクが見つからない
            return response()->json([
                'success' => false,
                'message' => '指定されたタスクが見つかりません',
                'error_code' => 'TASK_NOT_FOUND'
            ], 404);

        } catch (\Exception $e) {
            // 🔵 その他の予期せぬエラー
            Log::error('タスク完了処理で予期せぬエラー', [
                'task_id' => $task->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'システムエラーが発生しました',
                'error_code' => 'INTERNAL_ERROR'
            ], 500);
        }
    }
}
```

### 📊 各シナリオの挙動

| シナリオ     | Service 層                 | Controller     | レスポンス         |
| ------------ | -------------------------- | -------------- | ------------------ |
| 正常         | 処理完了 → return          | 成功レスポンス | 200 OK             |
| 権限なし     | throw TaskPermissionDenied | 1 番目の catch | 403 Forbidden      |
| 状態不正     | throw InvalidTaskStatus    | 2 番目の catch | 409 Conflict       |
| タスクなし   | ModelNotFoundException     | 3 番目の catch | 404 Not Found      |
| その他エラー | throw Exception            | 最後の catch   | 500 Internal Error |

👩‍💻「catch の順番も大事なんですね！」

🐘「せや！**具体的な例外から順番に書く**のがポイントや。`Exception`を最初に書いたら、全部そこでキャッチされてまうからな」

```php
// ❌ ダメな例（Exceptionが先）
try {
    // ...
} catch (Exception $e) {
    // すべてここに来てしまう！
} catch (InvalidTaskStatusException $e) {
    // ここには絶対来ない！
}

// ✅ 良い例（具体的な例外から）
try {
    // ...
} catch (TaskPermissionDeniedException $e) {
    // 権限エラー専用の処理
} catch (InvalidTaskStatusException $e) {
    // 状態エラー専用の処理
} catch (Exception $e) {
    // その他すべて（最後のセーフティネット）
}
```

---

## 🌐 第 9 章：グローバル例外ハンドラー

👩‍💻「全部のコントローラーに`try-catch`書くのって大変じゃないですか？」

🐘「ええとこに気づいたな！そこで登場するのが**グローバル例外ハンドラー**や！」

### 📝 bootstrap/app.php での設定

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use App\Http\Responses\ApiResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use App\Exceptions\InvalidTaskStatusException;
use App\Exceptions\TaskPermissionDeniedException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // ミドルウェア設定
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // 🎯 グローバル例外ハンドラー
        $response = new ApiResponse();

        // 404エラー
        $exceptions->render(function (NotFoundHttpException $e, Request $request) use ($response) {
            if ($request->is('api/*')) {
                return $response->notFound('リソースが見つかりません');
            }
        });

        // バリデーションエラー
        $exceptions->render(function (ValidationException $e, Request $request) use ($response) {
            if ($request->is('api/*')) {
                return $response->validationError('入力内容に誤りがあります', $e->errors());
            }
        });

        // 認証エラー
        $exceptions->render(function (AuthenticationException $e, Request $request) use ($response) {
            if ($request->is('api/*')) {
                return $response->unauthorized('認証が必要です');
            }
        });

        // タスク権限エラー
        $exceptions->render(function (TaskPermissionDeniedException $e, Request $request) use ($response) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'error_code' => 'TASK_PERMISSION_DENIED',
                ], 403);
            }
        });

        // タスク状態エラー
        $exceptions->render(function (InvalidTaskStatusException $e, Request $request) use ($response) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'error_code' => 'INVALID_TASK_STATUS',
                ], 409);
            }
        });

        // その他すべての例外（最後のセーフティネット）
        $exceptions->render(function (Throwable $e, Request $request) use ($response) {
            if ($request->is('api/*')) {
                // 本番では固定メッセージ、開発中は詳細表示
                $message = config('app.debug') ? $e->getMessage() : 'サーバーエラー';
                return $response->serverError($message);
            }
        });
    })
    ->create();
```

### 📊 グローバルハンドラーのメリット

```
┌──────────────────────────────────────────────────────┐
│         グローバル例外ハンドラーのメリット           │
├──────────────────────────────────────────────────────┤
│                                                      │
│  ✅ メリット                                         │
│  ├── すべてのAPIで統一されたエラーレスポンス         │
│  ├── 各コントローラーでtry-catchを書かなくて済む     │
│  ├── エラーハンドリングを一箇所で管理できる          │
│  └── 予期せぬエラーも必ずキャッチできる              │
│                                                      │
│  ⚠️ 注意点                                           │
│  ├── 細かい制御が必要な場合はControllerでcatch       │
│  ├── 例外の順番が重要（具体的 → 抽象的）            │
│  └── APIとWeb両方で使う場合は分岐が必要              │
│                                                      │
└──────────────────────────────────────────────────────┘
```

👩‍💻「なるほど！グローバルハンドラーがあれば、基本的なエラーは全部そこで処理できるんですね」

🐘「せや！でも、**細かい制御が必要な場合**はコントローラーで`try-catch`を書くのがええで」

---

## 🎓 第 10 章：まとめ

🐘「よっしゃ、今日の内容をまとめたるわ！」

### 📊 全体像

```
┌──────────────────────────────────────────────────────────────┐
│                   エラーハンドリング全体像                   │
├──────────────────────────────────────────────────────────────┤
│                                                              │
│  🎯 エラーハンドリング = 問題に備える仕組み                  │
│                                                              │
│  ┌────────────────────────────────────────────────────────┐ │
│  │ エラーの種類                                           │ │
│  │  🔴 文法エラー → コードを直す                          │ │
│  │  🟠 実行時エラー → try-catchで対処                     │ │
│  │  🟡 業務エラー → throw newで明示的に投げる             │ │
│  └────────────────────────────────────────────────────────┘ │
│                                                              │
│  ┌────────────────────────────────────────────────────────┐ │
│  │ 役割分担                                               │ │
│  │                                                        │ │
│  │  🏭 Service層（TaskService）                           │ │
│  │  └── ビジネスルール違反を検知 → throw new             │ │
│  │                                                        │ │
│  │  📥 Controller層（TaskController）                     │ │
│  │  └── try-catchで例外をキャッチ → レスポンス生成       │ │
│  │                                                        │ │
│  │  📋 グローバルハンドラー（bootstrap/app.php）          │ │
│  │  └── キャッチされなかった例外を最終処理               │ │
│  └────────────────────────────────────────────────────────┘ │
│                                                              │
└──────────────────────────────────────────────────────────────┘
```

### 📝 覚えておくべきポイント

| 項目                     | ポイント                         |
| ------------------------ | -------------------------------- |
| **try-catch**            | 例外を捕まえるセーフティネット   |
| **throw new**            | 問題を発見したら例外を投げて報告 |
| **カスタム例外**         | 業務エラーを明確に表現できる     |
| **catch の順番**         | 具体的なものから順に書く         |
| **グローバルハンドラー** | 最後の砦、漏れを防ぐ             |
| **APP_DEBUG**            | 本番では必ず`false`に！          |

👩‍💻「わかりやすかったです！でも、全部のメソッドに`try-catch`書くべきですか？」

🐘「いい質問やな！答えは**NO**や。必要なところだけでええんや」

```
┌──────────────────────────────────────────────────────┐
│           try-catch を書く/書かない判断基準          │
├──────────────────────────────────────────────────────┤
│                                                      │
│  ✅ 書くべき場面（このシステムで）                    │
│  ├── タスクのステータス遷移処理                      │
│  ├── プロジェクトメンバーの権限チェック              │
│  ├── ユーザーへのメッセージをカスタムしたい          │
│  ├── エラー後に後続処理（ロールバック等）が必要      │
│  └── 複数の例外を区別して処理したい                  │
│                                                      │
│  ❌ 不要な場面（グローバルハンドラーに任せる）        │
│  ├── 単純なタスク一覧取得（CRUDのR）                 │
│  ├── バリデーション（FormRequestに任せる）           │
│  └── 認証（ミドルウェアに任せる）                    │
│                                                      │
│  💡 迷ったら                                         │
│  └── まずはグローバルハンドラーで統一処理            │
│      必要に応じてControllerでtry-catchを追加         │
│                                                      │
└──────────────────────────────────────────────────────┘
```

### 📝 実装チェックリスト

```
┌──────────────────────────────────────────────────────┐
│           エラーハンドリング実装チェック             │
├──────────────────────────────────────────────────────┤
│                                                      │
│  □ 本番環境で APP_DEBUG=false になっているか         │
│  □ グローバル例外ハンドラーを設定したか              │
│  □ カスタム例外クラスを作成したか                    │
│  │  ├── InvalidTaskStatusException                  │
│  │  └── TaskPermissionDeniedException               │
│  □ Serviceで適切にthrow newしているか                │
│  │  ├── 権限チェック                                │
│  │  ├── ステータス遷移チェック                      │
│  │  └── ビジネスルール違反チェック                  │
│  □ Controllerでtry-catchしているか                   │
│  │  （必要な場合のみ）                              │
│  □ エラーログを記録しているか                        │
│  │  └── Log::error() で詳細を記録                   │
│  □ 適切なHTTPステータスコードを返しているか          │
│  │  ├── 403: 権限エラー                            │
│  │  ├── 404: リソースなし                          │
│  │  ├── 409: 状態競合                              │
│  │  └── 500: サーバーエラー                        │
│  □ エラーレスポンスにerror_codeを含めているか        │
│  □ フロントエンドが処理しやすい形式か                │
│                                                      │
└──────────────────────────────────────────────────────┘
```

---

## 🎬 エピローグ

🐘「どうや？エラーハンドリング、理解できたか？」

👩‍💻「はい！例外を『投げる側（Service）』と『捕まえる側（Controller）』の役割分担がよくわかりました。あと、最初にクライアントさんに表示されてた画面は、`APP_DEBUG=true`が原因だったんですね」

🐘「せやな。ワシの教え子のアインシュタインくんも言うとったで。『すべてを予測することはできへん。でも、予測できへんことへの備えはできる』ってな」

👩‍💻「…それ絶対アインシュタインさん言ってないですよね？」

🐘「…まぁ、言うてそうやない？💦 大事なのは本質や！**エラーは起きる。だから備える。**これがプロの仕事や！」

👩‍💻「名言風にごまかそうとしてますね…でも、内容は納得です！早速実装してきます！」

🐘「おう！次回教えて欲しかったら、あんみつ持ってくるんやで！🍨 **さすガネーシャや！**」
