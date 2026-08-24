<template>
    <div class="check-reservation-page">

        <div class="background-grid" aria-hidden="true"></div>

        <header class="site-header">
            <nav class="nav">

                <router-link to="/" class="brand">
                    <img :src="dinkYard" alt="DinkYard" class="brand-mark" />
                    <span>Court-<span style="color: white;">tesy</span></span>
                </router-link>

                <div class="nav-actions">
                    <router-link to="/" class="nav-btn nav-btn-ghost">
                        Book a Court
                    </router-link>
                </div>

            </nav>
        </header>

        <main class="main-content">

            <section class="search-section">

                <div class="search-copy">

                    <div class="eyebrow mono">
                        RESERVATION LOOKUP
                    </div>

                    <h1>
                        Check your
                        <span>reservation.</span>
                    </h1>

                    <p>
                        Enter your phone number or booking code to view
                        your reservation details, schedule, and booking status.
                    </p>

                </div>

                <div class="search-card">

                    <div class="card-header">
                        <div>
                            <span class="card-kicker mono">
                                FIND YOUR BOOKING
                            </span>

                            <h2>
                                Reservation details
                            </h2>
                        </div>

                        <div class="card-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <rect x="3" y="4" width="18" height="17" rx="2" />
                                <line x1="8" y1="2" x2="8" y2="6" />
                                <line x1="16" y1="2" x2="16" y2="6" />
                                <line x1="3" y1="10" x2="21" y2="10" />
                                <path d="M8 14h2" />
                                <path d="M14 14h2" />
                                <path d="M8 18h2" />
                                <path d="M14 18h2" />
                            </svg>
                        </div>
                    </div>

                    <div class="form-fields">

                        <div class="form-field">

                            <label class="form-label mono">
                                PHONE NUMBER
                            </label>

                            <div class="input-wrap">

                                <svg class="input-icon" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2
                                        19.79 19.79 0 0 1-8.63-3.07
                                        19.5 19.5 0 0 1-6-6
                                        19.79 19.79 0 0 1-3.07-8.67
                                        A2 2 0 0 1 4.11 2h3
                                        a2 2 0 0 1 2 1.72
                                        c.12.9.33 1.78.62 2.61
                                        a2 2 0 0 1-.45 2.11L8 9.73
                                        a16 16 0 0 0 6 6l1.29-1.29
                                        a2 2 0 0 1 2.11-.45
                                        c.83.29 1.71.5 2.61.62
                                        A2 2 0 0 1 22 16.92z" />
                                </svg>

                                <input v-model="lookupPhone" type="tel" class="form-input" placeholder="09XX XXX XXXX" autocomplete="tel" @keyup.enter="handleSearch" />

                            </div>

                        </div>

                        <div class="form-field">

                            <label class="form-label mono">
                                BOOKING CODE
                                <span>OPTIONAL</span>
                            </label>

                            <div class="input-wrap">

                                <svg class="input-icon" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M20.59 13.41
                                        13.41 20.59
                                        a2 2 0 0 1-2.82 0
                                        L3.41 13.41
                                        a2 2 0 0 1 0-2.82
                                        L10.59 3.41
                                        a2 2 0 0 1 2.82 0
                                        L20.59 10.59
                                        a2 2 0 0 1 0 2.82z" />

                                    <circle cx="9" cy="9" r="1.5" />
                                </svg>

                                <input v-model="lookupCode" type="text" class="form-input mono" placeholder="DY-XXXXXX" @keyup.enter="handleSearch" />

                            </div>

                        </div>

                    </div>

                    <button class="search-btn" :disabled="(!lookupPhone.trim() && !lookupCode.trim())
                        || searching
                        " @click="handleSearch">

                        <span v-if="!searching">
                            Find reservation
                        </span>

                        <span v-else class="loading-content">
                            <span class="spinner"></span>
                            Searching...
                        </span>

                        <svg v-if="!searching" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="7" />
                            <path d="m20 20-4-4" />
                        </svg>

                    </button>


                    <div class="search-note">
                        <span class="note-dot"></span>
                        You can search using either your phone number
                        or booking code.
                    </div>

                </div>

            </section>

            <transition name="results-fade">

                <section v-if="searched" class="results-section">

                    <div v-if="results.length" class="results-header">

                        <div>
                            <span class="section-kicker mono">
                                SEARCH RESULTS
                            </span>

                            <h2>Your reservations</h2>

                            <p>
                                {{ results.length }}
                                reservation{{ results.length === 1 ? '' : 's' }}
                                found for your search.
                            </p>
                        </div>
                        <div class="result-count">
                            <span class="count-number">
                                {{ results.length }}
                            </span>
                            <span class="count-label mono">
                                FOUND
                            </span>
                        </div>
                    </div>

                    <div v-if="results.length === 0" class="empty-card">

                        <div class="empty-icon">

                            <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                                <circle cx="11" cy="11" r="7" />

                                <path d="m20 20-4-4" />

                                <path d="M8 11h6" />

                            </svg>

                        </div>

                        <span class="empty-kicker mono">
                            NO MATCH FOUND
                        </span>

                        <h3>
                            We couldn't find that reservation.
                        </h3>

                        <p>
                            Double-check your phone number or booking code
                            and try searching again.
                        </p>

                        <button class="try-again-btn" @click="searched = false">
                            Try again
                        </button>

                    </div>

                    <div v-else class="reservation-list">

                        <article v-for="r in results" :key="r.booking_code" class="reservation-card" :class="{ expanded: selectedCode === r.booking_code }">
                            <button class="reservation-main" @click="toggleSelect(r.booking_code)">
                                <div class="reservation-info">
                                    <div class="reservation-top">
                                        <span class="reservation-code mono">
                                            {{ r.booking_code }}
                                        </span>
                                        <span class="status-pill" :class="r.status">
                                            <span class="status-dot"></span>
                                            {{ r.status }}
                                        </span>
                                    </div>
                                    <h3 class="reservation-title">
                                        <span>
                                            {{ r.venue?.name }}
                                        </span>
                                        <span class="separator">
                                            /
                                        </span>
                                        <span class="court-name">
                                            {{ r.court?.name }}
                                        </span>

                                        <span v-if="r.court?.tag" class="court-tag mono">
                                            {{ viewTags(r.court.tag) }}
                                        </span>
                                    </h3>
                                    <div class="reservation-time mono">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <circle cx="12" cy="12" r="9" />

                                            <path d="M12 7v5l3 2" />

                                        </svg>

                                        <span>
                                            {{ r.start_time }}
                                        </span>

                                        <span class="time-arrow">
                                            →
                                        </span>

                                        <span>
                                            {{ r.end_time }}
                                        </span>

                                    </div>

                                </div>
                                <div class="reservation-action">

                                    <span class="view-label mono">
                                        {{
                                            selectedCode === r.booking_code
                                                ? 'CLOSE'
                                                : 'VIEW'
                                        }}
                                    </span>

                                    <span class="chevron" :class="{
                                        open:
                                            selectedCode ===
                                            r.booking_code
                                    }">
                                        ↓
                                    </span>
                                </div>
                            </button>
                            <transition name="detail-expand">

                                <div v-if="
                                    selectedCode ===
                                    r.booking_code
                                " class="reservation-detail">
                                    <div class="detail-grid">
                                        <div class="detail-item">
                                            <span class="detail-label mono">
                                                BOOKED BY
                                            </span>
                                            <span class="detail-value">
                                                {{ r.customer_name || '—' }}
                                            </span>
                                        </div>
                                        <div class="detail-item">
                                            <span class="detail-label mono">
                                                PLAYERS
                                            </span>
                                            <span class="detail-value">
                                                {{
                                                    extractPlayers(r.notes)
                                                    || "-"
                                                }}
                                            </span>
                                        </div>
                                        <div class="detail-item">
                                            <span class="detail-label mono">
                                                DURATION
                                            </span>
                                            <span class="detail-value">
                                                {{ r.hours || '—' }}
                                                {{
                                                    Number(r.hours) === 1
                                                        ? 'hour'
                                                        : 'hours'
                                                }}
                                            </span>
                                        </div>

                                        <div class="detail-item">
                                            <span class="detail-label mono">
                                                Reservation Date:
                                            </span>
                                            <span class="detail-price">
                                                {{ dateFormat(r.booking_date) }}
                                            </span>

                                        </div>

                                    </div>
                                    <div class="location-row">
                                        <div class="location-icon">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                                <path d="M20 10
                                                    c0 5-8 11-8 11
                                                    S4 15 4 10
                                                    a8 8 0 1 1 16 0z" />

                                                <circle cx="12" cy="10" r="2.5" />
                                            </svg>
                                        </div>
                                        <div>
                                            <span class="location-label mono">
                                                VENUE
                                            </span>
                                            <span class="location-value">
                                                {{ r.venue?.name || '—' }}
                                                <span v-if="r.venue?.area">
                                                    · {{ r.venue.area }}
                                                </span>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="reference-row">

                                        <span class="mono">
                                            BOOKING REFERENCE
                                        </span>

                                        <strong class="mono">
                                            {{ r.booking_code }}
                                        </strong>

                                    </div>

                                </div>

                            </transition>

                        </article>

                    </div>

                </section>

            </transition>

        </main>

        <footer class="site-footer">

            <div class="footer-inner">

                <div class="footer-brand">

                    <div class="footer-logo">
                        <img :src="dinkYard" alt="DinkYard" class="footer-mark" />

                        <span>
                            Dink<span>Yard</span>
                        </span>
                    </div>

                    <p>
                        A simpler way to discover, book, and manage
                        your favorite courts.
                    </p>
                </div>
                <div class="footer-meta">
                    <span>
                        © 2026 Courttesy. All rights reserved.
                    </span>
                    <span class="footer-status">
                        <span></span>
                        BOOKING SYSTEM ONLINE
                    </span>
                </div>
            </div>
        </footer>
    </div>
</template>


<script setup>
import { ref } from 'vue';
import dinkYard from '@/component/assets/logo.jpg';
import { useBookingStore } from '@/stores/UseBooking';
import { dateFormat } from '@/utils/dateformat';

const lookupPhone = ref('');
const lookupCode = ref('');

const searching = ref(false);
const searched = ref(false);

const results = ref([]);
const selectedCode = ref(null);

const useBooking = useBookingStore();

const handleSearch = async () => {

    if (
        !lookupPhone.value.trim() &&
        !lookupCode.value.trim()
    ) {
        return;
    }

    searching.value = true;

    try {

        const response =
            await useBooking.getCheckBookingReservation({
                booking_code: lookupCode.value,
                customer_phone: lookupPhone.value
            });

        results.value = response || [];

    } catch (error) {

        console.error(
            'Failed to check reservation:',
            error
        );

        results.value = [];

    } finally {

        searching.value = false;
        searched.value = true;
        selectedCode.value = null;

    }
};


const viewTags = (tag) => {
    return tag?.join(', ') ?? ''
}

function toggleSelect(code) {

    selectedCode.value =
        selectedCode.value === code
            ? null
            : code;

}

const extractPlayers = (notes) => {
    const text = notes?.replace(/<[^>]*>/g, ' ');

    const match = text?.match(/Players:\s*(\d+)/i);

    return match
        ? Number(match[1])
        : null;
};

const totalPrice = (reservation) => {

    const price =
        Number(reservation?.court?.price || 0);

    const hours =
        Number(reservation?.hours || 0);

    return (
        price * hours
    ).toLocaleString('en-PH');

};
</script>


<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500;600;700&display=swap'
);


/* =========================================================
   DESIGN TOKENS
========================================================= */

.check-reservation-page {

    --navy: #001B3E;
    --navy-2: #04264F;
    --navy-3: #0B3568;

    --lime: #C3DD41;
    --lime-2: #9FB92F;

    --cream: #F4F7EA;
    --paper: #FBFCF7;

    --ink: #04101F;
    --ink-soft: #4A5A6B;

    --line: rgba(0, 27, 62, 0.10);
    --lime-line: rgba(196, 221, 65, 0.20);

    min-height: 100vh;

    font-family: 'Inter', sans-serif;

    color: var(--ink);

    background:
        linear-gradient(180deg,
            #F4F7EA 0%,
            #FBFCF7 42%,
            #FBFCF7 100%);

    -webkit-font-smoothing: antialiased;

    position: relative;
    overflow-x: hidden;
}


* {
    box-sizing: border-box;
}


.mono {
    font-family: 'JetBrains Mono', monospace;
    letter-spacing: 0.08em;
}


button,
input {
    font: inherit;
}


/* =========================================================
   BACKGROUND
========================================================= */

.background-grid {

    position: fixed;

    inset: 0;

    pointer-events: none;

    opacity: .38;

    background-image:
        repeating-linear-gradient(0deg,
            rgba(0, 27, 62, .025) 0 1px,
            transparent 1px 64px),
        repeating-linear-gradient(90deg,
            rgba(0, 27, 62, .025) 0 1px,
            transparent 1px 64px);

    mask-image:
        linear-gradient(to bottom,
            black,
            transparent 85%);

}


/* =========================================================
   HEADER
========================================================= */

.site-header {

    position: sticky;

    top: 0;

    z-index: 50;

    background:
        rgba(0, 27, 62, .94);

    backdrop-filter: blur(12px);

    border-bottom:
        1px solid rgba(196, 221, 65, .14);

}


.nav {

    width: 100%;

    max-width: 1240px;

    margin: 0 auto;

    padding: 17px 32px;

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 20px;

}


.brand {

    display: flex;

    align-items: center;

    gap: 10px;

    text-decoration: none;

    color: var(--paper);

}


.brand-mark {

    width: 34px;

    height: 34px;

    border-radius: 9px;

    object-fit: cover;

    border:
        2px solid rgba(196, 221, 65, .35);

}


.brand-name {

    font-family: 'Space Grotesk', sans-serif;

    font-size: 18px;

    font-weight: 700;

    letter-spacing: -.02em;

}


.brand-name span {

    color: var(--lime);

}


.nav-actions {

    display: flex;

    align-items: center;

    gap: 10px;

}


.nav-btn {

    padding: 9px 17px;

    border-radius: 8px;

    font-size: 12px;

    font-weight: 600;

    text-decoration: none;

    transition:
        .2s ease;

}


.nav-btn-ghost {

    color: var(--paper);

    border:
        1px solid rgba(244, 247, 234, .24);

    background: transparent;

}


.nav-btn-ghost:hover {

    color: var(--lime);

    border-color: var(--lime);

}


/* =========================================================
   MAIN
========================================================= */

.main-content {

    position: relative;

    z-index: 1;

    max-width: 1000px;

    margin: 0 auto;

    padding: 76px 32px 100px;

}


/* =========================================================
   SEARCH SECTION
========================================================= */

.search-section {

    display: grid;

    grid-template-columns:
        minmax(0, .9fr) minmax(400px, 1.1fr);

    gap: 60px;

    align-items: center;

}


.search-copy {

    padding-bottom: 12px;

}


.eyebrow {

    display: inline-flex;

    align-items: center;

    gap: 8px;

    margin-bottom: 20px;

    padding: 7px 12px;

    border-radius: 999px;

    background:
        rgba(196, 221, 65, .10);

    border:
        1px solid rgba(196, 221, 65, .34);

    color: var(--lime-2);

    font-size: 9px;

    font-weight: 700;

}


.eyebrow::before {

    content: "";

    width: 6px;

    height: 6px;

    border-radius: 50%;

    background: var(--lime);

}


.search-copy h1 {

    margin: 0;

    font-family: 'Space Grotesk', sans-serif;

    font-size: clamp(42px,
            5vw,
            64px);

    line-height: .98;

    letter-spacing: -.04em;

    text-transform: uppercase;

    color: var(--navy);

}


.search-copy h1 span {

    color: var(--lime-2);

}


.search-copy p {

    max-width: 430px;

    margin: 22px 0 0;

    color: var(--ink-soft);

    font-size: 14px;

    line-height: 1.7;

}


/* =========================================================
   SEARCH CARD
========================================================= */

.search-card {

    padding: 26px;

    border-radius: 18px;

    background:
        linear-gradient(155deg,
            #052A54,
            #001B3E 75%);

    border:
        1px solid rgba(196, 221, 65, .20);

    box-shadow:
        0 28px 60px -30px rgba(0, 27, 62, .60);

}


.card-header {

    display: flex;

    align-items: flex-start;

    justify-content: space-between;

    gap: 20px;

    margin-bottom: 24px;

}


.card-kicker {

    display: block;

    margin-bottom: 6px;

    color:
        rgba(196, 221, 65, .72);

    font-size: 9px;

    font-weight: 700;

}


.card-header h2 {

    margin: 0;

    color: var(--paper);

    font-family: 'Space Grotesk', sans-serif;

    font-size: 22px;

    letter-spacing: -.02em;

}


.card-icon {

    width: 40px;

    height: 40px;

    flex-shrink: 0;

    display: grid;

    place-items: center;

    border-radius: 10px;

    color: var(--lime);

    background:
        rgba(196, 221, 65, .10);

    border:
        1px solid rgba(196, 221, 65, .22);

}


/* =========================================================
   FORM
========================================================= */

.form-fields {

    display: flex;

    flex-direction: column;

    gap: 17px;

}


.form-field {

    display: flex;

    flex-direction: column;

    gap: 8px;

}


.form-label {

    color:
        rgba(244, 247, 234, .52);

    font-size: 9px;

    font-weight: 700;

}


.form-label span {

    color:
        rgba(244, 247, 234, .30);

    margin-left: 5px;

}


.input-wrap {

    position: relative;

}


.input-icon {

    position: absolute;

    left: 14px;

    top: 50%;

    transform: translateY(-50%);

    color:
        rgba(244, 247, 234, .35);

    pointer-events: none;

}


.form-input {

    width: 100%;

    height: 48px;

    padding:
        0 15px 0 42px;

    border-radius: 9px;

    outline: none;

    color: var(--paper);

    background:
        rgba(255, 255, 255, .045);

    border:
        1px solid rgba(244, 247, 234, .13);

    transition:
        border-color .2s ease,
        background .2s ease,
        box-shadow .2s ease;

}


.form-input::placeholder {

    color:
        rgba(244, 247, 234, .28);

}


.form-input:focus {

    border-color:
        rgba(196, 221, 65, .60);

    background:
        rgba(255, 255, 255, .065);

    box-shadow:
        0 0 0 3px rgba(196, 221, 65, .08);

}


.form-input.mono {

    font-size: 12px;

}


/* =========================================================
   SEARCH BUTTON
========================================================= */

.search-btn {

    width: 100%;

    min-height: 48px;

    margin-top: 20px;

    display: flex;

    align-items: center;

    justify-content: center;

    gap: 9px;

    border: none;

    border-radius: 9px;

    cursor: pointer;

    color: var(--navy);

    background: var(--lime);

    font-size: 13px;

    font-weight: 700;

    transition:
        transform .15s ease,
        background .2s ease,
        box-shadow .2s ease;

}


.search-btn:hover:not(:disabled) {

    transform: translateY(-1px);

    background: #d3ec5c;

    box-shadow:
        0 10px 25px -12px rgba(196, 221, 65, .65);

}


.search-btn:disabled {

    opacity: .48;

    cursor: not-allowed;

}


.loading-content {

    display: inline-flex;

    align-items: center;

    gap: 9px;

}


.spinner {

    width: 14px;

    height: 14px;

    border-radius: 50%;

    border:
        2px solid rgba(0, 27, 62, .25);

    border-top-color:
        var(--navy);

    animation:
        spin .7s linear infinite;

}


@keyframes spin {

    to {
        transform: rotate(360deg);
    }

}


.search-note {

    display: flex;

    align-items: center;

    justify-content: center;

    gap: 7px;

    margin-top: 15px;

    color:
        rgba(244, 247, 234, .36);

    font-size: 10px;

    line-height: 1.5;

    text-align: center;

}


.note-dot {

    width: 5px;

    height: 5px;

    flex-shrink: 0;

    border-radius: 50%;

    background: var(--lime);

}


/* =========================================================
   RESULTS
========================================================= */

.results-section {

    margin-top: 88px;

}


.results-header {

    display: flex;

    align-items: flex-end;

    justify-content: space-between;

    gap: 30px;

    margin-bottom: 25px;

}


.section-kicker {

    display: block;

    margin-bottom: 9px;

    color: var(--lime-2);

    font-size: 9px;

    font-weight: 700;

}


.results-header h2 {

    margin: 0;

    color: var(--navy);

    font-family: 'Space Grotesk', sans-serif;

    font-size: 38px;

    line-height: 1;

    letter-spacing: -.03em;

    text-transform: uppercase;

}


.results-header p {

    margin: 9px 0 0;

    color: var(--ink-soft);

    font-size: 13px;

}


.result-count {

    min-width: 82px;

    padding: 12px 15px;

    border-radius: 11px;

    text-align: right;

    background: var(--navy);

    border:
        1px solid rgba(196, 221, 65, .18);

}


.count-number {

    display: block;

    color: var(--lime);

    font-family: 'Space Grotesk', sans-serif;

    font-size: 26px;

    font-weight: 700;

    line-height: 1;

}


.count-label {

    display: block;

    margin-top: 5px;

    color:
        rgba(244, 247, 234, .42);

    font-size: 7px;

}


/* =========================================================
   EMPTY
========================================================= */

.empty-card {

    padding: 48px 30px;

    border-radius: 16px;

    text-align: center;

    background: var(--paper);

    border:
        1px solid rgba(0, 27, 62, .08);

    box-shadow:
        0 18px 45px -30px rgba(0, 27, 62, .35);

}


.empty-icon {

    width: 58px;

    height: 58px;

    margin: 0 auto 18px;

    display: grid;

    place-items: center;

    border-radius: 14px;

    color: var(--navy);

    background:
        rgba(0, 27, 62, .05);

    border:
        1px solid rgba(0, 27, 62, .08);

}


.empty-kicker {

    color: var(--lime-2);

    font-size: 9px;

    font-weight: 700;

}


.empty-card h3 {

    margin: 10px 0 7px;

    color: var(--navy);

    font-family: 'Space Grotesk', sans-serif;

    font-size: 22px;

}


.empty-card p {

    max-width: 450px;

    margin: 0 auto;

    color: var(--ink-soft);

    font-size: 13px;

    line-height: 1.6;

}


.try-again-btn {

    margin-top: 22px;

    padding: 10px 18px;

    border-radius: 8px;

    border:
        1px solid rgba(0, 27, 62, .18);

    color: var(--navy);

    background: transparent;

    cursor: pointer;

    font-size: 12px;

    font-weight: 600;

    transition: .2s ease;

}


.try-again-btn:hover {

    color: var(--paper);

    background: var(--navy);

    border-color: var(--navy);

}


/* =========================================================
   RESERVATION LIST
========================================================= */

.reservation-list {

    display: flex;

    flex-direction: column;

    gap: 12px;

}


.reservation-card {

    overflow: hidden;

    border-radius: 14px;

    background: var(--paper);

    border:
        1px solid rgba(0, 27, 62, .09);

    box-shadow:
        0 12px 30px -24px rgba(0, 27, 62, .45);

    transition:
        border-color .2s ease,
        box-shadow .2s ease;

}


.reservation-card:hover {

    border-color:
        rgba(196, 221, 65, .45);

}


.reservation-card.expanded {

    border-color:
        rgba(196, 221, 65, .55);

    box-shadow:
        0 18px 38px -25px rgba(0, 27, 62, .45);

}


/* =========================================================
   RESERVATION MAIN
========================================================= */

.reservation-main {

    width: 100%;

    padding: 19px 20px;

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 20px;

    border: none;

    background: transparent;

    text-align: left;

    cursor: pointer;

}


.reservation-info {

    min-width: 0;

    flex: 1;

}


.reservation-top {

    display: flex;

    align-items: center;

    flex-wrap: wrap;

    gap: 9px;

}


.reservation-code {

    color: var(--lime-2);

    font-size: 9px;

    font-weight: 700;

}


.status-pill {

    display: inline-flex;

    align-items: center;

    gap: 5px;

    padding: 4px 8px;

    border-radius: 999px;

    font-family: 'JetBrains Mono', monospace;

    font-size: 8px;

    font-weight: 700;

    text-transform: uppercase;

    letter-spacing: .04em;

}


.status-dot {

    width: 5px;

    height: 5px;

    border-radius: 50%;

}


/* CONFIRMED */

.status-pill.confirmed {

    color: #2c8e52;

    background:
        rgba(44, 142, 82, .10);

    border:
        1px solid rgba(44, 142, 82, .22);

}


.status-pill.confirmed .status-dot {

    background: #2c8e52;

}


/* COMPLETED */

.status-pill.completed {

    color: #777;

    background:
        rgba(100, 100, 100, .08);

    border:
        1px solid rgba(100, 100, 100, .15);

}


.status-pill.completed .status-dot {

    background: #777;

}


/* CANCELLED */

.status-pill.cancelled {

    color: #b23b3b;

    background:
        rgba(178, 59, 59, .09);

    border:
        1px solid rgba(178, 59, 59, .18);

}


.status-pill.cancelled .status-dot {

    background: #b23b3b;

}


/* =========================================================
   RESERVATION TITLE
========================================================= */

.reservation-title {

    margin: 8px 0 0;

    display: flex;

    align-items: center;

    flex-wrap: wrap;

    gap: 6px;

    color: var(--navy);

    font-family: 'Space Grotesk', sans-serif;

    font-size: 16px;

    font-weight: 700;

    letter-spacing: -.015em;

}


.separator {

    color: #B6BDC4;

    font-weight: 400;

}


.court-name {

    color: var(--navy-3);

}


.court-tag {

    padding: 3px 6px;

    border-radius: 5px;

    color: var(--ink-soft);

    background:
        rgba(0, 27, 62, .055);

    font-size: 7px;

    font-weight: 700;

}


/* =========================================================
   TIME
========================================================= */

.reservation-time {

    display: flex;

    align-items: center;

    gap: 7px;

    margin-top: 9px;

    color: var(--ink-soft);

    font-size: 9px;

}


.reservation-time svg {

    color: var(--lime-2);

}


.time-arrow {

    color: var(--lime-2);

}


/* =========================================================
   ACTION
========================================================= */

.reservation-action {

    display: flex;

    align-items: center;

    gap: 9px;

    flex-shrink: 0;

}


.view-label {

    color: #9CA5AD;

    font-size: 8px;

    font-weight: 600;

}


.chevron {

    width: 30px;

    height: 30px;

    display: grid;

    place-items: center;

    border-radius: 8px;

    color: #7F8992;

    border:
        1px solid rgba(0, 27, 62, .10);

    font-size: 13px;

    transition:
        transform .2s ease,
        color .2s ease,
        background .2s ease,
        border-color .2s ease;

}


.chevron.open {

    transform: rotate(180deg);

    color: var(--navy);

    background:
        rgba(196, 221, 65, .18);

    border-color:
        rgba(196, 221, 65, .45);

}


/* =========================================================
   DETAILS
========================================================= */

.reservation-detail {

    padding:
        0 20px 20px;

}


.detail-grid {

    display: grid;

    grid-template-columns:
        repeat(4, 1fr);

    gap: 1px;

    overflow: hidden;

    border-radius: 10px;

    background:
        rgba(0, 27, 62, .08);

    border:
        1px solid rgba(0, 27, 62, .07);

}


.detail-item {

    min-width: 0;

    padding: 15px;

    display: flex;

    flex-direction: column;

    gap: 6px;

    background:
        #F7F8F3;

}


.detail-label {

    color: #9CA5AD;

    font-size: 7px;

    font-weight: 700;

}


.detail-value {

    overflow: hidden;

    text-overflow: ellipsis;

    white-space: nowrap;

    color: var(--navy);

    font-size: 12px;

    font-weight: 700;

}


.total-item {

    text-align: right;

    align-items: flex-end;

}


.detail-price {

    color: var(--navy);

    font-family: 'Space Grotesk', sans-serif;

    font-size: 20px;

    font-weight: 700;

}


.location-row {

    margin-top: 12px;

    padding: 13px 15px;

    display: flex;

    align-items: center;

    gap: 10px;

    border-radius: 9px;

    background:
        rgba(0, 27, 62, .035);

    border:
        1px solid rgba(0, 27, 62, .06);

}


.location-icon {

    width: 30px;

    height: 30px;

    flex-shrink: 0;

    display: grid;

    place-items: center;

    color: var(--lime-2);

    border-radius: 8px;

    background:
        rgba(196, 221, 65, .12);

}


.location-label {

    display: block;

    margin-bottom: 3px;

    color: #A0A8AF;

    font-size: 7px;

    font-weight: 700;

}


.location-value {

    display: block;

    color: var(--navy);

    font-size: 11px;

    font-weight: 600;

}


/* =========================================================
   REFERENCE
========================================================= */

.reference-row {

    margin-top: 11px;

    padding-top: 12px;

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 15px;

    border-top:
        1px dashed rgba(0, 27, 62, .12);

    color: #A0A8AF;

    font-size: 7px;

}


.reference-row strong {

    color: var(--lime-2);

    font-size: 9px;

}


/* =========================================================
   TRANSITIONS
========================================================= */

.results-fade-enter-active,
.results-fade-leave-active {

    transition:
        opacity .3s ease,
        transform .3s ease;

}


.results-fade-enter-from,
.results-fade-leave-to {

    opacity: 0;

    transform: translateY(12px);

}


.detail-expand-enter-active,
.detail-expand-leave-active {

    transition:
        opacity .2s ease,
        max-height .25s ease;

}


.detail-expand-enter-from,
.detail-expand-leave-to {

    opacity: 0;

    max-height: 0;

}


/* =========================================================
   FOOTER
========================================================= */

.site-footer {

    position: relative;

    z-index: 1;

    background: #000F26;

    color:
        rgba(244, 247, 234, .45);

}


.footer-inner {

    max-width: 1000px;

    margin: 0 auto;

    padding: 42px 32px 24px;

}


.footer-brand {

    padding-bottom: 30px;

    border-bottom:
        1px solid rgba(255, 255, 255, .07);

}


.footer-logo {

    display: flex;

    align-items: center;

    gap: 9px;

    color: var(--paper);

    font-family: 'Space Grotesk', sans-serif;

    font-size: 17px;

    font-weight: 700;

}


.footer-logo span {

    color: var(--lime);

}


.footer-mark {

    width: 27px;

    height: 27px;

    border-radius: 7px;

    object-fit: cover;

}


.footer-brand p {

    max-width: 350px;

    margin: 12px 0 0;

    font-size: 11px;

    line-height: 1.6;

}


.footer-meta {

    padding-top: 20px;

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 20px;

    font-size: 9px;

}


.footer-status {

    display: flex;

    align-items: center;

    gap: 6px;

    font-family: 'JetBrains Mono', monospace;

    letter-spacing: .06em;

}


.footer-status span {

    width: 5px;

    height: 5px;

    border-radius: 50%;

    background: var(--lime);

    box-shadow:
        0 0 8px rgba(196, 221, 65, .7);

}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 850px) {

    .search-section {

        grid-template-columns: 1fr;

        gap: 35px;

    }


    .search-copy {

        text-align: center;

    }


    .search-copy p {

        margin-left: auto;

        margin-right: auto;

    }


    .search-card {

        max-width: 650px;

        width: 100%;

        margin: 0 auto;

    }

}


@media (max-width: 650px) {

    .nav {

        padding:
            13px 16px;

    }


    .brand-name {

        font-size: 16px;

    }


    .brand-mark {

        width: 29px;

        height: 29px;

    }


    .nav-btn {

        padding: 8px 10px;

        font-size: 10px;

    }


    .main-content {

        padding:
            52px 16px 70px;

    }


    .search-copy h1 {

        font-size: 42px;

    }


    .search-card {

        padding: 20px;

    }


    .results-section {

        margin-top: 60px;

    }


    .results-header {

        align-items: flex-start;

    }


    .results-header h2 {

        font-size: 30px;

    }


    .result-count {

        min-width: 68px;

        padding: 10px;

    }


    .count-number {

        font-size: 22px;

    }


    .reservation-main {

        padding: 16px;

    }


    .reservation-title {

        font-size: 14px;

    }


    .view-label {

        display: none;

    }


    .detail-grid {

        grid-template-columns:
            repeat(2, 1fr);

    }


    .total-item {

        align-items: flex-start;

        text-align: left;

    }


    .footer-inner {

        padding-left: 16px;

        padding-right: 16px;

    }


    .footer-meta {

        flex-direction: column;

        align-items: flex-start;

    }

}


@media (max-width: 430px) {

    .search-copy h1 {

        font-size: 36px;

    }


    .card-header h2 {

        font-size: 19px;

    }


    .reservation-action {

        gap: 0;

    }


    .reservation-time {

        font-size: 8px;

    }


    .detail-grid {

        grid-template-columns: 1fr;

    }


    .detail-item {

        padding: 12px;

    }


    .total-item {

        align-items: flex-start;

    }

}
</style>