<template>
    <GuestLayout>
        <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
            以下のフォームから新しいパスワードを設定してください。
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
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="パスワード" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel for="password_confirmation" value="パスワード（確認）" />

                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                />

                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <div class="mt-4 flex items-center justify-end">
                <PrimaryButton :class="{ 'opacity-25': processing }" :disabled="processing">
                    パスワードをリセット
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();

const form = ref({
    token: '',
    email: '',
    password: '',
    password_confirmation: '',
    errors: {}
});

const processing = ref(false);
const errors = ref('');

onMounted(() => {
    form.value.token = route.params.token;
    form.value.email = route.query.email || '';
});

const submit = async () => {
    processing.value = true;
    errors.value = '';
    form.value.errors = {};

    try {
        await authStore.resetPassword({
            token: form.value.token,
            email: form.value.email,
            password: form.value.password,
            password_confirmation: form.value.password_confirmation
        });
        
        router.push({ name: 'login' });
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

