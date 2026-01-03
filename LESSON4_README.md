# Lesson4: Service層の設計を振り返り、権限チェックとカプセル化を理解する

## 🎯 この Lesson の目的

この Lesson4 では、以下を学習・実装します：

1. **Service層での権限チェックの実装** - Policyを使わない汎用的なパターンを学ぶ
2. **適切な例外処理の実装** - Controller層とService層の責務分離を理解する
3. **カプセル化の徹底** - public/privateメソッドの使い分けを実践する
4. **責務の一貫性** - すべてのCRUD操作に統一した権限チェックを実装する

---

## 👀 まず最初にやること（重要）

Lesson4 のコードベースには、**Lesson3 で記述した内容がすべて残っています**。

### 実装を始める前に、必ず以下を行ってください：

- 各 Service クラスを開き、**一通り目を通す**
- 文法や細かい処理を正確に理解する必要はありません
- 次の点を意識して確認してください：

    - どんな処理が書かれているか
    - Service が何を担当しているか
    - 権限チェックがどこで行われているか（または行われていないか）

📌 ポイント

> コードを「理解し切る」よりも、
> **「どこに権限チェックが必要か」「どのメソッドが外部に公開されているか」** を把握することを優先してください。

---

## 💡 このレッスンで体験すること

Lesson3 では、以下のような処理を **Service クラスに分離** しました。

- バリデーション（FormRequest）
- ビジネスロジック（Service）
- private メソッドによる整理

Lesson4 では、さらに一歩進んで、

- **権限チェックの統一** - すべてのstore/update操作に権限チェックを追加
- **例外処理の統一** - Controller層での適切なエラーハンドリング
- **カプセル化の徹底** - 内部メソッドのprivate化
- **責務の明確化** - Controller層とService層の役割分担

を実装していきます。

---

## 🌿 ブランチ作成

```bash
git checkout main
git pull origin main

git fetch origin lesson4
git checkout lesson4
git pull origin lesson4

git checkout -b lesson4_自分の名字
```

---

## 📋 要件：実現すべき内容

Lesson4 では、以下の3点を行います。

### 1. 権限チェックの追加

- タスク作成時にプロジェクトメンバーかチェック
- タスク更新時にプロジェクトメンバーかチェック
- プロジェクト更新時にオーナー/管理者かチェック

---

### 2. 例外処理の統一

- Service層で例外を投げる
- Controller層でtry-catchして適切なHTTPステータスコードを返す
- すべての権限チェックがある操作に例外処理を実装

---

### 3. カプセル化の徹底

- 外部から呼ばれないメソッドをprivateに変更
- ProjectService, ProjectMemberServiceの内部ヘルパーメソッドをprivate化
- Controller層のビジネスロジックをService層に移動

---

## 📖 事前学習：権限チェックとカプセル化について

以下の観点で、Service層の設計について考えてください：

- **権限チェックはどこで行うべきか**
  - Controller層？Service層？
  - なぜその層で行うのか？

- **例外処理の責務**
  - Service層：ビジネスルール違反を検知し例外を投げる
  - Controller層：例外をキャッチしHTTPレスポンスに変換する

- **カプセル化の原則**
  - public: 外部から呼ばれる公開API
  - private: 内部でのみ使うヘルパーメソッド

---

## 📝 実装時の注意点

### 権限チェックの実装パターン

```php
// Service層
public function createTask(array $data, Project $project, User $user): Task
{
    // 権限チェック
    if (!$this->isProjectMember($project, $user)) {
        throw new \Exception('このプロジェクトのタスクを作成する権限がありません');
    }
    
    // ビジネスロジック
    $task = Task::create([...]);
    return $task;
}

// Controller層
public function store(TaskRequest $request, Project $project): JsonResponse
{
    try {
        $task = $this->taskService->createTask(
            $request->validated(),
            $project,
            $request->user()
        );
        return response()->json($task, 201);
    } catch (\Exception $e) {
        return response()->json(['message' => $e->getMessage()], 403);
    }
}
```

---

## 🚀 実装完了後の作業

実装が完了したら、ブランチの内容をpushしてからプルリクエストを作成してください：

**1. ブランチの内容をpushする：**

```bash
# 変更をステージング
git add .

# コミット
git commit -m "Lesson04 実装"

# ブランチをpush
git push origin lesson4_自分の名字
```

**2. プルリクエストを作成する：**

以下の形式でプルリクエストを作成してください。

```
【名前】Lesson04 実装
```

---

## 📘 この Lesson のまとめ

- Service層での権限チェックの実装方法を学ぶ
- Controller層とService層の責務分離を理解する
- カプセル化の重要性を実践する
- 一貫性のある設計の重要性を体験する

次の Lesson では、
**Service層をさらに細分化し、より保守性の高い設計** について考えていきます。