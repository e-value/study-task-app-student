# Lesson3: FormRequest と Service への切り分けを体験する

## 🎯 この Lesson の目的

この Lesson3 では、あなた自身で以下を実装します：

1. **FormRequest を利用して Request を Controller から切り分ける**
2. **Service クラスを作成し、処理を Controller から移動する**
3. **Controller をスリムに保つ構成を体験する**
4. **処理の配置によってコードの読みやすさがどう変わるかを理解する**

---

## 💡 このレッスンで体験すること

実際のシステム開発では、Controller に以下のような処理が集まりがちです：

-   入力チェック（バリデーション）
-   認可チェック
-   ビジネスロジック
-   例外・エラーハンドリング

Lesson2 では、これらを **すべて Controller に直接実装** しました。
Lesson3 では、次のステップとして、

-   **入力・認可を FormRequest に分離**
-   **処理のまとまりを Service クラスに分離**

することで、Controller の役割を整理していきます。

---

## 📖 事前調査（読むだけで OK）

実装に入る前に、以下の記事を一読してください。
内容を完全に理解する必要はありません。

### ① FormRequest について調べる

-   FormRequest の役割
-   Request クラスとの違い
-   `authorize()` と `rules()` の責務

※ 講師が用意した FormRequest のサンプルを参考に実装します。

---

### ② Service クラスについて調べる

-   ▼ Laravel サービスクラスを活用したメソッドの実装方法
    [https://zenn.dev/imkei/articles/5b1f7948a78f6f](https://zenn.dev/imkei/articles/5b1f7948a78f6f)

-   ▼ 参考：Laravel 開発、new を使ってます？DI・サービスクラス・コンテナの話
    [https://qiita.com/t-mandokoro/items/90df58141446b454bbf7](https://qiita.com/t-mandokoro/items/90df58141446b454bbf7)

---

## 🌿 ブランチ作成

Lesson2 と同様に、Lesson3 用の作業ブランチを作成してください。

```bash
# main ブランチに切り替え
git checkout main
git pull origin main

# lesson3 ブランチを取得
git fetch origin lesson3
git checkout lesson3
git pull origin lesson3

# 作業用ブランチを作成
git checkout -b lesson3_自分の名字
```

---

## 📋 要件：実現すべき内容

Lesson3 では、以下の 2 点を行います。

### 1. Request を外部に切り分ける

-   FormRequest クラスを作成する
-   バリデーション処理を Controller から移動する
-   認可チェック（可能なもの）は FormRequest に移動する
-   Controller では `$request->validated()` を使用する

### 2. Service を切り分ける

-   Service クラスを作成する
-   Controller に記載されている処理を Service に移動する
-   Controller は Service を呼び出す役割に専念させる

---

## 📝 Step 1: FormRequest の実装

すでに **1 つの FormRequest がサンプルとして用意されています**。
それを参考に、他の処理についても FormRequest を作成してください。

### 実装時のポイント

-   `Validator::make()` を Controller で直接使用しない
-   入力チェックは FormRequest にまとめる
-   Controller 内の記述量が減ることを確認する

---

## 📝 Step 2: Service クラスの実装

次に、Controller に書かれている処理を Service クラスへ移動します。

例：

-   `ProjectService`
-   `TaskService`
-   `MembershipService`

### 実装時のポイント

-   Controller に書かれている処理をそのまま Service に移す
-   public メソッドと private メソッドを使って整理する
-   Controller が「処理の呼び出し役」になっているかを確認する

---

## 📌 実装時の注意点

-   正解を探す必要はありません
-   すべてを綺麗に整理する必要はありません
-   まずは **切り分けること** を優先してください

---

## 🚀 実装完了後の作業

実装が完了したら、以下の形式でプルリクエストを作成してください。

### プルリクエストのタイトル形式

```
【名前】Lesson03 実装
```

---

## 📘 この Lesson のまとめ

-   Controller から Request と処理を切り分けることで、役割が明確になります
-   FormRequest と Service を使うことで、構成の選択肢が増えます
-   処理の配置によって、コードの見通しがどう変わるかを確認してください

次の Lesson では、今回切り分けた構成を元に、
**「どこに何を書くべきか」** をさらに深く考えていきます。
