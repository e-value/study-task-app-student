<script setup>
import { computed } from "vue";

const props = defineProps({
  pagination: {
    type: Object,
    required: true,
  },
});

const emit = defineEmits(["page-changed"]);

const pages = computed(() => {
  const current = props.pagination.current_page;
  const last = props.pagination.last_page;
  const delta = 2; // 現在ページの前後に表示するページ数
  
  const range = [];
  const rangeWithDots = [];
  let l;

  // 最初のページは常に表示
  range.push(1);

  // 現在ページ周辺のページ
  for (let i = current - delta; i <= current + delta; i++) {
    if (i > 1 && i < last) {
      range.push(i);
    }
  }

  // 最後のページは常に表示
  if (last > 1) {
    range.push(last);
  }

  // ... を挿入
  range.forEach((i) => {
    if (l) {
      if (i - l === 2) {
        rangeWithDots.push(l + 1);
      } else if (i - l !== 1) {
        rangeWithDots.push("...");
      }
    }
    rangeWithDots.push(i);
    l = i;
  });

  return rangeWithDots;
});

const goToPage = (page) => {
  if (
    page !== "..." &&
    page >= 1 &&
    page <= props.pagination.last_page &&
    page !== props.pagination.current_page
  ) {
    emit("page-changed", page);
  }
};
</script>

<template>
  <div
    v-if="pagination.last_page > 1"
    class="flex items-center justify-between px-6 py-4 bg-gradient-to-r from-slate-50/80 to-blue-50/80 border-t border-slate-200/50"
  >
    <!-- 情報表示 -->
    <div class="text-sm text-slate-600">
      <span class="font-semibold">{{ pagination.from || 0 }}</span>
      〜
      <span class="font-semibold">{{ pagination.to || 0 }}</span>
      件 / 全
      <span class="font-semibold text-blue-600">{{ pagination.total }}</span>
      件
    </div>

    <!-- ページネーションボタン -->
    <div class="flex items-center gap-2">
      <!-- 前へボタン -->
      <button
        @click="goToPage(pagination.current_page - 1)"
        :disabled="pagination.current_page === 1"
        :class="[
          'px-3 py-2 rounded-lg font-medium transition-all duration-300 flex items-center gap-1',
          pagination.current_page === 1
            ? 'bg-slate-100 text-slate-400 cursor-not-allowed'
            : 'bg-white hover:bg-blue-50 text-slate-700 hover:text-blue-600 border border-slate-200 hover:border-blue-300 shadow-sm hover:shadow',
        ]"
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
            d="M15 19l-7-7 7-7"
          />
        </svg>
        <span class="hidden sm:inline">前へ</span>
      </button>

      <!-- ページ番号 -->
      <div class="hidden sm:flex items-center gap-1">
        <button
          v-for="(page, index) in pages"
          :key="index"
          @click="goToPage(page)"
          :disabled="page === '...'"
          :class="[
            'min-w-[2.5rem] px-3 py-2 rounded-lg font-medium transition-all duration-300',
            page === pagination.current_page
              ? 'bg-gradient-to-r from-blue-500 to-indigo-500 text-white shadow-lg'
              : page === '...'
              ? 'text-slate-400 cursor-default'
              : 'bg-white hover:bg-blue-50 text-slate-700 hover:text-blue-600 border border-slate-200 hover:border-blue-300 shadow-sm hover:shadow',
          ]"
        >
          {{ page }}
        </button>
      </div>

      <!-- 現在ページ表示（モバイル） -->
      <div class="sm:hidden px-4 py-2 bg-white rounded-lg border border-slate-200">
        <span class="text-sm font-medium text-slate-700">
          {{ pagination.current_page }} / {{ pagination.last_page }}
        </span>
      </div>

      <!-- 次へボタン -->
      <button
        @click="goToPage(pagination.current_page + 1)"
        :disabled="pagination.current_page === pagination.last_page"
        :class="[
          'px-3 py-2 rounded-lg font-medium transition-all duration-300 flex items-center gap-1',
          pagination.current_page === pagination.last_page
            ? 'bg-slate-100 text-slate-400 cursor-not-allowed'
            : 'bg-white hover:bg-blue-50 text-slate-700 hover:text-blue-600 border border-slate-200 hover:border-blue-300 shadow-sm hover:shadow',
        ]"
      >
        <span class="hidden sm:inline">次へ</span>
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
            d="M9 5l7 7-7 7"
          />
        </svg>
      </button>
    </div>
  </div>
</template>


