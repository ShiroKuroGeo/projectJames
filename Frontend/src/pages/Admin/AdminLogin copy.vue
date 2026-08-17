<template>
    <!-- LOGIN -->
    <div class="auth-page" v-show="tab === 'login'">
        <div class="auth-grid">

            <!-- LEFT BRAND PANEL -->
            <section class="auth-brand">
                <div class="brand-top">
                    <div class="brand-logo">
                        Dink<span>Yard</span>
                    </div>

                    <div class="brand-tag mono">
                        VENUE MANAGEMENT
                    </div>
                </div>

                <div class="brand-content">
                    <div class="eyebrow mono">
                        Staff access
                    </div>

                    <h1>
                        Manage your
                        <span>court.</span>
                    </h1>

                    <p>
                        Keep your venue schedule, courts, and reservations
                        organized from one place.
                    </p>
                </div>

                <div class="brand-footer mono">
                    <span>ADMIN PORTAL</span>
                    <span>01 / 02</span>
                </div>
            </section>

            <!-- LOGIN PANEL -->
            <section class="auth-panel">
                <div class="auth-card">

                    <div class="mobile-brand">
                        <div class="brand-logo">
                            Dink<span>Yard</span>
                        </div>
                    </div>

                    <div class="card-header">
                        <div class="card-eyebrow mono">
                            Staff access
                        </div>

                        <h2>
                            Welcome<br>
                            <span>back.</span>
                        </h2>

                        <p>
                            Sign in to manage the schedule for your venue.
                        </p>
                    </div>

                    <form class="auth-form" @submit.prevent="handleSubmit">

                        <div class="form-field">
                            <label class="form-label mono">
                                Email
                            </label>

                            <div class="input-wrap">
                                <span class="input-index mono">01</span>

                                <input v-model="email" type="email" class="form-input" placeholder="you@dinkyard.com" autocomplete="username" />
                            </div>
                        </div>

                        <div class="form-field">
                            <label class="form-label mono">
                                Password
                            </label>

                            <div class="input-wrap">
                                <span class="input-index mono">02</span>

                                <input v-model="password" type="password" class="form-input" placeholder="••••••••" autocomplete="current-password" />
                            </div>
                        </div>

                        <div v-if="error" class="form-error">
                            <span class="error-mark">!</span>
                            {{ error }}
                        </div>

                        <button type="submit" class="login-btn" :disabled="submitting">
                            <span>
                                {{ submitting ? 'Signing in…' : 'Log in' }}
                            </span>

                            <span class="btn-arrow">
                                →
                            </span>
                        </button>
                    </form>

                    <div class="card-footer">
                        <span class="mono">
                            Don't have an account?
                        </span>

                        <button type="button" class="switch-btn" @click="tab = 'register'">
                            Request access
                            <span>↗</span>
                        </button>
                    </div>

                </div>
            </section>
        </div>
    </div>


    <!-- REGISTER / REQUEST ACCESS -->
    <div class="auth-page" v-show="tab === 'register'">
        <div class="auth-grid">

            <!-- LEFT BRAND PANEL -->
            <section class="auth-brand">
                <div class="brand-top">
                    <div class="brand-logo">
                        Dink<span>Yard</span>
                    </div>

                    <div class="brand-tag mono">
                        VENUE MANAGEMENT
                    </div>
                </div>

                <div class="brand-content">
                    <div class="eyebrow mono">
                        Access request
                    </div>

                    <h1>
                        Join the
                        <span>yard.</span>
                    </h1>

                    <p>
                        DinkYard accounts are created by a super admin
                        to keep venue access secure.
                    </p>
                </div>

                <div class="brand-footer mono">
                    <span>ADMIN PORTAL</span>
                    <span>02 / 02</span>
                </div>
            </section>

            <!-- REGISTER PANEL -->
            <section class="auth-panel">
                <div class="auth-card register-card">

                    <div class="mobile-brand">
                        <div class="brand-logo">
                            Dink<span>Yard</span>
                        </div>
                    </div>

                    <button type="button" class="back-btn mono" @click="tab = 'login'">
                        ← Back to login
                    </button>

                    <div class="card-header register-header">
                        <div class="card-eyebrow mono">
                            Account registration
                        </div>

                        <h2>
                            Request<br>
                            <span>access.</span>
                        </h2>

                        <p>
                            Accounts on this system aren't self-registered.
                            A super admin adds venue staff to the roster.
                        </p>
                    </div>

                    <!-- ACCESS PASS -->
                    <div class="access-card">

                        <div class="access-top">
                            <span class="mono">
                                ACCESS STATUS
                            </span>

                            <span class="status-pill mono">
                                INVITE ONLY
                            </span>
                        </div>

                        <div class="access-line"></div>

                        <div class="access-heading mono">
                            REQUEST ACCESS
                        </div>

                        <a class="access-item" href="mailto:info.alfeser.shiro@gmail.com">
                            <span class="access-number mono">
                                01
                            </span>

                            <span class="access-name">
                                Email us
                            </span>

                            <span class="access-arrow">
                                ↗
                            </span>
                        </a>

                        <a class="access-item" href="https://www.facebook.com/alfeser.shiro" target="_blank" rel="noopener">
                            <span class="access-number mono">
                                02
                            </span>

                            <span class="access-name">
                                Facebook
                            </span>

                            <span class="access-arrow">
                                ↗
                            </span>
                        </a>

                        <a class="access-item" href="tel:+639484750030">
                            <span class="access-number mono">
                                03
                            </span>

                            <span class="access-name">
                                Call us
                            </span>

                            <span class="access-arrow">
                                ↗
                            </span>
                        </a>
                    </div>

                    <div class="access-note">
                        <span class="note-mark mono">
                            NOTE
                        </span>

                        <p>
                            Include your name and the venue you manage.
                            A super admin will set up your login and send
                            you a temporary password.
                        </p>
                    </div>

                    <div class="card-footer register-footer">
                        <span class="mono">
                            Already have access?
                        </span>

                        <button type="button" class="switch-btn" @click="tab = 'login'">
                            Sign in
                            <span>→</span>
                        </button>
                    </div>

                </div>
            </section>

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

    const result = await useAuth.login(
        email.value,
        password.value
    );

    submitting.value = false;

    if (!result) {
        error.value = 'Credentials are incorrect. Please try again.';
        return;
    }

    const isSuperAdmin =
        useAuth.user.role === 'super_admin';

    const redirect = route.query.redirect;

    const fallback = isSuperAdmin
        ? '/superadmin'
        : '/admin';

    const redirectIsSuperAdminOnly =
        redirect?.startsWith('/superadmin');

    const canUseRedirect =
        redirect &&
        redirect !== '/admin/login' &&
        (
            !redirectIsSuperAdminOnly ||
            isSuperAdmin
        );

    router.push(
        canUseRedirect
            ? redirect
            : fallback
    );
};
</script>


<style scoped>
/* =========================================================
   DINKYARD AUTH
   ========================================================= */

.auth-page {
    min-height: 100vh;
    min-height: 100dvh;

    background:
        radial-gradient(circle at 15% 10%,
            rgba(196, 221, 65, 0.08),
            transparent 30%),
        #001b3e;

    color: #f5f4ed;

    overflow: hidden;
}


/* =========================================================
   MAIN GRID
   ========================================================= */

.auth-grid {
    min-height: 100vh;
    min-height: 100dvh;

    display: grid;
    grid-template-columns: 1.1fr 0.9fr;
}


/* =========================================================
   LEFT BRAND
   ========================================================= */

.auth-brand {
    position: relative;

    display: flex;
    flex-direction: column;

    padding: 42px 52px;

    border-right: 1px solid rgba(196, 221, 65, 0.14);

    background:
        linear-gradient(135deg,
            rgba(0, 27, 62, 0.96),
            rgba(0, 20, 47, 0.98));
}


/* decorative court lines */

.auth-brand::before {
    content: '';

    position: absolute;

    width: 420px;
    height: 420px;

    right: -180px;
    bottom: -170px;

    border: 1px solid rgba(196, 221, 65, 0.10);
    border-radius: 50%;

    pointer-events: none;
}

.auth-brand::after {
    content: '';

    position: absolute;

    width: 220px;
    height: 220px;

    right: -70px;
    bottom: -70px;

    border: 1px solid rgba(196, 221, 65, 0.08);
    border-radius: 50%;

    pointer-events: none;
}


/* =========================================================
   BRAND HEADER
   ========================================================= */

.brand-top {
    display: flex;
    align-items: center;
    justify-content: space-between;

    position: relative;
    z-index: 2;
}

.brand-logo {
    font-family: var(--font-display);
    font-size: 30px;
    line-height: 1;

    letter-spacing: -0.02em;

    color: #f5f4ed;
}

.brand-logo span {
    color: #c4dd41;
}

.brand-tag {
    font-size: 9px;
    letter-spacing: 0.14em;

    color: rgba(245, 244, 237, 0.45);
}


/* =========================================================
   BRAND CONTENT
   ========================================================= */

.brand-content {
    position: relative;
    z-index: 2;

    margin: auto 0;
    max-width: 570px;
}

.eyebrow {
    margin-bottom: 16px;

    color: #c4dd41;

    font-size: 10px;
    letter-spacing: 0.16em;
    text-transform: uppercase;
}

.brand-content h1 {
    margin: 0;

    font-family: var(--font-display);
    font-weight: 400;

    font-size: clamp(54px, 6vw, 88px);
    line-height: 0.91;

    letter-spacing: -0.035em;
}

.brand-content h1 span {
    color: #c4dd41;
}

.brand-content p {
    max-width: 420px;

    margin: 28px 0 0;

    color: rgba(245, 244, 237, 0.58);

    font-size: 15px;
    line-height: 1.65;
}


/* =========================================================
   BRAND FOOTER
   ========================================================= */

.brand-footer {
    position: relative;
    z-index: 2;

    display: flex;
    justify-content: space-between;

    color: rgba(245, 244, 237, 0.32);

    font-size: 9px;
    letter-spacing: 0.12em;
}


/* =========================================================
   RIGHT PANEL
   ========================================================= */

.auth-panel {
    display: flex;
    align-items: center;
    justify-content: center;

    padding: 40px;

    background: #f2f2ed;
}


/* =========================================================
   AUTH CARD
   ========================================================= */

.auth-card {
    width: 100%;
    max-width: 430px;

    color: #111d27;
}

.card-header {
    margin-bottom: 30px;
}

.card-eyebrow {
    margin-bottom: 10px;

    color: #65717b;

    font-size: 9px;
    letter-spacing: 0.14em;
    text-transform: uppercase;
}

.card-header h2 {
    margin: 0 0 12px;

    font-family: var(--font-display);
    font-weight: 400;

    font-size: 48px;
    line-height: 0.94;

    letter-spacing: -0.025em;
}

.card-header h2 span {
    color: #b3cd36;
}

.card-header p {
    margin: 0;

    max-width: 380px;

    color: #68747d;

    font-size: 13.5px;
    line-height: 1.6;
}


/* =========================================================
   FORM
   ========================================================= */

.auth-form {
    display: flex;
    flex-direction: column;

    gap: 18px;
}

.form-field {
    display: flex;
    flex-direction: column;

    gap: 7px;
}

.form-label {
    color: #66717a;

    font-size: 9px;
    letter-spacing: 0.12em;
    text-transform: uppercase;
}

.input-wrap {
    display: flex;
    align-items: center;

    min-height: 52px;

    background: #e8e9e4;

    border: 1px solid transparent;
    border-radius: 4px;

    transition:
        background .2s ease,
        border-color .2s ease;
}

.input-wrap:focus-within {
    background: #f8f8f4;
    border-color: #b3cd36;
}

.input-index {
    width: 46px;

    text-align: center;

    color: #9aa1a4;

    font-size: 9px;
    letter-spacing: 0.08em;

    border-right: 1px solid rgba(17, 29, 39, 0.08);
}

.form-input {
    flex: 1;

    width: 100%;

    padding: 14px;

    background: transparent;

    border: 0;
    outline: none;

    color: #111d27;

    font-family: var(--font-body);
    font-size: 14px;
}

.form-input::placeholder {
    color: #a2a8aa;
}


/* =========================================================
   ERROR
   ========================================================= */

.form-error {
    display: flex;
    align-items: center;
    gap: 9px;

    padding: 10px 12px;

    border-left: 3px solid #c65b46;

    background: rgba(198, 91, 70, 0.08);

    color: #a84432;

    font-size: 12px;
}

.error-mark {
    display: inline-flex;
    align-items: center;
    justify-content: center;

    width: 17px;
    height: 17px;

    border-radius: 50%;

    background: #c65b46;
    color: #fff;

    font-size: 10px;
    font-weight: 700;
}


/* =========================================================
   LOGIN BUTTON
   ========================================================= */

.login-btn {
    display: flex;
    align-items: center;
    justify-content: space-between;

    width: 100%;
    min-height: 54px;

    margin-top: 4px;

    padding: 0 17px;

    border: 0;
    border-radius: 4px;

    background: #b3cd36;
    color: #001b3e;

    font-family: var(--font-display);
    font-size: 18px;
    font-weight: 400;

    cursor: pointer;

    transition:
        transform .18s ease,
        background .18s ease;
}

.login-btn:hover:not(:disabled) {
    background: #c4dd41;
    transform: translateY(-1px);
}

.login-btn:disabled {
    opacity: .55;
    cursor: default;
}

.btn-arrow {
    font-family: var(--font-body);
    font-size: 22px;
}


/* =========================================================
   CARD FOOTER
   ========================================================= */

.card-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 15px;

    margin-top: 30px;
    padding-top: 20px;

    border-top: 1px solid rgba(17, 29, 39, 0.10);

    color: #92999d;

    font-size: 9px;
    letter-spacing: 0.06em;
    text-transform: uppercase;
}

.switch-btn {
    padding: 0;

    border: 0;
    background: transparent;

    color: #5e7415;

    font-family: var(--font-body);
    font-size: 11px;
    font-weight: 700;

    cursor: pointer;
}

.switch-btn span {
    margin-left: 4px;
}

.switch-btn:hover {
    color: #91aa24;
}


/* =========================================================
   MOBILE BRAND
   ========================================================= */

.mobile-brand {
    display: none;
}


/* =========================================================
   REGISTER
   ========================================================= */

.back-btn {
    padding: 0;
    margin-bottom: 28px;

    border: 0;
    background: transparent;

    color: #727b81;

    font-size: 9px;
    letter-spacing: 0.10em;
    text-transform: uppercase;

    cursor: pointer;
}

.back-btn:hover {
    color: #536a13;
}

.register-header {
    margin-bottom: 24px;
}

.register-header p {
    max-width: 410px;
}


/* =========================================================
   ACCESS CARD
   ========================================================= */

.access-card {
    position: relative;

    padding: 18px;

    background: #e8e9e4;

    border: 1px solid rgba(17, 29, 39, 0.08);
    border-radius: 5px;
}

.access-top {
    display: flex;
    align-items: center;
    justify-content: space-between;

    color: #747d82;

    font-size: 9px;
    letter-spacing: 0.12em;
}

.status-pill {
    padding: 5px 8px;

    background: rgba(179, 205, 54, 0.18);

    color: #617513;

    border-radius: 3px;

    font-size: 8px;
}

.access-line {
    margin: 15px 0;

    border-top: 1px dashed rgba(17, 29, 39, 0.16);
}

.access-heading {
    margin-bottom: 8px;

    color: #91999d;

    font-size: 8px;
    letter-spacing: 0.12em;
}

.access-item {
    display: flex;
    align-items: center;

    min-height: 44px;

    text-decoration: none;

    border-top: 1px solid rgba(17, 29, 39, 0.08);

    color: #17232d;

    transition: background .15s ease;
}

.access-item:hover {
    background: rgba(179, 205, 54, 0.08);
}

.access-number {
    width: 35px;

    color: #a2a8aa;

    font-size: 8px;
}

.access-name {
    flex: 1;

    font-size: 13px;
    font-weight: 600;
}

.access-arrow {
    color: #687b17;

    font-size: 18px;
}


/* =========================================================
   ACCESS NOTE
   ========================================================= */

.access-note {
    display: flex;
    gap: 12px;

    margin-top: 18px;

    color: #747c81;
}

.note-mark {
    flex-shrink: 0;

    color: #84991e;

    font-size: 8px;
    letter-spacing: 0.1em;
}

.access-note p {
    margin: 0;

    font-size: 11px;
    line-height: 1.55;
}


/* =========================================================
   RESPONSIVE
   ========================================================= */

@media (max-width: 900px) {

    .auth-grid {
        grid-template-columns: 1fr;
    }

    .auth-brand {
        display: none;
    }

    .auth-panel {
        min-height: 100vh;
        min-height: 100dvh;

        padding: 28px 22px;
    }

    .mobile-brand {
        display: block;

        margin-bottom: 42px;
    }

    .mobile-brand .brand-logo {
        color: #001b3e;
        font-size: 28px;
    }

    .mobile-brand .brand-logo span {
        color: #a9c22f;
    }

    .auth-card {
        max-width: 460px;
    }
}


@media (max-width: 520px) {

    .auth-panel {
        padding: 24px 18px;
    }

    .mobile-brand {
        margin-bottom: 34px;
    }

    .card-header h2 {
        font-size: 42px;
    }

    .card-header p {
        font-size: 13px;
    }

    .card-footer {
        align-items: flex-start;
        flex-direction: column;
        gap: 9px;
    }

    .access-card {
        padding: 15px;
    }

    .access-item {
        min-height: 48px;
    }
}
</style>