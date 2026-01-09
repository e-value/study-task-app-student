<script setup>
defineProps({
  message: {
    type: String,
    default: null,
  },
  fallbackMessage: {
    type: String,
    default: "エラーが発生しました",
  },
  requestId: {
    type: String,
    default: null,
  },
  statusCode: {
    type: Number,
    default: null,
  },
});
</script>

<template>
  <div
    v-if="message || fallbackMessage"
    class="backdrop-blur-lg bg-red-500/10 border border-red-300/50 rounded-2xl p-6 shadow-xl"
  >
    <!-- サーバーエラー（500）の場合の特別な表示 -->
    <div v-if="statusCode === 500" class="space-y-4">
      <div class="flex items-center gap-3">
        <svg
          class="w-6 h-6 text-red-600 flex-shrink-0"
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
        <p class="text-red-800 font-medium text-lg">
          ⚠️ サーバーエラーが発生しました
        </p>
      </div>

      <div class="pl-9 space-y-2">
        <p class="text-red-700">申し訳ありません。</p>
        <p class="text-red-700">
          問題が解決しない場合は、以下のIDを添えて<br />
          管理者までお問い合わせください。
        </p>

        <div v-if="requestId" class="mt-4 pt-3 border-t border-red-200/50">
          <p class="text-red-700 flex items-center gap-2">
            <span class="text-lg">📋</span>
            <span class="font-semibold">エラーID:</span>
            <code
              class="ml-2 px-3 py-1 bg-red-100/50 rounded text-red-900 font-mono text-sm"
            >
              {{ requestId }}
            </code>
          </p>
        </div>
      </div>
    </div>

    <!-- その他のエラー（500以外）の通常表示 -->
    <div v-else class="flex items-center gap-3">
      <svg
        class="w-6 h-6 text-red-600 flex-shrink-0"
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
      <p class="text-red-800 font-medium">
        {{ message || fallbackMessage }}
      </p>
    </div>
  </div>
</template>
