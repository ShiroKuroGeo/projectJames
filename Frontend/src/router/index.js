import { createRouter, createWebHistory } from 'vue-router'

import Homepage from '@/pages/Homepage.vue';
import Reservation from '@/pages/Reservation.vue';
import LinkMe from '@/pages/LinkMe.vue';
import AdminLogin from '@/pages/Admin/AdminLogin.vue';
import SuperAdminDashboard from '@/pages/Admin/Dashboard.vue';
import AdminDashboard from '@/pages/Admin/Subadmindashboard.vue';
import { useAuthStore } from '@/stores/UseAuth';
import CreateVenue from '@/pages/Admin/AddVenue.vue';
import CheckReservation from '@/pages/CheckReservation.vue';

const routes = [
    {
        path: '/',
        name: 'homepage',
        component: Homepage
    },
    {
        path: '/reservation',
        name: 'reservation',
        component: Reservation
    },
    {
        path: '/admin/login',
        name: 'admin-login',
        component: AdminLogin
    },
    {
        path: '/admin',
        name: 'admin-dashboard',
        component: AdminDashboard,
        meta: { requiresAdmin: true }
    },
    {
        path: '/admin/venue/create',
        name: 'create-venue',
        component: CreateVenue,
        props: true,
        meta: { requiresAdmin: true }
    },
    {
        path: '/superadmin',
        name: 'superadmin',
        component: SuperAdminDashboard,
        props: true,
        meta: { requiresAdmin: true, requiredRole: 'super_admin' },
    },
    {
        path: '/check-reservation',
        name: 'checkreservation',
        component: CheckReservation,
    },
    {
        path: '/linkme/:slug',
        name: 'venue',
        component: LinkMe,
        props: true
    },
    {
        path: '/payment',
        name: 'payment-result',
        component: () => import('@/pages/Payment.vue'),
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

router.beforeEach((to) => {
    if (!to.meta.requiresAdmin) return true;

    const authStore = useAuthStore();
    if (!authStore.isAuthenticated) {
        return { name: 'admin-login', query: { redirect: to.fullPath } };
    }

    if (to.meta.requiredRole && authStore.user.role !== to.meta.requiredRole) {
        return { name: 'admin' };
    }

    return true;
});

export default router