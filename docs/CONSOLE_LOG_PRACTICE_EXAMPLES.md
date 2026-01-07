# 🎯 console.log 実践サンプルコード集

このファイルには、実際にプロジェクトで使える console.log のサンプルコードをまとめています。
コピー＆ペーストして、実際に動かしてみましょう！

---

## 📝 目次

1. [タスク詳細ページ（Show.vue）のログ強化](#1-タスク詳細ページshowvueのログ強化)
2. [プロジェクト詳細ページ（Projects/Show.vue）のログ強化](#2-プロジェクト詳細ページprojectsshowvueのログ強化)
3. [エラーハンドリングの強化](#3-エラーハンドリングの強化)
4. [API通信時間の計測](#4-api通信時間の計測)
5. [開発環境でのみログを出す設定](#5-開発環境でのみログを出す設定)

---

## 1. タスク詳細ページ（Show.vue）のログ強化

### 📂 ファイル: `resources/js/Pages/Tasks/Show.vue`

### 🎯 fetchTask 関数にログを追加

**現在のコード（30-42行目）を以下に置き換えてください：**

```javascript
const fetchTask = async () => {
  console.group("🔍 タスク詳細取得処理");
  console.time("⏱️ タスク取得時間");
  console.log("🚀 処理開始");
  console.log("📍 タスクID:", taskId);
  
  try {
    loading.value = true;
    clearError();
    
    const url = `/api/tasks/${taskId}`;
    console.log("📡 APIリクエスト:", url);
    
    const response = await axios.get(url);
    
    console.log("✅ API通信成功");
    console.log("📦 レスポンス全体:", response);
    console.log("📊 HTTPステータス:", response.status, response.statusText);
    console.log("💬 メッセージ:", response.data.message);
    console.log("📝 取得したタスク:", response.data.data);
    
    // タスクデータをテーブル形式で見やすく表示
    if (response.data.data) {
      console.group("📋 タスク詳細データ");
      console.table({
        "ID": response.data.data.id,
        "タイトル": response.data.data.title,
        "説明": response.data.data.description,
        "ステータス": response.data.data.status,
        "作成者": response.data.data.created_by_user?.name,
        "プロジェクト": response.data.data.project?.name,
      });
      console.groupEnd();
    }
    
    task.value = response.data.data || response.data;
    
  } catch (err) {
    console.error("❌ API通信失敗");
    console.error("🔍 エラーオブジェクト:", err);
    
    if (err.response) {
      // サーバーからレスポンスが返ってきた場合
      console.error("📊 HTTPステータス:", err.response.status);
      console.error("💬 エラーメッセージ:", err.response.data?.message);
      console.error("🆔 リクエストID:", err.response.data?.request_id);
      console.error("📦 エラーレスポンス詳細:", err.response.data);
      
      // ステータスコード別の詳細メッセージ
      switch (err.response.status) {
        case 404:
          console.error("🔍 404エラー: タスクが見つかりませんでした");
          console.error("💡 確認事項: タスクID", taskId, "は存在しますか？");
          break;
        case 401:
          console.error("🔐 401エラー: 認証が必要です");
          console.error("💡 確認事項: ログインしていますか？");
          break;
        case 403:
          console.error("🚫 403エラー: アクセスが拒否されました");
          console.error("💡 確認事項: このタスクへのアクセス権限がありますか？");
          break;
        case 500:
          console.error("💥 500エラー: サーバー内部エラー");
          console.error("💡 確認事項: Laravelのログを確認してください");
          break;
      }
      
    } else if (err.request) {
      // リクエストは送信されたが、レスポンスがない
      console.error("🌐 ネットワークエラー");
      console.error("💡 確認事項:");
      console.error("  - サーバーは起動していますか？（php artisan serve）");
      console.error("  - ネットワーク接続は正常ですか？");
      
    } else {
      // リクエストの設定中にエラーが発生
      console.error("⚙️ リクエスト設定エラー");
      console.error("💬 エラーメッセージ:", err.message);
    }
    
    console.error("📍 エラー発生箇所:");
    console.error(err.stack);
    
  } finally {
    loading.value = false;
    console.timeEnd("⏱️ タスク取得時間");
    console.log("🏁 処理終了");
    console.groupEnd();
  }
};
```

---

### 🎯 saveChanges 関数にログを追加

**現在のコード（56-71行目）を以下に置き換えてください：**

```javascript
const saveChanges = async () => {
  console.group("💾 タスク更新処理");
  console.time("⏱️ タスク更新時間");
  console.log("🚀 処理開始");
  
  try {
    saving.value = true;
    clearError();
    
    console.log("📤 送信データ:", form.value);
    console.table({
      "タイトル": form.value.title,
      "説明": form.value.description,
      "ステータス": form.value.status,
    });
    console.log("📍 送信先URL:", `/api/tasks/${taskId}`);
    console.log("📡 HTTPメソッド: PUT");
    
    const response = await axios.put(`/api/tasks/${taskId}`, form.value);
    
    console.log("✅ 更新成功");
    console.log("📦 レスポンス:", response);
    console.log("💬 メッセージ:", response.data.message);
    console.log("📝 更新後のタスク:", response.data.data);
    
    // 更新前後の比較
    console.group("🔄 更新内容の比較");
    console.table({
      "フィールド": ["タイトル", "説明", "ステータス"],
      "更新前": [
        task.value.title,
        task.value.description,
        task.value.status,
      ],
      "更新後": [
        response.data.data.title,
        response.data.data.description,
        response.data.data.status,
      ],
    });
    console.groupEnd();
    
    task.value = response.data.data;
    editing.value = false;
    toast.success(response.data.message || "タスクを更新しました");
    
  } catch (err) {
    console.error("❌ 更新失敗");
    console.error("🔍 エラー:", err);
    
    if (err.response?.status === 422) {
      // バリデーションエラー
      console.error("📝 バリデーションエラー発生");
      console.error("⚠️ エラー詳細:", err.response.data.errors);
      console.table(err.response.data.errors);
    } else {
      console.error("📊 HTTPステータス:", err.response?.status);
      console.error("💬 エラーメッセージ:", err.response?.data?.message);
      toast.error("タスクの更新に失敗しました");
    }
    
  } finally {
    saving.value = false;
    console.timeEnd("⏱️ タスク更新時間");
    console.log("🏁 処理終了");
    console.groupEnd();
  }
};
```

---

### 🎯 startTask / completeTask にログを追加

**現在のコード（73-93行目）を以下に置き換えてください：**

```javascript
const startTask = async () => {
  console.group("▶️ タスク開始処理");
  console.log("🚀 タスクを開始します");
  console.log("📍 タスクID:", taskId);
  console.log("📊 現在のステータス:", task.value.status);
  
  try {
    const response = await axios.post(`/api/tasks/${taskId}/start`);
    
    console.log("✅ ステータス変更成功");
    console.log("📊 変更後のステータス:", response.data.data.status);
    
    task.value = response.data.data;
    toast.success("タスクを開始しました");
    
  } catch (err) {
    console.error("❌ ステータス変更失敗");
    console.error("🔍 エラー:", err);
    console.error("💬 エラーメッセージ:", err.response?.data?.message);
    toast.error("タスクの開始に失敗しました");
  } finally {
    console.log("🏁 処理終了");
    console.groupEnd();
  }
};

const completeTask = async () => {
  console.group("✅ タスク完了処理");
  console.log("🚀 タスクを完了させます");
  console.log("📍 タスクID:", taskId);
  console.log("📊 現在のステータス:", task.value.status);
  
  try {
    const response = await axios.post(`/api/tasks/${taskId}/complete`);
    
    console.log("✅ ステータス変更成功");
    console.log("📊 変更後のステータス:", response.data.data.status);
    console.log("🎉 タスクが完了しました！");
    
    task.value = response.data.data;
    toast.success("タスクを完了しました");
    
  } catch (err) {
    console.error("❌ ステータス変更失敗");
    console.error("🔍 エラー:", err);
    console.error("💬 エラーメッセージ:", err.response?.data?.message);
    toast.error("タスクの完了に失敗しました");
  } finally {
    console.log("🏁 処理終了");
    console.groupEnd();
  }
};
```

---

### 🎯 deleteTask にログを追加

**現在のコード（105-124行目）を以下に置き換えてください：**

```javascript
const deleteTask = async () => {
  console.group("🗑️ タスク削除処理");
  console.log("🚀 処理開始");
  console.log("📍 削除対象タスク:", {
    ID: taskId,
    タイトル: task.value.title,
    プロジェクト: task.value.project.name,
  });
  console.warn("⚠️ この操作は取り消せません");
  
  try {
    deleting.value = true;
    
    const response = await axios.delete(`/api/tasks/${taskId}`);
    
    console.log("✅ 削除成功");
    console.log("💬 メッセージ:", response.data.message);
    console.log("🔙 プロジェクト詳細ページに遷移します");
    
    toast.success(response.data.message || "タスクを削除しました");
    
    // トーストを表示させてからページ遷移
    setTimeout(() => {
      router.push({
        name: "project.detail",
        params: { id: task.value.project.id },
      });
    }, 500);
    
  } catch (err) {
    console.error("❌ 削除失敗");
    console.error("🔍 エラー:", err);
    console.error("📊 HTTPステータス:", err.response?.status);
    console.error("💬 エラーメッセージ:", err.response?.data?.message);
    toast.error("タスクの削除に失敗しました");
    deleting.value = false;
  } finally {
    console.log("🏁 処理終了");
    console.groupEnd();
  }
};
```

---

## 2. プロジェクト詳細ページ（Projects/Show.vue）のログ強化

### 📂 ファイル: `resources/js/Pages/Projects/Show.vue`

**プロジェクト詳細ページでも同様のログを追加しましょう！**

### 🎯 タスク作成処理にログを追加

```javascript
const handleTaskCreate = async () => {
  console.group("📝 タスク作成処理");
  console.time("⏱️ タスク作成時間");
  console.log("🚀 処理開始");
  console.log("📍 プロジェクトID:", projectId);
  
  console.group("📤 送信データ");
  console.table({
    "タイトル": taskForm.value.title,
    "説明": taskForm.value.description,
    "ステータス": taskForm.value.status,
  });
  console.groupEnd();
  
  console.log("📍 送信先URL:", `/api/projects/${projectId}/tasks`);
  console.log("📡 HTTPメソッド: POST");
  console.log("🕒 送信時刻:", new Date().toLocaleTimeString());
  
  try {
    const response = await axios.post(
      `/api/projects/${projectId}/tasks`,
      taskForm.value
    );
    
    console.log("✅ 作成成功！");
    console.log("📦 レスポンス:", response);
    console.log("📊 HTTPステータス:", response.status, response.statusText);
    console.log("💬 メッセージ:", response.data.message);
    console.log("🆕 作成されたタスク:", response.data.data);
    
    // 作成されたタスクの詳細を表示
    console.group("📋 作成されたタスク詳細");
    console.table({
      "ID": response.data.data.id,
      "タイトル": response.data.data.title,
      "説明": response.data.data.description,
      "ステータス": response.data.data.status,
    });
    console.groupEnd();
    
    // 成功処理...
    toast.success(response.data.message || "タスクを作成しました");
    showTaskModal.value = false;
    taskForm.value = { title: "", description: "", status: "todo" };
    
    // タスクリストを再取得
    await fetchTasks();
    
  } catch (err) {
    console.error("❌ 作成失敗！");
    console.error("🔍 エラーオブジェクト:", err);
    
    if (err.response) {
      console.error("📊 HTTPステータス:", err.response.status);
      console.error("💬 エラーメッセージ:", err.response.data?.message);
      console.error("🆔 リクエストID:", err.response.data?.request_id);
      
      // バリデーションエラーの場合
      if (err.response.status === 422 && err.response.data?.errors) {
        console.error("📝 バリデーションエラー発生");
        console.error("⚠️ エラー詳細:", err.response.data.errors);
        
        console.group("📋 バリデーションエラー一覧");
        console.table(err.response.data.errors);
        console.groupEnd();
        
        // 各フィールドのエラーを表示
        Object.entries(err.response.data.errors).forEach(([field, messages]) => {
          console.error(`  ❌ ${field}:`, messages.join(", "));
        });
      }
    } else if (err.request) {
      console.error("🌐 ネットワークエラー");
      console.error("💡 確認事項:");
      console.error("  - サーバーは起動していますか？");
      console.error("  - ネットワーク接続は正常ですか？");
    }
    
    toast.error(err.response?.data?.message || "タスクの作成に失敗しました");
    
  } finally {
    console.timeEnd("⏱️ タスク作成時間");
    console.log("🏁 処理終了");
    console.groupEnd();
  }
};
```

---

### 🎯 タスク一覧取得にログを追加

```javascript
const fetchTasks = async () => {
  console.group("📋 タスク一覧取得処理");
  console.time("⏱️ タスク一覧取得時間");
  console.log("🚀 処理開始");
  console.log("📍 プロジェクトID:", projectId);
  
  try {
    const url = `/api/projects/${projectId}/tasks`;
    console.log("📡 APIリクエスト:", url);
    
    const response = await axios.get(url);
    
    console.log("✅ 取得成功");
    console.log("📦 レスポンス:", response);
    console.log("📊 取得件数:", response.data.data?.length || 0, "件");
    
    if (response.data.data && response.data.data.length > 0) {
      console.group("📋 タスク一覧");
      console.table(
        response.data.data.map((task) => ({
          ID: task.id,
          タイトル: task.title,
          ステータス: task.status,
          作成者: task.created_by_user?.name,
        }))
      );
      console.groupEnd();
      
      // ステータス別の集計
      const statusCount = {
        todo: 0,
        doing: 0,
        done: 0,
      };
      response.data.data.forEach((task) => {
        statusCount[task.status]++;
      });
      
      console.group("📊 ステータス別集計");
      console.table(statusCount);
      console.log("未着手:", statusCount.todo, "件");
      console.log("作業中:", statusCount.doing, "件");
      console.log("完了:", statusCount.done, "件");
      console.groupEnd();
    } else {
      console.log("📭 タスクはまだありません");
    }
    
    tasks.value = response.data.data || [];
    
  } catch (err) {
    console.error("❌ 取得失敗");
    console.error("🔍 エラー:", err);
    console.error("📊 HTTPステータス:", err.response?.status);
    console.error("💬 エラーメッセージ:", err.response?.data?.message);
    
    toast.error("タスクの取得に失敗しました");
    
  } finally {
    console.timeEnd("⏱️ タスク一覧取得時間");
    console.log("🏁 処理終了");
    console.groupEnd();
  }
};
```

---

## 3. エラーハンドリングの強化

### 📂 参考：プロジェクトで使われている `useApiError.js`

**💡 注意：このプロジェクトには `useApiError.js` という composable が用意されていますが、まずは生の `console.error` を使って、エラーの構造を理解することが大切です。**

以下は参考として、プロジェクトで実際に使われているエラーハンドリングの例です。

```javascript
// これは参考コードです。まずは上記の例のように、
// 生の console.error でエラーを確認する練習をしましょう。

const handleError = (err, defaultMessage = "エラーが発生しました") => {
  // 開発環境でのみ詳細ログを出力
  if (import.meta.env.VITE_APP_DEBUG) {
    console.group("🚨 API Error 詳細分析");
    console.error("⏰ エラー発生時刻:", new Date().toLocaleString());
    console.error("🔍 エラーオブジェクト:", err);
    
    if (err.response) {
      // サーバーからレスポンスが返ってきた場合
      console.group("📡 レスポンス情報");
      console.error("📊 ステータスコード:", err.response.status);
      console.error("📋 ステータステキスト:", err.response.statusText);
      console.error("📍 リクエストURL:", err.config?.url);
      console.error("📡 HTTPメソッド:", err.config?.method?.toUpperCase());
      console.error("📦 レスポンスデータ:", err.response.data);
      console.error("🆔 リクエストID:", err.response.data?.request_id);
      console.groupEnd();
      
      // ステータスコード別の詳細情報
      console.group("💡 エラー詳細とトラブルシューティング");
      switch (err.response.status) {
        case 400:
          console.error("⚠️ 400 Bad Request");
          console.error("原因: リクエストの形式が不正です");
          console.error("確認事項:");
          console.error("  - 送信データの形式は正しいですか？");
          console.error("  - 必須パラメータは含まれていますか？");
          break;
          
        case 401:
          console.error("🔐 401 Unauthorized");
          console.error("原因: 認証が必要です");
          console.error("確認事項:");
          console.error("  - ログインしていますか？");
          console.error("  - トークンの有効期限は切れていませんか？");
          break;
          
        case 403:
          console.error("🚫 403 Forbidden");
          console.error("原因: アクセスが拒否されました");
          console.error("確認事項:");
          console.error("  - このリソースへのアクセス権限はありますか？");
          console.error("  - プロジェクトのメンバーですか？");
          break;
          
        case 404:
          console.error("🔍 404 Not Found");
          console.error("原因: リソースが見つかりません");
          console.error("確認事項:");
          console.error("  - URLは正しいですか？");
          console.error("  - リソースIDは存在しますか？");
          console.error("  - 削除されていませんか？");
          break;
          
        case 422:
          console.error("📝 422 Unprocessable Entity");
          console.error("原因: バリデーションエラー");
          console.error("バリデーションエラー詳細:");
          if (err.response.data?.errors) {
            console.table(err.response.data.errors);
            Object.entries(err.response.data.errors).forEach(([field, messages]) => {
              console.error(`  ❌ ${field}:`, messages);
            });
          }
          break;
          
        case 429:
          console.error("⏱️ 429 Too Many Requests");
          console.error("原因: リクエストが多すぎます");
          console.error("確認事項:");
          console.error("  - しばらく待ってから再試行してください");
          break;
          
        case 500:
          console.error("💥 500 Internal Server Error");
          console.error("原因: サーバー内部エラー");
          console.error("確認事項:");
          console.error("  - Laravelのログを確認してください");
          console.error("  - storage/logs/laravel.log を見てください");
          break;
          
        case 503:
          console.error("🔧 503 Service Unavailable");
          console.error("原因: サーバーが一時的に利用できません");
          console.error("確認事項:");
          console.error("  - メンテナンス中ではありませんか？");
          console.error("  - サーバーは起動していますか？");
          break;
          
        default:
          console.error(`❓ ${err.response.status} その他のエラー`);
          console.error("原因: 予期しないエラーが発生しました");
      }
      console.groupEnd();
      
      // リクエストデータの表示
      if (err.config?.data) {
        console.group("📤 送信したデータ");
        try {
          const requestData = JSON.parse(err.config.data);
          console.table(requestData);
        } catch {
          console.log(err.config.data);
        }
        console.groupEnd();
      }
      
    } else if (err.request) {
      // リクエストは送信されたが、レスポンスがない
      console.group("🌐 ネットワークエラー");
      console.error("原因: サーバーから応答がありません");
      console.error("確認事項:");
      console.error("  ✓ サーバーは起動していますか？");
      console.error("    → ターミナルで 'php artisan serve' を実行してください");
      console.error("  ✓ ネットワーク接続は正常ですか？");
      console.error("    → インターネット接続を確認してください");
      console.error("  ✓ CORSの設定は正しいですか？");
      console.error("    → config/cors.php を確認してください");
      console.error("  ✓ ファイアウォールでブロックされていませんか？");
      console.groupEnd();
      
    } else {
      // リクエストの設定中にエラーが発生
      console.group("⚙️ リクエスト設定エラー");
      console.error("原因: リクエストの設定に失敗しました");
      console.error("💬 エラーメッセージ:", err.message);
      console.error("📍 スタックトレース:", err.stack);
      console.groupEnd();
    }
    
    console.groupEnd();
  }
  
  // エラーメッセージを抽出（画面表示用）
  error.value = extractErrorMessage(err, defaultMessage);
  
  // リクエストIDとステータスコードを取得
  if (err.response?.data) {
    requestId.value = err.response.data.request_id || null;
    statusCode.value = err.response.status || null;
  } else {
    requestId.value = null;
    statusCode.value = null;
  }
  
  // バリデーションエラーを抽出
  const validation = extractValidationErrors(err);
  if (validation) {
    validationErrors.value = validation;
  } else {
    validationErrors.value = {};
  }
};
```

---

## 4. API通信時間の計測

### 汎用的なAPI通信ログ関数

**新しいユーティリティファイルを作成します。**

### 📂 新規ファイル: `resources/js/utils/apiLogger.js`

```javascript
/**
 * API通信のログを出力するユーティリティ
 */

// パフォーマンス計測用のマップ
const timers = new Map();

/**
 * APIリクエスト開始時のログ
 */
export function logApiStart(method, url, data = null) {
  if (!import.meta.env.VITE_APP_DEBUG) return;
  
  const requestId = `${method}-${url}-${Date.now()}`;
  timers.set(requestId, performance.now());
  
  console.group(`📡 API ${method.toUpperCase()} ${url}`);
  console.log("🚀 リクエスト開始");
  console.log("⏰ 時刻:", new Date().toLocaleTimeString());
  console.log("📍 URL:", url);
  console.log("📡 メソッド:", method.toUpperCase());
  
  if (data) {
    console.log("📤 送信データ:");
    console.table(data);
  }
  
  console.log("👉 Networkタブで通信を確認できます");
  console.groupEnd();
  
  return requestId;
}

/**
 * APIレスポンス成功時のログ
 */
export function logApiSuccess(requestId, response) {
  if (!import.meta.env.VITE_APP_DEBUG) return;
  
  const startTime = timers.get(requestId);
  const duration = startTime ? performance.now() - startTime : null;
  
  console.group(`✅ API Success`);
  console.log("📦 レスポンス:", response.data);
  console.log("📊 ステータス:", response.status, response.statusText);
  console.log("💬 メッセージ:", response.data.message);
  
  if (duration !== null) {
    const durationMs = duration.toFixed(2);
    
    if (duration < 200) {
      console.log(`⚡ 実行時間: ${durationMs}ms（高速）`);
    } else if (duration < 500) {
      console.warn(`⏱️ 実行時間: ${durationMs}ms（少し遅い）`);
    } else {
      console.error(`🐌 実行時間: ${durationMs}ms（遅い）`);
    }
    
    timers.delete(requestId);
  }
  
  console.log("👉 Networkタブで詳細を確認できます");
  console.groupEnd();
}

/**
 * APIエラー時のログ
 */
export function logApiError(requestId, error) {
  if (!import.meta.env.VITE_APP_DEBUG) return;
  
  const startTime = timers.get(requestId);
  const duration = startTime ? performance.now() - startTime : null;
  
  console.group(`❌ API Error`);
  
  if (error.response) {
    console.error("📊 ステータス:", error.response.status);
    console.error("💬 エラーメッセージ:", error.response.data?.message);
    console.error("📦 エラー詳細:", error.response.data);
  } else if (error.request) {
    console.error("🌐 ネットワークエラー: サーバーから応答がありません");
  } else {
    console.error("⚙️ リクエスト設定エラー:", error.message);
  }
  
  if (duration !== null) {
    console.error(`⏱️ エラーまでの時間: ${duration.toFixed(2)}ms`);
    timers.delete(requestId);
  }
  
  console.log("👉 Networkタブでエラーの詳細を確認できます");
  console.groupEnd();
}

/**
 * 使用例:
 * 
 * const requestId = logApiStart('GET', '/api/tasks/1');
 * try {
 *   const response = await axios.get('/api/tasks/1');
 *   logApiSuccess(requestId, response);
 * } catch (err) {
 *   logApiError(requestId, err);
 * }
 */
```

---

### 使用例：apiLogger を使った実装

```javascript
import { logApiStart, logApiSuccess, logApiError } from "@/utils/apiLogger";

const fetchTask = async () => {
  const requestId = logApiStart("GET", `/api/tasks/${taskId}`);
  
  try {
    loading.value = true;
    const response = await axios.get(`/api/tasks/${taskId}`);
    
    logApiSuccess(requestId, response);
    task.value = response.data.data;
    
  } catch (err) {
    logApiError(requestId, err);
  } finally {
    loading.value = false;
  }
};
```

---

## 5. 開発環境でのみログを出す設定

### 📂 ファイル: `.env`

```bash
# 開発環境
VITE_APP_DEBUG=true

# 本番環境
# VITE_APP_DEBUG=false
```

### 使い方

```javascript
// 方法1: 開発環境でのみログを出す
if (import.meta.env.DEV) {
  console.log("🔧 これは開発環境でのみ表示されます");
}

// 方法2: APP_DEBUG フラグで制御
if (import.meta.env.VITE_APP_DEBUG) {
  console.log("🔧 APP_DEBUG が true の時のみ表示されます");
}

// 方法3: 汎用的なログ関数を作成
function devLog(...args) {
  if (import.meta.env.VITE_APP_DEBUG) {
    console.log(...args);
  }
}

// 使用例
devLog("🔍", "デバッグ情報:", someVariable);
```

### ログレベルごとの関数

```javascript
// resources/js/utils/logger.js
export const logger = {
  log(...args) {
    if (import.meta.env.VITE_APP_DEBUG) {
      console.log(...args);
    }
  },
  
  error(...args) {
    if (import.meta.env.VITE_APP_DEBUG) {
      console.error(...args);
    }
  },
  
  warn(...args) {
    if (import.meta.env.VITE_APP_DEBUG) {
      console.warn(...args);
    }
  },
  
  table(data) {
    if (import.meta.env.VITE_APP_DEBUG) {
      console.table(data);
    }
  },
  
  group(label) {
    if (import.meta.env.VITE_APP_DEBUG) {
      console.group(label);
    }
  },
  
  groupEnd() {
    if (import.meta.env.VITE_APP_DEBUG) {
      console.groupEnd();
    }
  },
};

// 使用例
import { logger } from "@/utils/logger";

logger.log("✅ 処理成功");
logger.error("❌ エラー発生");
logger.table(users);
```

---

## 🎯 まとめ：実践的なログの追加手順

### ステップ1：基本のログを追加

```javascript
console.log("🚀 処理開始");
// ... 処理 ...
console.log("✅ 処理成功");
```

### ステップ2：データの中身を確認

```javascript
console.log("📦 データ:", response.data);
console.table(users);  // 配列やオブジェクトは table で
```

### ステップ3：エラーハンドリングを追加

```javascript
try {
  // ...
} catch (err) {
  console.error("❌ エラー:", err);
  console.error("📊 ステータス:", err.response?.status);
}
```

### ステップ4：グループ化して整理

```javascript
console.group("🎯 処理名");
// ... ログ ...
console.groupEnd();
```

### ステップ5：処理時間を計測

```javascript
console.time("⏱️ 処理時間");
// ... 処理 ...
console.timeEnd("⏱️ 処理時間");
```

---

## 🔥 今すぐ試せる演習

### 演習1：タスク詳細ページで試す

1. `resources/js/Pages/Tasks/Show.vue` を開く
2. 上記のサンプルコードをコピー＆ペースト
3. ブラウザで `/tasks/1` にアクセス
4. デベロッパーツールの Console タブを開く
5. ログを確認する

### 演習2：わざとエラーを発生させる

1. タスクIDを存在しないIDに変更（例：99999）
2. ブラウザをリロード
3. Console でエラーログを確認
4. Network タブで404エラーを確認

### 演習3：バリデーションエラーを確認

1. タスク作成フォームでタイトルを空欄にする
2. 送信ボタンをクリック
3. Console でバリデーションエラーを確認
4. console.table でエラー一覧を確認

---

**さぁ、実際に手を動かしてみましょう！🚀**
