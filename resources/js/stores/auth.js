import { defineStore } from 'pinia';
import axios from 'axios';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        checked: false,
    }),

    getters: {
        isAuthenticated: (state) => state.user !== null,
    },

    actions: {
        async checkAuth() {
            try {
                const response = await axios.get('/api/user');
                this.user = response.data;
                this.checked = true;
            } catch (error) {
                // 認証チェックの失敗は正常な動作（未ログイン状態）
                this.user = null;
                this.checked = true;
            }
        },

        async login(credentials) {
            // CSRFクッキーを取得
            await axios.get('/sanctum/csrf-cookie');
            
            // ログイン（エラーは呼び出し側でhandleErrorを使う）
            await axios.post('/login', credentials);
            
            // ユーザー情報を取得
            await this.checkAuth();
        },

        async register(data) {
            // CSRFクッキーを取得
            await axios.get('/sanctum/csrf-cookie');
            
            // 登録（エラーは呼び出し側でhandleErrorを使う）
            await axios.post('/register', data);
            
            // ユーザー情報を取得
            await this.checkAuth();
        },

        async logout() {
            // ログアウト（エラーは呼び出し側でhandleErrorを使う）
            await axios.post('/logout');
            this.user = null;
        },

        async forgotPassword(email) {
            await axios.get('/sanctum/csrf-cookie');
            // エラーは呼び出し側でhandleErrorを使う
            return await axios.post('/forgot-password', { email });
        },

        async resetPassword(data) {
            await axios.get('/sanctum/csrf-cookie');
            // エラーは呼び出し側でhandleErrorを使う
            return await axios.post('/reset-password', data);
        },
    },
});


