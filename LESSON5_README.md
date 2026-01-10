# Lesson5: コードリファクタリング実践 〜Controller の肥大化を解消する〜

## 🎯 この Lesson の目的

この Lesson5 では、**あなた自身で**以下を実施します：

1. **Lesson2 で作成したコードをリファクタリングする**
2. **Controller から責務を適切に切り分ける**
3. **コードの可読性・保守性・拡張性を向上させる**
4. **リファクタリング前後のコードを比較して改善を実感する**

---

## 👀 まず最初にやること（重要）

### リファクタリングの参考資料を確認する

実装を始める前に、以下の参考資料を確認してください：

👉 **リファクタリングの参考資料（必読）**  
<a href="https://claude.ai/public/artifacts/2e64c71d-5897-4dcc-836b-4cfaf10741a5" target="_blank">https://claude.ai/public/artifacts/2e64c71d-5897-4dcc-836b-4cfaf10741a5</a>

この資料では、以下の内容を解説しています：

-   Controller の責務とは何か
-   なぜ Controller を肥大化させてはいけないのか
-   どのようにコードを整理すべきか
-   具体的なリファクタリングの手法

📌 ポイント

> この資料を読むことで、**「何を」「どう」改善すべきか**の方向性が見えてきます。
> リファクタリングを始める前に、必ず目を通してください。

---

## 💡 このレッスンで体験すること

Lesson2 では、以下のような処理を **すべて Controller に直接記載** しました。

-   認証・認可チェック
-   バリデーション
-   ビジネスロジック
-   データベース操作

Lesson5 では、これらを整理するために、

-   **FormRequest は既に実装済み** - バリデーションは FormRequest に分離されています
-   **認可チェックを整理する** - 重複した権限チェックのコードを共通化します
-   **ビジネスロジックを Service や UseCase に分離する** - Controller をスリムに保ちます

---

## 📖 事前学習：リファクタリングについて調べる

リファクタリングを始める前に、以下の概念を理解してください。

### 1. リファクタリングとは何か

-   外部の動作を変えずに、内部構造を改善すること
-   コードの品質を高め、変更しやすくすること
-   バグを減らし、保守性を向上させること

### 2. Controller の責務

-   リクエストを受け取る
-   適切な処理に委譲する
-   レスポンスを返す

**Controller がやるべきではないこと：**

-   複雑なビジネスロジック
-   直接的なデータベース操作（複雑な場合）
-   重複したコードの記述

---

## 🌿 ブランチ作成

```bash
git checkout main
git pull origin main

git fetch origin lesson5
git checkout lesson5
git pull origin lesson5

git checkout -b lesson5_自分の名字
```

---

## 📋 要件：実現すべき内容

Lesson5 では、Lesson2 で作成したコードをリファクタリングします。

### 🎯 リファクタリングの目標

以下の観点でコードを改善してください：

#### 1. **可読性の向上**

-   メソッド名・変数名を意味のある名前にする
-   処理の意図が一目でわかるようにする
-   コメントがなくても理解できるコードにする

#### 2. **重複コードの削除**

-   同じ処理が複数の場所に記述されている場合、共通化する
-   特に認可チェック（メンバー判定・権限判定）の重複を解消する

#### 3. **責務の明確化**

-   Controller：リクエスト受け取り、処理委譲、レスポンス返却
-   FormRequest：バリデーション（既に実装済み）
-   Service / UseCase：ビジネスロジック
-   Model：データアクセス

---

## 📝 リファクタリングの進め方

### Step 1: 現状の問題点を把握する

まず、以下の Controller を開き、どのような問題があるかを確認してください：

-   `app/Http/Controllers/Api/TaskController.php`
-   `app/Http/Controllers/Api/ProjectController.php`
-   `app/Http/Controllers/Api/ProjectMemberController.php`

**確認すべき点：**

-   同じコードが何度も繰り返されていないか？
-   Controller が長すぎないか？
-   処理の意図がすぐに理解できるか？

### Step 2: まずは UseCase に移動する

**重要：最初から完璧を目指さない**

リファクタリングの第一歩は、**Controller の処理を UseCase に移動すること**です。

#### 2-1. UseCase を作成する

Controller の各メソッドに対応する UseCase を作成します。

```
1つのControllerメソッド = 1つのUseCaseファイル

例：
TaskController::store()     → CreateTaskUseCase
TaskController::index()     → GetTasksUseCase
TaskController::show()      → GetTaskUseCase
TaskController::update()    → UpdateTaskUseCase
TaskController::destroy()   → DeleteTaskUseCase
TaskController::start()     → StartTaskUseCase
TaskController::complete()  → CompleteTaskUseCase
```

**📌 命名のポイント：ファイル名は業務シナリオ名にする**

UseCase のファイル名は、「技術的な処理名」ではなく「業務シナリオ名」にすることが重要です。

```
❌ 悪い例（技術的な処理名）
- IndexTaskUseCase
- StoreTaskUseCase

✅ 良い例（業務シナリオ名）
- GetTasksUseCase          （タスク一覧を取得する）
- CreateTaskUseCase        （タスクを作成する）
- StartTaskUseCase         （タスクを開始する）
- CompleteTaskUseCase      （タスクを完了する）
```

**👉 「ユーザーが何をするのか」を表す名前にしてください。**

**UseCase の作成場所：**

```bash
# ディレクトリ作成
mkdir -p app/UseCases/Task
mkdir -p app/UseCases/Project
mkdir -p app/UseCases/Membership

# UseCaseファイルを作成（例）
touch app/UseCases/Task/CreateTaskUseCase.php
```

**📝 参考：既に作成済みの例**

プロジェクト一覧取得の UseCase は既に例として作成されています。

-   `app/UseCases/Project/GetProjectsUseCase.php`

このファイルを参考にしながら、他の UseCase も作成してください。

#### 2-2. Controller の処理をそのまま UseCase に移す

**まずは深く考えずに、Controller の処理をそのまま UseCase に移動してください。**

-   認可チェックも
-   ビジネスロジックも
-   データベース操作も

**すべてそのまま UseCase の `execute()` メソッドに移します。**

### Step 3: 共通ルールを発見して切り出す

UseCase に移動した後、**同じようなコードが複数の UseCase に出てきたら**、それを切り出します。

#### 3-1. まずは private メソッドに切り出す

同じ UseCase 内で繰り返し使うコードは、private メソッドに切り出します。

```php
class CreateTaskUseCase
{
    public function execute(...)
    {
        // メンバーチェック
        $this->ensureProjectMember($project, $user);

        // タスク作成
        // ...
    }

    // 👇 まずはここに書く
    private function ensureProjectMember(Project $project, User $user): void
    {
        // チェック処理
    }
}
```

#### 3-2. 複数の UseCase で使うようになったらルールクラスに移動

**「あれ？このチェック、他の UseCase でも使ってるな」**

と気づいたら、参考資料を読んで配置場所を考えてください。

**参考資料の配置基準：**

```
┌─────────────────────────────────────────────────┐
│ Q: このルールどこに置く？                        │
├─────────────────────────────────────────────────┤
│                                                 │
│ 1箇所だけで使う                                  │
│   → UseCase内のprivateメソッド                  │
│                                                 │
│ 同じドメイン内の複数UseCaseで使う                │
│   → UseCases/{Domain}/Rules/                   │
│   例: UseCases/Task/Rules/TaskRules.php         │
│                                                 │
│ 複数ドメインで使う                               │
│   → Services/{Domain}/                         │
│   例: Services/Project/ProjectRules.php         │
│                                                 │
└─────────────────────────────────────────────────┘
```

**👉 参考資料をよく読んで、「なぜその場所に配置するのか」を理解してください。**

#### 3-3. 段階的に進める

```
Week 1: Controller → UseCase に移動（そのまま移す）
Week 2: UseCase内で重複したコードをprivateメソッドに切り出す
Week 3: 他のUseCaseでも使うルールをRulesクラスに移動
Week 4: 参考資料を読んで、配置場所が適切か見直す
```

**焦らず、段階的に進めてください。**

---

## 📝 実装時の注意点

### 重要：完璧を求めすぎない

-   リファクタリングは段階的に行うものです
-   まずは 1 つの Controller から始めてください
-   **最初は Controller → UseCase に移すだけで OK**
-   共通化は後で考えます

### 参考資料を読みながら進める

-   UseCase に移した後、共通コードが出てきたら参考資料を読んでください
-   **「このルールはどこに配置すべきか？」** を参考資料で確認しながら進めます
-   配置場所の判断基準は参考資料に詳しく書かれています

---

## 📌 参考実装について

**今回は参考実装を用意していません。**

参考資料（<a href="https://claude.ai/public/artifacts/2e64c71d-5897-4dcc-836b-4cfaf10741a5" target="_blank">https://claude.ai/public/artifacts/2e64c71d-5897-4dcc-836b-4cfaf10741a5</a>）を読み、自分で考えて実装してください。

**考えるべきポイント：**

-   どの部分を切り出すか？
-   どのような名前をつけるか？
-   どこに配置するか？

これらを自分で判断することが、設計力を鍛えるための重要な訓練です。

---

## 🚀 実装完了後の作業

実装が完了したら、ブランチの内容を push してからプルリクエストを作成してください：

**1. ブランチの内容を push する：**

```bash
# 変更をステージング
git add .

# コミット
git commit -m "Lesson05 実装: コードリファクタリング"

# ブランチをpush
git push origin lesson5_自分の名字
```

**2. プルリクエストを作成する：**

以下の形式でプルリクエストを作成してください：

**プルリクエストのタイトル形式：**

```
【名前】Lesson05 実装
```

**例：**

-   【宮田】Lesson05 実装

**プルリクエストの説明に含めるべき内容：**

-   どの部分をリファクタリングしたか
-   どのような手法を採用したか（private メソッド、Trait、Service、Policy など）
-   リファクタリング前後でどのように改善されたか

---

## 📘 この Lesson のまとめ

-   Lesson2 の Controller をベースにリファクタリングを行う
-   重複コードを特定し、適切に共通化する
-   Controller の責務を明確にし、スリムに保つ
-   コードの可読性・保守性・拡張性を向上させる

**次のステップ：**

Lesson5 が完了したら、Lesson4 と同様に振り返りを行い、リファクタリング前後でどのような変化があったかを確認しましょう。

---

## 📚 参考リンク

-   **必読：** <a href="https://claude.ai/public/artifacts/2e64c71d-5897-4dcc-836b-4cfaf10741a5" target="_blank">リファクタリングの参考資料</a>
