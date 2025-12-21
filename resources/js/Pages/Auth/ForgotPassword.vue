<template>
    <GuestLayout>
        <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
            パスワードをお忘れですか？問題ありません。メールアドレスを入力していただければ、新しいパスワードを設定するためのリンクをメールでお送りします。
        </div>

        <div v-if="status" class="mb-4 text-sm font-medium text-green-600 dark:text-green-400">
            {{ status }}
        </div>

        <div v-if="errors" class="mb-4 text-sm font-medium text-red-600 dark:text-red-400">
            {{ errors }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="メールアドレス" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autofocus
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4 flex items-center justify-end">
                <PrimaryButton :class="{ 'opacity-25': processing }" :disabled="processing">
                    パスワードリセットリンクを送信
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>

<script setup>
import { ref } from 'vue';
import { useAuthStore } from '../../stores/auth';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const authStore = useAuthStore();

const form = ref({
    email: '',
    errors: {}
});

const processing = ref(false);
const errors = ref('');
const status = ref('');

const submit = async () => {
    processing.value = true;
    errors.value = '';
    status.value = '';
    form.value.errors = {};

    try {
        await authStore.forgotPassword(form.value.email);
        status.value = 'パスワードリセットリンクをメールで送信しました！';
        form.value.email = '';
    } catch (error) {
        if (error.response?.data?.errors) {
            form.value.errors = error.response.data.errors;
        } else if (error.response?.data?.message) {
            errors.value = error.response.data.message;
        } else {
            errors.value = 'エラーが発生しました。もう一度お試しください。';
        }
    } finally {
        processing.value = false;
    }
};
</script>

