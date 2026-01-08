<template>
    <GuestLayout>
        <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
            ご登録ありがとうございます！ご利用を開始する前に、登録時に入力いただいたメールアドレスに送信されたリンクをクリックして、メールアドレスを確認していただけますか？メールが届いていない場合は、再送信いたします。
        </div>

        <div v-if="verificationLinkSent" class="mb-4 text-sm font-medium text-green-600 dark:text-green-400">
            新しい確認リンクを、登録時に入力いただいたメールアドレスに送信しました。
        </div>

        <form @submit.prevent="submit">
            <div class="mt-4 flex items-center justify-between">
                <PrimaryButton :class="{ 'opacity-25': processing }" :disabled="processing">
                    確認メールを再送信
                </PrimaryButton>

                <button
                    type="button"
                    @click="logout"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:text-gray-400 dark:hover:text-gray-100 dark:focus:ring-offset-gray-800"
                >
                    ログアウト
                </button>
            </div>
        </form>
    </GuestLayout>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useToast } from 'vue-toastification';
import { useAuthStore } from '../../stores/auth';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import axios from 'axios';
import { useApiError } from '@/composables/useApiError';

const router = useRouter();
const toast = useToast();
const authStore = useAuthStore();

const processing = ref(false);
const verificationLinkSent = ref(false);

// エラーハンドリング用のComposable
const { error, handleError, clearError } = useApiError();

const submit = async () => {
    processing.value = true;
    verificationLinkSent.value = false;
    clearError();

    try {
        await axios.post('/email/verification-notification');
        verificationLinkSent.value = true;
        toast.success('確認メールを送信しました');
    } catch (err) {
        handleError(err, '確認メールの送信に失敗しました');
        toast.error(error.value);
    } finally {
        processing.value = false;
    }
};

const logout = async () => {
    await authStore.logout();
    router.push({ name: 'welcome' });
};
</script>

