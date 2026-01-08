<script setup>
import { useRouter } from "vue-router";
import GuestLayout from "@/Layouts/GuestLayout.vue";

const router = useRouter();

const goHome = () => {
  router.push({ name: "welcome" });
};

const goBack = () => {
  router.back();
};
</script>

<template>
  <GuestLayout>
    <div class="min-h-screen flex items-center justify-center px-4">
      <div class="max-w-md w-full text-center">
        <!-- 404アイコン -->
        <div class="mb-8">
          <h1 class="text-9xl font-bold text-gray-200">404</h1>
          <div class="text-4xl font-bold text-gray-800 mb-4">
            ページが見つかりません
          </div>
          <p class="text-gray-600 mb-8">
            お探しのページは存在しないか、移動した可能性があります。
          </p>
        </div>

        <!-- アクションボタン -->
        <div class="flex gap-4 justify-center">
          <button
            @click="goBack"
            class="px-6 py-3 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors"
          >
            戻る
          </button>
          <button
            @click="goHome"
            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
          >
            ホームへ
          </button>
        </div>
      </div>
    </div>
  </GuestLayout>
</template>
