<script setup>
import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useToast } from "vue-toastification";
import axios from "axios";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const route = useRoute();
const router = useRouter();
const toast = useToast();
const taskId = route.params.id;

const task = ref(null);
const loading = ref(true);
const editing = ref(false);
const saving = ref(false);
const deleting = ref(false);
const error = ref(null);

const form = ref({
  title: "",
  description: "",
  status: "",
});

const fetchTask = async () => {
  try {
    loading.value = true;
    const response = await axios.get(`/api/tasks/${taskId}`);
    task.value = response.data.data;
  } catch (err) {
    console.error("Failed to fetch task:", err);
    error.value =
      err.response?.data?.message || "タスクの読み込みに失敗しました";
  } finally {
    loading.value = false;
  }
};

const startEditing = () => {
  form.value.title = task.value.title;
  form.value.description = task.value.description || "";
  form.value.status = task.value.status;
  editing.value = true;
};

const cancelEditing = () => {
  editing.value = false;
  error.value = null;
};

const saveChanges = async () => {
  try {
    saving.value = true;
    error.value = null;

    const response = await axios.put(`/api/tasks/${taskId}`, form.value);
    task.value = response.data.data;
    editing.value = false;
    toast.success(response.data.message || "タスクを更新しました");
  } catch (err) {
    console.error("Failed to update task:", err);
    error.value = err.response?.data?.message || "タスクの更新に失敗しました";
    toast.error(error.value);
  } finally {
    saving.value = false;
  }
};

const startTask = async () => {
  try {
    const response = await axios.post(`/api/tasks/${taskId}/start`);
    task.value = response.data.data;
    toast.success("タスクを開始しました");
  } catch (err) {
    console.error("Failed to start task:", err);
    error.value = err.response?.data?.message || "タスクの開始に失敗しました";
    toast.error(error.value);
  }
};

const completeTask = async () => {
  try {
    const response = await axios.post(`/api/tasks/${taskId}/complete`);
    task.value = response.data.data;
    toast.success("タスクを完了しました");
  } catch (err) {
    console.error("Failed to complete task:", err);
    error.value = err.response?.data?.message || "タスクの完了に失敗しました";
    toast.error(error.value);
  }
};

const confirmDelete = () => {
  if (
    confirm(
      `本当に「${task.value.title}」を削除しますか？\n\nこの操作は取り消すことができません。`
    )
  ) {
    deleteTask();
  }
};

const deleteTask = async () => {
  try {
    deleting.value = true;
    const response = await axios.delete(`/api/tasks/${taskId}`);

    toast.success(response.data.message || "タスクを削除しました");

    // トーストを表示させてからページ遷移
    setTimeout(() => {
      router.push({
        name: "project.detail",
        params: { id: task.value.project.id },
      });
    }, 500);
  } catch (err) {
    console.error("Failed to delete task:", err);
    error.value = err.response?.data?.message || "タスクの削除に失敗しました";
    toast.error(error.value);
    deleting.value = false;
  }
};

const formatDate = (dateString) => {
  if (!dateString) return "";
  const date = new Date(dateString);
  const now = new Date();
  const diff = now - date;
  const days = Math.floor(diff / (1000 * 60 * 60 * 24));

  if (days === 0) return "今日";
  if (days === 1) return "昨日";
  if (days < 7) return `${days}日前`;

  return date.toLocaleDateString("ja-JP", {
    year: "numeric",
    month: "long",
    day: "numeric",
  });
};

const formatFullDate = (dateString) => {
  if (!dateString) return "";
  const date = new Date(dateString);
  return date.toLocaleString("ja-JP", {
    year: "numeric",
    month: "long",
    day: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  });
};

onMounted(() => {
  fetchTask();
});
</script>

<template>
  <AuthenticatedLayout>
    <div class="py-12 px-4">
      <div class="max-w-4xl mx-auto">
        <!-- ローディング -->
        <div v-if="loading" class="flex items-center justify-center py-20">
          <div class="relative">
            <div
              class="w-16 h-16 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin"
            ></div>
            <p class="mt-4 text-slate-500 text-sm">読み込み中...</p>
          </div>
        </div>

        <template v-else-if="task">
          <!-- パンくずリスト -->
          <nav class="mb-8 flex items-center gap-2 text-sm flex-wrap">
            <router-link
              :to="{ name: 'projects' }"
              class="flex items-center gap-1 text-slate-600 hover:text-blue-600 transition-colors"
            >
              <svg
                class="w-4 h-4"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                />
              </svg>
              プロジェクト一覧
            </router-link>
            <svg
              class="w-4 h-4 text-slate-400"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 5l7 7-7 7"
              />
            </svg>
            <router-link
              :to="{ name: 'project.detail', params: { id: task.project.id } }"
              class="text-slate-600 hover:text-blue-600 transition-colors"
            >
              {{ task.project.name }}
            </router-link>
            <svg
              class="w-4 h-4 text-slate-400"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 5l7 7-7 7"
              />
            </svg>
            <span class="text-slate-800 font-medium">{{ task.title }}</span>
          </nav>

          <!-- ヘッダー -->
          <div class="mb-8 flex items-start justify-between gap-4">
            <div class="flex-1">
              <div class="flex items-center gap-3 mb-3">
                <h1
                  v-if="!editing"
                  class="text-4xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600"
                >
                  {{ task.title }}
                </h1>
                <input
                  v-else
                  v-model="form.title"
                  type="text"
                  required
                  class="flex-1 text-3xl font-bold px-4 py-2 rounded-xl backdrop-blur-lg bg-white/80 border border-slate-200 focus:border-blue-400 focus:ring-4 focus:ring-blue-100 transition-all outline-none"
                />
              </div>

              <!-- ステータスバッジ -->
              <div class="flex items-center gap-3 flex-wrap">
                <span
                  :class="[
                    'inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-bold backdrop-blur-lg border',
                    task.status === 'todo'
                      ? 'bg-slate-500/20 text-slate-700 border-slate-300/50'
                      : task.status === 'doing'
                      ? 'bg-amber-500/20 text-amber-700 border-amber-300/50'
                      : 'bg-emerald-500/20 text-emerald-700 border-emerald-300/50',
                  ]"
                >
                  <span
                    :class="[
                      'w-2 h-2 rounded-full',
                      task.status === 'todo'
                        ? 'bg-slate-500'
                        : task.status === 'doing'
                        ? 'bg-amber-500 animate-pulse'
                        : 'bg-emerald-500',
                    ]"
                  ></span>
                  {{
                    task.status === "todo"
                      ? "未着手"
                      : task.status === "doing"
                      ? "作業中"
                      : "完了"
                  }}
                </span>

                <!-- 作成者 -->
                <div class="flex items-center gap-2 text-slate-600">
                  <div
                    class="w-8 h-8 rounded-lg bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center text-white text-sm font-bold"
                  >
                    {{ task.created_by_user?.name?.charAt(0) || "?" }}
                  </div>
                  <span class="text-sm font-medium">{{
                    task.created_by_user?.name || "不明"
                  }}</span>
                </div>

                <!-- 作成日時 -->
                <span class="text-sm text-slate-500">
                  {{ formatDate(task.created_at) }}
                </span>
              </div>
            </div>

            <!-- アクションボタン -->
            <div class="flex gap-2">
              <button
                v-if="!editing"
                @click="startEditing"
                class="p-3 backdrop-blur-lg bg-white/80 hover:bg-white rounded-xl border border-slate-200 transition-all"
                title="編集"
              >
                <svg
                  class="w-5 h-5 text-slate-600"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                  />
                </svg>
              </button>
              <button
                @click="confirmDelete"
                :disabled="deleting"
                class="p-3 bg-gradient-to-r from-red-400 to-rose-500 hover:shadow-lg rounded-xl transition-all disabled:opacity-50"
                title="削除"
              >
                <svg
                  class="w-5 h-5 text-white"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                  />
                </svg>
              </button>
            </div>
          </div>

          <!-- エラー表示 -->
          <div
            v-if="error"
            class="mb-6 backdrop-blur-lg bg-red-500/10 border border-red-300/50 rounded-2xl p-6 shadow-xl"
          >
            <div class="flex items-center gap-3">
              <svg
                class="w-6 h-6 text-red-600"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
              <p class="text-red-800 font-medium">{{ error }}</p>
            </div>
          </div>

          <!-- メインコンテンツ -->
          <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- 左側：説明 -->
            <div class="lg:col-span-2 space-y-6">
              <!-- 説明 -->
              <div
                class="backdrop-blur-lg bg-white/70 rounded-2xl shadow-xl border border-white/50 p-8"
              >
                <h2 class="text-xl font-bold text-slate-800 mb-4">説明</h2>
                <div v-if="!editing" class="prose prose-slate max-w-none">
                  <p
                    v-if="task.description"
                    class="text-slate-700 whitespace-pre-wrap"
                  >
                    {{ task.description }}
                  </p>
                  <p v-else class="text-slate-400 italic">説明がありません</p>
                </div>
                <textarea
                  v-else
                  v-model="form.description"
                  rows="6"
                  class="w-full px-4 py-3 rounded-xl backdrop-blur-lg bg-white/80 border border-slate-200 focus:border-blue-400 focus:ring-4 focus:ring-blue-100 transition-all outline-none resize-none"
                  placeholder="タスクの詳細を入力してください"
                ></textarea>
              </div>

              <!-- 編集ボタン -->
              <div v-if="editing" class="flex items-center gap-4">
                <button
                  @click="saveChanges"
                  :disabled="saving"
                  class="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-500 to-indigo-500 text-white font-semibold rounded-xl hover:shadow-lg disabled:opacity-50 transition-all"
                >
                  <svg
                    v-if="!saving"
                    class="w-5 h-5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M5 13l4 4L19 7"
                    />
                  </svg>
                  <div
                    v-else
                    class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"
                  ></div>
                  {{ saving ? "保存中..." : "変更を保存" }}
                </button>
                <button
                  @click="cancelEditing"
                  class="px-6 py-3 backdrop-blur-lg bg-white/80 text-slate-700 font-semibold rounded-xl hover:bg-white transition-all border border-slate-200"
                >
                  キャンセル
                </button>
              </div>
            </div>

            <!-- 右側：アクション -->
            <div class="space-y-6">
              <!-- ステータス変更 -->
              <div
                class="backdrop-blur-lg bg-white/70 rounded-2xl shadow-xl border border-white/50 p-6"
              >
                <h3 class="text-lg font-bold text-slate-800 mb-4">
                  ステータス変更
                </h3>
                <div class="space-y-3">
                  <button
                    v-if="task.status === 'todo'"
                    @click="startTask"
                    class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-gradient-to-r from-amber-400 to-orange-500 text-white font-semibold rounded-xl hover:shadow-lg transition-all"
                  >
                    <svg
                      class="w-5 h-5"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"
                      />
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                      />
                    </svg>
                    タスクを開始
                  </button>
                  <button
                    v-if="task.status === 'doing'"
                    @click="completeTask"
                    class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-gradient-to-r from-emerald-400 to-teal-500 text-white font-semibold rounded-xl hover:shadow-lg transition-all"
                  >
                    <svg
                      class="w-5 h-5"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                      />
                    </svg>
                    タスクを完了
                  </button>
                  <p
                    v-if="task.status === 'done'"
                    class="text-center text-sm text-slate-600"
                  >
                    このタスクは完了しています
                  </p>
                </div>
              </div>

              <!-- ステータス編集（編集モード時） -->
              <div
                v-if="editing"
                class="backdrop-blur-lg bg-white/70 rounded-2xl shadow-xl border border-white/50 p-6"
              >
                <h3 class="text-lg font-bold text-slate-800 mb-4">
                  ステータス
                </h3>
                <select
                  v-model="form.status"
                  class="w-full px-4 py-3 rounded-xl backdrop-blur-lg bg-white/80 border border-slate-200 focus:border-blue-400 focus:ring-4 focus:ring-blue-100 transition-all outline-none"
                >
                  <option value="todo">未着手</option>
                  <option value="doing">作業中</option>
                  <option value="done">完了</option>
                </select>
              </div>

              <!-- 情報 -->
              <div
                class="backdrop-blur-lg bg-blue-500/10 border border-blue-300/30 rounded-2xl p-6"
              >
                <h3 class="text-sm font-bold text-slate-800 mb-3">
                  タスク情報
                </h3>
                <div class="space-y-2 text-sm text-slate-600">
                  <div class="flex items-center gap-2">
                    <svg
                      class="w-4 h-4"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                      />
                    </svg>
                    <span>作成: {{ formatFullDate(task.created_at) }}</span>
                  </div>
                  <div class="flex items-center gap-2">
                    <svg
                      class="w-4 h-4"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                      />
                    </svg>
                    <span>更新: {{ formatFullDate(task.updated_at) }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </template>

        <!-- エラー（タスクが見つからない） -->
        <div
          v-else
          class="backdrop-blur-lg bg-red-500/10 border border-red-300/50 rounded-2xl p-6 shadow-xl"
        >
          <div class="flex items-center gap-3">
            <svg
              class="w-6 h-6 text-red-600"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
              />
            </svg>
            <p class="text-red-800 font-medium">タスクが見つかりませんでした</p>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
