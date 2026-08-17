<script setup>
import { computed, onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()

const status = computed(() => (route.query.status === 'success' ? 'success' : 'failed'))
const bookingCode = computed(() => route.query.booking_code || '—')

const revealed = ref(false)
onMounted(() => {
    requestAnimationFrame(() => { revealed.value = true })
})

const copy = computed(() => {
    return status.value === 'success'
        ? {
            eyebrow: 'Payment confirmed',
            headline: "Court's booked.",
            body: 'Your payment went through. We\u2019ve sent a receipt to your email — see you on the court.',
            cta: 'View my booking',
        }
        : {
            eyebrow: 'Payment not completed',
            headline: 'Play stopped.',
            body: 'The payment didn\u2019t go through, and nothing was charged. You can try again whenever you\u2019re ready.',
            cta: 'Try again',
        }
})

function primaryAction() {
    if (status.value === 'success') {
        router.push({ name: 'checkreservation', query: { booking_code: bookingCode.value } })
    } else {
        router.back()
    }
}

function goHome() {
    router.push({ name: 'homepage' })
}
</script>

<template>
    <div class="stage" :class="status">
        <div class="court-lines" aria-hidden="true">
            <svg viewBox="0 0 400 400" preserveAspectRatio="none">
                <rect x="20" y="20" width="360" height="360" class="line" />
                <line x1="20" y1="200" x2="380" y2="200" class="line" />
                <circle cx="200" cy="200" r="46" class="line" />
            </svg>
        </div>

        <div class="ticket" :class="{ show: revealed }">
            <div class="ticket__top">
                <span class="eyebrow">{{ copy.eyebrow }}</span>

                <div class="mark" :class="status">
                    <svg v-if="status === 'success'" viewBox="0 0 52 52" class="mark__svg">
                        <circle cx="26" cy="26" r="24" class="mark__ring" />
                        <path d="M15 27 L23 35 L38 18" class="mark__check" />
                    </svg>
                    <svg v-else viewBox="0 0 52 52" class="mark__svg">
                        <circle cx="26" cy="26" r="24" class="mark__ring" />
                        <path d="M18 18 L34 34 M34 18 L18 34" class="mark__x" />
                    </svg>
                </div>

                <h1 class="headline">{{ copy.headline }}</h1>
                <p class="body">{{ copy.body }}</p>
            </div>

            <div class="perforation" aria-hidden="true"></div>

            <div class="ticket__stub">
                <span class="stub__label">Booking code</span>
                <span class="stub__code">{{ bookingCode }}</span>
            </div>

            <div class="actions">
                <button class="btn btn--primary" @click="primaryAction">{{ copy.cta }}</button>
                <button class="btn btn--ghost" @click="goHome">Back to home</button>
            </div>
        </div>
    </div>
</template>

<style scoped>
.stage {
    --court-green: #1b4332;
    --court-green-deep: #10281c;
    --chalk: #f8f9f4;
    --amber: #e9c46a;
    --charcoal: #22333b;
    --clay: #bc4749;
    --sage: #74a57f;

    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--court-green-deep);
    background-image: radial-gradient(circle at 20% 15%, rgba(233, 196, 106, 0.08), transparent 45%),
        radial-gradient(circle at 85% 80%, rgba(116, 165, 127, 0.1), transparent 50%);
    font-family: 'Inter', system-ui, sans-serif;
    padding: 24px;
    position: relative;
    overflow: hidden;
}

.court-lines {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0.5;
}

.court-lines svg {
    width: min(90vw, 700px);
    height: min(90vw, 700px);
}

.court-lines .line {
    fill: none;
    stroke: rgba(248, 249, 244, 0.06);
    stroke-width: 1.5;
}

.ticket {
    position: relative;
    width: 100%;
    max-width: 420px;
    background: var(--chalk);
    border-radius: 20px;
    box-shadow: 0 30px 60px -20px rgba(0, 0, 0, 0.5);
    opacity: 0;
    transform: translateY(18px) scale(0.98);
    transition: opacity 0.6s cubic-bezier(0.16, 1, 0.3, 1), transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
}

.ticket.show {
    opacity: 1;
    transform: translateY(0) scale(1);
}

.ticket__top {
    padding: 40px 36px 28px;
    text-align: center;
}

.eyebrow {
    display: inline-block;
    font-family: 'JetBrains Mono', ui-monospace, monospace;
    font-size: 11px;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    color: var(--charcoal);
    opacity: 0.55;
    margin-bottom: 22px;
}

.mark {
    width: 64px;
    height: 64px;
    margin: 0 auto 22px;
}

.mark__svg {
    width: 100%;
    height: 100%;
}

.mark__ring {
    fill: none;
    stroke-width: 2.5;
}

.success .mark__ring {
    stroke: var(--court-green);
}

.failed .mark__ring {
    stroke: var(--clay);
}

.mark__check,
.mark__x {
    fill: none;
    stroke-linecap: round;
    stroke-linejoin: round;
    stroke-width: 3.5;
}

.success .mark__check {
    stroke: var(--court-green);
    stroke-dasharray: 40;
    stroke-dashoffset: 40;
    animation: draw 0.5s 0.35s cubic-bezier(0.65, 0, 0.35, 1) forwards;
}

.failed .mark__x {
    stroke: var(--clay);
    stroke-dasharray: 24;
    stroke-dashoffset: 24;
    animation: draw 0.4s 0.35s ease forwards;
}

@keyframes draw {
    to {
        stroke-dashoffset: 0;
    }
}

.headline {
    font-family: 'Fraunces', Georgia, serif;
    font-size: 30px;
    font-weight: 600;
    color: var(--charcoal);
    margin: 0 0 10px;
    letter-spacing: -0.01em;
}

.body {
    font-size: 14.5px;
    line-height: 1.55;
    color: var(--charcoal);
    opacity: 0.72;
    margin: 0;
}

.perforation {
    position: relative;
    height: 1px;
    background-image: repeating-linear-gradient(to right, rgba(34, 51, 59, 0.25) 0 8px, transparent 8px 16px);
    margin: 0 0 0;
}

.perforation::before,
.perforation::after {
    content: '';
    position: absolute;
    top: -11px;
    width: 22px;
    height: 22px;
    background: var(--court-green-deep);
    border-radius: 50%;
}

.perforation::before {
    left: -11px;
}

.perforation::after {
    right: -11px;
}

.ticket__stub {
    padding: 22px 36px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
}

.stub__label {
    font-size: 11px;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: var(--charcoal);
    opacity: 0.5;
}

.stub__code {
    font-family: 'JetBrains Mono', ui-monospace, monospace;
    font-size: 20px;
    font-weight: 600;
    letter-spacing: 0.08em;
    color: var(--court-green);
    background: rgba(27, 67, 50, 0.08);
    padding: 6px 16px;
    border-radius: 8px;
}

.actions {
    padding: 4px 36px 36px;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.btn {
    border: none;
    border-radius: 12px;
    padding: 13px 20px;
    font-size: 14.5px;
    font-weight: 600;
    cursor: pointer;
    transition: transform 0.15s ease, opacity 0.15s ease;
    font-family: inherit;
}

.btn:hover {
    transform: translateY(-1px);
}

.btn:focus-visible {
    outline: 2px solid var(--amber);
    outline-offset: 2px;
}

.btn--primary {
    background: var(--court-green);
    color: var(--chalk);
}

.failed .btn--primary {
    background: var(--clay);
}

.btn--ghost {
    background: transparent;
    color: var(--charcoal);
    opacity: 0.65;
}

@media (prefers-reduced-motion: reduce) {

    .ticket,
    .mark__check,
    .mark__x {
        animation: none !important;
        transition: none !important;
        opacity: 1 !important;
        transform: none !important;
        stroke-dashoffset: 0 !important;
    }
}
</style>