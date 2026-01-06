# フロントエンドエラーハンドリング共通化ガイド

## 📋 概要

フロントエンドのエラーハンドリングを共通化し、コードの重複を削減し、保守性を向上させました。

## 🏗️ アーキテクチャ

### 1. ユーティリティ関数 (`utils/apiError.js`)

API エラーからメッセージを抽出する関数を提供します。

```javascript
import { extractErrorMessage, extractValidationErrors } from "@/utils/apiError";

// エラーメッセージを抽出
const message = extractErrorMessage(error, "デフォルトメッセージ");

// バリデーションエラーを抽出
const validationErrors = extractValidationErrors(error);
```

### 2. Composable (`composables/useApiError.js`)

エラーハンドリング用の Composable を提供します。

```javascript
import { useApiError } from "@/composables/useApiError";

const { error, validationErrors, handleError, clearError } = useApiError();
```

### 3. 共通コンポーネント (`Components/ApiError.vue`)

エラー表示用の共通コンポーネントです。

```vue
<ApiError :message="error" fallback-message="デフォルトメッセージ" />
```

## 📝 使用方法

### 基本的な使い方

```vue
<script setup>
import { ref } from "vue";
import axios from "axios";
import { useApiError } from "@/composables/useApiError";
import ApiError from "@/Components/ApiError.vue";

const data = ref(null);
const loading = ref(false);

// エラーハンドリング用のComposable
const { error, handleError, clearError } = useApiError();

const fetchData = async () => {
    try {
        loading.value = true;
        clearError(); // エラーをクリア

        const response = await axios.get("/api/data");
        data.value = response.data.data || response.data;
    } catch (err) {
        handleError(err, "データの読み込みに失敗しました");
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <div>
        <!-- エラー表示 -->
        <ApiError v-if="error" :message="error" />

        <!-- データ表示 -->
        <div v-else-if="data">
            {{ data }}
        </div>
    </div>
</template>
```

### バリデーションエラーを含む場合

```vue
<script setup>
import { ref } from "vue";
import axios from "axios";
import { useApiError } from "@/composables/useApiError";
import ApiError from "@/Components/ApiError.vue";
import InputError from "@/Components/InputError.vue";

const form = ref({
    name: "",
    email: "",
});

const { error, validationErrors, handleError, clearError } = useApiError();

const submit = async () => {
    try {
        clearError();
        await axios.post("/api/form", form.value);
        // 成功処理
    } catch (err) {
        handleError(err, "送信に失敗しました");
    }
};
</script>

<template>
    <form @submit.prevent="submit">
        <!-- エラー表示 -->
        <ApiError v-if="error" :message="error" />

        <!-- バリデーションエラー表示 -->
        <div>
            <input v-model="form.name" />
            <InputError :message="validationErrors.name?.[0]" />
        </div>

        <div>
            <input v-model="form.email" />
            <InputError :message="validationErrors.email?.[0]" />
        </div>

        <button type="submit">送信</button>
    </form>
</template>
```

### フォールバックメッセージ付き

```vue
<template>
    <!-- エラーがない場合、フォールバックメッセージを表示 -->
    <ApiError
        v-else
        :message="error"
        fallback-message="データが見つかりませんでした"
    />
</template>
```

## 🔄 既存コンポーネントの移行方法

### Before（修正前）

```vue
<script setup>
const error = ref(null);

const fetchData = async () => {
    try {
        const response = await axios.get("/api/data");
        data.value = response.data;
    } catch (err) {
        error.value = err.response?.data?.message || "エラーが発生しました";
    }
};
</script>

<template>
    <div v-if="error" class="error">
        {{ error }}
    </div>
</template>
```

### After（修正後）

```vue
<script setup>
import { useApiError } from "@/composables/useApiError";
import ApiError from "@/Components/ApiError.vue";

const { error, handleError, clearError } = useApiError();

const fetchData = async () => {
    try {
        clearError();
        const response = await axios.get("/api/data");
        data.value = response.data;
    } catch (err) {
        handleError(err, "エラーが発生しました");
    }
};
</script>

<template>
    <ApiError v-if="error" :message="error" />
</template>
```

## 📊 対応しているエラーレスポンス形式

### 1. グローバル例外ハンドラー（ApiResponse）

```json
{
    "success": false,
    "message": "エラーメッセージ"
}
```

### 2. コントローラーの try-catch

```json
{
    "message": "エラーメッセージ"
}
```

### 3. バリデーションエラー

```json
{
    "message": "バリデーションエラー",
    "errors": {
        "name": ["名前は必須です"],
        "email": ["メールアドレスの形式が正しくありません"]
    }
}
```

## 🎯 メリット

1. **コードの重複削減**: エラーメッセージ抽出ロジックを一元化
2. **保守性の向上**: エラーハンドリングの変更が一箇所で済む
3. **一貫性**: すべてのコンポーネントで同じエラーハンドリングロジックを使用
4. **再利用性**: Composable とコンポーネントを再利用可能
5. **型安全性**: TypeScript 対応も容易

## 🔧 カスタマイズ

### カスタムエラーメッセージ

```javascript
// 特定のエラーに対してカスタムメッセージを設定
const { error, handleError } = useApiError();

try {
    await axios.get("/api/data");
} catch (err) {
    if (err.response?.status === 404) {
        handleError(err, "データが見つかりませんでした");
    } else {
        handleError(err, "エラーが発生しました");
    }
}
```

### エラーメッセージの手動設定

```javascript
const { error, setError } = useApiError();

// 手動でエラーメッセージを設定
setError("カスタムエラーメッセージ");
```

## 📚 関連ファイル

-   `resources/js/utils/apiError.js` - エラーメッセージ抽出ユーティリティ
-   `resources/js/composables/useApiError.js` - エラーハンドリング Composable
-   `resources/js/Components/ApiError.vue` - エラー表示コンポーネント

## 🚀 今後の拡張

-   [ ] TypeScript 対応
-   [ ] エラーログの自動送信（Sentry 等）
-   [ ] リトライ機能
-   [ ] エラーの種類に応じた表示のカスタマイズ
