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
[https://claude.ai/public/artifacts/2e64c71d-5897-4dcc-836b-4cfaf10741a5](https://claude.ai/public/artifacts/2e64c71d-5897-4dcc-836b-4cfaf10741a5)

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
-   エラーハンドリング
-   データベース操作

Lesson5 では、これらを整理するために、

-   **FormRequest は既に実装済み** - バリデーションは FormRequest に分離されています
-   **認可チェックを整理する** - 重複した権限チェックのコードを共通化します
-   **ビジネスロジックを Service や UseCase に分離する** - Controller をスリムに保ちます
-   **エラーハンドリングを統一する** - 一貫したエラーレスポンスを返します

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

### 3. 参考記事（推奨）

-   ▼ Laravel の Controller をスリムに保つ方法
    [https://laracasts.com/discuss/channels/laravel/keeping-controllers-thin](https://laracasts.com/discuss/channels/laravel/keeping-controllers-thin)

-   ▼ リファクタリングの基本原則
    [https://refactoring.guru/refactoring](https://refactoring.guru/refactoring)

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

#### 4. **エラーハンドリングの統一**

-   一貫したエラーレスポンス形式にする
-   適切な HTTP ステータスコードを返す（403, 404, 409, 422 など）

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

### Step 2: 重複コードを特定する

特に以下のコードが重複している箇所を探してください：

```php
// 自分が所属しているかチェック（よく出てくる）
$isMember = $project->users()
    ->where('users.id', $request->user()->id)
    ->exists();

if (!$isMember) {
    return response()->json([
        'message' => 'このプロジェクトにアクセスする権限がありません',
    ], 403);
}
```

このような重複したコードを、どのように共通化できるか考えてください。

### Step 3: 共通化の手法を検討する

重複したコードを共通化する方法として、以下の手法が考えられます：

#### 手法 A：private メソッドに切り出す

Controller 内で private メソッドを作成し、重複コードをまとめる

```php
private function checkProjectMember(Request $request, Project $project): void
{
    $isMember = $project->users()
        ->where('users.id', $request->user()->id)
        ->exists();

    if (!$isMember) {
        abort(403, 'このプロジェクトにアクセスする権限がありません');
    }
}
```

#### 手法 B：Trait を使用する

複数の Controller で使用する場合、Trait にまとめる

```php
// app/Http/Controllers/Api/Concerns/ChecksProjectMembership.php
trait ChecksProjectMembership
{
    protected function ensureProjectMember(Request $request, Project $project): void
    {
        // ...
    }
}
```

#### 手法 C：Service クラスに切り出す

認可チェックを専用の Service クラスに委譲する

```php
// app/Services/ProjectAuthorizationService.php
class ProjectAuthorizationService
{
    public function ensureMember(User $user, Project $project): void
    {
        // ...
    }
}
```

#### 手法 D：Policy を使用する（Laravel の標準機能）

Laravel の Policy を使って認可ロジックを管理する

```php
// app/Policies/ProjectPolicy.php
class ProjectPolicy
{
    public function view(User $user, Project $project): bool
    {
        // ...
    }
}
```

**どの手法を選ぶべきか？**

-   シンプルな場合：private メソッドや Trait
-   複雑な認可ロジック：Policy
-   ビジネスロジックと一緒に管理：Service クラス

**参考資料を確認しながら、自分で最適な手法を選んでください。**

---

## 📝 実装時の注意点

### 重要：完璧を求めすぎない

-   リファクタリングは段階的に行うものです
-   まずは 1 つの Controller から始めてください
-   動作を確認しながら、少しずつ改善していきましょう

### テストを活用する

-   リファクタリング前後で動作が変わっていないことを確認してください
-   Postman や API テストツールを使って動作確認しましょう

### 既存の FormRequest を活用する

-   バリデーションは既に FormRequest に切り出されています
-   FormRequest を変更する必要はありません（変更しても OK）

---

## 📌 参考実装について

**今回は参考実装を用意していません。**

参考資料（[https://claude.ai/public/artifacts/2e64c71d-5897-4dcc-836b-4cfaf10741a5](https://claude.ai/public/artifacts/2e64c71d-5897-4dcc-836b-4cfaf10741a5)）を読み、自分で考えて実装してください。

**考えるべきポイント：**

-   どの部分を切り出すか？
-   どのような名前をつけるか？
-   どこに配置するか？

これらを自分で判断することが、設計力を鍛えるための重要な訓練です。

---

## 🧪 動作確認

リファクタリング後、以下を確認してください：

### 1. API の動作確認（Postman など）

以下のエンドポイントが正しく動作することを確認：

-   プロジェクト一覧取得
-   プロジェクト作成
-   プロジェクト詳細取得
-   プロジェクト更新
-   プロジェクト削除
-   タスク一覧取得
-   タスク作成・更新・削除
-   タスク開始・完了

### 2. エラーケースの確認

以下のエラーケースが正しく動作することを確認：

-   権限がない場合（403）
-   存在しないリソースへのアクセス（404）
-   バリデーションエラー（422）
-   ステータス遷移エラー（409）

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

-   **必読：** [リファクタリングの参考資料](https://claude.ai/public/artifacts/2e64c71d-5897-4dcc-836b-4cfaf10741a5)
-   [Laravel Controllers - 公式ドキュメント](https://laravel.com/docs/11.x/controllers)
-   [Laravel Authorization - 公式ドキュメント](https://laravel.com/docs/11.x/authorization)
-   [Laravel Policies - 公式ドキュメント](https://laravel.com/docs/11.x/authorization#creating-policies)
-   [Refactoring Guru](https://refactoring.guru/refactoring)
