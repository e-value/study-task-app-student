# 🎓 フロントエンド開発者のためのコンソールログ実践講座

## 🐘 ガネーシャ先生の JavaScript デバッグ道場

---

## 📚 この講座で学べること

✅ `console.log()` の基本から応用まで  
✅ Laravel の API 通信を**デベロッパーツールで確認する方法**  
✅ エラーをコンソールで追跡する実践テクニック  
✅ **このプロジェクトのコードで実際に手を動かして学ぶ！**

---

# 第 1 章：まずは基礎から！console.log って何や？

## 🐘 ガネーシャ登場

**ガネーシャ 🐘**：「おう、自分！今日はワシがフロントエンド開発の極意を教えたるで！」

**生徒 👩‍💻**：「ガネーシャ先生、console.log って何ですか？」

**ガネーシャ 🐘**：「ええ質問やな！console.log はな、**プログラムの中が見える魔法の虫眼鏡**や！プログラムは普通、何を考えてるか見えへん。でもな、console.log を使うとな、『あ、今この変数にはこんな値が入ってるんやな』って分かるんや」

**生徒 👩‍💻**：「虫眼鏡...ですか？」

**ガネーシャ 🐘**：「せや！例えばな、お前が料理してるとするやろ？レシピ通りに作っとるつもりでも、『あれ、この調味料どれくらい入れたっけ？』って分からんくなる時あるやろ？そんな時に味見するのと同じや。console.log は**プログラムの味見**なんや！」

```
🍳 料理の世界              💻 プログラムの世界
─────────────────        ─────────────────
味見をする                console.logで確認する
├ 塩加減を確認            ├ 変数の値を確認
├ 温度を確かめる          ├ 処理の流れを確認
└ 盛り付けを見る          └ エラーの原因を特定
```

**生徒 👩‍💻**：「なるほど！でも、どうやって使うんですか？」

**ガネーシャ 🐘**：「まぁまぁ、焦るなや。まずは基本から教えたるわ」

---

## 📖 console.log の基本文法

### ✅ 最もシンプルな使い方

```javascript
console.log("Hello, World!");
```

### ✅ 変数の中身を見る

```javascript
const userName = "太郎";
console.log(userName); // → 太郎
```

### ✅ ラベルを付けて分かりやすく

```javascript
const userName = "太郎";
console.log("ユーザー名:", userName); // → ユーザー名: 太郎
```

### ✅ 複数の値を一度に出力

```javascript
const name = "太郎";
const age = 25;
console.log("名前:", name, "年齢:", age); // → 名前: 太郎 年齢: 25
```

---

**ガネーシャ 🐘**：「ワシの教え子のエジソンくんもな、『天才は 1%のひらめきと 99%の汗』って言うとったけど、プログラミングもな、**1%のコードと 99%のデバッグ**や！console.log を制する者がデバッグを制するんや！」

**生徒 👩‍💻**：「エジソンってガネーシャ先生の教え子なんですか！？」

**ガネーシャ 🐘**：「せやで！あ、いや...まぁそういう設定や（小声）。さ、次いこか！」

---

# 第 2 章：実践！このプロジェクトで試してみよう

## 🎯 ミッション 1：基本の console.log を試す

**ガネーシャ 🐘**：「さぁ、ここからが本番や！お前のプロジェクトの実際のコードで console.log を使ってみるで！」

### 📝 手順 1：ブラウザのデベロッパーツールを開く

**生徒 👩‍💻**：「デベロッパーツール...？」

**ガネーシャ 🐘**：「それが**虫眼鏡の本体**や！これを開かんことには始まらへんで」

#### 🔑 デベロッパーツールの開き方（これだけ覚えれば OK！）

**Mac の場合：**

-   **`Cmd + Option + I`** を同時に押す

**どのパソコンでも使える万能な方法：**

-   ページ上で**右クリック** → **「検証」** をクリック

```
┌─────────────────────────────────┐
│  ブラウザの画面                   │
│                                 │
│  [ここで右クリック]              │
│   ↓                             │
│  ┌──────────────┐              │
│  │ 検証         │ ← これをクリック │
│  └──────────────┘              │
└─────────────────────────────────┘
```

**ガネーシャ 🐘**：「一回覚えたら、あとはずっとこの方法で OK や！毎回説明せんからな」

**生徒 👩‍💻**：「開きました！なんかいっぱいタブがありますね...」

**ガネーシャ 🐘**：「せや！まずは**Console（コンソール）タブ**をクリックしてみ。ここがワシらの戦場や！」

```
┌─────────────────────────────────────┐
│ Elements  Console  Sources  Network │  ← このタブの中の
│━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━│     「Console」をクリック！
│                                     │
│ > _                                 │  ← ここに console.log の
│                                     │     結果が表示される！
└─────────────────────────────────────┘
```

---

### 📝 手順 2：実際の Vue ファイルに console.log を追加してみよう

**ガネーシャ 🐘**：「ほな、お前のプロジェクトの `Show.vue` ファイルを開いてみ。タスク詳細ページのコードや」

開くファイル：`resources/js/Pages/Tasks/Show.vue`

**現在のコード（30 行目あたり）：**

```javascript
const fetchTask = async () => {
    try {
        loading.value = true;
        const response = await axios.get(`/api/tasks/${taskId}`);
        task.value = response.data.data || response.data;
    } catch (err) {
        alert("タスクの読み込みに失敗しました");
    } finally {
        loading.value = false;
    }
};
```

**ガネーシャ 🐘**：「このコードにな、console.log を仕込むんや！今から生のエラーを確認する方法を教えたるわ」

**生徒 👩‍💻**：「どうやってやるんですか？」

**ガネーシャ 🐘**：「まぁまぁ、焦るなや。基本の出汁の取り方を学ぶようなもんやな。まずはシンプルにやってみよう」

**追加後のコード：**

```javascript
const fetchTask = async () => {
    console.log("🚀 fetchTask が呼ばれたで！");
    console.log("📍 タスクID:", taskId);

    try {
        loading.value = true;

        console.log("📡 APIリクエストを送信するで：", `/api/tasks/${taskId}`);

        const response = await axios.get(`/api/tasks/${taskId}`);

        console.log("✅ APIレスポンス成功！", response);
        console.log("📦 response.data の中身：", response.data);
        console.log("📝 取得したタスク：", response.data.data);

        task.value = response.data.data || response.data;
    } catch (err) {
        // ❌ ここが重要！生のエラーをそのまま確認する
        console.error("❌ エラーが発生したで！");
        console.error("🔍 エラーオブジェクト全体:", err);
        console.error("📊 エラーレスポンス:", err.response);
        console.error("📋 ステータスコード:", err.response?.status);
        console.error("💬 エラーデータ:", err.response?.data);

        // 画面にもエラーを表示（ユーザー向け）
        alert("タスクの読み込みに失敗しました");
    } finally {
        loading.value = false;
        console.log("🏁 fetchTask 終了！");
    }
};
```

**生徒 👩‍💻**：「絵文字を付けてるんですね！」

**ガネーシャ 🐘**：「せや！絵文字を付けるとな、コンソールがめっちゃ見やすくなるんや。ログが大量にある時でも、絵文字でパッと目的のログが見つかるんやで。これはワシの教え子のスティーブ・ジョブズくんから学んだ『デザインの重要性』や！」

**生徒 👩‍💻**：「ジョブズも教え子なんですか...（疑惑の目）」

**ガネーシャ 🐘**：「（咳払い）ま、まぁええやん！次行くで！」

---

### 📝 手順 3：成功パターンを確認しよう（まずはこれ！）

**ガネーシャ 🐘**：「コードを保存したら、ブラウザでタスク詳細ページを開いてみ。まずは**成功するパターン**から見てみよう」

#### 🚀 サーバーを起動する

**ガネーシャ 🐘**：「このプロジェクトは **Laravel Sail** を使っとるから、起動方法がちょっと特別やで」

```bash
# ターミナルで実行（Sail環境の起動）
sail up -d

# Vite（フロントエンド）を起動
sail npm run dev
```

**ガネーシャ 🐘**：「Sail は Docker を使って Laravel を動かす仕組みや。`sail up -d` で Laravel サーバーが起動するで」

#### 🌐 ブラウザで確認

1. ブラウザで `http://localhost/tasks/1` にアクセス
2. デベロッパーツールを開いて Console タブを表示（さっき覚えた方法で！）

**生徒 👩‍💻**：「わぁ！コンソールにログがいっぱい出ました！」

```
🚀 fetchTask が呼ばれたで！
📍 タスクID: 1
📡 APIリクエストを送信するで： /api/tasks/1
✅ APIレスポンス成功！ ▶︎ {data: {•••}, status: 200, statusText: 'OK', headers: AxiosHeaders, config: {•••}, •••}
📦 response.data の中身： ▶︎ {data: {•••}}
📝 取得したタスク： ▶︎ {id: 1, project_id: 1, title: '...', status: 'todo', •••}
🏁 fetchTask 終了！
```

**ガネーシャ 🐘**：「せやろ！これが**プログラムの中が見える瞬間**や！成功した時の流れが全部見えとるな」

**生徒 👩‍💻**：「あれ？`Object {...}` じゃなくて、`{data: {•••}, status: 200, ...}` って表示されてます。最初から一部が見えてますね」

**ガネーシャ 🐘**：「おお、ええところに気づいたな！Console はな、**親切に一部のプロパティを最初から見せてくれる**んや。でも `{•••}` って部分はまだ隠れとるやろ？そこを開いていくんや」

---

### 📝 手順 3.5：コンソールでオブジェクトの中身を見る魔法

**生徒 👩‍💻**：「`{data: {•••}, status: 200, ...}` の `{•••}` の部分って何ですか？中身が見えません...」

**ガネーシャ 🐘**：「おお、ええ質問や！これはな、**JavaScript のオブジェクト（データの塊）**の**要約表示**なんや」

#### 💡 オブジェクトと `{•••}` 表示について

**ガネーシャ 🐘**：「まず、**Object（オブジェクト）**ってのはな、**データを入れる箱**や。例えば：」

```javascript
// これがオブジェクト
const user = {
    name: "太郎",
    age: 25,
    email: "taro@example.com",
};
```

**ガネーシャ 🐘**：「Console はな、オブジェクトが大きすぎると全部表示したら見づらいやろ？だから、**一部のプロパティを表示して、残りは `{•••}` で要約**してくれとるんや」

```
例：
✅ APIレスポンス成功！ ▶︎ {data: {•••}, status: 200, statusText: 'OK', ...}
                        ↑          ↑
                    最初から見える  隠れてる部分
```

**生徒 👩‍💻**：「なるほど！じゃあ、`{•••}` の中を見るにはどうすればいいんですか？」

#### 🎯 オブジェクトを展開する方法（超重要！）

**ガネーシャ 🐘**：「さぁ、ここからが本番や！Console に表示されとる行の**左側に小さな ▶︎ マーク**があるはずや。これをクリックするんや！」

**Console 画面（実際の表示）：**

```
✅ APIレスポンス成功！ ▶︎ {data: {•••}, status: 200, statusText: 'OK', headers: AxiosHeaders, config: {•••}, •••}
                       ↑
                   この三角をクリック！
```

**生徒 👩‍💻**：「あ！小さな三角がありました！クリックします」

**ガネーシャ 🐘**：「よっしゃ！▶︎ をクリックしたら、▼ に変わって**全部の中身**が見えるで！」

**▶︎ をクリックすると → ▼ に変わって全体が展開される：**

```
✅ APIレスポンス成功！ ▼ {data: {•••}, status: 200, statusText: 'OK', ...}
  config: ▶︎ {transitional: {•••}, adapter: [...], ...}
  data: ▶︎ {data: {•••}}         ← これもクリックできる！
  headers: ▶︎ AxiosHeaders {•••}
  request: ▶︎ XMLHttpRequest {•••}
  status: 200                   ← 数値はそのまま表示
  statusText: "OK"              ← 文字列もそのまま表示
```

**生徒 👩‍💻**：「わぁ！全部の項目が見えました！でも、`data: ▶︎ {data: {•••}}` ってまた `{•••}` がありますね...」

**ガネーシャ 🐘**：「せやろ！**オブジェクトの中にオブジェクトが入っとる**んや！これを**入れ子構造（ネスト）**って言うんや。箱の中に箱が入っとる感じやな」

```
📦 axios のレスポンス（大きな箱）
  ├─ 📝 status: 200（数値）
  ├─ 📝 statusText: "OK"（文字列）
  ├─ 📦 data:（また箱！）← Laravel からのデータ
  │    └─ 📦 data:（さらに箱！）← 実際のタスクデータ
  ├─ 📦 headers:（ヘッダー情報）
  └─ 📦 config:（設定情報）
```

**ガネーシャ 🐘**：「だから、**▶︎ を何回もクリックして、階層を深く掘っていく**んや。これが Console の基本テクニックや！」

#### 🔄 記号の意味を覚えよう

**ガネーシャ 🐘**：「Console の記号の意味を整理しとこうや」

| 記号      | 意味           | 状態                         |
| :-------- | :------------- | :--------------------------- |
| **▶︎**    | 右向き三角     | 閉じた状態（中身が隠れてる） |
| **▼**     | 下向き三角     | 開いた状態（中身が見えてる） |
| **{...}** | 波カッコに点々 | オブジェクトの要約表示       |
| **[...]** | 角カッコに点々 | 配列の要約表示               |

**生徒 👩‍💻**：「なるほど！じゃあ、`data` の中も開いてみます！」

**ガネーシャ 🐘**：「ええ調子や！どんどん開いていこう！」

#### 📖 実際に階層を開いていく

**ステップ 1：最初の `data` を開く**

**`data: ▶︎ {data: {•••}}` をクリックすると：**

```
✅ APIレスポンス成功！ ▼ {data: {•••}, status: 200, ...}
  config: ▶︎ {...}
  data: ▼ {data: {•••}}       ← 今ここを開いた！
    data: ▶︎ {id: 1, project_id: 1, title: '...', •••}  ← え！また data がある！
  headers: ▶︎ {...}
  request: ▶︎ XMLHttpRequest {...}
  status: 200
  statusText: "OK"
```

**生徒 👩‍💻**：「あ！`data` の中に、また `data` がありますね...これは？」

**ガネーシャ 🐘**：「ええところに気づいたな！これが **response.data.data** って二重になってる理由や」

#### 💡 なぜ data.data になるのか？

**ガネーシャ 🐘**：「これはな、**Laravel の API Resource** の仕組みなんや」

```
🎁 axios のレスポンス全体
  └─ 📦 data（axios が response.data に格納）
       └─ 📦 data（Laravel の TaskResource が data でラップ）
            └─ 💎 実際のタスクデータ（id, title, status など）
```

**Laravel 側のコード（参考）：**

```php
// app/Http/Controllers/Api/TaskController.php
public function show(Task $task) {
    return new TaskResource($task);  // ← TaskResource で返す
}

// TaskResource は自動的に {data: {...}} でラップされる
```

**実際の JSON レスポンス：**

```json
{
  "data": {
    "id": 1,
    "project_id": 1,
    "title": "サンプルタスク",
    "status": "todo",
    ...
  }
}
```

**ガネーシャ 🐘**：「axios は `response.data` にサーバーからの JSON を入れる。Laravel の TaskResource はデータを `{data: {...}}` でラップする。だから `response.data.data` で実際のタスクにアクセスできるんや」

**生徒 👩‍💻**：「なるほど！じゃあ、内側の `data` も開いてみます！」

**ステップ 2：内側の `data` を開く（実際のタスクデータ）**

**`data: ▶︎ {id: 1, project_id: 1, ...}` をクリックすると：**

```
✅ APIレスポンス成功！ ▼ {data: {•••}, status: 200, ...}
  config: ▶︎ {...}
  data: ▼ {data: {•••}}
    data: ▼ {id: 1, project_id: 1, title: '...', •••}  ← ついに最深部が開いた！
      id: 1                         ← タスクID
      project_id: 1                 ← プロジェクトID
      title: "サンプルタスク"        ← タスクのタイトル
      description: "これはサンプル"   ← 説明
      status: "todo"                ← ステータス（todo/doing/done）
      created_by: 1                 ← 作成者ID
      created_by_user: ▶︎ {id: 1, name: '...', •••}  ← 作成者情報（これも開ける！）
      project: ▶︎ {id: 1, name: '...', •••}          ← プロジェクト情報（これも開ける！）
      created_at: "2024-01-01T00:00:00.000000Z"
      updated_at: "2024-01-01T00:00:00.000000Z"
  headers: ▶︎ {...}
  request: ▶︎ XMLHttpRequest {...}
  status: 200
  statusText: "OK"
```

**生徒 👩‍💻**：「わぁ！タスクの詳細が全部見えました！」

**ガネーシャ 🐘**：「せやろ！これが**オブジェクトの展開**や！**Laravel の TaskResource** が返したタスクの全データが見えとるやろ」

#### 🎯 さらに深く！ネストした Object も開ける

**生徒 👩‍💻**：「`created_by_user: ▶︎ {id: 1, name: '...', •••}` ってのもありますね。これも開けますか？」

**ガネーシャ 🐘**：「もちろんや！何階層でも開けるで！」

**`created_by_user: ▶︎ {id: 1, name: '...', •••}` をクリック：**

```
data: ▼ {id: 1, project_id: 1, title: '...', •••}
  id: 1
  project_id: 1
  title: "サンプルタスク"
  created_by_user: ▼ {id: 1, name: '山田太郎', email: 'taro@example.com'}  ← 開いた！
    id: 1                      ← ユーザーID
    name: "山田太郎"            ← ユーザー名
    email: "taro@example.com"  ← メールアドレス
  project: ▶︎ {id: 1, name: '...', •••}
  status: "todo"
  ...
```

**生徒 👩‍💻**：「すごい！どんどん深く見ていけるんですね！これも **UserResource** で整形されたデータなんですか？」

**ガネーシャ 🐘**：「せや！TaskResource の中で `new UserResource($this->createdBy)` って呼んでるから、ユーザー情報もきれいに整形されとるんや。**▶︎ を見つけたらクリック、▶︎ を見つけたらクリック**。これを繰り返すのが Console マスターへの第一歩や！」

#### 📝 オブジェクト展開のコツまとめ

**ガネーシャ 🐘**：「ここまでのコツをまとめるで」

| やること                             | 説明                                     |
| :----------------------------------- | :--------------------------------------- |
| **1. `{•••}` や `{...}` を見つける** | オブジェクトの要約表示（中身が隠れてる） |
| **2. 左の ▶︎ をクリック**            | 全ての中身が展開されて ▼ になる          |
| **3. さらに ▶︎ があればクリック**    | 何階層でも開ける！                       |
| **4. ▼ をクリック**                  | 閉じて ▶︎ に戻る                         |

**生徒 👩‍💻**：「これなら API から返ってきたデータの中身が全部確認できますね！」

**ガネーシャ 🐘**：「さすガネーシャの生徒や！もう Console の使い方の基本は完璧やな！**▶︎ をクリックして階層を開いていく**のが Console を使いこなす第一歩や。これを**オブジェクトの展開**っていうんや」

---

### 📝 手順 3.6：Laravel から返ってきたデータの構造を理解しよう

**ガネーシャ 🐘**：「今見たデータはな、Laravel の API が返してくれとるんや。構造を整理してみよう」

#### 📊 axios レスポンスの全体構造（実際の形）

```javascript
// axios のレスポンス全体
{
    status: 200,           // HTTP ステータスコード
    statusText: "OK",      // ステータステキスト
    data: {                // ← Laravel から返ってきた JSON
        data: {            // ← TaskResource が自動的に data でラップ
            id: 1,
            project_id: 1,
            title: "サンプルタスク",
            description: "...",
            status: "todo",
            created_by: 1,
            created_by_user: {...},  // UserResource
            project: {...},          // ProjectResource
            created_at: "...",
            updated_at: "..."
        }
    },
    headers: {...},        // レスポンスヘッダー
    config: {...},         // リクエスト設定
    request: {...}         // リクエストオブジェクト
}
```

**ガネーシャ 🐘**：「`response.data.data` って二重になってるのは、**axios の response.data** と **Laravel の Resource が自動的に付ける data** が重なってるからやな」

**生徒 👩‍💻**：「なるほど！Laravel のどこでこの形式を作ってるんですか？」

**ガネーシャ 🐘**：「ええ質問や！Laravel 側のコードを見てみよう」

#### 💡 Laravel 側のコード（実際のプロジェクト）

```php
// app/Http/Controllers/Api/TaskController.php

public function show(Request $request, Task $task): TaskResource|JsonResponse
{
    try {
        $task = $this->taskService->getTask($task, $request->user());
        return new TaskResource($task);  // ← これが自動的に {data: {...}} でラップされる
    } catch (\Exception $e) {
        return response()->json([
            'message' => $e->getMessage(),
        ], 403);
    }
}
```

```php
// app/Http/Resources/TaskResource.php

public function toArray(Request $request): array
{
    return [
        'id' => $this->id,
        'project_id' => $this->project_id,
        'title' => $this->title,
        'description' => $this->description,
        'status' => $this->status,
        'created_by' => $this->created_by,
        'created_by_user' => new UserResource($this->whenLoaded('createdBy')),
        'project' => new ProjectResource($this->whenLoaded('project')),
        'created_at' => $this->created_at,
        'updated_at' => $this->updated_at,
    ];
}
```

**Laravel が返す JSON（最終形）：**

```json
{
    "data": {
        "id": 1,
        "project_id": 1,
        "title": "サンプルタスク",
        "description": "これはサンプルのタスクです",
        "status": "todo",
        "created_by": 1,
        "created_by_user": {
            "id": 1,
            "name": "山田太郎",
            "email": "taro@example.com"
        },
        "project": {
            "id": 1,
            "name": "サンプルプロジェクト"
        },
        "created_at": "2024-01-01T00:00:00.000000Z",
        "updated_at": "2024-01-01T00:00:00.000000Z"
    }
}
```

**ガネーシャ 🐘**：「Laravel の **API Resource（JsonResource）** はな、**自動的に `data` でラップ**してくれるんや。これは Laravel の仕様なんや」

**生徒 👩‍💻**：「なるほど！だから `new TaskResource($task)` って返すだけで、`{data: {...}}` の形式になるんですね」

**ガネーシャ 🐘**：「せや！これが**Laravel API Resource** の便利なところや。統一された形式でデータを返してくれるから、フロントエンド側が扱いやすいんやな」

---

### 📝 手順 4：エラーパターンも試してみよう（わざとエラーを起こす！）

**ガネーシャ 🐘**：「成功パターンが分かったところで、次はエラーの時を見てみよう。わざとエラーを起こすで！」

**生徒 👩‍💻**：「わざと...ですか？」

**ガネーシャ 🐘**：「せや！エラーを理解するには、エラーを起こして確認するのが一番や。ワシの教え子のエジソンくんも『失敗は成功の母』って言うとったで」

#### 🎯 わざとエラーを起こす

**コードを一時的に変更：**

```javascript
const fetchTask = async () => {
    console.log("🚀 fetchTask が呼ばれたで！");
    console.log("📍 タスクID:", taskId);

    try {
        loading.value = true;

        // わざと存在しないIDを指定してエラーを起こす
        console.log("📡 APIリクエストを送信するで：", `/api/tasks/99999`);
        const response = await axios.get(`/api/tasks/99999`);  // ← ここを変更

        // ...以下同じ
```

**ガネーシャ 🐘**：「コードを保存して、ブラウザをリロード（`F5` または `Cmd + R`）してみ」

**Console に表示される内容：**

```
🚀 fetchTask が呼ばれたで！
📍 タスクID: 1
📡 APIリクエストを送信するで： /api/tasks/99999
❌ エラーが発生したで！
🔍 エラーオブジェクト全体: ▶︎ Error: Request failed with status code 404
📊 エラーレスポンス: ▶︎ Object
📋 ステータスコード: 404
💬 エラーデータ: ▶︎ Object
🏁 fetchTask 終了！
```

**生徒 👩‍💻**：「エラーのログが出ました！今度は赤く表示されてますね」

**ガネーシャ 🐘**：「せや！`console.error()` で出力したログは赤く表示されるんや。エラーレスポンスの中身も ▶︎ をクリックして見てみよう」

**`📊 エラーレスポンス: ▶︎ Object` をクリックすると：**

```
📊 エラーレスポンス: ▼ {status: 404, statusText: "Not Found", data: {•••}, ...}
  status: 404
  statusText: "Not Found"
  data: ▼ {message: '...', exception: '...', file: '...', ...}
    message: "No query results for model [App\\Models\\Task] 99999"  ← Laravel のエラーメッセージ
    exception: "Symfony\\Component\\HttpKernel\\Exception\\NotFoundHttpException"
    file: "/var/www/html/vendor/laravel/framework/src/Illuminate/..."
    line: 419
    trace: ▶︎ [...]  ← スタックトレース
  headers: ▶︎ {...}
  config: ▶︎ {...}
  request: ▶︎ XMLHttpRequest {...}
```

**生徒 👩‍💻**：「あれ？成功時と構造が全然違いますね...`data: {data: {...}}` じゃないです」

**ガネーシャ 🐘**：「おお、ええところに気づいたな！これはな、**Laravel のエラーレスポンス**なんや」

#### 💡 なぜエラー時の構造が違うのか？

**ガネーシャ 🐘**：「成功時は TaskResource が `{data: {...}}` の形で返すけど、エラー時は Laravel が直接エラー情報を返すから、構造が違うんや」

**成功時と エラー時の違い：**

| 状態         | レスポンス構造                            | 特徴                                      |
| :----------- | :---------------------------------------- | :---------------------------------------- |
| **成功時**   | `{data: {id: 1, ...}}`                    | TaskResource が整形したデータ             |
| **エラー時** | `{message: "...", exception: "...", ...}` | Laravel のエラー情報（詳細な trace 付き） |

**生徒 👩‍💻**：「エラーレスポンスの方が情報が多いですね」

**ガネーシャ 🐘**：「せや！エラー時は `exception`、`file`、`line`、`trace` とかの詳細情報が入っとるんや。これを見れば、**どこでエラーが起きたか**が分かるんやで」

**生徒 👩‍💻**：「なるほど！Console で ▶︎ をクリックすれば、エラーの詳細も全部確認できますね」

**ガネーシャ 🐘**：「せや！**▶︎ をクリックして展開する**のを忘れんようにな。エラーの `trace` を開けば、エラーが発生した経路が全部見えるで。これができれば、デバッグが 100 倍楽になるで」

#### 📝 コードを元に戻そう

**ガネーシャ 🐘**：「エラーの確認ができたら、コードを元に戻しておこうや」

```javascript
// 変更したコード
const response = await axios.get(`/api/tasks/99999`);

// 元に戻す（正しいコード）
const response = await axios.get(`/api/tasks/${taskId}`);
```

**ガネーシャ 🐘**：「保存してリロードしたら、また成功のログが出るはずや」

**生徒 👩‍💻**：「はい！元に戻りました」

---

## 🎯 ポイント整理（ここまでのまとめ）

**ガネーシャ 🐘**：「ここまでで学んだことを整理しとこうや」

| やったこと               | 何が分かったか                              | 使うツール / 仕組み                 |
| :----------------------- | :------------------------------------------ | :---------------------------------- |
| **Console タブを開く**   | デベロッパーツールの使い方                  | `Cmd + Option + I` または右クリック |
| **console.log で確認**   | 処理の流れを追跡                            | `console.log()`                     |
| **▶︎ をクリック**        | `{•••}` の中身を展開して全部見る            | Console の展開機能                  |
| **Laravel のレスポンス** | `{data: {...}}` の形式で返ってくる          | TaskResource（JsonResource）        |
| **response.data.data**   | 二重の data になる理由を理解                | axios + Laravel の仕様              |
| **成功時の確認**         | `{data: {id: 1, ...}}` の構造               | Console で展開して確認              |
| **エラー時の確認**       | `{message: "...", exception: "..."}` の構造 | Laravel のエラーレスポンス          |
| **エラー詳細の確認**     | trace を展開してエラー発生経路を追跡        | Console の展開機能                  |
| **Sail 環境**            | Docker で Laravel を動かす                  | `sail up -d`                        |

**生徒 👩‍💻**：「Console の使い方が分かってきました！」

**ガネーシャ 🐘**：「ええ調子や！次はもっと詳しくエラーの種類を学んでいくで」

---

### 📝 手順 5：Laravel のエラーを完全理解する（超重要！）

**ガネーシャ 🐘**：「ここからが本番や！エラーが出た時に、**何が起きてるか理解できる**ようになるで」

**生徒 👩‍💻**：「エラーって難しそう...」

**ガネーシャ 🐘**：「怖ないで！エラーはな、**プログラムからの手紙**なんや。『ここがおかしいで！』って優しく教えてくれとるんや」

---

#### 📊 エラーオブジェクトの構造を完全理解

**ガネーシャ 🐘**：「エラーが起きた時、axios は `err` っていうオブジェクトを返すんや。まずはこいつの構造を見てみよう」

**Console で確認する方法：**

```javascript
catch (err) {
    console.error("❌ エラーオブジェクト全体:", err);
    console.error("📊 err.response:", err.response);
    console.error("📦 err.response.data:", err.response?.data);
}
```

**Console に表示される内容（404 エラーの場合）：**

```
❌ エラーオブジェクト全体: Error: Request failed with status code 404
  ├─ message: "Request failed with status code 404"
  ├─ name: "AxiosError"
  ├─ code: "ERR_BAD_REQUEST"
  │
  ├─ config: {...}  // リクエストの設定情報
  │   ├─ url: "/api/tasks/99999"
  │   ├─ method: "get"
  │   └─ headers: {...}
  │
  ├─ request: {...}  // 送信したリクエスト
  │
  └─ response: {...}  // ⭐ ここが最重要！
      ├─ status: 404  // HTTPステータスコード
      ├─ statusText: "Not Found"
      ├─ headers: {...}
      │
      └─ data: {...}  // ⭐⭐ Laravel が返したデータ
          ├─ message: "No query results for model [App\\Models\\Task] 9999"
          ├─ exception: "Symfony\\Component\\HttpKernel\\Exception\\NotFoundHttpException"
          ├─ file: "/var/www/html/vendor/laravel/framework/src/Illuminate/..."
          ├─ line: 668
          └─ trace: [{...}, {...}, ...]  // スタックトレース
```

**ガネーシャ 🐘**：「この中で、特に重要なのが **`err.response.data`** や！ここに Laravel からのエラー情報が入っとるんや」

**生徒 👩‍💻**：「`err.response.data` を見ればいいんですね！」

**ガネーシャ 🐘**：「せや！でもな、`err.response` が `undefined` の時もあるから、`?.` （オプショナルチェーン）を使うんやで」

```javascript
// ❌ ダメな例（エラーになる可能性がある）
console.error(err.response.data);

// ✅ 良い例（安全）
console.error(err.response?.data);
console.error(err.response?.status);
```

---

#### 🎯 Laravel が返すエラーの種類を完全マスター

**ガネーシャ 🐘**：「Laravel はな、エラーの種類によって違うデータを返してくれるんや。全部覚えよう！」

---

##### 1️⃣ 404 エラー（Not Found）

**いつ起きる？**

-   存在しないタスク ID を指定した時
-   存在しない URL にアクセスした時

**試してみよう：**

```javascript
// わざと存在しないIDを指定
const response = await axios.get("/api/tasks/99999");
```

**Console に表示される内容：**

```javascript
📊 err.response: {
    status: 404,
    statusText: "Not Found",
    data: {
        message: "No query results for model [App\\Models\\Task] 9999",
        exception: "Symfony\\Component\\HttpKernel\\Exception\\NotFoundHttpException",
        file: "/var/www/html/vendor/laravel/framework/src/Illuminate/Foundation/Exceptions/Handler.php",
        line: 668,
        trace: [{...}, {...}, ...]
    }
}
```

**確認コード：**

```javascript
catch (err) {
    if (err.response?.status === 404) {
        console.error("🔍 404エラー: リソースが見つかりません");
        console.error("💬 Laravel のメッセージ:", err.response.data.message);
        console.error("📝 例外クラス:", err.response.data.exception);
        console.error("📍 確認: 指定したIDは存在しますか？");
    }
}
```

---

##### 2️⃣ 422 エラー（Validation Error）

**いつ起きる？**

-   タイトルを空欄にして送信した時
-   文字数制限を超えた時
-   必須項目が入力されていない時

**試してみよう：**

```javascript
// わざとタイトルを空にして送信
const response = await axios.post("/api/projects/1/tasks", {
    title: "", // 空欄！
    description: "あいうえお".repeat(100), // 長すぎる！
});
```

**Console に表示される内容：**

```javascript
📊 err.response: {
    status: 422,
    statusText: "Unprocessable Entity",
    data: {
        message: "The title field is required. (and 1 more error)",
        errors: {  // ← フィールドごとのエラー詳細
            title: [
                "The title field is required."
            ],
            description: [
                "The description field must not be greater than 255 characters."
            ]
        }
    }
}
```

**確認コード：**

```javascript
catch (err) {
    if (err.response?.status === 422) {
        console.error("📝 バリデーションエラー");
        console.error("⚠️ エラー詳細:", err.response.data.errors);

        // フィールドごとに表示
        Object.entries(err.response.data.errors).forEach(([field, messages]) => {
            console.error(`  ❌ ${field}:`, messages.join(", "));
        });

        // テーブル形式で見やすく表示
        console.table(err.response.data.errors);
    }
}
```

**Console 出力：**

```
📝 バリデーションエラー
⚠️ エラー詳細: {title: Array(1), description: Array(1)}
  ❌ title: The title field is required.
  ❌ description: The description field must not be greater than 255 characters.

┌────────────────┬───────────────────────────────────────────────────────────┐
│ (index)        │ Values                                                    │
├────────────────┼───────────────────────────────────────────────────────────┤
│ title          │ ["The title field is required."]                          │
│ description    │ ["The description field must not be greater than 255..."] │
└────────────────┴───────────────────────────────────────────────────────────┘
```

**生徒 👩‍💻**：「テーブル表示、見やすい！」

**ガネーシャ 🐘**：「せやろ！`console.table()` は配列やオブジェクトを見る時の最強ツールや」

---

##### 3️⃣ 401 エラー（Unauthorized）

**いつ起きる？**

-   ログインが必要なのにログインしていない時
-   トークンの有効期限が切れた時

**Console に表示される内容：**

```javascript
📊 err.response: {
    status: 401,
    statusText: "Unauthorized",
    data: {
        message: "Unauthenticated."
    }
}
```

**確認コード：**

```javascript
catch (err) {
    if (err.response?.status === 401) {
        console.error("🔐 401エラー: 認証が必要です");
        console.error("💬 Laravel のメッセージ:", err.response.data.message);
        console.error("📍 確認: ログインしていますか？");
        console.error("📍 確認: トークンは有効ですか？");
    }
}
```

---

##### 4️⃣ 500 エラー（Server Error）

**いつ起きる？**

-   Laravel 側でプログラムエラーが起きた時
-   データベース接続エラーが起きた時

**Console に表示される内容：**

```javascript
📊 err.response: {
    status: 500,
    statusText: "Internal Server Error",
    data: {
        message: "Server Error",
        exception: "ErrorException",
        file: "/var/www/html/app/Services/TaskService.php",
        line: 42,
        trace: [{...}, {...}, ...]
    }
}
```

**確認コード：**

```javascript
catch (err) {
    if (err.response?.status === 500) {
        console.error("💥 500エラー: サーバー内部エラー");
        console.error("💬 エラーメッセージ:", err.response.data.message);
        console.error("📝 例外クラス:", err.response.data.exception);
        console.error("📁 エラー発生ファイル:", err.response.data.file);
        console.error("📍 確認: storage/logs/laravel.log を見てください");
        console.error("💡 Laravel 側のログで詳細を確認できます");
    }
}
```

**ガネーシャ 🐘**：「500 エラーの時はな、**Laravel のログファイル**を見るのが鉄則や！」

```bash
# Laravel のログを確認
tail -f storage/logs/laravel.log
```

---

##### 5️⃣ ネットワークエラー（err.response がない）

**いつ起きる？**

-   Laravel サーバーが起動していない時
-   インターネット接続が切れた時
-   CORS エラーが起きた時

**Console に表示される内容：**

```javascript
❌ エラーオブジェクト全体: Error: Network Error
  ├─ message: "Network Error"
  ├─ request: {...}  // リクエストはある
  └─ response: undefined  // ← レスポンスがない！
```

**確認コード：**

```javascript
catch (err) {
    if (!err.response) {
        // レスポンスがない = サーバーに届いていない
        console.error("🌐 ネットワークエラー");
        console.error("💬 メッセージ:", err.message);
        console.error("📍 確認事項:");
        console.error("  ✓ Laravel サーバーは起動していますか？");
        console.error("    → ターミナルで 'php artisan serve' を実行");
        console.error("  ✓ ネットワーク接続は正常ですか？");
        console.error("  ✓ CORS の設定は正しいですか？");
    }
}
```

---

#### 📋 エラー確認の完全フロー（プロの技）

**ガネーシャ 🐘**：「エラーが起きた時は、この順番で確認するんがプロの流儀や」

```javascript
catch (err) {
    console.group("❌ エラー詳細分析");

    // Step 1: エラーの種類を確認
    if (err.response) {
        // サーバーからレスポンスが返ってきた
        console.error("🔴 サーバーエラー（Laravel からレスポンスあり）");
        console.error("📊 ステータスコード:", err.response.status);
        console.error("📋 ステータステキスト:", err.response.statusText);
        console.error("💬 Laravel のメッセージ:", err.response.data.message);
        console.error("📝 例外クラス:", err.response.data.exception);

        // Step 2: ステータスコード別に詳細確認
        console.group("💡 エラー別トラブルシューティング");
        switch (err.response.status) {
            case 400:
                console.error("⚠️ 400 Bad Request: リクエストが不正です");
                console.error("確認: 送信データの形式は正しいですか？");
                break;

            case 401:
                console.error("🔐 401 Unauthorized: 認証が必要です");
                console.error("確認: ログインしていますか？");
                console.error("確認: トークンは有効ですか？");
                break;

            case 403:
                console.error("🚫 403 Forbidden: アクセスが拒否されました");
                console.error("確認: このリソースへのアクセス権限はありますか？");
                break;

            case 404:
                console.error("🔍 404 Not Found: リソースが見つかりません");
                console.error("確認: URLは正しいですか？");
                console.error("確認: リソースIDは存在しますか？");
                break;

            case 422:
                console.error("📝 422 Unprocessable Entity: バリデーションエラー");
                console.error("バリデーションエラー詳細:");
                if (err.response.data.errors) {
                    console.table(err.response.data.errors);
                    Object.entries(err.response.data.errors).forEach(([field, messages]) => {
                        console.error(`  ❌ ${field}:`, messages.join(", "));
                    });
                }
                break;

            case 500:
                console.error("💥 500 Internal Server Error: サーバー内部エラー");
                console.error("確認: storage/logs/laravel.log を確認してください");
                break;

            default:
                console.error(`❓ ${err.response.status}: その他のエラー`);
        }
        console.groupEnd();

        // Step 3: 完全なレスポンスデータを確認
        console.group("📦 完全なレスポンスデータ");
        console.error("Response Data:", err.response.data);
        console.groupEnd();

    } else if (err.request) {
        // リクエストは送信されたが、レスポンスがない
        console.error("🌐 ネットワークエラー（レスポンスなし）");
        console.error("💬 メッセージ:", err.message);
        console.error("📍 トラブルシューティング:");
        console.error("  1. Laravel サーバーは起動していますか？");
        console.error("     → ターミナルで 'php artisan serve' を確認");
        console.error("  2. Vite は起動していますか？");
        console.error("     → ターミナルで 'npm run dev' を確認");
        console.error("  3. ネットワーク接続は正常ですか？");
        console.error("  4. CORS の設定は正しいですか？");

    } else {
        // リクエストの設定中にエラーが発生
        console.error("⚙️ リクエスト設定エラー");
        console.error("💬 エラーメッセージ:", err.message);
        console.error("📍 確認: axios の設定を確認してください");
    }

    console.groupEnd();
}
```

**生徒 👩‍💻**：「すごく詳しい！これなら何が起きてるか完全に分かりますね！」

**ガネーシャ 🐘**：「せやろ！これが**プロのエラー確認フロー**や。エラーが出ても、もう怖くないやろ？」

**生徒 👩‍💻**：「はい！エラーが出るのが楽しみになってきました（笑）」

**ガネーシャ 🐘**：「ええ心がけや！エラーは**成長のチャンス**やからな」

---

## 🎯 ポイント整理

| やったこと                  | 何が分かったか       | Laravel で言うと         |
| :-------------------------- | :------------------- | :----------------------- |
| `console.log("メッセージ")` | 処理の流れを追跡     | `Log::info()` や `dd()`  |
| `console.log(変数)`         | 変数の中身を確認     | `dump($variable)`        |
| `console.log(response)`     | API レスポンスの確認 | レスポンスログの確認     |
| `console.log(err)`          | エラー内容の確認     | `catch` ブロックでのログ |

---

# 第 3 章：レベルアップ！console.log の仲間たち

**ガネーシャ 🐘**：「お前もだいぶ console.log が分かってきたな！でもな、console には他にも便利な仲間がおるんや」

**生徒 👩‍💻**：「仲間...？」

**ガネーシャ 🐘**：「せや！console.log は万能やけど、状況に応じて使い分けるともっと便利になるんや」

---

## 📊 console ファミリー図鑑

### 1️⃣ `console.log()` - 普段使いの万能選手

```javascript
console.log("普通のログやで");
```

**用途**：通常のログ出力。一番よく使う。

---

### 2️⃣ `console.error()` - エラー専門家

```javascript
console.error("❌ エラーが発生したで！");
```

```
出力イメージ：
  ❌ エラーが発生したで！  ← 赤く表示される！
```

**用途**：エラーを目立たせたい時。赤色で表示される。

**生徒 👩‍💻**：「赤くなるんですか！」

**ガネーシャ 🐘**：「せや！ログがいっぱいある時でも、エラーがすぐ分かるようになるんや」

---

### 3️⃣ `console.warn()` - 注意喚起担当

```javascript
console.warn("⚠️ これは警告やで");
```

```
出力イメージ：
  ⚠️ これは警告やで  ← 黄色で表示される！
```

**用途**：警告メッセージ。黄色で表示される。

---

### 4️⃣ `console.table()` - データの整理整頓名人

```javascript
const users = [
    { id: 1, name: "太郎", age: 25 },
    { id: 2, name: "花子", age: 30 },
    { id: 3, name: "次郎", age: 28 },
];

console.table(users);
```

```
出力イメージ：
┌─────────┬────┬────────┬─────┐
│ (index) │ id │  name  │ age │
├─────────┼────┼────────┼─────┤
│    0    │ 1  │ '太郎' │ 25  │
│    1    │ 2  │ '花子' │ 30  │
│    2    │ 3  │ '次郎' │ 28  │
└─────────┴────┴────────┴─────┘
```

**生徒 👩‍💻**：「わぁ！表になってる！見やすい！」

**ガネーシャ 🐘**：「せやろ！配列やオブジェクトの中身を確認する時は console.table が最強や！」

---

### 5️⃣ `console.group()` と `console.groupEnd()` - 整理整頓の達人

```javascript
console.group("🎯 ユーザー情報");
console.log("名前:", "太郎");
console.log("年齢:", 25);
console.log("職業:", "エンジニア");
console.groupEnd();

console.group("🎯 プロジェクト情報");
console.log("名前:", "Webアプリ開発");
console.log("期限:", "2024-12-31");
console.groupEnd();
```

```
出力イメージ：
▼ 🎯 ユーザー情報
    名前: 太郎
    年齢: 25
    職業: エンジニア
▼ 🎯 プロジェクト情報
    名前: Webアプリ開発
    期限: 2024-12-31
```

**ガネーシャ 🐘**：「グループ化すると、関連するログをまとめて折りたたみできるんや。ログが見やすくなるで！」

**生徒 👩‍💻**：「なるほど！グループ化すると見やすくなりますね！」

**ガネーシャ 🐘**：「せや！ちゃんと整理されたログは**未来の自分への贈り物**やで」

---

### 6️⃣ `console.time()` と `console.timeEnd()` - 処理速度の計測専門家

```javascript
console.time("⏱️ API呼び出し時間");

await axios.get("/api/tasks");

console.timeEnd("⏱️ API呼び出し時間");
```

```
出力イメージ：
⏱️ API呼び出し時間: 245.123ms
```

**用途**：処理にかかった時間を計測。パフォーマンス改善に役立つ。

**ガネーシャ 🐘**：「API が遅い時とか、どこがボトルネックか調べる時に使うんや」

---

## 📝 console ファミリー比較表

| メソッド          |   表示色   | 用途               | Laravel で言うと      |
| :---------------- | :--------: | :----------------- | :-------------------- |
| `console.log()`   |     黒     | 通常のログ         | `Log::info()`         |
| `console.error()` |     赤     | エラー             | `Log::error()`        |
| `console.warn()`  |     黄     | 警告               | `Log::warning()`      |
| `console.table()` |   表形式   | データ一覧表示     | `dump()` + 整形       |
| `console.group()` | グループ化 | ログの整理         | ログのグルーピング    |
| `console.time()`  |  時間計測  | パフォーマンス計測 | `Debugbar` のタイマー |

---

# 第 4 章：実践ミッション！API 通信を完全に理解する

**ガネーシャ 🐘**：「さぁ、ここからが本番の本番や！お前のプロジェクトで実際に API 通信を**完全に可視化**してみるで！」

**生徒 👩‍💻**：「可視化...ですか？」

**ガネーシャ 🐘**：「せや！今からやることは以下の 4 つや：」

```
🎯 実践ミッション

1. リクエストの内容を確認する
2. レスポンスの中身を徹底的に見る
3. エラーの原因を特定する
4. Networkタブと連携してプロのデバッグをする
```

---

## 🎯 ミッション 1：リクエスト内容を完全に把握する

**ガネーシャ 🐘**：「まずはな、**自分が何をサーバーに送ってるか**を理解せなアカンで」

### 📝 実装：タスク作成処理にログを追加

開くファイル：`resources/js/Pages/Projects/Show.vue` （プロジェクト詳細ページ）

**ガネーシャ 🐘**：「このファイルの中にタスク作成処理があるはずや。探してみ」

**生徒 👩‍💻**：「ありました！`handleTaskCreate` 関数ですね」

```javascript
const handleTaskCreate = async () => {
    console.group("📝 タスク作成処理開始");
    console.log("📤 送信するデータ:", taskForm.value);
    console.log("📍 送信先URL:", `/api/projects/${projectId}/tasks`);
    console.log("🕒 送信時刻:", new Date().toLocaleTimeString());

    try {
        const response = await axios.post(
            `/api/projects/${projectId}/tasks`,
            taskForm.value
        );

        console.log("✅ 作成成功！");
        console.log("📦 レスポンス全体:", response);
        console.log("📊 ステータスコード:", response.status);
        console.log("📝 レスポンスデータ:", response.data);
        console.log("🆕 作成されたタスク:", response.data.data);

        // 成功処理...
    } catch (err) {
        console.error("❌ 作成失敗！");
        console.error("🔍 エラーオブジェクト:", err);
        console.error("📊 HTTPステータス:", err.response?.status);
        console.error("📝 エラーレスポンス:", err.response?.data);
        console.error("💬 エラーメッセージ:", err.response?.data?.message);

        // バリデーションエラーの場合
        if (err.response?.data?.errors) {
            console.error("⚠️ バリデーションエラー:", err.response.data.errors);
            console.table(err.response.data.errors);
        }
    } finally {
        console.log("🏁 タスク作成処理終了");
        console.groupEnd();
    }
};
```

**生徒 👩‍💻**：「すごく詳しくログを出してますね！」

**ガネーシャ 🐘**：「せや！これが**プロのデバッグ**や。問題が起きた時に、どこで何が起きてるか一目瞭然になるんや」

---

## 🎯 ミッション 2：レスポンスの構造を理解する

**ガネーシャ 🐘**：「次はな、**Laravel から返ってくるレスポンスの構造**を理解するんや」

**生徒 👩‍💻**：「レスポンスの構造...？」

**ガネーシャ 🐘**：「そや。お前のプロジェクトではな、Laravel が以下のような形式でレスポンスを返しとる：」

### 📊 Laravel のレスポンス構造

```javascript
// ✅ 成功時のレスポンス
{
  success: true,
  message: "タスクを作成しました",
  data: {
    id: 1,
    title: "サンプルタスク",
    description: "説明文",
    status: "todo",
    project: { ... },
    created_by_user: { ... }
  }
}

// ❌ エラー時のレスポンス（バリデーションエラーの例）
{
  message: "The title field is required. (and 1 more error)",
  errors: {
    title: ["The title field is required."],
    description: ["The description field must not be greater than 255 characters."]
  }
}
```

**ガネーシャ 🐘**：「この構造を理解しとけば、どこにどんなデータがあるか分かるやろ？」

**生徒 👩‍💻**：「なるほど！成功時は `data` プロパティにデータが入ってて、エラー時（404や500）は `exception` と `trace` が、バリデーションエラー時（422）は `errors` プロパティが入ってるんですね」

**ガネーシャ 🐘**：「さすガネーシャの生徒や！飲み込みが早いな！」

---

### 📝 実装：レスポンス構造を確認するログ

```javascript
// レスポンスを受け取った直後
console.group("📦 レスポンス詳細分析");

console.log("🔍 レスポンス全体:", response);

// 第1階層（axios レスポンス）
console.log("📊 HTTPステータス:", response.status); // 200, 201, 400, 404, 500 など
console.log("📋 HTTPステータステキスト:", response.statusText); // "OK", "Created" など

// 第2階層（response.data - Laravel からの JSON）
console.log("📦 data プロパティ:", response.data);

// 第3階層（response.data.data - TaskResource のデータ）
console.log("📝 実際のタスクデータ:", response.data.data);

// オブジェクトの詳細表示
if (response.data.data) {
    console.table(response.data.data);
}

console.groupEnd();
```

**生徒 👩‍💻**：「これを実行すると、レスポンスの構造が階層的に分かるんですね！」

**ガネーシャ 🐘**：「せやせや！これが**データの読み解き方**や。Laravel で言えば、`dd($response)` でダンプするのと同じやな」

---

## 🎯 ミッション 3：エラーを徹底的に追跡する

**ガネーシャ 🐘**：「さて、次はエラーの時にもっと詳しい情報を出すようにしてみよう」

**生徒 👩‍💻**：「エラーの時もconsole.logで確認できるんですか？」

**ガネーシャ 🐘**：「せやで！さっきのfetchTask関数のcatchブロックにな、console.errorを追加するんや」

---

### 📝 実装：より詳細なエラーログ

**ガネーシャ 🐘**：「エラーの時にもっと詳しい情報を出すコードを追加や！」

```javascript
// エラーをキャッチした時
catch (err) {
  console.group("❌ エラー詳細分析");

  // エラーの種類を判定
  if (err.response) {
    // サーバーからレスポンスが返ってきた（400番台、500番台）
    console.error("🔴 サーバーエラー");
    console.error("📊 ステータスコード:", err.response.status);
    console.error("💬 メッセージ:", err.response.data.message);
    console.error("📝 例外クラス:", err.response.data.exception);

    // ステータスコード別の詳細
    switch (err.response.status) {
      case 400:
        console.error("⚠️ 400: リクエストが不正です");
        break;
      case 401:
        console.error("🔐 401: 認証が必要です");
        break;
      case 403:
        console.error("🚫 403: アクセスが拒否されました");
        break;
      case 404:
        console.error("🔍 404: リソースが見つかりません");
        break;
      case 422:
        console.error("📝 422: バリデーションエラー");
        console.table(err.response.data.errors);
        break;
      case 500:
        console.error("💥 500: サーバー内部エラー");
        break;
      default:
        console.error("❓ その他のエラー");
    }

    console.error("📦 レスポンス詳細:", err.response.data);

  } else if (err.request) {
    // リクエストは送信されたが、レスポンスがない（ネットワークエラー）
    console.error("🌐 ネットワークエラー");
    console.error("💬 原因: サーバーに接続できませんでした");
    console.error("🔍 確認事項:");
    console.error("  - サーバーは起動していますか？");
    console.error("  - ネットワーク接続は正常ですか？");
    console.error("  - CORSの設定は正しいですか？");

  } else {
    // リクエストの設定中にエラーが発生
    console.error("⚙️ リクエスト設定エラー");
    console.error("💬 メッセージ:", err.message);
  }

  console.error("📍 エラー発生箇所:", err.stack);
  console.groupEnd();

  alert("タスクの読み込みに失敗しました");
}
```

**生徒 👩‍💻**：「うわぁ！これならエラーの原因がすぐ分かりますね！」

**ガネーシャ 🐘**：「せやろ！console.logとconsole.errorを使いこなせば、**未来の自分を助ける**ことになるんや。ワシの教え子のベンジャミン・フランクリンくんも『時間こそ金なり』って言うとったけど、デバッグ時間を短縮することは**時間を生み出すこと**なんやで」

---

## 🎯 ミッション 4：Network タブと連携してプロのデバッグをする

**ガネーシャ 🐘**：「さぁ、最後の仕上げや！Console タブだけやなくて、Network タブも使いこなすんや」

**生徒 👩‍💻**：「Network タブ...？」

**ガネーシャ 🐘**：「せや！Network タブはな、**ブラウザとサーバー間の全ての通信を記録してくれる最強のツール**や」

---

### 📝 Network タブの使い方

**手順 1：Network タブを開く**

```
デベロッパーツールの上部にあるタブから
「Network」をクリック

┌─────────────────────────────────────┐
│ Elements  Console  Sources  Network │  ← これ！
│━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━│
│                                     │
│ Name      Status  Type  Size  Time │
│ ─────────────────────────────────  │
│ tasks     200     xhr   2.1KB 245ms│
│ projects  200     xhr   1.5KB 180ms│
└─────────────────────────────────────┘
```

**手順 2：API 通信を実行**

**ガネーシャ 🐘**：「お前のアプリでタスクを作成したり、詳細ページを開いたりしてみ」

**生徒 👩‍💻**：「あ！Network タブに通信の履歴が表示されました！」

```
Name            Status  Type    Size    Time
───────────────────────────────────────────
login           200     xhr     1.2KB   150ms
user            200     xhr     856B    80ms
projects        200     xhr     3.4KB   220ms
tasks           200     xhr     2.1KB   180ms
```

**手順 3：通信の詳細を確認**

**ガネーシャ 🐘**：「どれか一つをクリックしてみ。例えば `tasks` をクリックしてみ」

```
クリックすると右側に詳細が表示される：

▼ Headers（ヘッダー情報）
  Request URL: http://localhost/api/tasks/1
  Request Method: GET
  Status Code: 200 OK

▼ Payload（送信データ）
  title: "新しいタスク"
  description: "説明文"
  status: "todo"

▼ Preview（プレビュー）
  成功時のレスポンスが整形されて表示される

▼ Response（生のレスポンス）
  {
    "success": true,
    "message": "タスクを取得しました",
    "data": { ... }
  }
```

**生徒 👩‍💻**：「すごい！これなら Console タブを見なくても、通信の内容が全部分かりますね！」

**ガネーシャ 🐘**：「せやろ！でもな、Console タブと Network タブは**両方使うのが最強**なんや」

---

### 📊 Console タブ vs Network タブ 比較表

| 観点               | Console タブ             | Network タブ                  | おすすめの使い方                                     |
| :----------------- | :----------------------- | :---------------------------- | :--------------------------------------------------- |
| **ログの出力**     | 自分で書いた console.log | 自動で全通信を記録            | 両方使う                                             |
| **詳細度**         | 自分で必要な情報だけ     | 全ての情報が見える            | Console で絞り込み、Network で詳細確認               |
| **リクエスト**     | console.log で出力       | Headers タブで確認            | Network の方が見やすい                               |
| **レスポンス**     | console.log で出力       | Response / Preview タブで確認 | Network の方が整形されて見やすい                     |
| **処理の流れ**     | 時系列で追える           | 通信のタイミングが分かる      | Console で流れ、Network でタイミング                 |
| **エラー内容**     | 詳細にログを出せる       | ステータスコードが赤く表示    | Console でスタックトレース、Network でレスポンス確認 |
| **パフォーマンス** | console.time で計測      | Time 列で自動計測             | 両方で確認                                           |

---

### 📝 実践：Console と Network を連携させる

**ガネーシャ 🐘**：「ほな、両方を活用した最強のデバッグ方法を教えるで」

```javascript
const fetchTask = async (taskId) => {
    // Console: 処理開始を記録
    console.time(`⏱️ タスク取得: ID=${taskId}`);
    console.log(`🚀 タスク取得開始: /api/tasks/${taskId}`);
    console.log("👉 Network タブで通信を確認してください");

    try {
        const response = await axios.get(`/api/tasks/${taskId}`);

        // Console: 成功ログ
        console.log("✅ 取得成功");
        console.log("📦 データ:", response.data);
        console.log("👉 Network タブで Response を確認してください");

        return response.data;
    } catch (err) {
        // Console: エラーログ
        console.error("❌ 取得失敗");
        console.error("📊 ステータス:", err.response?.status);
        console.error("👉 Network タブで失敗した通信を確認してください");
        console.error("👉 赤く表示されている行をクリックしてください");

        throw err;
    } finally {
        // Console: 処理時間を表示
        console.timeEnd(`⏱️ タスク取得: ID=${taskId}`);
    }
};
```

**生徒 👩‍💻**：「Console でガイドを出しながら、Network タブで詳細を確認するんですね！」

**ガネーシャ 🐘**：「せや！これが**プロの連携技**や！両方の良いところを使うんや」

---

### 🎯 実践デバッグフロー

```
🔍 デバッグの流れ（プロの技）

1. Console タブで処理の流れを確認
   ├ どの関数が呼ばれたか
   ├ どんなデータを送信したか
   └ エラーが発生したか

2. Network タブで通信の詳細を確認
   ├ リクエストは正しく送られたか（Headers, Payload）
   ├ レスポンスは正しく返ってきたか（Response, Preview）
   └ ステータスコードは何か（Status）

3. Console タブでエラーの原因を特定
   ├ エラーメッセージは何か
   ├ スタックトレースはどこを指しているか
   └ バリデーションエラーはあるか

4. 修正して再実行
   └ 1に戻る
```

**ガネーシャ 🐘**：「この流れを繰り返せば、どんなバグでも倒せるで！」

---

# 第 5 章：実践演習問題

**ガネーシャ 🐘**：「さぁ、ここまで学んだことを実際に手を動かして試すで！」

**生徒 👩‍💻**：「はい！やってみます！」

---

## 🎯 演習 1：タスク一覧取得のログを追加する

### 問題

`resources/js/Pages/Projects/Show.vue` のタスク一覧取得処理に、以下のログを追加してください：

1. 処理開始のログ
2. リクエスト URL のログ
3. 取得したタスクの件数
4. 取得したタスクのリスト（テーブル形式）
5. 処理時間の計測

### ヒント

```javascript
const fetchTasks = async () => {
    // ここにログを追加してみよう！
    // console.time, console.log, console.table を使ってね

    try {
        const response = await axios.get(`/api/projects/${projectId}/tasks`);
        // ...
    } catch (err) {
        // ...
    } finally {
        // ...
    }
};
```

---

## 🎯 演習 2：バリデーションエラーを見やすく表示する

### 問題

タスク作成時にバリデーションエラーが発生した場合、エラー内容をコンソールに**テーブル形式**で表示してください。

### 例

```
タイトルを空欄にしてタスクを作成
↓
コンソールに以下のように表示される：

❌ バリデーションエラー
┌──────────────┬────────────────────────────────┐
│   フィールド   │          エラー内容              │
├──────────────┼────────────────────────────────┤
│    title     │ タイトルは必須です               │
│ description  │ 説明は255文字以内で入力してください │
└──────────────┴────────────────────────────────┘
```

### ヒント

```javascript
if (err.response?.status === 422) {
    // バリデーションエラーの場合
    // console.table を使ってみよう！
}
```

---

## 🎯 演習 3：API レスポンス時間を計測する

### 問題

全ての API 通信の実行時間を計測し、以下の条件で警告を出してください：

-   200ms 以下：正常（緑色のログ）
-   200ms〜500ms：注意（黄色のログ）
-   500ms 以上：遅い（赤色のログ）

### ヒント

```javascript
console.time("API通信");
const response = await axios.get("/api/tasks");
console.timeEnd("API通信");

// 時間を取得するには...
const startTime = performance.now();
// ... 処理 ...
const endTime = performance.now();
const duration = endTime - startTime;

if (duration < 200) {
    console.log(`✅ 速い: ${duration}ms`);
} else if (duration < 500) {
    console.warn(`⚠️ 少し遅い: ${duration}ms`);
} else {
    console.error(`❌ 遅い: ${duration}ms`);
}
```

---

## 🎯 演習 4：エラー発生時のスクリーンショットを取る

### 問題

デベロッパーツールの Console タブと Network タブを開いた状態で、以下のエラーを**意図的に発生させて**、スクリーンショットを撮ってください：

1. 404 エラー（存在しないタスクを取得）
2. 422 エラー（バリデーションエラー）
3. 500 エラー（サーバーエラー）
4. ネットワークエラー（サーバーを停止させる）

### 実行方法

```javascript
// 404 エラーを発生させる
const response = await axios.get("/api/tasks/99999");

// 422 エラーを発生させる
const response = await axios.post("/api/projects/1/tasks", {
    title: "", // 空欄にする
    description: "",
});

// 500 エラーを発生させる
// routes/api.php の /api/test エンドポイントを使う
const response = await axios.get("/api/test");

// ネットワークエラーを発生させる
// ターミナルで php artisan serve を停止してからリクエスト
```

**ガネーシャ 🐘**：「各エラーの時に、Console と Network でどんな情報が表示されるか確認するんやで！」

---

# 第 6 章：実務で使える console.log のベストプラクティス

**ガネーシャ 🐘**：「最後に、実務で使える console.log の**黄金ルール**を教えるで」

---

## 🏆 黄金ルール 10 カ条

### 1️⃣ ログには必ず絵文字を付ける

```javascript
// ❌ ダメな例
console.log("処理開始");

// ✅ 良い例
console.log("🚀 処理開始");
```

**理由**：ログが大量にある時でも、絵文字で一瞬で目的のログが見つかる。

---

### 2️⃣ ログにはラベルを付ける

```javascript
// ❌ ダメな例
console.log(response);

// ✅ 良い例
console.log("📦 APIレスポンス:", response);
```

**理由**：何のログか分からないと後で混乱する。

---

### 3️⃣ グループ化して整理する

```javascript
// ✅ 良い例
console.group("🎯 タスク作成処理");
console.log("送信データ:", data);
console.log("送信先:", url);
console.groupEnd();
```

**理由**：関連するログをまとめて見やすくする。

---

### 4️⃣ 配列・オブジェクトは console.table を使う

```javascript
// ❌ ダメな例
console.log(users); // [Object, Object, Object] みたいに表示される

// ✅ 良い例
console.table(users); // 表形式で見やすく表示される
```

**理由**：データの構造が一目で分かる。

---

### 5️⃣ エラーには console.error を使う

```javascript
// ❌ ダメな例
console.log("エラー:", err);

// ✅ 良い例
console.error("❌ エラー:", err);
```

**理由**：赤く表示されて目立つ。スタックトレースも自動で表示される。

---

### 6️⃣ 警告には console.warn を使う

```javascript
// ✅ 良い例
console.warn("⚠️ この機能は非推奨です");
```

**理由**：黄色で表示されて注意を引ける。

---

### 7️⃣ 処理時間を計測する

```javascript
// ✅ 良い例
console.time("⏱️ データ取得");
await fetchData();
console.timeEnd("⏱️ データ取得"); // ⏱️ データ取得: 245.123ms
```

**理由**：パフォーマンス改善のボトルネックが分かる。

---

### 8️⃣ 本番環境ではログを出さない

```javascript
// ✅ 良い例
if (import.meta.env.DEV) {
    console.log("🔧 開発環境でのみ表示されるログ");
}
```

**理由**：セキュリティとパフォーマンスのため。本番環境では不要なログを出さないようにしましょう。

---

### 9️⃣ ログは処理の「前」「後」「エラー」に分ける

```javascript
// ✅ 良い例
console.log("🚀 処理開始"); // 前
try {
    const result = await doSomething();
    console.log("✅ 処理成功", result); // 後（成功）
} catch (err) {
    console.error("❌ 処理失敗", err); // 後（失敗）
}
```

**理由**：処理の流れが追いやすくなる。

---

### 🔟 ログは「削除」ではなく「コメントアウト」する

```javascript
// ✅ 良い例
// console.log("🔍 デバッグ用ログ"); ← 後で使うかもしれないのでコメントアウト

// ❌ ダメな例
// 削除してしまうと、後でまた書くのが面倒
```

**理由**：後でデバッグする時にまた必要になる可能性がある。

---

## 📊 ログの優先度レベル

| レベル | メソッド          |  色   | 使うタイミング     | 絵文字例 |
| :----: | :---------------- | :---: | :----------------- | :------: |
|   1    | `console.error()` | 🔴 赤 | エラー発生時       | ❌ 💥 🚨 |
|   2    | `console.warn()`  | 🟡 黄 | 警告・非推奨       |  ⚠️ 🟡   |
|   3    | `console.log()`   | ⚫ 黒 | 通常のログ         | ✅ 🚀 📦 |
|   4    | `console.debug()` | 🔵 青 | 詳細なデバッグ情報 |  🔍 🔧   |

---

## 🎨 おすすめ絵文字リスト

| カテゴリ       | 絵文字 | 使うタイミング |
| :------------- | :----: | :------------- |
| **開始・終了** |   🚀   | 処理開始       |
|                |   🏁   | 処理終了       |
|                |   ⏱️   | 時間計測       |
| **成功**       |   ✅   | 成功           |
|                |   🎉   | 完了           |
|                |   👍   | OK             |
| **エラー**     |   ❌   | エラー         |
|                |   💥   | 致命的エラー   |
|                |   🚨   | 緊急エラー     |
| **警告**       |   ⚠️   | 警告           |
|                |   🟡   | 注意           |
| **データ**     |   📦   | レスポンス     |
|                |   📝   | データ         |
|                |   📊   | ステータス     |
| **通信**       |   📡   | リクエスト     |
|                |   🌐   | ネットワーク   |
|                |   🔌   | 接続           |
| **デバッグ**   |   🔍   | 調査中         |
|                |   🔧   | デバッグ情報   |
|                |   🐛   | バグ           |
| **ユーザー**   |   👤   | ユーザー       |
|                |   🔐   | 認証           |
|                |   🔑   | トークン       |

---

# 最終章：ガネーシャからのメッセージ

**ガネーシャ 🐘**：「お前、よう頑張ったな！ここまでで console.log の基本から応用まで全部マスターしたで」

**生徒 👩‍💻**：「ありがとうございます！でも、まだ不安です...」

**ガネーシャ 🐘**：「まぁまぁ、落ち着けや。大事なことを最後に教えたるわ」

---

## 🐘 ガネーシャの教え

### 1. **ログは未来の自分への手紙や**

```
今日書いたログが、明日のお前を助ける。
1週間後のお前を助ける。
1ヶ月後のお前を助ける。

「なんでこんなバグが起きてるんや！」って時に、
過去の自分が仕込んだログが救ってくれるんや。
```

### 2. **デバッグは恥ずかしいことやない**

```
ワシの教え子のエジソンくんは
「私は失敗したことがない。
 ただ、1万通りのうまくいかない方法を見つけただけだ」
って言うとった。

お前も console.log でバグを見つけるたびに、
「また一つ、うまくいく方法に近づいた」って思うんや。
```

### 3. **完璧なコードなんてない**

```
バグのないコードなんて存在せんのや。
だからこそ、console.log でこまめに確認する習慣が大事なんや。

早めに気づけば、早めに直せる。
それが console.log の本当の価値や。
```

### 4. **ログは芸術や**

```
見やすいログ = 美しいコード

絵文字、ラベル、グループ化...
これは全部「美しさ」のためや。

美しいログは、デバッグを楽しくする。
楽しくなれば、バグを倒すのも苦やなくなる。
```

---

## 📚 まとめ：今日学んだこと

| 項目                   | 内容                     | Laravel で言うと                |
| :--------------------- | :----------------------- | :------------------------------ |
| **console.log()**      | 変数や処理の流れを確認   | `dd()`, `dump()`, `Log::info()` |
| **console.error()**    | エラーを赤く表示         | `Log::error()`                  |
| **console.warn()**     | 警告を黄色で表示         | `Log::warning()`                |
| **console.table()**    | データを表形式で表示     | `dump()` + 整形                 |
| **console.group()**    | ログをグループ化         | ログのグルーピング              |
| **console.time()**     | 処理時間を計測           | `Debugbar` のタイマー           |
| **デベロッパーツール** | ブラウザのデバッグツール | `php artisan tinker` に近い     |
| **Console タブ**       | ログを確認する場所       | ログファイル `storage/logs/`    |
| **Network タブ**       | API 通信を確認する場所   | `Debugbar` の Network タブ      |

---

## 🎯 次のステップ

1. **実際にログを追加してみよう**

    - このプロジェクトの各 Vue コンポーネントにログを追加
    - エラーハンドリングを強化

2. **デベロッパーツールを使いこなそう**

    - Console タブと Network タブを行き来する
    - エラーが出たら必ず確認する習慣をつける

3. **チーム開発で活かそう**
    - レビュー時にログの有無を確認
    - バグ報告時にコンソールログをスクリーンショット

---

**ガネーシャ 🐘**：「さぁ、お前の旅はここからや！console.log を武器に、どんなバグも倒していくんやで！」

**生徒 👩‍💻**：「はい！頑張ります！」

**ガネーシャ 🐘**：「最後に一つだけ...あんみつ食べたいな～ 🍨」

**生徒 👩‍💻**：「結局それかい！」

**ガネーシャ 🐘**：「はい、Oh, My God!!」

---

# 🎓 おめでとうございます！

この講座を完了しました 🎉

これからは console.log を使いこなして、
楽しいフロントエンド開発ライフを送ってください！

```
       🐘
      /||\
     / || \
    🍨    🍨

ガネーシャより愛を込めて
```

---

## 📖 付録：困った時のチェックリスト

### ✅ ログが表示されない時

-   [ ] デベロッパーツールの Console タブを開いているか？
-   [ ] `console.log()` の記述場所は正しいか？
-   [ ] ブラウザをリロードしたか？
-   [ ] JavaScript のエラーで処理が止まっていないか？

### ✅ API 通信が失敗する時

-   [ ] サーバーは起動しているか？（`php artisan serve`）
-   [ ] Network タブでリクエストを確認したか？
-   [ ] ステータスコードは何か？（404? 500? 422?）
-   [ ] Console タブにエラーログは出ているか？
-   [ ] リクエスト URL は正しいか？

### ✅ エラーの原因が分からない時

-   [ ] Console タブの赤いエラーを確認したか？
-   [ ] Network タブで失敗した通信を確認したか？
-   [ ] スタックトレースを読んだか？
-   [ ] `console.log()` で変数の中身を確認したか？
-   [ ] 一つ前の状態に戻して動作確認したか？

---

**頑張ってください！🚀**
