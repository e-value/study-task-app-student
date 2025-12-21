import axios from "axios";
import router from "./router";

window.axios = axios;

window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
window.axios.defaults.withCredentials = true;
window.axios.defaults.withXSRFToken = true;

// 401エラー（未認証）の場合、ログイン画面にリダイレクト
window.axios.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401) {
            // 認証が必要なのにログインしていない場合
            router.push({ name: "login" });
        }
        return Promise.reject(error);
    }
);
