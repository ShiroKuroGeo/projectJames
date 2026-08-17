<template>
    <div class="admin-shell" v-show="tab === 'login'">
        <div class="login-card">
            <div class="login-eyebrow mono">Staff access</div>
            <h1 class="login-title">Venue<br><span class="accent">admin.</span></h1>
            <p class="login-sub">
                Sign in to manage the schedule for your venue.
                <a href="#!" @click="tab = 'register'">Register</a>
            </p>

            <form class="login-form" @submit.prevent="handleSubmit">
                <div class="form-field">
                    <label class="form-label mono">Email</label>
                    <input v-model="email" type="email" class="form-input" placeholder="you@dinkyard.com"
                        autocomplete="username">
                </div>
                <div class="form-field">
                    <label class="form-label mono">Password</label>
                    <input v-model="password" type="password" class="form-input" placeholder="••••••••"
                        autocomplete="current-password">
                </div>

                <div class="form-error" v-if="error">{{ error }}</div>

                <button type="submit" class="login-btn" :disabled="submitting">
                    {{ submitting ? 'Signing in…' : 'Log in' }}
                </button>
            </form>
        </div>
    </div>

    <div class="admin-shell" v-show="tab === 'register'">
        <div class="login-card">
            <div class="login-eyebrow mono">Back</div>
            <h1 class="login-title">Register<br><span class="accent">Account.</span></h1>
            <p class="login-sub2">
                Accounts on this system aren't self-registered — a super admin adds you to the roster.
            </p>

            <div class="access-pass">
                <div class="access-pass__row">
                    <span class="access-pass__label mono">Status</span>
                    <span class="access-pass__value mono">Invite only</span>
                </div>
                <div class="access-pass__divider" aria-hidden="true"></div>
                <span class="access-pass__label mono">Request access : <br><br> </span>
                <div class="access-pass__row">
                    <a class="access-pass__value access-pass__link mono" href="mailto:info.alfeser.shiro@gmail.com">
                        Email us
                    </a>
                </div>
                <div class="access-pass__row">
                    <a class="access-pass__value access-pass__link mono" href="https://www.facebook.com/alfeser.shiro">
                        Facebook Link
                    </a>
                </div>
                <div class="access-pass__row">
                    <a class="access-pass__value access-pass__link mono" href="tel:+639484750030">
                        Call us
                    </a>
                </div>
            </div>
            <p class="access-note">
                Include your name and the venue you manage. A super admin will set up your login and send you a
                temporary password.
            </p>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useAuthStore } from '@/stores/UseAuth';

const router = useRouter();
const route = useRoute();
const useAuth = useAuthStore();
const tab = ref('login');

const email = ref('');
const password = ref('');
const error = ref('');
const submitting = ref(false);

const handleSubmit = async () => {
    error.value = '';

    if (!email.value.trim() || !password.value.trim()) {
        error.value = 'Enter both your email and password.';
        return;
    }

    submitting.value = true;
    const result = await useAuth.login(email.value, password.value);
    submitting.value = false;

    if(!result) return error.value = 'Credentials are incorrect. Please try again.'

    const isSuperAdmin = useAuth.user.role === 'super_admin';

    const redirect = route.query.redirect;
    const fallback = isSuperAdmin ? '/superadmin' : '/admin';

    const redirectIsSuperAdminOnly = redirect?.startsWith('/superadmin');
    const canUseRedirect = redirect && redirect !== '/admin/login' && (!redirectIsSuperAdminOnly || isSuperAdmin);

    router.push(canUseRedirect ? redirect : fallback);
};
</script>

<style scoped>
.admin-shell {
    min-height: 100vh;
    min-height: 100dvh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 24px;
    background: radial-gradient(circle at 20% -10%, var(--court-2) 0%, var(--court) 50%, #123638 100%);
}

.login-card {
    width: 100%;
    max-width: 380px;
    background: var(--chalk);
    border-radius: var(--radius-lg);
    padding: 32px 26px;
    box-shadow: 0 20px 40px -20px rgba(0, 0, 0, 0.6);
}

.login-eyebrow {
    font-size: 10px;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    color: var(--mango);
    margin-bottom: 6px;
}

.login-title {
    font-family: var(--font-display);
    font-weight: 400;
    font-size: 34px;
    line-height: 1.02;
    letter-spacing: 0.01em;
    color: var(--ink);
    margin: 0 0 8px;
}

.login-title .accent {
    color: var(--mango);
}

.login-sub {
    color: var(--ink-soft);
    font-size: 13.5px;
    line-height: 1.5;
    margin: 0 0 22px;
}

.login-sub2 {
    color: var(--ink-soft);
    font-size: 15.5px;
    line-height: 1.5;
    margin: 0 0 22px;
}

.login-form {
    display: flex;
    flex-direction: column;
    gap: 14px;
}

.form-field {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.form-label {
    font-size: 10.5px;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--ink-soft);
}

.form-input {
    width: 100%;
    background: var(--chalk-dim);
    border: 1px solid transparent;
    border-radius: var(--radius-sm);
    padding: 12px 14px;
    font-family: var(--font-body);
    font-size: 14px;
    color: var(--ink);
    transition: border-color .2s ease, background .2s ease;
}

.form-input::placeholder {
    color: var(--ink-faint);
}

.form-input:focus {
    outline: none;
    border-color: var(--mango);
    background: var(--chalk);
}

.form-error {
    font-size: 12.5px;
    color: var(--danger);
    background: var(--danger-dim);
    border-radius: var(--radius-sm);
    padding: 9px 12px;
}

.login-btn {
    font-family: var(--font-display);
    font-weight: 400;
    letter-spacing: 0.02em;
    font-size: 17px;
    background: var(--mango);
    color: var(--chalk);
    border: none;
    padding: 13px;
    border-radius: var(--radius-sm);
    cursor: pointer;
    min-height: 48px;
    margin-top: 4px;
    transition: opacity .2s ease, transform .15s ease;
}

.login-btn:hover:not(:disabled) {
    transform: translateY(-1px);
    opacity: 0.92;
}

.login-btn:disabled {
    opacity: 0.6;
    cursor: default;
}

.access-pass {
    margin-top: 28px;
    border: 1px dashed color-mix(in srgb, var(--ink, #1a1d21) 25%, transparent);
    border-radius: 10px;
    padding: 18px 20px;
    position: relative;
    background: color-mix(in srgb, var(--ink, #1a1d21) 3%, transparent);
    color: #1a1d21;
}

.access-pass::before,
.access-pass::after {
    content: '';
    position: absolute;
    top: 50%;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    background: var(--page-bg, #f4f4f2);
    transform: translateY(-50%);
}

.access-pass::before {
    left: -8px;
}

.access-pass::after {
    right: -8px;
}

.access-pass__row {
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    gap: 12px;
}

.access-pass__row+.access-pass__row {
    margin-top: 10px;
}

.access-pass__divider {
    border-top: 1px dashed color-mix(in srgb, var(--ink, #1a1d21) 18%, transparent);
    margin: 12px 0;
}

.access-pass__label {
    font-size: 0.72rem;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    opacity: 0.55;
}

.access-pass__value {
    font-size: 0.85rem;
    font-weight: 600;
}

.access-pass__link {
    text-decoration: none;
    color: var(--accent, #c9622f);
    border-bottom: 1px solid currentColor;
}

.access-pass__link:hover,
.access-pass__link:focus-visible {
    opacity: 0.8;
}

.access-pass__link:focus-visible {
    outline: 2px solid var(--accent, #c9622f);
    outline-offset: 3px;
    border-radius: 2px;
}

.access-note {
    margin-top: 14px;
    font-size: 0.8rem;
    line-height: 1.5;
    opacity: 0.65;
    color: #564242;
}
</style>