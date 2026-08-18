<template>
    <div class="auth-page">

        <div class="auth-view" v-show="tab === 'login'">
            <div class="auth-grid">

                <section class="auth-brand">
                    <div class="brand-top">
                        <div class="logo">
                            <img :src="logo" class="logo-mark" alt="" />
                            <span>Dink<span style="color: white;">Yard</span></span>
                        </div>

                        <div class="brand-tag mono">VENUE MANAGEMENT</div>
                    </div>

                    <div class="brand-content">
                        <span class="eyebrow mono">
                            Staff access
                        </span>

                        <h1>Manage your<br><em>court.</em></h1>

                        <p class="lede">
                            Keep your venue schedule, courts, and reservations
                            organized from one place.
                        </p>
                    </div>

                    <div class="court-motif" aria-hidden="true">
                        <svg viewBox="0 0 300 170" width="100%" height="auto">
                            <rect x="6" y="6" width="288" height="158" rx="6" fill="none" stroke="#C3DD41" stroke-width="2" opacity="0.85" />
                            <line x1="150" y1="6" x2="150" y2="164" stroke="#C3DD41" stroke-width="2" stroke-dasharray="5 5" opacity="0.5" />
                            <line x1="97" y1="6" x2="97" y2="164" stroke="#C3DD41" stroke-width="1.4" opacity="0.35" />
                            <line x1="203" y1="6" x2="203" y2="164" stroke="#C3DD41" stroke-width="1.4" opacity="0.35" />
                            <line x1="6" y1="85" x2="294" y2="85" stroke="#C3DD41" stroke-width="1" opacity="0.2" />
                        </svg>
                    </div>

                    <div class="brand-footer">
                        <div class="stat-cell">
                            <div class="k mono">PORTAL</div>
                            <div class="v mono">ADMIN</div>
                        </div>
                        <div class="stat-cell">
                            <div class="k mono">STEP</div>
                            <div class="v mono">01 / 02</div>
                        </div>
                    </div>
                </section>

                <section class="auth-panel">
                    <div class="auth-card">
                        <div class="mobile-brand">
                            <div class="logo">
                                <img :src="logo" class="logo-mark" alt="" />
                                <span>Dink<span style="color: var(--navy);">Yard</span></span>
                            </div>
                        </div>
                        <div class="card-header">
                            <span class="card-eyebrow mono">Staff access</span>
                            <h2>Welcome<br><em>back.</em></h2>
                            <p>Sign in to manage the schedule for your venue.</p>
                        </div>

                        <form class="auth-form" @submit.prevent="handleSubmit">

                            <div class="form-field">
                                <label class="form-label mono">Email</label>

                                <div class="input-wrap">
                                    <span class="input-index mono">01</span>
                                    <input v-model="email" type="email" class="form-input" placeholder="you@dinkyard.com" autocomplete="username" />
                                </div>
                            </div>

                            <div class="form-field">
                                <label class="form-label mono">Password</label>

                                <div class="input-wrap">
                                    <span class="input-index mono">02</span>
                                    <input v-model="password" type="password" class="form-input" placeholder="••••••••" autocomplete="current-password" />
                                </div>
                            </div>

                            <div v-if="error" class="form-error">
                                <span class="error-mark">!</span>
                                {{ error }}
                            </div>

                            <button type="submit" class="btn btn-lime login-btn" :disabled="submitting">
                                <span>{{ submitting ? 'Signing in…' : 'Log in' }}</span>
                                <span class="btn-arrow">→</span>
                            </button>
                        </form>

                        <div class="card-footer">
                            <span class="mono">Don't have an account?</span>

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
        <div class="auth-view" v-show="tab === 'register'">
            <div class="auth-grid">

                <!-- LEFT BRAND PANEL -->
                <section class="auth-brand">
                    <div class="brand-top">
                        <div class="logo">
                            <img :src="logo" class="logo-mark" alt="" />
                            <span>Dink<span style="color: white;">Yard</span></span>
                        </div>

                        <div class="brand-tag mono">VENUE MANAGEMENT</div>
                    </div>

                    <div class="brand-content">
                        <span class="eyebrow mono">
                            Access request
                        </span>

                        <h1>Join the<br><em>yard.</em></h1>

                        <p class="lede">
                            DinkYard accounts are created by a super admin
                            to keep venue access secure.
                        </p>
                    </div>

                    <div class="court-motif" aria-hidden="true">
                        <svg viewBox="0 0 300 170" width="100%" height="auto">
                            <rect x="6" y="6" width="288" height="158" rx="6" fill="none" stroke="#C3DD41" stroke-width="2" opacity="0.85" />
                            <line x1="150" y1="6" x2="150" y2="164" stroke="#C3DD41" stroke-width="2" stroke-dasharray="5 5" opacity="0.5" />
                            <line x1="97" y1="6" x2="97" y2="164" stroke="#C3DD41" stroke-width="1.4" opacity="0.35" />
                            <line x1="203" y1="6" x2="203" y2="164" stroke="#C3DD41" stroke-width="1.4" opacity="0.35" />
                            <line x1="6" y1="85" x2="294" y2="85" stroke="#C3DD41" stroke-width="1" opacity="0.2" />
                        </svg>
                    </div>

                    <div class="brand-footer">
                        <div class="stat-cell">
                            <div class="k mono">PORTAL</div>
                            <div class="v mono">ADMIN</div>
                        </div>
                        <div class="stat-cell">
                            <div class="k mono">STEP</div>
                            <div class="v mono">02 / 02</div>
                        </div>
                    </div>
                </section>

                <!-- REGISTER PANEL -->
                <section class="auth-panel">
                    <div class="auth-card register-card">

                        <div class="mobile-brand">
                            <div class="logo">
                                <img :src="logo" class="logo-mark" alt="" />
                                <span>Dink<span style="color: var(--navy);">Yard</span></span>
                            </div>
                        </div>

                        <button type="button" class="back-btn mono" @click="tab = 'login'">
                            ← Back to login
                        </button>

                        <div class="card-header register-header">
                            <span class="card-eyebrow mono">Account registration</span>

                            <h2>Request<br><em>access.</em></h2>

                            <p>
                                Accounts on this system aren't self-registered.
                                A super admin adds venue staff to the roster.
                            </p>
                        </div>

                        <!-- ACCESS PASS -->
                        <div class="access-card">

                            <div class="access-top">
                                <span class="mono">ACCESS STATUS</span>
                                <span class="status-pill mono">INVITE ONLY</span>
                            </div>

                            <div class="access-line"></div>

                            <div class="access-heading mono">REQUEST ACCESS</div>

                            <a class="access-item" href="mailto:info.alfeser.shiro@gmail.com">
                                <span class="access-number mono">01</span>
                                <span class="access-name">Email us</span>
                                <span class="access-arrow">↗</span>
                            </a>

                            <a class="access-item" href="https://www.facebook.com/alfeser.shiro" target="_blank" rel="noopener">
                                <span class="access-number mono">02</span>
                                <span class="access-name">Facebook</span>
                                <span class="access-arrow">↗</span>
                            </a>

                            <a class="access-item" href="tel:+639484750030">
                                <span class="access-number mono">03</span>
                                <span class="access-name">Call us</span>
                                <span class="access-arrow">↗</span>
                            </a>
                        </div>

                        <div class="access-note">
                            <span class="note-mark mono">NOTE</span>
                            <p>
                                Include your name and the venue you manage.
                                A super admin will set up your login and send
                                you a temporary password.
                            </p>
                        </div>

                        <div class="card-footer register-footer">
                            <span class="mono">Already have access?</span>

                            <button type="button" class="switch-btn" @click="tab = 'login'">
                                Sign in
                                <span>→</span>
                            </button>
                        </div>

                    </div>
                </section>

            </div>
        </div>
    </div>
</template>


<script setup>
import { ref } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useAuthStore } from '@/stores/UseAuth';
import logo from '@/component/assets/logo.jpg'

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
@import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500;600&display=swap');

/* =========================================================
   TOKENS (shared with homepage)
   ========================================================= */

.auth-page {
    --navy: #001B3E;
    --navy-2: #04264F;
    --navy-3: #0B3568;
    --lime: #C3DD41;
    --lime-2: #9FB92F;
    --cream: #F4F7EA;
    --paper: #FBFCF7;
    --ink: #04101F;
    --ink-soft: #4A5A6B;
    --line: rgba(196, 221, 65, 0.18);

    min-height: 100vh;
    min-height: 100dvh;

    font-family: 'Inter', sans-serif;
    color: var(--ink);
    -webkit-font-smoothing: antialiased;
}

.auth-page h1,
.auth-page h2,
.auth-page .display {
    font-family: 'Space Grotesk', sans-serif;
}

.mono {
    font-family: 'JetBrains Mono', monospace;
    letter-spacing: 0.08em;
}

.auth-page a {
    color: inherit;
    text-decoration: none;
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
   LEFT BRAND PANEL
   ========================================================= */

.auth-brand {
    position: relative;
    overflow: hidden;

    display: flex;
    flex-direction: column;

    padding: 42px 52px;

    border-right: 1px solid var(--line);

    background: radial-gradient(1200px 600px at 12% -10%, #06305e 0%, var(--navy) 55%), var(--navy);
    color: var(--paper);
}

.auth-brand::after {
    content: "";
    position: absolute;
    inset: 0;
    background-image:
        repeating-linear-gradient(0deg, rgba(196, 221, 65, 0.05) 0 1px, transparent 1px 64px),
        repeating-linear-gradient(90deg, rgba(196, 221, 65, 0.05) 0 1px, transparent 1px 64px);
    pointer-events: none;
}

.brand-top {
    display: flex;
    align-items: center;
    justify-content: space-between;

    position: relative;
    z-index: 2;
}

.logo {
    display: flex;
    align-items: center;
    gap: 10px;
    color: var(--paper);
    font-weight: 700;
    font-size: 18px;
}

.logo-mark {
    width: 32px;
    height: 32px;
    border-radius: 9px;
    background: var(--lime);
    object-fit: cover;
}

.logo span {
    color: var(--lime);
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
    margin: auto 0 34px;
}

.eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 7px 14px;
    border-radius: 999px;
    background: rgba(196, 221, 65, 0.1);
    border: 1px solid rgba(196, 221, 65, 0.35);
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.14em;
    color: var(--lime);
    margin-bottom: 22px;
}

.eyebrow::before {
    content: "";
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: var(--lime);
}

.auth-brand h1 {
    font-size: clamp(38px, 4.4vw, 56px);
    line-height: 1;
    font-weight: 700;
    letter-spacing: -0.02em;
    text-transform: uppercase;
    margin-bottom: 18px;
}

.auth-brand h1 em {
    font-style: normal;
    color: var(--lime);
}

.auth-brand .lede {
    max-width: 360px;
    font-size: 15px;
    line-height: 1.6;
    color: rgba(244, 247, 234, 0.65);
}


/* =========================================================
   COURT MOTIF (signature element)
   ========================================================= */

.court-motif {
    position: relative;
    z-index: 1;
    max-width: 340px;
    opacity: 0.9;
    margin-bottom: 34px;
}


/* =========================================================
   BRAND FOOTER
   ========================================================= */

.brand-footer {
    position: relative;
    z-index: 2;

    display: flex;
    gap: 32px;

    padding-top: 20px;
    border-top: 1px solid rgba(196, 221, 65, 0.14);
}

.stat-cell .k {
    font-size: 10px;
    color: rgba(244, 247, 234, 0.45);
    margin-bottom: 4px;
}

.stat-cell .v {
    font-size: 12px;
    color: var(--lime);
}


/* =========================================================
   RIGHT PANEL
   ========================================================= */

.auth-panel {
    display: flex;
    align-items: center;
    justify-content: center;

    background: var(--paper);
    padding: 40px;
}

.auth-card {
    width: 100%;
    max-width: 420px;
}

.mobile-brand {
    display: none;
}


/* =========================================================
   CARD HEADER
   ========================================================= */

.card-eyebrow {
    display: inline-block;
    padding: 6px 12px;
    margin-bottom: 20px;

    border-radius: 999px;
    background: rgba(196, 221, 65, 0.12);
    border: 1px solid rgba(196, 221, 65, 0.3);

    font-size: 10px;
    font-weight: 600;
    letter-spacing: 0.12em;
    color: var(--lime-2);
}

.card-header h2 {
    font-size: clamp(34px, 4vw, 48px);
    line-height: 1;
    font-weight: 700;
    letter-spacing: -0.02em;
    color: var(--navy);
    text-transform: uppercase;
    margin-bottom: 12px;
}

.card-header h2 em {
    font-style: normal;
    color: var(--lime-2);
}

.card-header p {
    color: var(--ink-soft);
    font-size: 14px;
    line-height: 1.6;
    max-width: 340px;
}

.register-header {
    margin-bottom: 22px;
}

.register-header p {
    max-width: 400px;
}


/* =========================================================
   FORM
   ========================================================= */

.auth-form {
    margin-top: 28px;
    display: flex;
    flex-direction: column;
    gap: 18px;
}

.form-label {
    display: block;
    margin-bottom: 8px;

    font-size: 10px;
    letter-spacing: 0.14em;
    color: var(--ink-soft);
    text-transform: uppercase;
}

.input-wrap {
    position: relative;
    display: flex;
    align-items: center;

    border: 1px solid rgba(0, 27, 62, 0.14);
    border-radius: 10px;
    background: #fff;

    transition: border-color .18s ease, box-shadow .18s ease;
}

.input-wrap:focus-within {
    border-color: var(--lime-2);
    box-shadow: 0 0 0 3px rgba(196, 221, 65, 0.18);
}

.input-index {
    padding: 0 4px 0 16px;
    font-size: 11px;
    color: rgba(0, 27, 62, 0.28);
}

.form-input {
    flex: 1;
    width: 100%;
    padding: 14px 16px 14px 8px;

    border: 0;
    background: transparent;
    outline: none;

    font-family: 'Inter', sans-serif;
    font-size: 14px;
    color: var(--ink);
}

.form-input::placeholder {
    color: rgba(4, 16, 31, 0.32);
}

.form-error {
    display: flex;
    align-items: center;
    gap: 10px;

    padding: 12px 14px;
    border-radius: 10px;

    background: rgba(196, 60, 41, 0.08);
    border: 1px solid rgba(196, 60, 41, 0.22);

    color: #9c2c1c;
    font-size: 13px;
}

.error-mark {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;

    width: 18px;
    height: 18px;
    border-radius: 50%;

    background: #c33c29;
    color: #fff;

    font-size: 11px;
    font-weight: 700;
}


/* =========================================================
   BUTTONS (shared with homepage .btn / .btn-lime)
   ========================================================= */

.btn {
    padding: 10px 18px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    border: 1px solid transparent;
    cursor: pointer;
    white-space: nowrap;
    transition: transform .15s ease, background .2s ease, border-color .2s ease;
    display: inline-block;
}

.btn:hover {
    transform: translateY(-1px);
}

.btn-lime {
    background: var(--lime);
    color: var(--navy);
}

.btn-lime:hover:not(:disabled) {
    background: #d3ec5c;
}

.login-btn {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;

    padding: 14px 18px;
    font-size: 14px;
}

.login-btn:disabled {
    opacity: .55;
    cursor: default;
    transform: none;
}

.btn-arrow {
    font-size: 16px;
    line-height: 1;
}


/* =========================================================
   CARD FOOTER
   ========================================================= */

.card-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;

    margin-top: 28px;
    padding-top: 20px;

    border-top: 1px solid rgba(0, 27, 62, 0.08);

    color: var(--ink-soft);
    font-size: 10px;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

.switch-btn {
    padding: 0;
    border: 0;
    background: transparent;

    color: var(--lime-2);
    font-family: 'Inter', sans-serif;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
}

.switch-btn span {
    margin-left: 4px;
}

.switch-btn:hover {
    color: var(--navy);
}


/* =========================================================
   MOBILE BRAND
   ========================================================= */

.mobile-brand .logo {
    color: var(--navy);
}

.mobile-brand .logo-mark {
    background: var(--navy);
}


/* =========================================================
   REGISTER — BACK LINK
   ========================================================= */

.back-btn {
    padding: 0;
    margin-bottom: 26px;

    border: 0;
    background: transparent;

    color: var(--ink-soft);
    font-size: 10px;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    cursor: pointer;
}

.back-btn:hover {
    color: var(--lime-2);
}


/* =========================================================
   ACCESS CARD (role-card pattern from homepage)
   ========================================================= */

.access-card {
    position: relative;
    overflow: hidden;

    padding: 22px;

    background: var(--cream);
    border: 1px solid rgba(0, 27, 62, 0.08);
    border-radius: 16px;
}

.access-card::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--lime);
}

.access-top {
    display: flex;
    align-items: center;
    justify-content: space-between;

    color: var(--ink-soft);
    font-size: 9px;
    letter-spacing: 0.12em;
}

.status-pill {
    padding: 5px 8px;
    border-radius: 4px;

    background: rgba(159, 185, 47, 0.18);
    color: var(--lime-2);
    font-size: 8px;
}

.access-line {
    margin: 16px 0;
    border-top: 1px dashed rgba(0, 27, 62, 0.14);
}

.access-heading {
    margin-bottom: 6px;
    color: rgba(4, 16, 31, 0.4);
    font-size: 8px;
    letter-spacing: 0.12em;
}

.access-item {
    display: flex;
    align-items: center;

    min-height: 44px;

    border-top: 1px solid rgba(0, 27, 62, 0.08);
    color: var(--ink);
    transition: background .15s ease;
}

.access-item:hover {
    background: rgba(196, 221, 65, 0.12);
}

.access-number {
    width: 32px;
    color: rgba(4, 16, 31, 0.32);
    font-size: 8px;
}

.access-name {
    flex: 1;
    font-size: 13.5px;
    font-weight: 600;
}

.access-arrow {
    color: var(--lime-2);
    font-size: 17px;
}


/* =========================================================
   ACCESS NOTE
   ========================================================= */

.access-note {
    display: flex;
    gap: 12px;
    margin-top: 18px;
    color: var(--ink-soft);
}

.note-mark {
    flex-shrink: 0;
    color: var(--lime-2);
    font-size: 8px;
    letter-spacing: 0.1em;
    margin-top: 2px;
}

.access-note p {
    margin: 0;
    font-size: 12px;
    line-height: 1.6;
}


/* =========================================================
   RESPONSIVE
   ========================================================= */

@media (max-width: 960px) {
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
        margin-bottom: 40px;
    }

    .mobile-brand .logo {
        font-size: 20px;
    }

    .auth-card {
        max-width: 460px;
        margin: 0 auto;
    }
}

@media (max-width: 520px) {
    .auth-panel {
        padding: 24px 18px;
    }

    .card-header h2 {
        font-size: 36px;
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
        padding: 16px;
    }

    .access-item {
        min-height: 48px;
    }
}
</style>