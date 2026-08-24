<script setup>

import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { image } from '@/utils/image'
import logo from '@/component/assets/logo.jpg'
import { usePaymentStore } from '@/stores/UsePayment'

const route = useRoute()
const router = useRouter()
const usePayment = usePaymentStore();

const reservations = ref([]);

const step = ref('select')
const methods = ref([])
const loading = ref(false)
const loadError = ref('')

const bookingCode = ref('');
const selected = ref(null)
const file = ref(null)
const preview = ref(null)
const findIdType = ref(0)
const booking_id = ref(0)
const submitting = ref(false)

// const total = computed(() =>
//     reservations.value.reduce((sum, r) => sum + Number(r.amount || 0), 0)
// )

const total = ref(0);

const qrImages = ref([]);
const displayNames = ref([]);
const displayName = (type) => displayNames[type] || (type ? type.charAt(0).toUpperCase() + type.slice(1) : '')
const methodLabel = computed(() => (selected.value ? displayName(selected.value.payment_type) : ''))
const qrImage = ref('');

const fetchMethods = async (id) => {
    loading.value = true
    loadError.value = ''
    try {
        const res = await usePayment.listPaymentMethod(id);
        booking_id.value = res.booking_id;
        qrImages.value = res.image;
        displayNames.value = res.types
        methods.value = res.data
        console.log(res)
        reservations.value = res.reservations;
        total.value = res.reservations.amount;
    } catch (err) {
        loadError.value = 'Could not load payment methods. Please try again.'
    } finally {
        loading.value = false
    }
}

onMounted(() => {
    bookingCode.value = route.params.id;
    fetchMethods(route.params.id);
})

const selectMethod = (method) => {
    selected.value = method
}

const goToQr = () => {
    if (!selected.value) return

    const paymentType = selected.value.payment_type

    qrImage.value = qrImages.value[paymentType];

    findIdType.value = methods.value.find(ar => ar.payment_type == paymentType);

    step.value = 'qr'
}

const onFile = (e) => {
    const f = e.target.files?.[0]
    if (!f) return
    file.value = f
    preview.value = URL.createObjectURL(f)
}

const removeFile = () => {
    file.value = null
    preview.value = null
}

const submitPayment = async () => {
    if (!file.value || submitting.value) return
    submitting.value = true
    try {
        const formData = new FormData()

        formData.append('payment_id', findIdType.value.id)
        formData.append('booking_id', booking_id.value)
        formData.append('image', file.value)
        formData.append('booking_code', bookingCode.value)

        const response = await usePayment.submitPaymentQR(formData);

        if(response){
            setTimeout(()=>{
                router.push({ name: 'checkreservation' })
            }, 2000)
        }

    } catch (err) {
        loadError.value = 'Payment submission failed. Please try again.'
    } finally {
        submitting.value = false
    }
}
</script>

<template>
    <div class="payment-page">
        <header>
            <nav>
                <div class="logo" @click="router.push({ name: 'homepage' })">
                    <img :src="logo" class="logo-mark" alt="">
                    <span>Court-<span style="color: white;">tesy</span></span>
                </div>
                <div class="nav-actions">
                    <button class="btn btn-ghost" @click="router.push({ name: 'homepage' })">Back to Home</button>
                    <button class="btn btn-solid" @click="router.push({ name: 'checkreservation' })">Confirm Booking</button>
                </div>
            </nav>
        </header>

        <section class="payment-hero">
            <div class="wrap">
                <div class="section-head">
                    <span class="kicker mono">SECURE PAYMENT</span>
                    <h1>Complete your booking</h1>
                    <p>Choose how you'd like to pay, then upload proof once you're done.</p>
                </div>

                <div class="payment-panel">
                    <div v-if="loading" class="ps-state">
                        <span class="ps-spinner"></span>
                        <span>Loading payment methods…</span>
                    </div>

                    <div v-else-if="loadError" class="ps-state ps-state-error">
                        <span>{{ loadError }}</span>
                        <button class="btn btn-outline-navy small" @click="fetchMethods">Retry</button>
                    </div>

                    <div v-else-if="!methods.length" class="ps-state">
                        <span>No payment methods are available right now.</span>
                    </div>

                    <div v-else-if="step === 'select'" class="payment-step">
                        <div class="summary-box">
                            <div class="summary-row">
                                <span>{{ reservations.label }}</span>
                                <span>₱{{ reservations.amount }}</span>
                            </div>
                            <div class="summary-total">
                                <span>Total</span>
                                <span>₱{{ total }}</span>
                            </div>
                        </div>

                        <div class="method-grid">
                            <button v-for="method in methods" :key="method.id" type="button" class="method-card" :class="{ active: selected?.id === method.id }" @click="selectMethod(method)">
                                <span class="method-logo mono" :class="method.payment_type">
                                    {{ displayName(method.payment_type) }}
                                </span>
                                <span class="method-check" v-if="selected?.id === method.id">✓</span>
                            </button>
                        </div>

                        <button class="btn btn-lime full" :disabled="!selected" @click="goToQr">
                            Continue
                        </button>
                    </div>

                    <div v-else-if="step === 'qr'" class="payment-step">
                        <button type="button" class="ps-back mono" @click="step = 'select'">← Back</button>
                        <span class="kicker mono">{{ methodLabel.toUpperCase() }} PAYMENT</span>
                        <h3>Scan to pay ₱{{ total }}</h3>

                        <div class="ps-notice">
                            <span class="ps-notice-icon">⚠</span>
                            <span>
                                Save a <strong>screenshot</strong> of your successful {{ methodLabel }} payment
                                after paying — you'll need to upload it as proof on the next step.
                            </span>
                        </div>

                        <div class="qr-box">
                            <img :src="image(qrImage)" alt="Payment QR code" class="qr-img" />
                            <a :href="image(qrImage)" :download="`${selected.payment_type}-payment-qr.jpg`" class="btn btn-outline-navy small">
                                Download QR
                            </a>
                        </div>

                        <button class="btn btn-lime full" @click="step = 'upload'">
                            I've paid — Continue
                        </button>
                    </div>

                    <div v-else class="payment-step">
                        <button type="button" class="ps-back mono" @click="step = 'qr'">← Back</button>
                        <span class="kicker mono">CONFIRM PAYMENT</span>
                        <h3>Upload payment screenshot</h3>
                        <p class="ps-sub">Attach the screenshot you saved after paying via {{ methodLabel }}.</p>

                        <div class="summary-box compact">
                            <div class="summary-row"><span>Method</span><span>{{ methodLabel }}</span></div>
                            <div class="summary-total"><span>Amount Paid</span><span>₱{{ total }}</span></div>
                        </div>

                        <label class="upload-drop" :class="{ filled: preview }">
                            <input type="file" accept="image/*" @change="onFile" hidden />
                            <template v-if="!preview">
                                <span class="upload-icon">⇧</span>
                                <span class="upload-text">Click to upload screenshot</span>
                                <span class="upload-hint mono">PNG or JPG</span>
                            </template>
                            <img v-else :src="preview" class="upload-preview" alt="Screenshot preview" />
                        </label>

                        <button v-if="preview" type="button" class="ps-remove mono" @click.prevent="removeFile">
                            Remove image
                        </button>

                        <button class="btn btn-lime full" :disabled="!file || submitting" @click="submitPayment">
                            {{ submitting ? 'Submitting…' : 'Submit Payment' }}
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <footer>
            <div class="wrap">
                <div class="simple-footer">
                    <div class="footer-main">
                        <div class="footer-brand">
                            <div class="logo">
                                <div class="logo-mark"></div>
                                Dink<span>Yard</span>
                            </div>
                            <p>A simpler way to discover, book, and manage your favorite courts.</p>
                        </div>

                        <div class="footer-links">
                            <a href="#">Courts</a>
                            <a href="#">How It Works</a>
                            <a href="#">FAQ</a>
                            <a href="#">Contact</a>
                        </div>
                    </div>

                    <div class="foot-bottom">
                        <span>© 2026 DinkYard. All rights reserved.</span>
                        <div class="footer-legal">
                            <a href="#">Privacy</a>
                            <a href="#">Terms</a>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500;600&display=swap');

.payment-page {
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

    font-family: 'Inter', sans-serif;
    background: var(--paper);
    color: var(--ink);
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    -webkit-font-smoothing: antialiased;
}

h1,
h2,
h3 {
    font-family: 'Space Grotesk', sans-serif;
}

.mono {
    font-family: 'JetBrains Mono', monospace;
    letter-spacing: 0.08em;
}

a {
    color: inherit;
    text-decoration: none;
}

.wrap {
    max-width: 1240px;
    margin: 0 auto;
    padding: 0 32px;
}

/* ===== HEADER (matches Homepage.vue) ===== */
header {
    position: sticky;
    top: 0;
    z-index: 50;
    background: rgba(0, 27, 62, 0.92);
    backdrop-filter: blur(10px);
    border-bottom: 1px solid rgba(196, 221, 65, 0.14);
}

nav {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 18px 32px;
    max-width: 1240px;
    margin: 0 auto;
}

header .logo {
    display: flex;
    align-items: center;
    gap: 10px;
    color: var(--paper);
    font-weight: 700;
    font-size: 18px;
}

header .logo-mark {
    width: 32px;
    height: 32px;
    border-radius: 9px;
    background: var(--lime);
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
}

header .logo span {
    color: var(--lime);
}

.nav-actions {
    display: flex;
    align-items: center;
    gap: 10px;
}

.btn {
    padding: 10px 18px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    border: 1px solid transparent;
    cursor: pointer;
    white-space: nowrap;
    transition: transform .15s ease, background .2s ease, border-color .2s ease, opacity .2s ease;
    display: inline-block;
    text-align: center;
}

.btn:hover {
    transform: translateY(-1px);
}

.btn:disabled {
    opacity: 0.45;
    cursor: not-allowed;
    transform: none;
}

.btn-ghost {
    background: transparent;
    color: var(--paper);
    border-color: rgba(244, 247, 234, 0.25);
}

.btn-ghost:hover {
    border-color: var(--lime);
    color: var(--lime);
}

.btn-solid {
    background: var(--navy-3);
    color: var(--paper);
    border-color: rgba(196, 221, 65, 0.25);
}

.btn-lime {
    background: var(--lime);
    color: var(--navy);
}

.btn-lime:hover:not(:disabled) {
    background: #d3ec5c;
}

.btn-outline-navy {
    background: transparent;
    color: var(--navy);
    border-color: rgba(0, 27, 62, 0.25);
}

.btn-outline-navy:hover {
    border-color: var(--navy);
}

.btn.full {
    display: block;
    width: 100%;
    padding: 13px 18px;
    font-size: 14px;
}

.btn.small {
    padding: 8px 14px;
    font-size: 12px;
}

@media (max-width: 600px) {
    nav {
        padding: 12px 16px;
        gap: 12px;
    }

    header .logo {
        font-size: 15px;
    }

    .btn {
        padding: 8px 10px;
        font-size: 11px;
        border-radius: 7px;
    }
}

/* ===== PAYMENT HERO ===== */
.payment-hero {
    flex: 1;
    padding: 72px 0 96px;
}

.section-head {
    max-width: 560px;
    margin: 0 auto 40px;
    text-align: center;
}

.kicker {
    display: inline-block;
    font-size: 11px;
    font-weight: 600;
    color: var(--lime-2);
    letter-spacing: 0.12em;
    margin-bottom: 12px;
}

.section-head h1 {
    font-size: clamp(30px, 4vw, 42px);
    margin-bottom: 10px;
    color: var(--navy);
    text-transform: uppercase;
}

.section-head p {
    color: var(--ink-soft);
    font-size: 14.5px;
    line-height: 1.6;
}

.payment-panel h3 {
    font-size: 22px;
    margin: 8px 0 4px;
    color: var(--navy);
}

.ps-sub {
    color: var(--ink-soft);
    font-size: 13.5px;
    line-height: 1.5;
    margin-bottom: 18px;
}

.payment-panel {
    max-width: 440px;
    margin: 0 auto;
    background: var(--cream);
    border: 1px solid rgba(0, 27, 62, 0.08);
    border-radius: 20px;
    padding: 32px 28px;
    box-shadow: 0 30px 60px -30px rgba(0, 27, 62, 0.25);
}

.ps-back {
    display: inline-block;
    background: none;
    border: none;
    color: var(--ink-soft);
    font-size: 11px;
    cursor: pointer;
    padding: 0;
    margin-bottom: 14px;
}

.ps-back:hover {
    color: var(--navy);
}

/* ---- loading / error / empty states ---- */
.ps-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 14px;
    padding: 40px 10px;
    color: var(--ink-soft);
    font-size: 13.5px;
    text-align: center;
}

.ps-state-error {
    color: #C0392B;
}

.ps-spinner {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    border: 3px solid rgba(0, 27, 62, 0.12);
    border-top-color: var(--lime-2);
    animation: ps-spin 0.8s linear infinite;
}

@keyframes ps-spin {
    to {
        transform: rotate(360deg);
    }
}

/* ---- summary box ---- */
.summary-box {
    background: var(--paper);
    border: 1px solid rgba(0, 27, 62, 0.08);
    border-radius: 14px;
    padding: 16px 18px;
    margin: 0 0 20px;
}

.summary-box.compact {
    margin-top: 4px;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    font-size: 13.5px;
    color: var(--ink-soft);
    padding: 4px 0;
}

.summary-total {
    display: flex;
    justify-content: space-between;
    margin-top: 8px;
    padding-top: 10px;
    border-top: 1px dashed rgba(0, 27, 62, 0.15);
    font-family: 'Space Grotesk', sans-serif;
    font-weight: 700;
    font-size: 16px;
    color: var(--navy);
}

/* ---- method selection ---- */
.method-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 12px;
    margin-bottom: 22px;
}

.method-card {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 22px 10px;
    border-radius: 14px;
    border: 1.5px solid rgba(0, 27, 62, 0.1);
    background: var(--paper);
    cursor: pointer;
    transition: border-color .15s ease, transform .15s ease, box-shadow .15s ease;
}

.method-card:hover {
    transform: translateY(-2px);
}

.method-card.active {
    border-color: var(--lime-2);
    box-shadow: 0 0 0 3px rgba(196, 221, 65, 0.25);
}

.method-logo {
    font-weight: 600;
    font-size: 13px;
    padding: 9px 16px;
    border-radius: 8px;
    letter-spacing: 0.03em;
    background: var(--navy);
    color: #fff;
}

.method-logo.gcash {
    background: #0072CE;
    color: #fff;
}

.method-logo.maya {
    background: #00D563;
    color: #001B3E;
}

.method-check {
    position: absolute;
    top: -8px;
    right: -8px;
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: var(--lime);
    color: var(--navy);
    font-size: 12px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* ---- notice ---- */
.ps-notice {
    display: flex;
    gap: 10px;
    align-items: flex-start;
    background: rgba(196, 221, 65, 0.22);
    border: 1px solid rgba(159, 185, 47, 0.4);
    border-radius: 12px;
    padding: 12px 14px;
    font-size: 12.5px;
    color: var(--navy);
    line-height: 1.5;
    margin: 16px 0 20px;
}

.ps-notice-icon {
    font-size: 15px;
    line-height: 1;
    flex-shrink: 0;
}

/* ---- QR box ---- */
.qr-box {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 14px;
    background: var(--paper);
    border: 1px solid rgba(0, 27, 62, 0.08);
    border-radius: 16px;
    padding: 20px;
    margin-bottom: 20px;
}

.qr-img {
    width: 220px;
    height: 220px;
    object-fit: contain;
    border-radius: 10px;
    background: #fff;
    border: 1px solid rgba(0, 27, 62, 0.08);
}

/* ---- upload ---- */
.upload-drop {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 6px;
    min-height: 160px;
    border: 1.5px dashed rgba(0, 27, 62, 0.22);
    border-radius: 14px;
    background: var(--paper);
    color: var(--ink-soft);
    cursor: pointer;
    padding: 18px;
    margin-bottom: 12px;
    transition: border-color .15s ease, background .15s ease;
}

.upload-drop:hover {
    border-color: var(--lime-2);
}

.upload-drop.filled {
    padding: 8px;
    border-style: solid;
}

.upload-icon {
    font-size: 20px;
    color: var(--lime-2);
}

.upload-text {
    font-size: 13.5px;
    font-weight: 500;
    color: var(--navy);
}

.upload-hint {
    font-size: 10px;
    color: var(--ink-soft);
}

.upload-preview {
    max-height: 220px;
    width: auto;
    border-radius: 8px;
    display: block;
}

.ps-remove {
    display: block;
    margin: 0 auto 18px;
    background: none;
    border: none;
    color: #C0392B;
    font-size: 11px;
    cursor: pointer;
}

/* ===== FOOTER (matches Homepage.vue) ===== */
footer {
    background: var(--navy);
    color: rgba(244, 247, 234, 0.7);
    font-size: 13.5px;
}

.simple-footer {
    padding: 48px 0 24px;
}

.footer-main {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 40px;
    padding-bottom: 35px;
    border-bottom: 1px solid rgba(244, 247, 234, 0.1);
}

.footer-brand {
    max-width: 360px;
}

.footer-brand .logo {
    display: flex;
    align-items: center;
    gap: 10px;
    color: var(--paper);
    font-weight: 700;
    font-size: 18px;
    margin-bottom: 12px;
}

.footer-brand .logo span {
    color: var(--lime);
}

.footer-brand .logo-mark {
    width: 28px;
    height: 28px;
    border-radius: 8px;
    background: var(--lime);
}

.footer-brand p {
    margin: 0;
    color: rgba(244, 247, 234, 0.55);
    line-height: 1.6;
}

.footer-links {
    display: flex;
    align-items: center;
    gap: 26px;
}

.footer-links a,
.footer-legal a {
    color: rgba(244, 247, 234, 0.6);
    font-size: 12.5px;
    transition: color 0.2s ease;
}

.footer-links a:hover,
.footer-legal a:hover {
    color: var(--lime);
}

.foot-bottom {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    padding-top: 20px;
    color: rgba(244, 247, 234, 0.4);
    font-size: 11px;
}

.footer-legal {
    display: flex;
    gap: 20px;
}

@media (max-width: 700px) {
    .footer-main {
        flex-direction: column;
        gap: 25px;
    }

    .footer-links {
        flex-wrap: wrap;
        gap: 15px 22px;
    }

    .foot-bottom {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
}

@media (max-width: 600px) {
    .payment-hero {
        padding: 48px 0 64px;
    }

    .payment-panel {
        padding: 26px 20px;
        border-radius: 16px;
    }
}
</style>