<template>
  <div
    class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50"
  >
    <!-- モダンなヘッダー -->
    <nav
      class="backdrop-blur-lg bg-white/80 border-b border-white/50 shadow-sm sticky top-0 z-50"
    >
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 justify-between items-center">
          <!-- 左側：ロゴ＋ナビゲーション -->
          <div class="flex items-center gap-8">
            <!-- ロゴ -->
            <router-link
              :to="{ name: 'projects' }"
              class="flex items-center gap-3 group"
            >
              <div
                class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg group-hover:shadow-xl transition-all duration-300 group-hover:scale-105"
              >
                <svg
                  class="w-6 h-6 text-white"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                  />
                </svg>
              </div>
              <span
                class="text-xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600 hidden sm:block"
              >
                タスク管理
              </span>
            </router-link>

            <!-- ナビゲーションリンク -->
            <div class="hidden md:flex items-center gap-2">
              <router-link
                :to="{ name: 'projects' }"
                :class="[
                  'px-4 py-2 rounded-xl font-medium transition-all duration-300',
                  $route.name === 'projects' || $route.name === 'project.detail'
                    ? 'bg-gradient-to-r from-blue-500 to-indigo-500 text-white shadow-lg'
                    : 'text-slate-600 hover:text-slate-800 hover:bg-white/50',
                ]"
              >
                <div class="flex items-center gap-2">
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
                      d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"
                    />
                  </svg>
                  プロジェクト
                </div>
              </router-link>
              <router-link
                :to="{ name: 'users' }"
                :class="[
                  'px-4 py-2 rounded-xl font-medium transition-all duration-300',
                  $route.name === 'users'
                    ? 'bg-gradient-to-r from-blue-500 to-indigo-500 text-white shadow-lg'
                    : 'text-slate-600 hover:text-slate-800 hover:bg-white/50',
                ]"
              >
                <div class="flex items-center gap-2">
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
                      d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                    />
                  </svg>
                  ユーザー
                </div>
              </router-link>
            </div>
          </div>

          <!-- 右側：ユーザーメニュー -->
          <div class="flex items-center gap-4">
            <!-- ユーザー情報＋ドロップダウン -->
            <div class="hidden sm:flex items-center">
              <Dropdown align="right" width="56">
                <template #trigger>
                  <button
                    type="button"
                    class="flex items-center gap-3 px-4 py-2 rounded-xl backdrop-blur-lg bg-white/60 hover:bg-white/80 border border-white/50 transition-all duration-300 hover:shadow-md"
                  >
                    <!-- アバター -->
                    <div
                      class="w-8 h-8 rounded-lg bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center text-white font-bold text-sm shadow-md"
                    >
                      {{ authStore.user?.name?.charAt(0) || "?" }}
                    </div>
                    <div class="text-left hidden lg:block">
                      <div class="text-sm font-semibold text-slate-800">
                        {{ authStore.user?.name }}
                      </div>
                      <div class="text-xs text-slate-500">
                        {{ authStore.user?.email }}
                      </div>
                    </div>
                    <svg
                      class="w-4 h-4 text-slate-600"
                      xmlns="http://www.w3.org/2000/svg"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                        clip-rule="evenodd"
                      />
                    </svg>
                  </button>
                </template>

                <template #content>
                  <div class="py-2">
                    <!-- プロフィール情報 -->
                    <div class="px-4 py-3 border-b border-slate-100">
                      <div class="text-sm font-semibold text-slate-800">
                        {{ authStore.user?.name }}
                      </div>
                      <div class="text-xs text-slate-500 mt-1">
                        {{ authStore.user?.email }}
                      </div>
                    </div>

                    <!-- メニュー項目 -->
                    <div class="py-2">
                      <DropdownLink
                        :to="{ name: 'projects' }"
                        class="flex items-center gap-3"
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
                            d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"
                          />
                        </svg>
                        プロジェクト一覧
                      </DropdownLink>
                      <DropdownLink
                        :to="{ name: 'users' }"
                        class="flex items-center gap-3"
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
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                          />
                        </svg>
                        ユーザー一覧
                      </DropdownLink>
                    </div>

                    <div class="border-t border-slate-100 py-2">
                      <DropdownLink
                        @click="handleLogout"
                        as="button"
                        class="flex items-center gap-3 text-red-600 hover:text-red-700"
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
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                          />
                        </svg>
                        ログアウト
                      </DropdownLink>
                    </div>
                  </div>
                </template>
              </Dropdown>
            </div>

            <!-- ハンバーガーメニュー（モバイル） -->
            <div class="sm:hidden">
              <button
                @click="showingNavigationDropdown = !showingNavigationDropdown"
                class="p-2 rounded-xl backdrop-blur-lg bg-white/60 hover:bg-white/80 border border-white/50 transition-all duration-300"
              >
                <svg
                  class="h-6 w-6 text-slate-600"
                  stroke="currentColor"
                  fill="none"
                  viewBox="0 0 24 24"
                >
                  <path
                    :class="{
                      hidden: showingNavigationDropdown,
                      'inline-flex': !showingNavigationDropdown,
                    }"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M4 6h16M4 12h16M4 18h16"
                  />
                  <path
                    :class="{
                      hidden: !showingNavigationDropdown,
                      'inline-flex': showingNavigationDropdown,
                    }"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M6 18L18 6M6 6l12 12"
                  />
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- レスポンシブナビゲーションメニュー -->
      <div
        :class="{
          block: showingNavigationDropdown,
          hidden: !showingNavigationDropdown,
        }"
        class="sm:hidden border-t border-white/50 backdrop-blur-lg bg-white/90"
      >
        <div class="space-y-1 px-4 py-3">
          <ResponsiveNavLink
            :to="{ name: 'projects' }"
            :active="
              $route.name === 'projects' || $route.name === 'project.detail'
            "
            class="flex items-center gap-3"
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
                d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"
              />
            </svg>
            プロジェクト
          </ResponsiveNavLink>
          <ResponsiveNavLink
            :to="{ name: 'users' }"
            :active="$route.name === 'users'"
            class="flex items-center gap-3"
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
                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
              />
            </svg>
            ユーザー
          </ResponsiveNavLink>
        </div>

        <!-- ユーザー情報 -->
        <div class="border-t border-white/50 px-4 py-3">
          <div class="flex items-center gap-3 mb-3">
            <div
              class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center text-white font-bold shadow-md"
            >
              {{ authStore.user?.name?.charAt(0) || "?" }}
            </div>
            <div>
              <div class="text-sm font-semibold text-slate-800">
                {{ authStore.user?.name }}
              </div>
              <div class="text-xs text-slate-500">
                {{ authStore.user?.email }}
              </div>
            </div>
          </div>

          <div class="space-y-1">
            <ResponsiveNavLink
              @click="handleLogout"
              as="button"
              class="flex items-center gap-3 text-red-600"
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
                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                />
              </svg>
              ログアウト
            </ResponsiveNavLink>
          </div>
        </div>
      </div>
    </nav>

    <!-- ページコンテンツ -->
    <main>
      <slot />
    </main>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "../stores/auth";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";

const router = useRouter();
const authStore = useAuthStore();
const showingNavigationDropdown = ref(false);

const handleLogout = async () => {
  await authStore.logout();
  router.push({ name: "login" });
};
</script>
