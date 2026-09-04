<script setup>

import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { image } from '@/utils/image'
import logo from '@/component/assets/logo.jpg'
import { usePaymentStore } from '@/stores/UsePayment'
import Swal from 'sweetalert2'

const route = useRoute()
const router = useRouter()
const usePayment = usePaymentStore();

const reservations = ref([]);

const step = ref('select')
const steps = ['select', 'qr', 'upload']
const stepIndex = computed(() => steps.indexOf(step.value))

const methods = ref([])
const loading = ref(false)
const loadError = ref('')

const bookingCode = ref('');
const selected = ref(null)
const file = ref(null)
const ispaid = ref(false)
const preview = ref(null)
const findIdType = ref(0)
const booking_id = ref(0)
const submitting = ref(false)
const isDragging = ref(false)

const agreeNoRefund = ref(false)
const agreeTerms = ref(false)

const total = ref(0);

const qrImages = ref([]);
const displayNames = ref([]);
const displayName = (type) => displayNames[type] || (type ? type.charAt(0).toUpperCase() + type.slice(1) : '')
const methodLabel = computed(() => (selected.value ? displayName(selected.value.payment_type) : ''))
const qrImage = ref('');

const canSubmit = computed(() =>
    !!file.value && agreeNoRefund.value && agreeTerms.value && !submitting.value
)

const ctaLabel = computed(() => {
    if (step.value === 'select') return 'Continue'
    if (step.value === 'qr') return "I've paid — Continue"
    return submitting.value ? 'Submitting…' : 'Submit Payment'
})

const ctaDisabled = computed(() => {
    if (step.value === 'select') return !selected.value
    if (step.value === 'qr') return false
    return !canSubmit.value
})

const fetchMethods = async (id) => {
    loading.value = true
    loadError.value = ''
    try {
        const res = await usePayment.listPaymentMethod(id);
        booking_id.value = res.booking_id;
        qrImages.value = res.image;
        displayNames.value = res.types
        methods.value = res.data
        reservations.value = res.reservations;
        total.value = res.reservations.amount;
        ispaid.value = res.ispaid === 'paid' ? true : false;
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

const setFile = (f) => {
    if (!f) return
    file.value = f
    preview.value = URL.createObjectURL(f)
}

const onFile = (e) => {
    const f = e.target.files?.[0]
    setFile(f)
}

const onDrop = (e) => {
    isDragging.value = false
    const f = e.dataTransfer.files?.[0]
    if (f && f.type.startsWith('image/')) setFile(f)
}

const removeFile = () => {
    file.value = null
    preview.value = null
}

const submitPayment = async () => {
    if (!canSubmit.value) return
    submitting.value = true
    try {
        const formData = new FormData()

        formData.append('payment_id', findIdType.value.id)
        formData.append('booking_id', booking_id.value)
        formData.append('image', file.value)
        formData.append('booking_code', bookingCode.value)

        const response = await usePayment.submitPaymentQR(formData);

        if (response) {

            const result = Swal.fire({
                icon: 'success',
                title: 'Reservation Submitted!',
                html: `
                    <p style="margin-bottom: 10px;">Check your reservation status — once confirmed, your payment is complete.</p>
                    <div style="background-color: #f3f4f6; padding: 10px; border-radius: 6px; margin: 12px 0;">
                    <small style="color: #6b7280; display: block; margin-bottom: 4px;">YOUR BOOKING CODE</small>
                    <strong style="font-size: 1.25rem; letter-spacing: 1px; color: #111827;">${bookingCode.value}</strong>
                    </div>
                    <p style="font-size: 0.9rem; color: #4b5563;">Keep this code or use your registered phone number to look up your booking.</p>
                `,
                confirmButtonText: 'Done',
                confirmButtonColor: '#16a34a'
            });

            if (result.isConfirmed) {
                ispaid.value = true;
            }
            // setTimeout(() => {
            //     router.push({ name: 'checkreservation' })
            // }, 2000)
        }

    } catch (err) {
        loadError.value = 'Payment submission failed. Please try again.'
    } finally {
        submitting.value = false
    }
}

const handleCta = () => {
    if (step.value === 'select') goToQr()
    else if (step.value === 'qr') step.value = 'upload'
    else submitPayment()
}

const handleBack = () => {
    if (step.value === 'qr') step.value = 'select'
    else if (step.value === 'upload') step.value = 'qr'
    else router.push({ name: 'homepage' })
}
</script>

<template>
    <div class="payment-page">
        <div class="topbar">
            <button class="topbar-back" @click="handleBack" aria-label="Back">
                <span>←</span>
            </button>
            <div class="topbar-brand">
                <img :src="logo" class="topbar-logo" alt="" />
                <span>Court-<span class="accent">tesy</span></span>
            </div>
            <button class="topbar-close" @click="router.push({ name: 'homepage' })" aria-label="Close">✕</button>
        </div>

        <main class="checkout" v-if="!ispaid">
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

            <div v-else class="checkout-grid">
                <section class="checkout-main">
                    <span class="kicker mono">STEP {{ stepIndex + 1 }} OF 3</span>
                    <h1>{{ step === 'select' ? 'Choose how to pay' : step === 'qr' ? 'Scan & pay' : 'Confirm your
                        payment' }}</h1>

                    <div class="score-track">
                        <div class="score-fill"
                            :style="{ width: stepIndex === 0 ? '8%' : stepIndex === 1 ? '54%' : '100%' }"></div>
                    </div>

                    <transition name="ps-fade" mode="out-in">
                        <div v-if="step === 'select'" class="step-block" key="select">
                            <span class="field-label mono">PAYMENT METHOD</span>
                            <div class="method-grid">
                                <button v-for="method in methods" :key="method.id" type="button" class="method-card"
                                    :class="{ active: selected?.id === method.id }" @click="selectMethod(method)">
                                    <span class="method-logo mono" :class="method.payment_type">
                                        {{ displayName(method.payment_type) }}
                                    </span>
                                    <span class="method-check" v-if="selected?.id === method.id">✓</span>
                                </button>
                            </div>
                        </div>

                        <div v-else-if="step === 'qr'" class="step-block" key="qr">
                            <span class="field-label mono">{{ methodLabel.toUpperCase() }} QR CODE</span>

                            <div class="ps-notice">
                                <span class="ps-notice-icon">⚠</span>
                                <span>
                                    Save a <strong>screenshot</strong> of your successful {{ methodLabel }} payment —
                                    you'll upload it as proof on the next step.
                                </span>
                            </div>

                            <div class="qr-box">
                                <img :src="image(qrImage)" alt="Payment QR code" class="qr-img" />
                                <a :href="image(qrImage)" :download="`${selected.payment_type}-payment-qr.jpg`"
                                    class="btn btn-outline-navy small">
                                    Download QR
                                </a>
                            </div>
                        </div>

                        <div v-else class="step-block" key="upload">
                            <span class="field-label mono">PAYMENT PROOF</span>
                            <p class="ps-sub">Attach the screenshot you saved after paying via {{ methodLabel }}.</p>

                            <label class="upload-drop" :class="{ filled: preview, dragging: isDragging }"
                                @dragover.prevent="isDragging = true" @dragleave.prevent="isDragging = false"
                                @drop.prevent="onDrop">
                                <input type="file" accept="image/*" @change="onFile" hidden />
                                <template v-if="!preview">
                                    <span class="upload-icon">⇧</span>
                                    <span class="upload-text">Click or drop screenshot here</span>
                                    <span class="upload-hint mono">PNG OR JPG</span>
                                </template>
                                <img v-else :src="preview" class="upload-preview" alt="Screenshot preview" />
                            </label>

                            <button v-if="preview" type="button" class="ps-remove mono" @click.prevent="removeFile">
                                Remove image
                            </button>

                            <div class="ps-agree">
                                <label class="ps-check">
                                    <input type="checkbox" v-model="agreeNoRefund" />
                                    <span class="ps-check-box"></span>
                                    <span class="ps-check-text">I understand this payment is
                                        <strong>non-refundable</strong> once confirmed.</span>
                                </label>

                                <label class="ps-check">
                                    <input type="checkbox" v-model="agreeTerms" />
                                    <span class="ps-check-box"></span>
                                    <span class="ps-check-text">I agree to the <a href="#" @click.prevent>Terms &
                                            Conditions</a> and <a href="#" @click.prevent>Privacy Policy</a>.</span>
                                </label>
                            </div>
                        </div>
                    </transition>
                </section>

                <aside class="checkout-side">
                    <div class="side-card">
                        <div class="side-row">
                            <span>{{ reservations.label || 'Court booking' }}</span>
                            <span>₱{{ reservations.amount ?? total }}</span>
                        </div>
                        <div class="side-total">
                            <span>Total Downpayment: </span>
                            <span>₱{{ total }}</span>
                        </div>
                        <div class="side-row">
                            <span>
                                Remaining Balance:
                                <small>TO BE PAID ON COURT</small>
                            </span>
                            <span>₱{{ total }}</span>
                        </div>

                        <button class="btn btn-lime full" :disabled="ctaDisabled" @click="handleCta">
                            {{ ctaLabel }}
                        </button>

                        <div class="side-method" v-if="selected">
                            <span class="mono">PAYING VIA</span>
                            <span class="side-method-name">{{ methodLabel }}</span>
                        </div>
                    </div>
                </aside>
            </div>
        </main>
        <main class="checkout" v-else>
            <div class="ps-state">
                <span>
                    Booking payment is already submitted. You can check
                    <router-link :to="{ name: 'checkreservation' }" style="color: skyblue;"> HERE</router-link>. <br>
                    Use the
                    following code. <br><br>
                    <h1>{{ bookingCode }}</h1>
                </span>
            </div>
        </main>

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
    --line: rgba(0, 27, 62, 0.1);
    --coral: #C0392B;
    --coral-bg: rgba(192, 57, 43, 0.07);

    font-family: 'Inter', sans-serif;
    background: var(--paper);
    color: var(--ink);
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    -webkit-font-smoothing: antialiased;
}

h1 {
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

.accent {
    color: var(--lime-2);
}

/* ===== TOP UTILITY BAR (reference-inspired) ===== */
.topbar {
    display: grid;
    grid-template-columns: 44px 1fr 44px;
    align-items: center;
    padding: 20px 28px;
    border-bottom: 1px solid var(--line);
    background: var(--paper);
}

.topbar-back,
.topbar-close {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    border: none;
    background: transparent;
    color: var(--ink-soft);
    font-size: 16px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background .15s ease, color .15s ease;
}

.topbar-back:hover,
.topbar-close:hover {
    background: var(--cream);
    color: var(--navy);
}

.topbar-brand {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 9px;
    font-weight: 700;
    font-size: 15px;
    color: var(--navy);
}

.topbar-logo {
    width: 24px;
    height: 24px;
    border-radius: 6px;
    object-fit: cover;
}

/* ===== CHECKOUT LAYOUT ===== */
.checkout {
    flex: 1;
    padding: 56px 28px 80px;
    max-width: 940px;
    width: 100%;
    margin: 0 auto;
}

.checkout-grid {
    display: grid;
    grid-template-columns: 1.15fr 0.85fr;
    gap: 56px;
    align-items: start;
}

.checkout-main {
    max-width: 480px;
}

.kicker {
    display: inline-block;
    font-size: 10.5px;
    font-weight: 600;
    color: var(--lime-2);
    letter-spacing: 0.14em;
    margin-bottom: 10px;
}

.checkout-main h1 {
    font-size: clamp(24px, 3vw, 30px);
    color: var(--navy);
    margin: 0 0 18px;
    letter-spacing: -0.01em;
}

.score-track {
    position: relative;
    height: 3px;
    background: rgba(0, 27, 62, 0.08);
    border-radius: 3px;
    margin-bottom: 32px;
    overflow: hidden;
}

.score-fill {
    position: absolute;
    left: 0;
    top: 0;
    height: 100%;
    background: linear-gradient(90deg, var(--lime-2), var(--lime));
    border-radius: 3px;
    transition: width .4s cubic-bezier(.65, 0, .35, 1);
}

.field-label {
    display: block;
    font-size: 10.5px;
    font-weight: 600;
    color: var(--ink-soft);
    margin-bottom: 12px;
}

.ps-fade-enter-active,
.ps-fade-leave-active {
    transition: opacity .2s ease, transform .2s ease;
}

.ps-fade-enter-from {
    opacity: 0;
    transform: translateY(6px);
}

.ps-fade-leave-to {
    opacity: 0;
    transform: translateY(-6px);
}

.ps-sub {
    color: var(--ink-soft);
    font-size: 13.5px;
    line-height: 1.5;
    margin: 0 0 18px;
}

/* ---- state screens ---- */
.ps-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 14px;
    padding: 100px 10px;
    color: var(--ink-soft);
    font-size: 13.5px;
    text-align: center;
}

.ps-state-error {
    color: var(--coral);
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

/* ---- method selection ---- */
.method-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 12px;
}

.method-card {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 22px 10px;
    border-radius: 14px;
    border: 1.5px solid var(--line);
    background: var(--paper);
    cursor: pointer;
    transition: border-color .15s ease, transform .15s ease, box-shadow .15s ease;
}

.method-card:hover {
    transform: translateY(-2px);
    border-color: rgba(0, 27, 62, 0.2);
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
    background: rgba(196, 221, 65, 0.2);
    border: 1px solid rgba(159, 185, 47, 0.4);
    border-radius: 12px;
    padding: 12px 14px;
    font-size: 12.5px;
    color: var(--navy);
    line-height: 1.5;
    margin-bottom: 20px;
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
    background: var(--cream);
    border: 1px solid var(--line);
    border-radius: 16px;
    padding: 24px;
}

.qr-img {
    width: 200px;
    height: 200px;
    object-fit: contain;
    border-radius: 10px;
    background: #fff;
    border: 1px solid var(--line);
    padding: 8px;
}

/* ---- upload ---- */
.upload-drop {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 6px;
    min-height: 150px;
    border: 1.5px dashed rgba(0, 27, 62, 0.22);
    border-radius: 14px;
    background: var(--cream);
    color: var(--ink-soft);
    cursor: pointer;
    padding: 18px;
    margin-bottom: 12px;
    transition: border-color .15s ease, background .15s ease;
}

.upload-drop:hover {
    border-color: var(--lime-2);
}

.upload-drop.dragging {
    border-color: var(--lime-2);
    background: rgba(196, 221, 65, 0.14);
}

.upload-drop.filled {
    padding: 8px;
    border-style: solid;
    background: var(--paper);
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
    max-height: 200px;
    width: auto;
    border-radius: 8px;
    display: block;
}

.ps-remove {
    display: block;
    margin: 0 0 20px;
    background: none;
    border: none;
    color: var(--coral);
    font-size: 11px;
    cursor: pointer;
}

/* ---- agreement checkboxes ---- */
.ps-agree {
    display: flex;
    flex-direction: column;
    gap: 12px;
    padding: 14px;
    background: var(--coral-bg);
    border: 1px solid rgba(192, 57, 43, 0.16);
    border-radius: 12px;
}

.ps-check {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    cursor: pointer;
    user-select: none;
}

.ps-check input[type="checkbox"] {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
}

.ps-check-box {
    flex-shrink: 0;
    width: 18px;
    height: 18px;
    border-radius: 5px;
    border: 1.5px solid rgba(0, 27, 62, 0.28);
    background: var(--paper);
    margin-top: 1px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background .15s ease, border-color .15s ease;
}

.ps-check-box::after {
    content: '';
    width: 5px;
    height: 9px;
    border: solid var(--navy);
    border-width: 0 2px 2px 0;
    transform: rotate(45deg) scale(0);
    transition: transform .15s ease;
    margin-bottom: 2px;
}

.ps-check input:checked+.ps-check-box {
    background: var(--lime);
    border-color: var(--lime-2);
}

.ps-check input:checked+.ps-check-box::after {
    transform: rotate(45deg) scale(1);
}

.ps-check input:focus-visible+.ps-check-box {
    outline: 2px solid var(--navy-3);
    outline-offset: 2px;
}

.ps-check-text {
    font-size: 12.5px;
    line-height: 1.5;
    color: var(--ink);
}

.ps-check-text a {
    color: var(--navy);
    font-weight: 600;
    text-decoration: underline;
}

/* ===== SIDEBAR (reference's right column, restyled) ===== */
.checkout-side {
    position: sticky;
    top: 90px;
}

.side-card {
    background: var(--cream);
    border: 1px solid var(--line);
    border-radius: 18px;
    padding: 24px;
    box-shadow: 0 24px 48px -28px rgba(0, 27, 62, 0.22);
}

.side-row {
    display: flex;
    justify-content: space-between;
    font-size: 13.5px;
    color: var(--ink-soft);
    padding-bottom: 14px;
}

.side-total {
    display: flex;
    justify-content: space-between;
    padding-top: 14px;
    margin-bottom: 20px;
    border-top: 1px dashed rgba(0, 27, 62, 0.15);
    font-family: 'Space Grotesk', sans-serif;
    font-weight: 700;
    font-size: 18px;
    color: var(--navy);
}

.side-method {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 16px;
    padding-top: 16px;
    border-top: 1px solid var(--line);
    font-size: 10.5px;
    color: var(--ink-soft);
}

.side-method-name {
    font-family: 'Space Grotesk', sans-serif;
    font-weight: 600;
    font-size: 13px;
    color: var(--navy);
    letter-spacing: 0;
}

/* ===== BUTTONS ===== */
.btn {
    padding: 10px 18px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    border: 1px solid transparent;
    cursor: pointer;
    white-space: nowrap;
    transition: transform .15s ease, background .2s ease, border-color .2s ease, opacity .2s ease, box-shadow .2s ease;
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

.btn-lime {
    background: var(--lime);
    color: var(--navy);
}

.btn-lime:hover:not(:disabled) {
    background: #d3ec5c;
    box-shadow: 0 8px 20px -8px rgba(196, 221, 65, 0.6);
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
    padding: 14px 18px;
    font-size: 14.5px;
    border-radius: 10px;
    margin-bottom: 4px;
}

.btn.small {
    padding: 8px 14px;
    font-size: 12px;
}

/* ===== FOOTER ===== */
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

@media (max-width: 860px) {
    .checkout-grid {
        grid-template-columns: 1fr;
        gap: 32px;
    }

    .checkout-main {
        max-width: 100%;
    }

    .checkout-side {
        position: static;
    }
}

@media (max-width: 600px) {
    .checkout {
        padding: 32px 18px 64px;
    }

    .topbar {
        padding: 16px 18px;
    }
}
</style>