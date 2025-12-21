import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/auth';

// Layouts
import AuthenticatedLayout from '../Layouts/AuthenticatedLayout.vue';
import GuestLayout from '../Layouts/GuestLayout.vue';

// Pages
import Welcome from '../Pages/Welcome.vue';
import Dashboard from '../Pages/Dashboard.vue';
import Login from '../Pages/Auth/Login.vue';
import Register from '../Pages/Auth/Register.vue';
import ForgotPassword from '../Pages/Auth/ForgotPassword.vue';
import ResetPassword from '../Pages/Auth/ResetPassword.vue';
import VerifyEmail from '../Pages/Auth/VerifyEmail.vue';
import ProfileEdit from '../Pages/Profile/Edit.vue';
import ProjectsIndex from '../Pages/Projects/Index.vue';
import ProjectsCreate from '../Pages/Projects/Create.vue';
import ProjectsShow from '../Pages/Projects/Show.vue';

const routes = [
    {
        path: '/',
        name: 'welcome',
        component: Welcome,
        meta: { guest: true }
    },
    {
        path: '/login',
        name: 'login',
        component: Login,
        meta: { guest: true, layout: 'guest' }
    },
    {
        path: '/register',
        name: 'register',
        component: Register,
        meta: { guest: true, layout: 'guest' }
    },
    {
        path: '/forgot-password',
        name: 'password.request',
        component: ForgotPassword,
        meta: { guest: true, layout: 'guest' }
    },
    {
        path: '/reset-password/:token',
        name: 'password.reset',
        component: ResetPassword,
        meta: { guest: true, layout: 'guest' }
    },
    {
        path: '/verify-email',
        name: 'verification.notice',
        component: VerifyEmail,
        meta: { requiresAuth: true, layout: 'guest' }
    },
    {
        path: '/dashboard',
        name: 'dashboard',
        component: Dashboard,
        meta: { requiresAuth: true, layout: 'authenticated' }
    },
    {
        path: '/profile',
        name: 'profile.edit',
        component: ProfileEdit,
        meta: { requiresAuth: true, layout: 'authenticated' }
    },
    {
        path: '/projects',
        name: 'projects.index',
        component: ProjectsIndex,
        meta: { requiresAuth: true, layout: 'authenticated' }
    },
    {
        path: '/projects/create',
        name: 'projects.create',
        component: ProjectsCreate,
        meta: { requiresAuth: true, layout: 'authenticated' }
    },
    {
        path: '/projects/:id',
        name: 'projects.show',
        component: ProjectsShow,
        meta: { requiresAuth: true, layout: 'authenticated' }
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

// ナビゲーションガード
router.beforeEach(async (to, from, next) => {
    const authStore = useAuthStore();
    
    // 認証状態をチェック
    if (!authStore.checked) {
        await authStore.checkAuth();
    }

    const isAuthenticated = authStore.isAuthenticated;
    const requiresAuth = to.matched.some(record => record.meta.requiresAuth);
    const isGuest = to.matched.some(record => record.meta.guest);

    if (requiresAuth && !isAuthenticated) {
        // 認証が必要なページで未認証の場合
        next({ name: 'login' });
    } else if (isGuest && isAuthenticated && to.name !== 'welcome') {
        // ゲスト専用ページで認証済みの場合
        next({ name: 'dashboard' });
    } else {
        next();
    }
});

export default router;

