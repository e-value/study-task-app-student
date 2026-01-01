# Lesson3: FormRequest と Service への切り分けを体験する

## 🎯 この Lesson の目的

この Lesson3 では、あなた自身で以下を実装します：

1. **FormRequest を利用して Request を Controller から切り分ける**
2. **Service クラスを作成し、処理を Controller から移動する**
3. **Service 内で private メソッドを使って処理を整理する**
4. **処理の配置によってコードの読みやすさがどう変わるかを理解する**

---

## 👀 まず最初にやること（重要）

Lesson3 のコードベースには、**Lesson2 で記述した内容がすべて残っています**。

### 実装を始める前に、必ず以下を行ってください：

-   各 Controller を開き、**一通り目を通す**
-   文法や細かい処理を正確に理解する必要はありません
-   次の点を意識して確認してください：

    -   どんな処理が書かれているか
    -   Controller が何を担当しているか
    -   処理の流れはどうなっているか

📌 ポイント

> コードを「理解し切る」よりも、
> **「どんな役割のコードが詰め込まれているか」** を把握することを優先してください。

---

## 💡 このレッスンで体験すること

Lesson2 では、以下のような処理を **すべて Controller に直接記載** しました。

-   バリデーション
-   認可チェック
-   ビジネスロジック
-   エラーハンドリング

Lesson3 では、これらを整理するために、

-   **入力・認可を FormRequest に分離**
-   **処理を Service クラスに分離**
-   **Service 内で private メソッドを使って整理**

していきます。

---

## 📖 事前学習 ①：Service クラスについて調べる

まずは、**Service クラスとは何か**を把握してください。

-   ▼ Laravel サービスクラスを活用したメソッドの実装方法
    [https://zenn.dev/imkei/articles/5b1f7948a78f6f](https://zenn.dev/imkei/articles/5b1f7948a78f6f)

-   ▼ 参考：Laravel 開発、new を使ってます？DI・サービスクラス・コンテナの話
    [https://qiita.com/t-mandokoro/items/90df58141446b454bbf7](https://qiita.com/t-mandokoro/items/90df58141446b454bbf7)

---

## 📖 事前学習 ②：private メソッドを理解する

Service の役割を把握したら、次に **Service 内をどう整理するか** を学びます。

以下の教材を読んで、private メソッドの考え方を理解してください：

👉 private メソッドの教材
[https://chatgpt.com/canvas/shared/6955a7a612e08191bdef34f2c0d873e8](https://chatgpt.com/canvas/shared/6955a7a612e08191bdef34f2c0d873e8)

この教材では、

-   public / private の違い
-   なぜ private メソッドに切り出すのか
-   「共通処理」や「意味のある処理のまとまり」の考え方

を解説しています。

---

## 📖 事前学習 ③：FormRequest について調べる

FormRequest について知らない場合は、以下を調べてから実装してください：

-   FormRequest の役割
-   通常の Request との違い
-   `authorize()` と `rules()` の責務

※ **FormRequest は 1 つサンプルが用意されています**。
それを参考に、他の処理も切り分けてください。

---

## 🌿 ブランチ作成

```bash
git checkout main
git pull origin main

git fetch origin lesson3
git checkout lesson3
git pull origin lesson3

git checkout -b lesson3_自分の名字
```

---

## 📋 要件：実現すべき内容

Lesson3 では、以下の 2 点を行います。

### 1. Request を外部に切り分ける（FormRequest）

-   FormRequest クラスを作成する
-   バリデーション処理を Controller から移動する
-   認可チェック（可能なもの）は FormRequest に移動する
-   Controller では `$request->validated()` を使用する

---

### 2. Service を切り分ける

#### 基本方針

-   **まずは Service クラスを作成して、Controller のメソッドを Service クラスのメソッドに移動してください**
-   最初から整理しきる必要はありません

#### Service 内での整理（private メソッド）

Service に移した後、以下を意識してください：

-   Service 内で使われている共通処理
-   意味のある処理のまとまり
-   処理の意図が読み取りにくい部分

これらを **private メソッドとして切り分けます**。

---

### 📌 参考実装について

-   **ProjectService の `store` メソッドを例として、Service への切り分けと private メソッドの使い方が実装されています**
-   Service への移し方・private メソッドの切り方の参考にしてください
-   同じ形にする必要はありませんが、考え方は参考にしてください

---

## 📝 実装時の注意点

-   正解を探す必要はありません
-   設計を完成させることが目的ではありません
-   「Service に移すとどう見えるか」を確認してください

---

## 🚀 実装完了後の作業

以下の形式でプルリクエストを作成してください。

```
【名前】Lesson03 実装
```

---

## 📘 この Lesson のまとめ

-   Lesson2 の Controller をベースに整理を行う
-   Service の役割を理解した上で切り分ける
-   private メソッドを使って Service 内を整理する
-   コードの見通しがどう変わるかを確認する

次の Lesson では、
**Service に集まった処理をさらにどう分割していくか** を考えていきます。
