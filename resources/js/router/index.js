import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from "../stores/auth";

// Layouts
import AuthenticatedLayout from "../Layouts/AuthenticatedLayout.vue";
import GuestLayout from "../Layouts/GuestLayout.vue";

// Pages
import Welcome from "../Pages/Welcome.vue";
import Dashboard from "../Pages/Dashboard.vue";
import Login from "../Pages/Auth/Login.vue";
import Register from "../Pages/Auth/Register.vue";
import ForgotPassword from "../Pages/Auth/ForgotPassword.vue";
import ResetPassword from "../Pages/Auth/ResetPassword.vue";
import VerifyEmail from "../Pages/Auth/VerifyEmail.vue";
import ConfirmPassword from "../Pages/Auth/ConfirmPassword.vue";
import ProjectList from "../Pages/Projects/Index.vue";
import ProjectCreate from "../Pages/Projects/Create.vue";
import ProjectDetail from "../Pages/Projects/Show.vue";
import ProjectEdit from "../Pages/Projects/Edit.vue";
import TaskDetail from "../Pages/Tasks/Show.vue";
import UserList from "../Pages/Users/Index.vue";
import NotFound from "../Pages/NotFound.vue";

const routes = [
    {
        path: "/",
        name: "welcome",
        component: Welcome,
        meta: { guest: true },
    },
    {
        path: "/login",
        name: "login",
        component: Login,
        meta: { guest: true, layout: "guest" },
    },
    {
        path: "/register",
        name: "register",
        component: Register,
        meta: { guest: true, layout: "guest" },
    },
    {
        path: "/forgot-password",
        name: "password.request",
        component: ForgotPassword,
        meta: { guest: true, layout: "guest" },
    },
    {
        path: "/reset-password/:token",
        name: "password.reset",
        component: ResetPassword,
        meta: { guest: true, layout: "guest" },
    },
    {
        path: "/verify-email",
        name: "verification.notice",
        component: VerifyEmail,
        meta: { requiresAuth: true, layout: "guest" },
    },
    {
        path: "/confirm-password",
        name: "password.confirm",
        component: ConfirmPassword,
        meta: { requiresAuth: true, layout: "guest" },
    },
    {
        path: "/dashboard",
        name: "dashboard",
        component: Dashboard,
        meta: { requiresAuth: true, layout: "authenticated" },
    },
    {
        path: "/projects",
        name: "projects",
        component: ProjectList,
        meta: { requiresAuth: true, layout: "authenticated" },
    },
    {
        path: "/projects/create",
        name: "project.create",
        component: ProjectCreate,
        meta: { requiresAuth: true, layout: "authenticated" },
    },
    {
        path: "/projects/:id",
        name: "project.detail",
        component: ProjectDetail,
        meta: { requiresAuth: true, layout: "authenticated" },
    },
    {
        path: "/projects/:id/edit",
        name: "project.edit",
        component: ProjectEdit,
        meta: { requiresAuth: true, layout: "authenticated" },
    },
    {
        path: "/tasks/:id",
        name: "task.detail",
        component: TaskDetail,
        meta: { requiresAuth: true, layout: "authenticated" },
    },
    {
        path: "/users",
        name: "users",
        component: UserList,
        meta: { requiresAuth: true, layout: "authenticated" },
    },
    // キャッチオールルート（404ページ）- 最後に配置すること
    {
        path: "/:pathMatch(.*)*",
        name: "notfound",
        component: NotFound,
        meta: { layout: "guest" },
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

// ナビゲーションガード
router.beforeEach(async (to, from, next) => {
    const authStore = useAuthStore();

    // 認証状態をチェック
    if (!authStore.checked) {
        await authStore.checkAuth();
    }

    const isAuthenticated = authStore.isAuthenticated;
    const requiresAuth = to.matched.some((record) => record.meta.requiresAuth);
    const isGuest = to.matched.some((record) => record.meta.guest);

    if (requiresAuth && !isAuthenticated) {
        // 認証が必要なページで未認証の場合
        next({ name: "login" });
    } else if (isGuest && isAuthenticated && to.name !== "welcome") {
        // ゲスト専用ページで認証済みの場合
        next({ name: "projects" });
    } else {
        next();
    }
});

export default router;
