<script setup>
defineProps({
  message: {
    type: String,
    default: null,
  },
  fallbackMessage: {
    type: String,
    default: 'エラーが発生しました',
  },
});
</script>

<template>
  <div
    v-if="message || fallbackMessage"
    class="backdrop-blur-lg bg-red-500/10 border border-red-300/50 rounded-2xl p-6 shadow-xl"
  >
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
      <p class="text-red-800 font-medium">
        {{ message || fallbackMessage }}
      </p>
    </div>
  </div>
</template>
