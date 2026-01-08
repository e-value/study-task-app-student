# 💀 本番環境でのエラー露出実践演習

## 〜APP_DEBUG=true の恐怖を体験する〜

---

## 🎬 プロローグ：納品から 2 週間後、突然の電話

👩‍💻「ふぅ...システム納品してから 2 週間。クライアントさんからも好評で順調です！🎉」

🐘「おお、ええことやな！エラーハンドリングもちゃんと学んだし、もう怖いもんなしやろ？」

👩‍💻「はい！でも...実は昨日、タスク機能を少し修正したんです」

🐘「ほう？どんな修正や？」

👩‍💻「タスクの作成者情報の表示を改善しようと思って...」

🐘「それで？」

👩‍💻「その時に誤って ProjectService.php も開いちゃって...保存した気がするんです」

🐘「...まさか」

📞 **プルルルル...プルルルル...** ☎️

👩‍💻「あ、クライアントさんから電話です...」

🐘「嫌な予感がするで...」

---

👩‍💻「お電話ありがとうございます！」

📞 **クライアント**：「あの...急ぎの用件なんですけど...プロジェクトのページを開いたら、変な英語の文字がいっぱい出てきて...😰」

👩‍💻「え...？でも、これまで 2 週間問題なく使えてたはずでは...」

📞 **クライアント**：「スクリーンショット送りますね...何かエラーって書いてあるんですけど...」

🐘「（やっぱりや...）」

---

**📧 受信した画像**

```
┌─────────────────────────────────────────────────────────────────┐
│  🔴 クライアントに見えている画面                                │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  ❌ プロジェクト詳細の読み込みに失敗しました                     │
│                                                                 │
│  （でも、デベロッパーツールを開くと...）                         │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

👩‍💻「え...どういうことですか？画面には普通のエラーメッセージしか出てないみたいですけど...」

🐘「デベロッパーツールを開いてもらったんか？」

📞 **クライアント**：「実は、社内のエンジニアが『開発者ツールで見てみましょう』って言って確認したんです...そしたら...」

---

**📧 2通目の画像（Network タブのスクリーンショット）**

```json
{
  "message": "Call to undefined relationship [usrs] on model [App\\Models\\Project].",
  "exception": "BadMethodCallException",
  "file": "/var/www/html/app/Services/ProjectService.php",
  "line": 53,
  "trace": [...]
}
```

🐘「**あちゃー！やってもうたな！**」

👩‍💻「え...え...ええええ！？😱 内部情報が丸見えじゃないですか！」

📞 **クライアント**：「セキュリティ担当から『これは情報漏洩のリスクがある』って指摘されて...大丈夫なんでしょうか？」

👩‍💻「す、すみません！すぐに確認して修正します！」

---

🐘「電話切ったか？」

👩‍💻「はい...でも、どうしてこんなことに...私、エラーハンドリングちゃんと勉強したのに...」

🐘「落ち着きや。原因は2つある」

```
🚨 今回の事故の原因

1️⃣ ProjectService.php で users → usrs にタイポしてしまった
   └─ 昨日の修正時に誤って変更してしまった

2️⃣ 本番環境で APP_DEBUG=true のまま運用していた
   └─ これが致命的！内部情報が全て露出してしまう
```

👩‍💻「APP_DEBUG...そういえば、納品前に false にするの忘れてました...」

🐘「**それが命取りや！**今から、この事故を再現して、何が危険で、どう対処すべきかを学ぶで」

👩‍💻「お願いします！」

---

## 📚 この教材で学べること

✅ APP_DEBUG=true の危険性を**実際に体験**する  
✅ クライアント画面（Network タブ）でエラーが丸見えになる状態を確認  
✅ Console タブでもエラー詳細が露出することを確認  
✅ どんな情報が漏洩するのか、セキュリティリスクを理解  
✅ 安全なエラーハンドリングの実装方法を習得  
✅ 本番環境と開発環境の適切な設定を学ぶ

---

## ⚠️ 注意事項

```
この演習は開発環境でのみ実施してください。
実際の本番環境では絶対に試さないでください！

また、演習が終わったら必ず元の設定に戻してください。
```

---

# 第 1 章：エラーが丸見えになる状態を体験する

🐘「さて、まずは**本番環境で APP_DEBUG=true にしてしまった時の危険性**を体験するで」

👩‍💻「はい！」

---

## 🎯 演習 1：現在の状態を確認する

### 📝 Step 1-1：APP_DEBUG の設定を確認

**ターミナルで実行：**

```bash
grep APP_DEBUG .env
```

**期待される結果：**

```bash
APP_DEBUG=true  # 開発環境なので true
```

🐘「開発中は`true`でええんや。でもな、これを**本番環境で true にしたまま運用**すると...今から見せるような地獄が待っとる」

---

### 📝 Step 1-2：例外ハンドラーの状態を確認

**開くファイル：** `bootstrap/app.php`

**29〜42行目を確認：**

```php
// 研修用に必要に応じて以下のコメントアウトの切り替え
// （コメントアウト時はLaravelのデフォルトハンドラーが使用される）

// // API例外ハンドラーを登録
// $apiHandler = new ApiExceptionHandler();

// $exceptions->render(function (\Throwable $e, Request $request) use ($apiHandler) {
//     return $apiHandler->handle($e, $request);
// });
```

🐘「今はコメントアウトされとるな？これは**Laravel のデフォルトハンドラー**が動いとる状態や」

👩‍💻「デフォルトハンドラーだと何が問題なんですか？」

🐘「`APP_DEBUG=true` の時は、**エラーの詳細が全部ユーザーに見える**んや」

---

### 📝 Step 1-3：ProjectService.php のタイポを確認

**開くファイル：** `app/Services/ProjectService.php`

**53行目を確認：**

```php
// リレーションをロード
// ⚠️ ERROR_HANDLING_LESSON用：タイポを意図的に作成
// タスク機能の修正中に誤って users → usrs に変更してしまった想定
$project->load(['usrs', 'tasks.createdBy']);
```

🐘「見てみ？`users` が `usrs` になっとるやろ？これが**昨日の修正で誤って入れてしまったタイポ**や」

👩‍💻「たった1文字...でもこれがエラーの原因なんですね」

🐘「せや。でも問題はタイポじゃない。**タイポは誰にでも起こる**。問題は、**そのエラーが丸見えになってしまうこと**や」

---

## 🔴 演習 2：クライアント画面でエラーが丸見えになる状態を確認

### 📝 Step 2-1：ブラウザでプロジェクト詳細ページを開く

**ブラウザで以下のURLにアクセス：**

```
http://localhost/projects/1
```

👩‍💻「開きました！」

---

### 📝 Step 2-2：デベロッパーツールを開く

**開き方：**

```
Mac: Cmd + Option + I
Windows: F12 または 右クリック → 検証
```

🐘「**Console タブと Network タブ、両方開いとく**んやで」

---

### 📝 Step 2-3：ページをリロード

**強制リロード：**

```
Mac: Cmd + Shift + R
Windows: Ctrl + Shift + R
```

👩‍💻「リロードしました！」

🐘「さて、何が表示されとる？」

👩‍💻「画面が真っ白で...プロジェクト詳細が表示されていません。トーストで『プロジェクト詳細の読み込みに失敗しました』って出ました」

🐘「せやろ？でもな、**Network タブを見てみ**。そこに恐ろしいものが隠れとる」

---

### 🚨 Step 2-4：Network タブで内部情報が露出していることを確認

**操作手順：**

```
1. Network タブをクリック
2. 「projects」という名前のリクエストを探す
   （XHR または Fetch でフィルターすると見つけやすい）
3. クリックして「Response」タブを見る
```

👩‍💻「あ...！これが表示されてます...」

**Network タブ > Response に表示される内容：**

```json
{
  "message": "Call to undefined relationship [usrs] on model [App\\Models\\Project].",
  "exception": "BadMethodCallException",
  "file": "/var/www/html/app/Services/ProjectService.php",
  "line": 53,
  "trace": [
    {
      "file": "/var/www/html/app/Services/ProjectService.php",
      "line": 53,
      "function": "__call",
      "class": "Illuminate\\Database\\Eloquent\\Model",
      "type": "->"
    },
    {
      "file": "/var/www/html/app/Http/Controllers/Api/ProjectController.php",
      "line": 67,
      "function": "getProject",
      "class": "App\\Services\\ProjectService",
      "type": "->"
    },
    {
      "file": "/var/www/html/vendor/laravel/framework/src/Illuminate/Routing/Controller.php",
      "line": 54,
      "function": "show",
      "class": "App\\Http\\Controllers\\Api\\ProjectController",
      "type": "->"
    },
    {
      "file": "/var/www/html/vendor/laravel/framework/src/Illuminate/Routing/ControllerDispatcher.php",
      "line": 43,
      "function": "callAction"
    }
    // ... さらに30個以上のスタックトレースが続く
  ]
}
```

👩‍💻「うわぁ...ファイルのパスとか、行番号とか、全部見えてます...😱」

🐘「**これが本番環境でクライアントに見えてしまう状態や！**」

---

### 💀 Step 2-5：何が露出しているのか？詳しく見てみよう

🐘「Network タブの Response を見ながら、何が漏れとるか確認するで」

**露出している危険な情報：**

```
🚨 露出している情報（Network タブ）

【基本情報】
├─ ✗ エラーメッセージ: "Call to undefined relationship [usrs]..."
├─ ✗ 例外クラス名: BadMethodCallException
└─ ✗ モデル名: App\Models\Project

【ファイル情報】
├─ ✗ 絶対パス: /var/www/html/app/Services/ProjectService.php
├─ ✗ エラーが発生した行番号: 53
└─ ✗ ファイル構造: app/Services/, app/Http/Controllers/, vendor/laravel/

【スタックトレース（38個！）】
├─ ✗ 関数呼び出しの順番が全て分かる
├─ ✗ 内部実装の流れが推測できる
├─ ✗ Laravel のバージョンが推測できる
└─ ✗ MVC の構造が丸わかり
```

👩‍💻「こんなに情報が...」

🐘「これがあれば、ハッカーは**攻撃の糸口**をいくらでも見つけられるで」

---

## 🔍 演習 3：Console タブでもエラーが丸見えになっている状態を確認

### 📝 Step 3-1：Console タブを開く

**操作手順：**

```
デベロッパーツールの上部にある「Console」タブをクリック
```

👩‍💻「Console タブを開きました！」

---

### 📝 Step 3-2：Console に表示されているエラーログを確認

**Console タブに表示される内容：**

```
🚀 fetchProject が呼ばれたで！
📍 プロジェクトID: 1
📡 APIリクエストを送信するで： /api/projects/1

❌ エラーが発生したで！

🔍 エラーオブジェクト全体: ▶︎ AxiosError
  message: "Request failed with status code 500"
  name: "AxiosError"
  code: "ERR_BAD_RESPONSE"
  config: {transitional: {...}, adapter: Array(3), ...}
  request: XMLHttpRequest {...}
  response: ▶︎ {data: {...}, status: 500, ...}

📊 エラーレスポンス: ▶︎ {data: {...}, status: 500, statusText: 'Internal Server Error', ...}
  config: {...}
  data: ▼ {message: 'Call to undefined relationship [usrs]...', exception: 'BadMethodCallException', file: '/var/www/html/app/Services/ProjectService.php', line: 53, trace: Array(38)}
    exception: "BadMethodCallException"
    file: "/var/www/html/app/Services/ProjectService.php"  ← ファイルパスが丸見え！
    line: 53  ← 行番号まで分かる！
    message: "Call to undefined relationship [usrs] on model [App\\Models\\Project]."
    trace: ▼ Array(38)  ← スタックトレースが38個も！
      0: ▼ {file: '/var/www/html/app/Services/ProjectService.php', line: 53, ...}
        class: "Illuminate\\Database\\Eloquent\\Model"
        file: "/var/www/html/app/Services/ProjectService.php"
        function: "__call"
        line: 53
        type: "->"
      1: ▼ {file: '/var/www/html/app/Http/Controllers/Api/ProjectController.php', line: 67, ...}
      2: {file: '/var/www/html/vendor/laravel/framework/src/Illuminate/Routing/Controller.php', line: 54, ...}
      ... (さらに続く)
  headers: AxiosHeaders {...}
  request: XMLHttpRequest {...}
  status: 500
  statusText: "Internal Server Error"

📋 ステータスコード: 500

💬 エラーデータ: ▼ {message: 'Call to undefined relationship [usrs]...', exception: 'BadMethodCallException', file: '/var/www/html/app/Services/ProjectService.php', line: 53, trace: Array(38)}

🏁 fetchProject 終了！
```

👩‍💻「Console にも全く同じエラー情報が表示されてます...」

---

### 📝 Step 3-3：trace（スタックトレース）を展開してみる

**操作手順：**

```
1. Console タブの「trace: ▶︎ Array(38)」をクリック
2. 展開された配列の中の「0: ▶︎」をクリック
3. さらに詳細が表示される
```

**展開した内容：**

```
trace: ▼ Array(38)
  0: ▼ {file: '/var/www/html/app/Services/ProjectService.php', line: 53, ...}
    class: "Illuminate\\Database\\Eloquent\\Model"
    file: "/var/www/html/app/Services/ProjectService.php"
    function: "__call"
    line: 53
    type: "->"
  1: ▼ {file: '/var/www/html/app/Http/Controllers/Api/ProjectController.php', line: 67, ...}
    class: "App\\Services\\ProjectService"
    file: "/var/www/html/app/Http/Controllers/Api/ProjectController.php"
    function: "getProject"
    line: 67
    type: "->"
  2: ▼ {file: '/var/www/html/vendor/laravel/framework/src/Illuminate/Routing/Controller.php', line: 54, ...}
  ... (全部で38個のスタックトレース)
```

👩‍💻「わぁ...関数呼び出しの流れが全部見えます...」

🐘「せや。これが**Laravel の内部実装**や。普通は見せたくないもんやろ？」

---

### 🚨 Step 3-4：Console タブで露出している情報をまとめる

**露出している危険な情報（Console タブ）：**

```
🚨 Console タブで露出している情報

【CONSOLE_LOG_LESSON で仕込んだログ】
├─ ✓ これは問題なし（開発者用のデバッグ情報）
└─ ✓ console.log, console.error は開発者しか見ない前提

【問題なのは err.response の中身】
├─ ✗ ファイルパスが丸見え
├─ ✗ 行番号が分かる
├─ ✗ スタックトレース38個が全て見える
├─ ✗ Laravel の内部構造が推測できる
├─ ✗ 例外クラス名から Laravel のバージョンが推測できる
└─ ✗ MVC の構造が丸わかり
```

🐘「console.log 自体は問題やない。問題は、**Laravel が返すエラーレスポンスに内部情報が含まれとる**ことや」

👩‍💻「つまり、`APP_DEBUG=true` が原因で、API レスポンスに詳細なエラー情報が含まれてしまっているんですね」

🐘「**その通りや！**」

---

## 💀 演習 4：セキュリティリスクを理解する

### 📝 Step 4-1：ハッカーの視点で考えてみる

🐘「さて、もしお前がハッカーやったら、この情報を見てどう思う？」

👩‍💻「えっと...攻撃の手がかりがたくさんありますね...」

🐘「具体的に何ができるか、リストアップしてみよか」

---

### 🐛 ハッカーができること

```
🐛 この情報があれば、ハッカーはこんなことができる

1️⃣ システムの構造を把握
   ├─ Laravel を使っていることが確定
   ├─ /var/www/html がドキュメントルート
   ├─ app/Services/ProjectService.php が存在
   ├─ app/Http/Controllers/Api/ProjectController.php が存在
   └─ MVC アーキテクチャを採用

2️⃣ Laravel のバージョンを推測
   ├─ vendor/laravel/framework のパスから推測可能
   ├─ Eloquent\Model の __call メソッドを使用
   └─ 既知の脆弱性がないか調査できる

3️⃣ データベース構造を推測
   ├─ Project モデルが存在
   ├─ users リレーション（usrs のタイポから推測）
   ├─ tasks リレーションが存在
   ├─ createdBy リレーションが存在
   └─ 中間テーブルの存在も推測可能

4️⃣ API エンドポイントの構造を把握
   ├─ /api/projects/1 が存在
   ├─ RESTful な設計
   ├─ ProjectController の show メソッド
   └─ 他のエンドポイントも同様の構造と推測

5️⃣ 認証・認可の仕組みを推測
   ├─ ProjectService で権限チェックをしている可能性
   ├─ isProjectMember のようなメソッドが存在しそう
   └─ 権限チェックをバイパスする方法を探せる

6️⃣ 攻撃の糸口を見つける
   ├─ SQLインジェクションが可能か試す
   ├─ XSS攻撃が可能か試す
   ├─ CSRF攻撃が可能か試す
   ├─ 権限昇格の脆弱性がないか探る
   └─ 他のエンドポイントでも同様のエラーを誘発できないか試す

7️⃣ 継続的な攻撃の計画
   ├─ 定期的にエラーを誘発させて情報収集
   ├─ 他のユーザーのデータを盗めないか試す
   ├─ 管理者権限を奪えないか試す
   └─ システム全体を乗っ取る計画を立てる
```

👩‍💻「こ、怖すぎます...😱 たった1つのエラーメッセージから、ここまで分かるんですか？」

🐘「せや。**情報漏洩は積み重なる**んや。1つ1つは小さくても、組み合わせると**システム全体が丸裸**になるで」

---

### 📊 情報漏洩のリスクレベル

```
📊 露出している情報のリスクレベル

| 情報 | リスクレベル | 説明 |
|:-----|:----------:|:-----|
| ファイルの絶対パス | 🔴 高 | サーバーの構造が分かる |
| エラーが発生した行番号 | 🔴 高 | 脆弱性のある箇所が特定できる |
| 例外クラス名 | 🟡 中 | Laravel のバージョンが推測できる |
| モデル名 | 🟡 中 | データベース構造が推測できる |
| リレーション名 | 🟡 中 | テーブル間の関係が分かる |
| スタックトレース | 🔴 高 | 内部実装が丸わかり |
| フレームワークのパス | 🟡 中 | ライブラリのバージョンが推測できる |

総合リスク: 🔴🔴🔴 極めて高い
```

---

## 📸 演習 5：証拠を残す（スクリーンショット）

🐘「ここで、**クライアントに説明するための証拠**を残しておくんや」

👩‍💻「証拠...ですか？」

🐘「せや。ビフォー・アフターを見せるためにな」

---

### 📝 Step 5-1：Network タブのスクリーンショットを撮る

**撮影手順：**

```
1. Network タブを開く
2. 「projects」のリクエストをクリック
3. 「Response」タブを表示
4. エラーの詳細が見える状態でスクリーンショット
```

**ファイル名の例：** `before_network_tab.png`

---

### 📝 Step 5-2：Console タブのスクリーンショットを撮る

**撮影手順：**

```
1. Console タブを開く
2. エラーログが見える状態でスクリーンショット
3. できれば trace も展開した状態で撮る
```

**ファイル名の例：** `before_console_tab.png`

---

🐘「これを後でクライアントに見せながら、『APP_DEBUG=false にしたら、こんなに安全になりました』って説明するんや」

👩‍💻「なるほど！」

---

# 第 2 章：安全な状態にする（セキュリティ対策）

🐘「さて、恐怖体験は終わりや。次は**安全な状態**にするで！」

👩‍💻「お願いします！」

---

## 🛡️ 演習 6：APP_DEBUG を false にする

### 📝 Step 6-1：.env ファイルを開く

**開くファイル：** `.env`

---

### 📝 Step 6-2：APP_DEBUG を false に変更

**修正箇所：**

```bash
# 修正前（危険な状態）
APP_DEBUG=true

# 修正後（安全な状態）
APP_DEBUG=false
```

**保存：** `Cmd + S` (Mac) / `Ctrl + S` (Windows)

---

### 📝 Step 6-3：設定キャッシュをクリア

🐘「.env を変更したら、必ずキャッシュをクリアするんやで」

**ターミナルで実行：**

```bash
php artisan config:clear
```

**表示される内容：**

```
Configuration cache cleared successfully.
```

👩‍💻「クリアしました！」

🐘「よっしゃ！でもな、まだ終わりやない。次は**API例外ハンドラー**を有効にするで」

---

## 🛡️ 演習 7：API例外ハンドラーを有効にする

### 📝 Step 7-1：bootstrap/app.php を開く

**開くファイル：** `bootstrap/app.php`

---

### 📝 Step 7-2：コメントアウトを外す

**29〜42行目を修正：**

```php
// 修正前（コメントアウトされている）
// 研修用に必要に応じて以下のコメントアウトの切り替え
// （コメントアウト時はLaravelのデフォルトハンドラーが使用される）



// // API例外ハンドラーを登録
// $apiHandler = new ApiExceptionHandler();

// $exceptions->render(function (\Throwable $e, Request $request) use ($apiHandler) {
//     return $apiHandler->handle($e, $request);
// });
```

**修正後（コメントアウトを外す）：**

```php
// 研修用に必要に応じて以下のコメントアウトの切り替え
// （コメントアウト時はLaravelのデフォルトハンドラーが使用される）

// API例外ハンドラーを登録
$apiHandler = new ApiExceptionHandler();

$exceptions->render(function (\Throwable $e, Request $request) use ($apiHandler) {
    return $apiHandler->handle($e, $request);
});
```

**保存：** `Cmd + S` (Mac) / `Ctrl + S` (Windows)

---

🐘「これで、カスタム例外ハンドラーが有効になるで。このハンドラーは**内部情報を隠して、安全なメッセージだけ返す**んや」

👩‍💻「分かりました！」

---

## ✅ 演習 8：安全な状態になったことを確認する

### 📝 Step 8-1：ブラウザをリロード

**強制リロード：**

```
Mac: Cmd + Shift + R
Windows: Ctrl + Shift + R
```

👩‍💻「リロードしました！」

🐘「さて、Network タブを見てみ」

---

### 📝 Step 8-2：Network タブで安全なレスポンスを確認

**操作手順：**

```
1. Network タブを開く
2. 「projects」のリクエストをクリック
3. 「Response」タブを見る
```

**Network タブ > Response に表示される内容：**

```json
{
  "success": false,
  "message": "サーバーエラー",
  "request_id": "req_65a1b2c3d4e5f6a7b8c9"
}
```

👩‍💻「あ！今度はシンプルなメッセージになりました！」

🐘「せやろ！**内部情報が一切表示されてない**やろ？」

---

### ✅ 何が変わったのか？

```
✅ 安全なレスポンス

├─ ✓ シンプルなメッセージのみ: "サーバーエラー"
├─ ✓ ファイルパスは非表示
├─ ✓ 行番号は非表示
├─ ✓ スタックトレースは非表示
├─ ✓ 例外クラス名は非表示
└─ ✓ request_id でエラー追跡が可能
```

---

### 📝 Step 8-3：Console タブで安全なログを確認

**Console タブの表示内容：**

```
🚀 fetchProject が呼ばれたで！
📍 プロジェクトID: 1
📡 APIリクエストを送信するで： /api/projects/1

❌ エラーが発生したで！

🔍 エラーオブジェクト全体: ▶︎ AxiosError {message: 'Request failed with status code 500', ...}

📊 エラーレスポンス: ▶︎ {data: {success: false, message: 'サーバーエラー', request_id: 'req_...'}, status: 500, ...}
  config: {...}
  data: ▼ {success: false, message: 'サーバーエラー', request_id: 'req_65a1b2c3d4e5f6a7b8c9'}
    message: "サーバーエラー"  ← シンプルなメッセージのみ！
    request_id: "req_65a1b2c3d4e5f6a7b8c9"  ← エラー追跡用ID
    success: false
  status: 500
  statusText: "Internal Server Error"

📋 ステータスコード: 500

💬 エラーデータ: {success: false, message: 'サーバーエラー', request_id: 'req_65a1b2c3d4e5f6a7b8c9'}

🏁 fetchProject 終了！
```

👩‍💻「Console にもシンプルなメッセージだけ表示されてます！ファイルパスもスタックトレースも出ていません！」

🐘「**これが安全な状態や！**」

---

### 📝 Step 8-4：スクリーンショットを撮る（After）

**撮影手順：**

```
1. Network タブの安全なレスポンスをスクリーンショット
2. Console タブの安全なログをスクリーンショット
```

**ファイル名の例：**

-   `after_network_tab.png`
-   `after_console_tab.png`

---

## 📊 演習 9：Before / After を比較する

🐘「ビフォー・アフターを並べて比較してみよか」

---

### 📊 Network タブの比較

```
┌─────────────────────────────────────────────────────────────┐
│              Before（APP_DEBUG=true）                       │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  {                                                          │
│    "message": "Call to undefined relationship [usrs]...",  │  ← 詳細すぎる
│    "exception": "BadMethodCallException",                   │  ← 内部情報
│    "file": "/var/www/html/app/Services/ProjectService.php",│  ← 危険！
│    "line": 53,                                              │  ← 危険！
│    "trace": [...]  // 38個のスタックトレース                │  ← 危険！
│  }                                                          │
│                                                             │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│        After（APP_DEBUG=false + 例外ハンドラー）            │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  {                                                          │
│    "success": false,                                        │
│    "message": "サーバーエラー",                              │  ← 安全！
│    "request_id": "req_65a1b2c3d4e5f6a7b8c9"                 │  ← 追跡可能！
│  }                                                          │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

---

### 📊 Console タブの比較

```
┌─────────────────────────────────────────────────────────────┐
│              Before（APP_DEBUG=true）                       │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  💬 エラーデータ: ▼ {message: '...', exception: '...', ... }│
│    exception: "BadMethodCallException"                      │
│    file: "/var/www/html/app/Services/ProjectService.php"   │  ← 危険！
│    line: 53                                                 │  ← 危険！
│    message: "Call to undefined relationship [usrs]..."      │
│    trace: Array(38)  ← 38個のスタックトレース               │  ← 危険！
│                                                             │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│        After（APP_DEBUG=false + 例外ハンドラー）            │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  💬 エラーデータ: {success: false, message: '...', ...}     │
│    message: "サーバーエラー"  ← シンプル！                   │  ← 安全！
│    request_id: "req_..."      ← 追跡用ID                    │  ← 便利！
│    success: false                                           │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

👩‍💻「全然違いますね！After の方が断然安全です！」

---

## 🔍 演習 10：エラーの詳細はログで確認する

🐘「でもな、エラーの詳細を確認できひんかったら、開発者が困るやろ？」

👩‍💻「そうですね...どうやって原因を調べればいいんですか？」

🐘「**サーバーのログ**に記録されとるんや」

---

### 📝 Step 10-1：Laravel のログファイルを確認

**ターミナルで実行：**

```bash
tail -f storage/logs/laravel.log
```

**表示される内容：**

```
[2024-01-15 10:30:45] local.ERROR: Call to undefined relationship [usrs] on model [App\Models\Project]. 
{
  "exception": "BadMethodCallException",
  "file": "/var/www/html/app/Services/ProjectService.php",
  "line": 53,
  "trace": [
    {
      "file": "/var/www/html/app/Services/ProjectService.php",
      "line": 53,
      "function": "__call",
      "class": "Illuminate\\Database\\Eloquent\\Model",
      "type": "->"
    },
    ...
  ]
}
[ErrorID: req_65a1b2c3d4e5f6a7b8c9]
```

👩‍💻「あ！ログには詳細な情報が残ってます！しかも `request_id` で紐づけできる！」

🐘「せやろ？**ユーザーには安全なメッセージだけ見せて、開発者はログで詳細を確認する**んや」

---

### 🔗 request_id の使い方

```
🔗 request_id でエラーを追跡

【クライアントからの問い合わせ】
👤 クライアント: 「プロジェクト詳細が見れないんですけど...」

【開発者の対応】
👨‍💻 開発者: 「エラーメッセージに表示されている request_id を教えてください」
👤 クライアント: 「req_65a1b2c3d4e5f6a7b8c9 です」

【ログ検索】
$ grep "req_65a1b2c3d4e5f6a7b8c9" storage/logs/laravel.log
→ エラーの詳細が見つかる！

【原因特定】
ProjectService.php の 53行目で users → usrs のタイポ

【修正】
usrs → users に修正

【完了】
👨‍💻 開発者: 「修正しました！」
```

👩‍💻「なるほど！`request_id` があれば、ユーザーに内部情報を見せずにエラー追跡ができるんですね！」

🐘「**その通り！さすガネーシャの生徒や！**」

---

## 🔧 演習 11：エラーの原因を修正する

🐘「さて、原因が分かったから修正しよう」

---

### 📝 Step 11-1：ProjectService.php を開く

**開くファイル：** `app/Services/ProjectService.php`

---

### 📝 Step 11-2：53行目のタイポを修正

**53行目を修正：**

```php
// 修正前（タイポあり）
$project->load(['usrs', 'tasks.createdBy']);

// 修正後（タイポ修正）
$project->load(['users', 'tasks.createdBy']);
```

**保存：** `Cmd + S` (Mac) / `Ctrl + S` (Windows)

---

### 📝 Step 11-3：ブラウザをリロードして確認

**強制リロード：**

```
Mac: Cmd + Shift + R
Windows: Ctrl + Shift + R
```

👩‍💻「やりました！今度はエラーが出ずに、プロジェクト詳細が正しく表示されました！」

🐘「よっしゃ！**修正完了や！**」

---

# 第 3 章：開発環境に戻す（重要！）

🐘「演習が終わったら、**開発環境の設定に戻す**んやで」

👩‍💻「あ、そうですね！」

---

## 🔄 演習 12：開発環境の設定に戻す

### 📝 Step 12-1：APP_DEBUG を true に戻す

**開くファイル：** `.env`

**修正箇所：**

```bash
# 本番環境の設定（演習で設定した）
APP_DEBUG=false

# 開発環境に戻す
APP_DEBUG=true
```

**保存：** `Cmd + S` (Mac) / `Ctrl + S` (Windows)

---

### 📝 Step 12-2：設定キャッシュをクリア

**ターミナルで実行：**

```bash
php artisan config:clear
```

---

### 📝 Step 12-3：例外ハンドラーはそのまま（推奨）

🐘「例外ハンドラーはな、**そのまま有効にしておく方がええ**で」

👩‍💻「え？開発中も有効にしておくんですか？」

🐘「せや。開発中でも**統一されたエラーレスポンス**の方が、フロントエンドの実装がしやすいからな」

**オプション1：有効なまま（推奨）**

```php
// bootstrap/app.php
// このまま有効にしておく（推奨）
$apiHandler = new ApiExceptionHandler();

$exceptions->render(function (\Throwable $e, Request $request) use ($apiHandler) {
    return $apiHandler->handle($e, $request);
});
```

**オプション2：コメントアウトに戻す（非推奨）**

```php
// bootstrap/app.php
// コメントアウトに戻すこともできる（非推奨）
// // API例外ハンドラーを登録
// $apiHandler = new ApiExceptionHandler();
//
// $exceptions->render(function (\Throwable $e, Request $request) use ($apiHandler) {
//     return $apiHandler->handle($e, $request);
// });
```

🐘「ワシのおすすめは**オプション1（有効なまま）**や」

---

### 📝 Step 12-4：動作確認

**ブラウザでプロジェクト詳細ページを開く：**

```
http://localhost/projects/1
```

👩‍💻「正しく表示されました！開発環境に戻りました！」

🐘「よっしゃ！**これで完了や！**」

---

# 第 4 章：まとめ

## 📊 本番環境と開発環境の設定比較

🐘「最後に、環境ごとの設定をまとめておくで」

### 📋 環境ごとの設定一覧

| 環境 | APP_DEBUG | 例外ハンドラー | エラー表示 | 用途 |
|:-----|:---------:|:-------------:|:----------|:-----|
| **開発** | `true` | 有効（推奨） | 詳細なエラー（ログに記録） | デバッグ |
| **ステージング** | `false` | 有効 | 安全なメッセージ | 本番前テスト |
| **本番** | `false` | 有効 | 安全なメッセージ | ユーザー利用 |

---

## ✅ 本番環境チェックリスト

```
本番環境デプロイ前に必ず確認すること

□ APP_DEBUG=false になっているか？
□ APP_ENV=production になっているか？
□ API例外ハンドラーが有効か？
□ エラーログが記録されることを確認したか？
□ Sentryなどの監視ツールを導入したか？
□ ユーザーに内部情報が表示されないことを確認したか？
□ request_id でエラー追跡ができることを確認したか？
□ タイポや記述ミスがないか最終チェックしたか？
```

---

## ❌ 本番環境で絶対にやってはいけないこと

```
❌ APP_DEBUG=true にする
❌ APP_ENV=local のままにする
❌ エラーメッセージにファイルパスを含める
❌ スタックトレースをユーザーに見せる
❌ データベースの情報を露出させる
❌ 内部のファイル構造を推測できる情報を出す
❌ 例外ハンドラーを無効にする
❌ Sentryなどの監視ツールを設定しない
```

---

## 🎯 今日学んだ最も重要なこと

```
💡 この演習で学んだ重要ポイント

1️⃣ たった1文字のタイポでもエラーは起きる
   └─ コピペミス、タイポは誰にでも起こる
   └─ 問題はタイポではなく、エラーの見せ方

2️⃣ APP_DEBUG=true は開発者専用
   └─ 本番環境では必ず false にする
   └─ 内部情報の露出はセキュリティリスク

3️⃣ エラーハンドリングは「保険」
   └─ 何も起きない時は無駄に見えるが
   └─ いざという時に会社を救う

4️⃣ ログで詳細を確認、ユーザーには優しいメッセージ
   └─ 開発者：ログで詳細を確認
   └─ ユーザー：安全で分かりやすいメッセージ

5️⃣ request_id でエラー追跡
   └─ ユーザーに内部情報を見せずに
   └─ エラーの原因を特定できる
```

---

## 🐘 ガネーシャからの最終メッセージ

🐘「お疲れさまやで！今日の演習、どうやった？」

👩‍💻「実際に体験してみて、本当に怖さが分かりました...たった1つの設定ミスで、こんなに情報が漏れてしまうなんて...」

🐘「せやろ？でもな、**安全な環境で失敗を体験できた**のが一番の収穫や」

👩‍💻「本番環境で同じミスをしていたら...と思うとゾッとします」

🐘「その『ゾッとする感覚』を忘れんようにな。それが**セキュリティ意識**の始まりや」

---

### 🎓 偉人の教え

🐘「ワシの教え子のベンジャミン・フランクリンくんも言うとったで」

```
💬 ベンジャミン・フランクリンの名言

"An ounce of prevention is worth a pound of cure."
（1オンスの予防は、1ポンドの治療に値する）

日本語訳：
わずかな予防は、大きな治療に匹敵する
```

🐘「エラーハンドリングはまさに**予防**や。今日学んだことを活かして、安全なシステムを作るんやで」

👩‍💻「はい！ありがとうございました！」

🐘「ほな、あんみつ買ってきてや！🍨」

👩‍💻「...はい😅（また始まった）」

---

**はい、Oh, My God!!** 🐘✨

---

## 📚 関連教材

-   `ERROR_HANDLING_LESSON.md` - エラーハンドリングの基礎
-   `CONSOLE_LOG_LESSON.md` - コンソールログの実践
-   `CONSOLE_LOG_PRACTICE_EXAMPLES.md` - コンソールログの実例集

---

## 🎓 おめでとうございます！

この演習を完了しました 🎉

これからは APP_DEBUG の設定に気をつけて、
安全なシステム開発を心がけてください！

```
       🐘
      /||\
     / || \
    🍨    🍨

ガネーシャより愛を込めて
セキュリティは予防が命やで！
```
