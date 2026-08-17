<template>
    <div class="court-bg" aria-hidden="true">
        <div class="court-glow"></div>
        <svg class="court-lines" viewBox="0 0 600 900" preserveAspectRatio="xMidYMin slice">
            <line x1="40" y1="0" x2="40" y2="900" />
            <line x1="560" y1="0" x2="560" y2="900" />
            <line x1="40" y1="180" x2="560" y2="180" stroke-dasharray="3 7" />
            <line x1="40" y1="720" x2="560" y2="720" stroke-dasharray="3 7" />
            <line x1="300" y1="0" x2="300" y2="180" />
            <line x1="300" y1="720" x2="300" y2="900" />
        </svg>
    </div>

    <div class="app-shell">
        <header class="site-header">
            <div class="brand">
                <img :src="dinkYard" alt="" class="brand-mark">
                <span class="brand-word">Court<span class="brand-word-accent">tesy</span></span>
            </div>
            <router-link to="/" style="text-decoration: none;" class="start-over-link">Book a court</router-link>
        </header>
        <div class="screen">
            <section class="step">
                <div class="step-head">
                    <span class="step-tag mono">Find your match</span>
                    <h2 class="step-title">Check your<br><span class="accent">reservation.</span></h2>
                </div>
                <div class="form-card">
                    <div class="form-row">
                        <div class="form-field">
                            <label class="form-label mono">Phone number</label>
                            <input v-model="lookupPhone" type="tel" class="form-input" placeholder="09XX XXX XXXX" autocomplete="tel" @keyup.enter="handleSearch">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-field">
                            <label class="form-label mono">Booking code <span class="optional">if Remembered</span></label>
                            <input v-model="lookupCode" type="text" class="form-input mono" placeholder="CT-XXXXXX" @keyup.enter="handleSearch">
                        </div>
                    </div>
                    <button class="confirm-btn full" :disabled="(!lookupPhone.trim() && !lookupCode) || searching" @click="handleSearch">
                        {{ searching ? 'Searching…' : 'Find reservation' }}
                    </button>
                </div>
            </section>
            <transition name="fade">
                <section class="step kitchen-divider" v-if="searched">
                    <div class="step-head" v-if="results.length">
                        <span class="step-tag mono">{{ results.length }} found</span>
                        <h2 class="step-title">Your reservations</h2>
                    </div>
                    <div v-if="results.length === 0" class="empty-card">
                        <p>No reservation found for that number. Double check the phone number or booking code and try again.</p>
                    </div>
                    <div v-else class="reservation-list">
                        <div v-for="r in results" :key="r.code" class="reservation-card">
                            <div class="reservation-main" @click="toggleSelect(r.booking_code)">
                                <div class="reservation-left">
                                    <span class="reservation-code mono">{{ r.booking_code }}</span>
                                    <span class="reservation-venue">{{ r.venue.name }} · {{ r.court.name }} <small style="font-size: 10px; color: #F2691C;"> {{ r.court.tag }}</small></span>
                                    <span class="reservation-when mono">{{ r.start_time }} · {{ r.end_time }}</span>
                                </div>
                                <div class="reservation-right">
                                    <span class="status-pill" :class="r.status">{{ r.status }}</span>
                                    <span class="reservation-chevron" :class="{ open: selectedCode === r.booking_code }">⌄</span>
                                </div>
                            </div>
                            <div class="reservation-detail" v-if="selectedCode === r.booking_code">
                                <table class="detail-table">
                                    <tbody>
                                        <tr>
                                            <th>Booked by</th>
                                            <th style="text-align: end;">{{ r.customer_name }}</th>
                                        </tr>
                                        <tr>
                                            <th>Players</th>
                                            <td>{{ extractPlayers(r.notes) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Total</th>
                                            <td>₱{{ r.court.price * parseInt(r.hours) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>
            </transition>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import dinkYard from '@/component/assets/logo.jpg';
import { useBookingStore } from '@/stores/UseBooking';

const lookupPhone = ref('');
const lookupCode = ref('');
const searching = ref(false);
const searched = ref(false);
const results = ref([]);
const selectedCode = ref(null);
const useBooking = useBookingStore();

function loadBookings() {
    try {
        return JSON.parse(localStorage.getItem('dinkyard_bookings') || '[]');
    } catch {
        return [];
    }
}

const handleSearch = async () => {
    searching.value = true;

    const response = await useBooking.getCheckBookingReservation({
        'booking_code': lookupCode.value,
        'customer_phone': lookupPhone.value,
    });
    searching.value = false;
    searched.value = true;
    results.value = response;
}

function toggleSelect(code) {
    selectedCode.value = selectedCode.value === code ? null : code;
}

const extractPlayers = (notes) => {
    const match = notes?.match(/total player of (\d+)/i);
    return match ? Number(match[1]) : null;
};

function cancelReservation(r) {
    const all = loadBookings();
    const target = all.find(b => b.code === r.code);
    if (target) target.status = 'cancelled';
    localStorage.setItem('dinkyard_bookings', JSON.stringify(all));
    r.status = 'cancelled';
}
</script>

<style scoped>
.empty-card {
    background: var(--chalk);
    border-radius: var(--radius-lg);
    padding: 26px 20px;
    text-align: center;
    box-shadow: 0 8px 20px -14px rgba(0, 0, 0, 0.6);
}

.empty-card p {
    margin: 0;
    color: var(--ink-soft);
    font-size: 13.5px;
    line-height: 1.6;
}

.reservation-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.reservation-card {
    background: var(--chalk);
    border-radius: var(--radius-md);
    overflow: hidden;
    box-shadow: 0 8px 20px -14px rgba(0, 0, 0, 0.6);
}

.reservation-main {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 15px 16px;
    cursor: pointer;
}

.reservation-left {
    display: flex;
    flex-direction: column;
    gap: 3px;
    min-width: 0;
}

.reservation-code {
    font-size: 10px;
    color: var(--mango-deep);
}

.reservation-venue {
    font-weight: 700;
    font-size: 14px;
    color: var(--ink);
}

.reservation-when {
    font-size: 10.5px;
    color: var(--ink-soft);
}

.reservation-right {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-shrink: 0;
}

.status-pill {
    font-family: var(--font-mono);
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    padding: 3px 9px;
    border-radius: 20px;
}

.status-pill.confirmed {
    color: var(--success);
    background: var(--success-dim);
    border: 1px solid var(--success);
}

.status-pill.completed {
    color: var(--ink-soft);
    background: var(--chalk-dim);
    border: 1px solid var(--line-onchalk);
}

.status-pill.cancelled {
    color: #b23b3b;
    background: rgba(178, 59, 59, 0.12);
    border: 1px solid #b23b3b;
}

.reservation-chevron {
    font-size: 12px;
    color: var(--ink-faint);
    transition: transform .15s ease;
}

.reservation-chevron.open {
    transform: rotate(180deg);
}

.reservation-detail {
    padding: 0 16px 16px;
    border-top: 1px dashed var(--line-onchalk);
}

.detail-table {
    width: 100%;
    border-collapse: collapse;
    margin: 12px 0 14px;
}

.detail-table th,
.detail-table td {
    padding: 8px 0;
    font-size: 13px;
    border-bottom: 1px dashed var(--line-onchalk);
}

.detail-table tr:last-child th,
.detail-table tr:last-child td {
    border-bottom: none;
}

.detail-table th {
    text-align: left;
    font-weight: 500;
    color: var(--ink-soft);
}

.detail-table td {
    text-align: right;
    font-weight: 700;
    font-family: var(--font-mono);
    color: var(--ink);
}

.action-btn.ghost-danger {
    width: 100%;
    background: transparent;
    border: 1px solid #b23b3b;
    color: #b23b3b;
    font-family: var(--font-body);
    font-weight: 500;
    font-size: 13px;
    padding: 12px;
    border-radius: var(--radius-sm);
    cursor: pointer;
    min-height: 46px;
    transition: background .2s ease;
}

.action-btn.ghost-danger:hover {
    background: rgba(178, 59, 59, 0.08);
}

.confirm-btn.full {
    width: 100%;
    margin-top: 2px;
}
</style>
