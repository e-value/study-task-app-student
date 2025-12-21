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

const router = useRouter();

const form = ref({
    password: '',
    errors: {}
});

const processing = ref(false);
const errors = ref('');

const submit = async () => {
    processing.value = true;
    errors.value = '';
    form.value.errors = {};

    try {
        await axios.post('/confirm-password', {
            password: form.value.password
        });
        
        // パスワード確認成功後、元のページに戻るか、ダッシュボードへ
        router.back();
    } catch (error) {
        if (error.response?.data?.errors) {
            form.value.errors = error.response.data.errors;
        } else if (error.response?.data?.message) {
            errors.value = error.response.data.message;
        } else {
            errors.value = '入力されたパスワードが正しくありません。';
        }
    } finally {
        processing.value = false;
        form.value.password = '';
    }
};
</script>

