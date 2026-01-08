<template>
    <GuestLayout>
        <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
            これは保護されたエリアです。続行する前に、パスワードを確認してください。
        </div>

        <div v-if="errors" class="mb-4 text-sm font-medium text-red-600 dark:text-red-400">
            {{ errors }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="password" value="パスワード" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                    autofocus
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4 flex justify-end">
                <PrimaryButton :class="{ 'opacity-25': processing }" :disabled="processing">
                    確認
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import axios from 'axios';
import { useApiError } from '@/composables/useApiError';

const router = useRouter();

const form = ref({
    password: '',
    errors: {}
});

const processing = ref(false);

// エラーハンドリング用のComposable
const { error, validationErrors, handleError, clearError } = useApiError();
const errors = ref('');

const submit = async () => {
    processing.value = true;
    clearError();
    form.value.errors = {};

    try {
        await axios.post('/confirm-password', {
            password: form.value.password
        });
        
        // パスワード確認成功後、元のページに戻るか、ダッシュボードへ
        router.back();
    } catch (err) {
        handleError(err, '入力されたパスワードが正しくありません。');
        errors.value = error.value;
        form.value.errors = validationErrors.value;
    } finally {
        processing.value = false;
        form.value.password = '';
    }
};
</script>

