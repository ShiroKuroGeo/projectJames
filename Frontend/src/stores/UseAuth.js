import { defineStore } from "pinia";
import api from "@/api/axios";
import { computed, ref } from "vue";
import { useRouter } from "vue-router";

export const useAuthStore = defineStore('Auth', () => {

    const user = ref(null);
    const token = ref(localStorage.getItem('auth_token') || null);
    const router = useRouter();
    const isAuthenticated = computed(() => !!token.value);

    const login = async (email, password) => {
        try {
            const response = await api.post('login', { email, password });
            
            if (response.data.status !== 200) return response.data.message;

            token.value = response.data.token;
            user.value = response.data.data;

            localStorage.setItem('auth_token', token.value);

            return response.data;
        } catch (err) {
            if (err.response) {
                return err.response.data.message || 'Something went wrong.';
            }
            return 'Cannot connect to the server.';
        }
    }

    const logout = async () => {
        token.value = null;
        user.value = null;
        localStorage.removeItem('auth_token');
        router.push({ name: 'admin-login' });
    };

    return { login, logout, user, token, isAuthenticated }


});