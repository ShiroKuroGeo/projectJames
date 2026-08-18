<template>
    <div class="page">
        <nav class="nav">
            <div class="nav-inner">
                <router-link to="/" class="brand">
                    <div class="brand-mark">
                        <img :src="Logo" alt="" style="width: 28px; height: 28px;">
                    </div>
                    <span class="brand-word">
                        <span>Court-<span style="color: white;">tesy</span></span>
                    </span>
                </router-link>
                <div class="nav-right">
                    <button v-if="courtSelected?.id" class="nav-book-btn" @click="openReservation()">
                        Book a Court
                        <span>→</span>
                    </button>
                </div>
            </div>
        </nav>
        <header class="venue-hero">
            <div class="hero-grid"></div>
            <div class="wrap hero-content">
                <div class="breadcrumb mono">
                    VENUES
                    <span>/</span>
                    <span>
                        {{ venue?.name || 'LOADING...' }}
                    </span>
                </div>
                <div v-if="venue" class="venue-header-grid">
                    <div class="venue-logo-wrap">
                        <img :src="image(venue?.admins[0]?.image)" :alt="`${venue?.name || 'Venue'} logo`"
                            class="venue-logo" loading="eager" />
                        <div class="logo-status">
                            <span></span>
                            OPEN FOR BOOKINGS
                        </div>
                    </div>
                    <div class="venue-main-info">
                        <div class="venue-eyebrow mono">
                            COURT-TESY FACILITY
                        </div>
                        <h1>{{ venue.name || 'LOADING...' }}</h1>
                        <p v-if="venue.area" class="venue-address">
                            <span class="location-dot">◎</span>
                            {{ venue.area }}
                        </p>
                        <div class="venue-tags">
                            <span v-if="courts.length" class="vbadge primary">
                                {{ courts.length }}
                                {{ courts.length === 1 ? 'COURT' : 'COURTS' }}
                                ON-SITE
                            </span>
                            <span class="vbadge">
                                INDOOR & OUTDOOR
                            </span>
                            <span class="vbadge">
                                LIVE SCHEDULE
                            </span>
                        </div>
                    </div>
                </div>
                <div v-else class="venue-loading">
                    Loading venue...
                </div>
            </div>
        </header>
        <section class="sec courts-section" id="courts">
            <div class="wrap">
                <div class="section-heading-row">
                    <div class="sec-head">
                        <span class="eyebrow mono">
                            AVAILABLE COURTS
                        </span>
                        <h2>
                            Choose where<br>
                            you want to <em>play.</em>
                        </h2>
                        <p>
                            Select a court below to view its environment,
                            pricing, and available reservation schedule.
                        </p>
                    </div>
                    <div v-if="courts.length" class="court-count-box">
                        <span class="mono">FACILITY COURTS</span>
                        <strong> {{ courts.length }} </strong>
                    </div>
                </div>
                <div v-if="loadingCourts" class="empty-state">
                    <div class="loading-spinner"></div>
                    <p>Loading available courts...</p>
                </div>
                <div v-else-if="!courts.length" class="empty-state">
                    <div class="empty-icon">◎</div>
                    <h3>No courts available</h3>
                    <p>This venue currently has no courts availablefor booking.</p>
                </div>
                <div v-else class="court-grid">
                    <button v-for="(court, index) in courts" :key="court.id" type="button" class="court-card" :class="{
                        'is-selected':
                            court.id === selectedCourtId
                    }" @click="selectedCourt(court)">
                        <div class="court-thumb">
                            <svg viewBox="0 0 400 250" xmlns="http://www.w3.org/2000/svg">
                                <rect width="400" height="250" :fill="court.base || '#052A54'" />
                                <defs>
                                    <pattern :id="`courtPattern-${court.id}`" width="24" height="24"
                                        patternUnits="userSpaceOnUse">
                                        <path d="M 24 0 L 0 0 0 24" fill="none" stroke="#C3DD41" stroke-width="0.7"
                                            opacity="0.08" />
                                    </pattern>
                                </defs>
                                <rect width="400" height="250" :fill="`url(#courtPattern-${court.id})`" />
                                <rect x="18" y="18" width="364" height="214" rx="3" fill="none" stroke="#FBFCF7"
                                    stroke-width="2" opacity="0.9" />
                                <line x1="200" y1="18" x2="200" y2="232" stroke="#FBFCF7" stroke-width="2"
                                    stroke-dasharray="8 7" opacity="0.5" />
                                <line x1="105" y1="18" x2="105" y2="232" stroke="#FBFCF7" stroke-width="1.5"
                                    opacity="0.55" />
                                <line x1="295" y1="18" x2="295" y2="232" stroke="#FBFCF7" stroke-width="1.5"
                                    opacity="0.55" />
                                <line x1="18" y1="125" x2="382" y2="125" stroke="#FBFCF7" stroke-width="1.2"
                                    opacity="0.25" />
                                <circle cx="200" cy="125" r="11" :fill="court.ball || '#C3DD41'" />
                                <circle cx="197" cy="122" r="3" fill="#FFFFFF" opacity="0.45" />
                            </svg>
                            <div class="court-number mono">
                                COURT {{ index + 1 }}
                            </div>
                            <span v-if="court.id === selectedCourtId" class="selected-pill">✓ SELECTED</span>
                        </div>
                        <div class="court-body">
                            <div class="court-topline">
                                <span class="court-index mono">0{{ index + 1 }}</span>
                                <span class="court-status"><span></span>Available</span>
                            </div>
                            <div class="cname">
                                {{ court.name }}
                            </div>
                            <div class="ctype mono">
                                {{ viewTags(court.tag) || 'STANDARD COURT' }}
                                <span>·</span>
                                {{ court.price_definition || 'HOURLY' }}
                            </div>
                            <div class="court-card-footer">
                                <div class="price-block">
                                    <span class="price-label">STARTING FROM </span>
                                    <strong>₱{{ court.price }} </strong>
                                    <span class="price-unit">/ HR</span>
                                </div>
                                <div class="court-arrow" :class="{ active: court.id === selectedCourtId }">→</div>
                            </div>
                        </div>
                    </button>
                </div>
            </div>
        </section>
        <section class="location-section" id="location">
            <div class="wrap">
                <div class="section-heading-row location-heading">
                    <div class="sec-head">
                        <span class="eyebrow mono">
                            FIND THE VENUE
                        </span>
                        <h2>
                            Right place.<br>
                            Right <em>court.</em>
                        </h2>
                        <p>
                            Check the facility location and review the
                            selected court before starting your reservation.
                        </p>
                    </div>
                </div>
                <div class="map-layout">
                    <div class="map-box">
                        <div class="map-header">
                            <div>
                                <span class="map-label mono">
                                    LOCATION
                                </span>

                                <strong>
                                    {{ venue?.area || 'Venue location' }}
                                </strong>
                            </div>
                            <div class="map-live">
                                <span></span>
                                LIVE MAP
                            </div>
                        </div>
                        <div id="leaflet-map" class="leaflet-container"></div>
                    </div>
                    <div class="map-detail">
                        <span class="eyebrow mono">SELECTED COURT</span>
                        <div class="selected-court-title">
                            <div class="selected-court-icon">
                                <span>◎</span>
                            </div>
                            <div>
                                <h3>
                                    {{ courtSelected?.name || 'Select a court' }}
                                </h3>
                                <p>
                                    {{ venue?.area || 'Venue location' }}
                                </p>
                            </div>
                        </div>
                        <div class="detail-price">
                            <span class="mono">
                                HOURLY RATE
                            </span>
                            <strong>
                                ₱{{ courtSelected?.price || 0 }}
                                <small>/HR</small>
                            </strong>
                        </div>
                        <div class="detail-list">
                            <div class="detail-row">
                                <span>Environment</span>
                                <strong>
                                    {{ viewTags(courtSelected?.tag) || '—' }}
                                </strong>
                            </div>
                            <div class="detail-row">
                                <span>Pricing</span>
                                <strong>
                                    {{ courtSelected?.price_definition || '—' }}
                                </strong>
                            </div>
                            <div class="detail-row">
                                <span>Courts On-Site</span>
                                <strong>
                                    {{ courts.length }}
                                </strong>
                            </div>
                        </div>
                        <button class="btn btn-lime map-cta" :disabled="!courtSelected?.id" @click="openReservation()">
                            Book {{ courtSelected?.name || 'Court' }}
                            <span>→</span>
                        </button>
                        <div class="map-note">
                            <span>●</span>
                            Select a date and time inside the reservation panel.
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <Teleport to="body">
            <Transition name="fade">
                <div v-if="isOpen" class="offcanvas-backdrop" @click="closeReservation"></div>
            </Transition>
            <Transition name="slide-right">
                <aside v-if="isOpen" class="offcanvas" aria-label="Reservation details">
                    <div class="offcanvas-header">
                        <div>
                            <span class="drawer-eyebrow mono">
                                COURT-TESY RESERVATION
                            </span>
                            <h3 class="offcanvas-title">
                                Reserve Your Court
                            </h3>
                        </div>
                        <button type="button" class="btn-close" @click="closeReservation"
                            aria-label="Close reservation">
                            ✕
                        </button>
                    </div>
                    <div class="offcanvas-body">
                        <div class="booking-court-card">
                            <div class="booking-court-visual">
                                <svg viewBox="0 0 120 80" xmlns="http://www.w3.org/2000/svg">
                                    <rect width="120" height="80" rx="6" :fill="courtSelected?.base || '#052A54'" />
                                    <rect x="7" y="7" width="106" height="66" fill="none" stroke="#C3DD41"
                                        stroke-width="1.5" />
                                    <line x1="60" y1="7" x2="60" y2="73" stroke="#C3DD41" stroke-width="1"
                                        stroke-dasharray="3 3" />
                                    <line x1="31" y1="7" x2="31" y2="73" stroke="#C3DD41" stroke-width="1"
                                        opacity=".5" />
                                    <line x1="89" y1="7" x2="89" y2="73" stroke="#C3DD41" stroke-width="1"
                                        opacity=".5" />
                                    <circle cx="60" cy="40" r="4" fill="#C3DD41" />
                                </svg>
                            </div>
                            <div class="booking-court-info">
                                <span class="mono">
                                    SELECTED COURT
                                </span>
                                <strong>
                                    {{ courtSelected?.name || 'Court' }}
                                </strong>
                                <small>
                                    {{ venue?.name }}
                                </small>
                            </div>
                            <div class="booking-court-price">
                                ₱{{ courtSelected?.price || 0 }}
                                <small>/hr</small>
                            </div>
                        </div>
                        <section class="step" id="pickDate-section">
                            <div class="step-head">
                                <div class="step-number">
                                    01
                                </div>
                                <div>
                                    <span class="step-tag mono">
                                        DATE
                                    </span>
                                    <h2 class="step-title">
                                        Pick a date
                                    </h2>
                                </div>
                            </div>
                            <div class="cal-card">
                                <div class="cal-head">
                                    <div>
                                        <span class="calendar-label mono">
                                            RESERVATION DATE
                                        </span>
                                        <div class="cal-month">
                                            {{ monthLabel }}
                                        </div>
                                    </div>
                                    <div class="cal-nav">
                                        <button type="button" @click="shiftMonth(-1)" :disabled="!canGoPrevMonth">
                                            ‹
                                        </button>
                                        <button type="button" @click="shiftMonth(1)" :disabled="!canGoNextMonth">
                                            ›
                                        </button>
                                    </div>
                                </div>
                                <div class="cal-weekdays">
                                    <span v-for="(dayName, i) in ['S', 'M', 'T', 'W', 'T', 'F', 'S']" :key="i">
                                        {{ dayName }}
                                    </span>
                                </div>
                                <div class="cal-grid">
                                    <div v-for="(cell, i) in calendarCells" :key="i" class="cal-day"
                                        :class="cellClass(cell)" @click="
                                            cell &&
                                            !isDateBlocked(cell) &&
                                            selectDate(cell)
                                            ">
                                        {{ cell ? cell.day : '' }}
                                    </div>
                                </div>
                                <div v-if="date" class="selected-date">
                                    <span>✓</span>
                                    {{ dateLabel }}
                                </div>
                            </div>
                        </section>
                        <section class="step" id="pickTime-section">
                            <div class="step-head">
                                <div class="step-number">
                                    02
                                </div>
                                <div>
                                    <span class="step-tag mono">
                                        TIME
                                    </span>
                                    <h2 class="step-title">
                                        Pick a time
                                    </h2>
                                </div>
                            </div>
                            <div class="time-date-label">
                                {{ dateLabel }}
                            </div>
                            <div class="slot-grid">
                                <button v-for="s in slots" :key="s.time" type="button" class="slot" :class="{
                                    taken: s.taken,
                                    reserved: s.reserved,
                                    blocked: s.blocked,
                                    selected: selectedSlots.some(selected => selected.time === s.time)
                                }" :disabled="s.taken" @click=" !s.taken && selectSlot(s)">
                                    <span class="slot-time">
                                        {{ s.time }}
                                    </span>
                                    <span v-if="s.reserved" class="slot-status">
                                        Reserved
                                    </span>
                                    <span v-else-if="s.blocked" class="slot-status">
                                        Blocked
                                    </span>
                                    <span v-else-if="selectedSlots.some(selected => selected.time === s.time)"
                                        class="slot-status">
                                        Selected
                                    </span>
                                    <span v-else class="slot-status">
                                        Available
                                    </span>
                                </button>
                            </div>
                            <div v-if="selectedSlots.length" class="time-selection-summary">

                                <div>
                                    <span class="mono">
                                        SELECTED
                                    </span>

                                    <strong>
                                        {{ timeLabel }}
                                    </strong>
                                </div>

                                <button type="button" @click="clearSelectedTime">
                                    Clear
                                </button>

                            </div>


                        </section>

                        <section class="step">
                            <div class="step-head">
                                <div class="step-number">
                                    03
                                </div>
                                <div>
                                    <span class="step-tag mono">
                                        PLAYER
                                    </span>
                                    <h2 class="step-title">
                                        Contact & players
                                    </h2>
                                </div>
                            </div>

                            <div class="form-card">
                                <div class="form-field">
                                    <label class="form-label mono">
                                        First name
                                    </label>
                                    <input v-model="firstName" type="text" class="form-input" placeholder="Juan"
                                        autocomplete="given-name" />
                                </div>

                                <div class="form-field">

                                    <label class="form-label mono">
                                        Last name
                                    </label>

                                    <input v-model="lastName" type="text" class="form-input" placeholder="Dela Cruz"
                                        autocomplete="family-name" />

                                </div>

                                <div class="form-field">
                                    <label class="form-label mono">
                                        Phone number
                                    </label>
                                    <input v-model="phone" type="tel" class="form-input" placeholder="09XX XXX XXXX"
                                        autocomplete="tel" />
                                </div>

                                <div class="form-field">
                                    <label class="form-label mono">
                                        Email
                                        <span class="optional">
                                            optional
                                        </span>
                                    </label>
                                    <input v-model="email" type="email" class="form-input" placeholder="juan@email.com"
                                        autocomplete="email" />
                                </div>

                                <div class="form-field">

                                    <label class="form-label mono">
                                        Players
                                    </label>

                                    <div class="stepper">

                                        <button type="button" class="stepper-btn" @click="decPlayers"
                                            :disabled="players <= 2">
                                            −
                                        </button>

                                        <div class="stepper-value">
                                            <strong>
                                                {{ players }}
                                            </strong>
                                            <span>
                                                PLAYERS
                                            </span>
                                        </div>
                                        <button type="button" class="stepper-btn" @click="incPlayers">
                                            +
                                        </button>
                                    </div>
                                </div>

                                <div class="form-field">

                                    <label class="form-label mono">
                                        Notes
                                        <span class="optional">
                                            optional
                                        </span>
                                    </label>
                                    <textarea v-model="notes" class="form-input form-textarea" rows="3"
                                        placeholder="Anything the court staff should know?"></textarea>
                                </div>
                            </div>
                        </section>
                        <div v-if="!canConfirmBooking" class="booking-hint">
                            <span>!</span>
                            <div>
                                <strong>
                                    Complete your booking details
                                </strong>
                                <p>
                                    Select a date, time, and provide your
                                    contact information before confirming.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="offcanvas-footer">
                        <div class="booking-summary">
                            <div class="summary-main">
                                <span class="summary-label mono">
                                    TOTAL
                                </span>
                                <strong>
                                    {{ totalHours }}
                                    {{ totalHours === 1 ? 'hour' : 'hours' }}
                                </strong>
                                <small>
                                    {{ timeLabel || 'No time selected' }}
                                </small>
                            </div>
                            <div class="summary-price">
                                <span>ESTIMATED</span>
                                <strong> ₱{{ bookingTotal }} </strong>
                            </div>
                        </div>
                        <button type="button" class="btn btn-lime confirm-btn" :disabled="!canConfirmBooking"
                            @click="confirmBooking">
                            {{ confirmed ? 'Court Held ✓' : 'Confirm & Pay' }}
                            <span v-if="!confirmed"> → </span>
                        </button>
                        <p class="secure-note">
                            Your reservation details will be sent
                            after confirmation.
                        </p>
                    </div>
                </aside>
            </Transition>
        </Teleport>


        <footer>

            <div class="wrap footer-inner">

                <div class="footer-brand">

                    <div class="brand">

                        <div class="brand-mark">
                            <svg width="28" height="28" viewBox="0 0 30 30">
                                <rect width="30" height="30" rx="8" fill="#C3DD41" />

                                <circle cx="15" cy="15" r="6.5" fill="none" stroke="#001B3E" stroke-width="2" />

                                <line x1="15" y1="4" x2="15" y2="26" stroke="#001B3E" stroke-width="1.2"
                                    stroke-dasharray="1.6 2.4" />
                            </svg>
                        </div>

                        <span class="brand-word">
                            Court<span>tesy</span>
                        </span>

                    </div>

                    <p>
                        Simple court reservations for players,
                        facilities, and growing sports communities.
                    </p>

                </div>

                <div class="footer-links">

                    <router-link to="/">
                        Home
                    </router-link>

                    <router-link to="/">
                        Venues
                    </router-link>

                    <a href="#courts">
                        Courts
                    </a>

                    <a href="#location">
                        Location
                    </a>

                </div>

            </div>

            <div class="wrap foot-bottom">

                <span>
                    Court-tesy © {{ year }}
                    · {{ venue?.name || 'Venue' }}
                </span>

                <span>
                    Terms · Support
                </span>

            </div>

        </footer>

    </div>
</template>


<script setup>
import {
    ref,
    computed,
    onMounted,
    nextTick,
    onBeforeUnmount
} from 'vue'

import { TIMES } from '@/constants/times.js'
import { COURT_COLOR_CYCLE } from '@/constants/courtcolor'
import { useCourtStore } from '@/stores/UseCourt'
import { useVenueStore } from '@/stores/UseVenues'
import { useRoute } from 'vue-router'
import Logo from '@/component/assets/logo.jpg'
import { image } from '@/utils/image'
import { useBookingStore } from '@/stores/UseBooking'
import { usePaymentStore } from '@/stores/UsePayment'

const useCourt = useCourtStore()
const useVenue = useVenueStore()
const useBooking = useBookingStore();
const usePayment = usePaymentStore();
const route = useRoute()

const venue = ref(null)
const courts = ref([])
const loadingCourts = ref(false)
const isOpen = ref(false)
const confirmed = ref(false)
const activeSlug = ref('')
const selectedCourtId = ref(null)
const courtSelected = ref(null)
const map = ref(null)
const date = ref(null)
const selectedSlots = ref([])
const reservedTimes = ref([])
const blockedTimes = ref([])
const venueClosedDates = ref([])
const totalHours = ref(0)
const timeLabel = ref('')
const firstName = ref('')
const lastName = ref('')
const phone = ref('')
const email = ref('')
const players = ref(4)
const notes = ref('')
const today = new Date()

today.setHours(0, 0, 0, 0)


today.setDate(today.getDate() + 1)

const MAX_DAYS_AHEAD = 21

const maxDate = new Date(today)

maxDate.setDate(
    maxDate.getDate() + MAX_DAYS_AHEAD
)

const viewYear = ref(
    today.getFullYear()
)

const viewTags = (tag) => {
    return tag?.join(', ') ?? ''
}

const viewMonth = ref(
    today.getMonth()
)

const monthLabel = computed(() => {
    return new Date(
        viewYear.value,
        viewMonth.value,
        1
    ).toLocaleDateString(
        'en-US',
        {
            month: 'long',
            year: 'numeric'
        }
    )
})


function pad(number) {
    return String(number).padStart(2, '0')
}


function keyOf(year, month, day) {
    return `${year}-${pad(month + 1)}-${pad(day)}`
}


const canGoPrevMonth = computed(() => {
    return !(
        viewYear.value === today.getFullYear() &&
        viewMonth.value === today.getMonth()
    )
})


const canGoNextMonth = computed(() => {
    const firstOfView = new Date(
        viewYear.value,
        viewMonth.value,
        1
    )

    const firstOfMax = new Date(
        maxDate.getFullYear(),
        maxDate.getMonth(),
        1
    )

    return firstOfView < firstOfMax
})


const calendarCells = computed(() => {

    const firstDay = new Date(
        viewYear.value,
        viewMonth.value,
        1
    )

    const startOffset = firstDay.getDay()

    const daysInMonth = new Date(
        viewYear.value,
        viewMonth.value + 1,
        0
    ).getDate()

    const cells = []

    for (
        let i = 0;
        i < startOffset;
        i++
    ) {
        cells.push(null)
    }

    for (
        let day = 1;
        day <= daysInMonth;
        day++
    ) {

        const cellDate = new Date(
            viewYear.value,
            viewMonth.value,
            day
        )

        cellDate.setHours(0, 0, 0, 0)

        cells.push({
            day,
            key: keyOf(
                viewYear.value,
                viewMonth.value,
                day
            ),
            disabled:
                cellDate < today ||
                cellDate > maxDate,
            isToday:
                cellDate.getTime() === today.getTime()
        })
    }

    return cells
})


function shiftMonth(direction) {

    let month =
        viewMonth.value + direction

    let year =
        viewYear.value

    if (month < 0) {
        month = 11
        year--
    }

    if (month > 11) {
        month = 0
        year++
    }

    viewMonth.value = month
    viewYear.value = year
}

function isDateBlocked(cell) {

    if (!cell) {
        return true
    }

    const isClosed =
        venueClosedDates.value.some(
            item =>
                item.closed_date === cell.key
        )

    return (
        isClosed ||
        cell.disabled
    )
}


const cellClass = cell => {

    if (!cell) {
        return 'empty'
    }

    const closed =
        venueClosedDates.value.some(
            item =>
                item.closed_date === cell.key
        )

    return {
        disabled: isDateBlocked(cell),
        today: cell.isToday,
        selected:
            date.value === cell.key,
        blocked: closed
    }
}


const dateLabel = computed(() => {

    if (!date.value) {
        return 'SELECT A DATE'
    }

    const selectedDate =
        new Date(`${date.value}T00:00:00`)

    return selectedDate.toLocaleDateString(
        'en-US',
        {
            weekday: 'short',
            month: 'short',
            day: 'numeric'
        }
    ).toUpperCase()
})


function selectDate(cell) {

    if (
        !cell ||
        isDateBlocked(cell)
    ) {
        return
    }

    date.value = cell.key

    selectedSlots.value = []

    timeLabel.value = ''

    updateTimeDate();

    totalHours.value = 0
    nextTick(() => {
        document
            .getElementById('pickTime-section')
            ?.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            })
    })
}

function normalize(time) {

    return time
        .toString()
        .trim()
        .toUpperCase()
        .replace(/^0/, '')
}


function convertToMinutes(time) {

    if (!time) {
        return 0
    }

    const [
        timePart,
        period
    ] = time.split(' ')

    let [
        hour,
        minute
    ] = timePart
        .split(':')
        .map(Number)

    if (
        period === 'PM' &&
        hour !== 12
    ) {
        hour += 12
    }

    if (
        period === 'AM' &&
        hour === 12
    ) {
        hour = 0
    }

    return (
        hour * 60 +
        minute
    )
}


function formatMinutesToTime(
    totalMinutes
) {

    let hours =
        Math.floor(
            totalMinutes / 60
        ) % 24

    const minutes =
        totalMinutes % 60

    const period =
        hours >= 12
            ? 'PM'
            : 'AM'

    hours =
        hours % 12

    if (hours === 0) {
        hours = 12
    }

    const formattedMinutes =
        minutes < 10
            ? `0${minutes}`
            : minutes

    return `${hours}:${formattedMinutes} ${period}`
}


const slotStatus = (time) => {

    if (!time) {
        return {
            taken: false,
            reserved: false,
            blocked: false
        }
    }

    const formatted =
        normalize(time)

    const isReserved =
        reservedTimes.value.some(
            slot =>
                normalize(slot) === formatted
        )

    const isBlocked =
        !isReserved &&
        blockedTimes.value.some(
            slot =>
                normalize(slot) === formatted
        )

    return {
        taken:
            isReserved ||
            isBlocked,
        reserved: isReserved,
        blocked: isBlocked
    }
}


const slots = computed(() => {
    return TIMES
        .map(t => ({
            time: t,
            ...slotStatus(t)
        }));
});


function selectSlot(slot) {
    if (slot.taken || slot.reserved || slot.blocked) {
        return
    }

    const isAlreadySelected = selectedSlots.value.some(
        selected => selected.time === slot.time
    )

    if (isAlreadySelected) {
        selectedSlots.value = []
        updateTimeLabel()
        updateTimeDate()
        return
    }

    if (selectedSlots.value.length === 0) {
        selectedSlots.value = [slot]
        updateTimeLabel()
        updateTimeDate()
        return
    }

    const existingMinutes = selectedSlots.value.map(s => convertToMinutes(s.time))
    const clickedMinutes = convertToMinutes(slot.time)

    const rangeStart = Math.min(...existingMinutes, clickedMinutes)
    const rangeEnd = Math.max(...existingMinutes, clickedMinutes)

    const slotsInRange = slots.value.filter(s => {
        const m = convertToMinutes(s.time)
        return m >= rangeStart && m <= rangeEnd
    })

    const rangeHasBlocker = slotsInRange.some(
        s => s.taken || s.reserved || s.blocked
    )

    if (rangeHasBlocker) {
        selectedSlots.value = [slot]
    } else {
        selectedSlots.value = slotsInRange
    }

    selectedSlots.value.sort(
        (a, b) => convertToMinutes(a.time) - convertToMinutes(b.time)
    )

    updateTimeLabel()
    updateTimeDate();
}

function clearSelectedTime() {

    selectedSlots.value = []

    timeLabel.value = ''

    totalHours.value = 0
}


function updateTimeLabel() {
    if (selectedSlots.value.length === 0) {
        timeLabel.value = ''
        totalHours.value = 0
        return
    }

    const sorted = [...selectedSlots.value].sort(
        (a, b) =>
            convertToMinutes(a.time) -
            convertToMinutes(b.time)
    )

    const startTime = sorted[0].time

    const lastSlotMinutes = convertToMinutes(
        sorted[sorted.length - 1].time
    )

    // Each selected slot = 1 hour
    const durationHours = sorted.length

    // Last selected hour ends at :59
    const endMinutes = lastSlotMinutes + 59

    const endTime = formatMinutesToTime(endMinutes)

    totalHours.value = durationHours

    timeLabel.value = `${startTime} – ${endTime}`
}

// function updateTimeLabel() {

//     if (
//         selectedSlots.value.length === 0
//     ) {

//         timeLabel.value = ''

//         totalHours.value = 0

//         return
//     }

//     const sorted = [
//         ...selectedSlots.value
//     ].sort(
//         (a, b) =>
//             convertToMinutes(a.time) -
//             convertToMinutes(b.time)
//     )

//     const startMinutes =
//         convertToMinutes(
//             sorted[0].time
//         )

//     const lastSlotMinutes =
//         convertToMinutes(
//             sorted[
//                 sorted.length - 1
//             ].time
//         )

//     const isSingleSlot =
//         sorted.length === 1

//     const slotDurationMinutes = 60

//     const endMinutes =
//         isSingleSlot
//             ? lastSlotMinutes + slotDurationMinutes
//             : lastSlotMinutes

//     const durationHours =
//         (endMinutes -
//             startMinutes) / 60

//     totalHours.value = durationHours === 1
//         ? 1
//         : durationHours + 1

//     const startTime =
//         sorted[0].time

//     const endTime =
//         formatMinutesToTime(
//             endMinutes
//         )

//     timeLabel.value =
//         `${startTime} – ${endTime}`
// }


const bookingTotal = computed(() => {

    const price =
        Number(
            courtSelected.value?.price || 0
        )

    return (
        price *
        Number(totalHours.value || 0)
    )
})

function incPlayers() {
    players.value++
}


function decPlayers() {

    if (players.value > 2) {
        players.value--
    }
}

const detailsValid = computed(() => {

    return !!(
        firstName.value.trim() &&
        lastName.value.trim() &&
        phone.value.trim()
    )
})


const canConfirmBooking = computed(() => {

    return !!(
        courtSelected.value?.id &&
        date.value &&
        selectedSlots.value.length &&
        detailsValid.value
    )
})

async function loadCourts(currentVenue) {

    if (!currentVenue?.id) {

        courts.value = []

        selectedCourtId.value = null

        courtSelected.value = null

        return
    }

    loadingCourts.value = true

    try {

        const response =
            await useCourt.getCourts({
                venue_id: currentVenue.id
            })

        const list =
            Array.isArray(response)
                ? response
                : []

        courts.value =
            list.map(
                (court, index) => {

                    const color =
                        COURT_COLOR_CYCLE[
                        index %
                        COURT_COLOR_CYCLE.length
                        ]

                    return {
                        ...court,

                        price:
                            Number(
                                court.price ??
                                court.priceOffpeak ??
                                0
                            ),

                        base:
                            color?.base ||
                            '#052A54',

                        line:
                            color?.line ||
                            '#FBFCF7',

                        ball:
                            color?.ball ||
                            '#C3DD41',

                        openCount:
                            court.openCount ??
                            0,

                        total:
                            court.total ??
                            0
                    }
                }
            )


        if (courts.value.length) {

            selectedCourtId.value =
                courts.value[0].id

            courtSelected.value =
                courts.value[0]

        } else {

            selectedCourtId.value =
                null

            courtSelected.value =
                null
        }

    } catch (error) {

        console.error(
            'Failed to load courts:',
            error
        )

        courts.value = []

        selectedCourtId.value = null

        courtSelected.value = null

    } finally {

        loadingCourts.value = false
    }
}

function selectedCourt(court) {

    if (!court) {
        return
    }

    selectedCourtId.value =
        court.id

    courtSelected.value =
        court

    date.value = null

    selectedSlots.value = []

    timeLabel.value = ''

    totalHours.value = 0

    reservedTimes.value = []

    blockedTimes.value = []

    confirmed.value = false

    nextTick(() => {

        document
            .getElementById('location')
            ?.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            })

    })
}

function clearContactDetails() {

    firstName.value = ''

    lastName.value = ''

    email.value = ''

    phone.value = ''

    players.value = 4

    notes.value = ''
}

const updateTimeDate = async () => {

    let venue_id = venue.value?.id;
    let court_id = selectedCourtId.value;
    let booking_date = date.value;

    const closeTimeCourt = await useCourt.courtCloseTime({ court_id: court_id, schedule: booking_date });

    if (closeTimeCourt) {
        blockedTimes.value = closeTimeCourt?.closed_times;
    } else {
        blockedTimes.value = []
    }

    const reservedTimeCourt = await useBooking.getReservation({ venue_id: venue_id, court_id: court_id, booking_date: booking_date })
    console.log(reservedTimeCourt)
    if (reservedTimeCourt) {
        reservedTimes.value = reservedTimeCourt;
    } else {
        reservedTimes.value = []
    }

    const closingVenueDate = await useVenue.getVenueCloseDateById({ venue_id: venue_id })

    if (closingVenueDate) {
        venueClosedDates.value = closingVenueDate
    } else {
        venueClosedDates.value = [];
    }

}

const openReservation = async () => {

    if (!courtSelected.value) {
        return
    }

    updateTimeDate();

    confirmed.value = false

    isOpen.value = true

    document.body.classList.add(
        'reservation-open'
    )

    nextTick(() => {
        if (map.value) {
            setTimeout(() => {
                map.value.invalidateSize()
            }, 300)
        }

    })
}

function closeReservation() {

    isOpen.value = false

    document.body.classList.remove(
        'reservation-open'
    )
}

const getBookingTime = (slots) => {
    if (!slots?.length) {
        return {
            start_time: null,
            end_time: null,
            hours: 0,
        }
    }

    const times = slots.map(slot => slot.time)

    const start_time = times[0]
    const end_time = times[times.length - 1]
    const hours = slots.length

    return {
        start_time,
        end_time,
        hours,
    }
}

const generateBookingCode = () => {
    const now = new Date()

    const timestamp =
        now.getFullYear().toString() +
        String(now.getMonth() + 1).padStart(2, '0') +
        String(now.getDate()).padStart(2, '0') +
        String(now.getHours()).padStart(2, '0') +
        String(now.getMinutes()).padStart(2, '0') +
        String(now.getSeconds()).padStart(2, '0')

    let hash = 0

    for (let i = 0; i < timestamp.length; i++) {
        hash = ((hash << 5) - hash) + timestamp.charCodeAt(i)
        hash |= 0
    }

    const number = Math.abs(hash) % 1000000

    return `CT-${String(number).padStart(6, '0')}`
}

const confirmBooking = async () => {

    if (!canConfirmBooking.value) {
        return
    }

    let clicked = false;

    if (clicked) { alert('Too many attempts to click'); return };
    clicked = true;

    const bookingTime = getBookingTime(selectedSlots.value);
    const downpayment = (bookingTime.hours * courtSelected.value.price) * .5;
    const bookingCode = generateBookingCode();
    const amount = bookingTime.hours <= 2 ? downpayment : 350;

    const formData = {
        booking_code: bookingCode,
        venue_id: venue.value.id,
        court_id: courtSelected.value.id,
        booking_date: date.value,
        start_time: bookingTime.start_time,
        end_time: bookingTime.end_time,
        hours: bookingTime.hours,
        customer_name: `${lastName.value}, ${firstName.value} `,
        customer_phone: phone.value,
        customer_email: email.value,
        players: players.value,
        amount: courtSelected.value.price,
        notes: `<div><strong>Players Notes:</strong> ${notes.value || 'None'}<br><strong>Total Hours:</strong> ${bookingTime.hours}<br><strong>Downpayment:</strong> ₱${downpayment >= 350 ? 350 : downpayment}<br><strong>Players:</strong> ${players.value}</div>`
    }

    await useBooking.createBooking(formData)

    const result = await usePayment.submitPayment(amount * 100, bookingCode);

    if (result?.data?.checkout_url) {
        window.location.href = result.data.checkout_url;
        confirmed.value = true
    } else {
        console.error("Checkout URL missing from response:", result);
    }

}

async function selectVenue(slug) {

    if (!slug) {
        return
    }

    try {

        const response =
            await useVenue.getVenueBySlug({
                slugs: slug
            })

        venue.value =
            response || null

        await loadCourts(
            venue.value
        )

    } catch (error) {

        console.error(
            'Failed to load venue:',
            error
        )

        venue.value = null

        courts.value = []

    }
}


const initLeafletMap = currentVenue => {

    if (
        typeof L === 'undefined'
    ) {
        console.warn(
            'Leaflet is not available.'
        )

        return
    }

    if (
        !currentVenue ||
        !currentVenue.latitude ||
        !currentVenue.longitude
    ) {
        console.warn(
            'Venue coordinates are missing.'
        )

        return
    }

    if (map.value) {

        map.value.remove()

        map.value = null
    }

    map.value =
        L.map(
            'leaflet-map'
        ).setView(
            [
                Number(
                    currentVenue.latitude
                ),
                Number(
                    currentVenue.longitude
                )
            ],
            15
        )


    L.tileLayer(
        'https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png',
        {
            attribution:
                '&copy; OpenStreetMap &copy; CARTO',
            maxZoom: 19
        }
    ).addTo(
        map.value
    )


    const marker =
        L.marker([
            Number(
                currentVenue.latitude
            ),
            Number(
                currentVenue.longitude
            )
        ]).addTo(
            map.value
        )


    marker.bindPopup(
        `
            <div style="
                font-family: Inter, sans-serif;
                padding: 4px;
            ">
                <strong>
                    ${currentVenue.name || 'Venue'}
                </strong>
                <br>
                <span>
                    ${currentVenue.area || ''}
                </span>
            </div>
        `
    ).openPopup()
}

onMounted(async () => {

    const slug =
        route.params.slug ||
        localStorage.getItem('slug')

    if (!slug) {
        localStorage.removeItem(
            'slug'
        )
        return
    }

    activeSlug.value = slug

    localStorage.setItem(
        'slug',
        slug
    )

    await selectVenue(
        activeSlug.value
    )

    await nextTick()

    setTimeout(() => {
        if (venue.value) {
            initLeafletMap(
                venue.value
            )
        }
    }, 100)

})

onBeforeUnmount(() => {

    document.body.classList.remove(
        'reservation-open'
    )

    if (map.value) {

        map.value.remove()

        map.value = null
    }
})


/* ============================================================
   YEAR
============================================================ */

const year =
    new Date().getFullYear()
</script>


<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500;600&display=swap');


/* ============================================================
   ROOT
============================================================ */

.page {
    --navy: #001B3E;
    --navy-2: #04264F;
    --navy-3: #0B3568;

    --lime: #C3DD41;
    --lime-2: #9FB92F;

    --cream: #F4F7EA;
    --paper: #FBFCF7;

    --ink: #04101F;
    --ink-soft: #4A5A6B;
    --ink-faint: #7D8894;

    --line: rgba(0, 27, 62, .10);
    --line-light: rgba(195, 221, 65, .18);

    --danger: #E15D5D;
    --success: #56C878;

    --r-lg: 22px;
    --r-md: 16px;
    --r-sm: 10px;

    font-family: 'Inter', sans-serif;

    color: var(--ink);

    background: var(--paper);

    min-height: 100vh;

    -webkit-font-smoothing: antialiased;
    text-rendering: optimizeLegibility;
}


* {
    box-sizing: border-box;
}


a {
    color: inherit;
    text-decoration: none;
}


button,
input,
textarea {
    font: inherit;
}


button {
    border: 0;
}


.wrap {
    width: min(1240px,
            calc(100% - 48px));

    margin: 0 auto;
}


.mono {
    font-family: 'JetBrains Mono', monospace;
}


/* ============================================================
   NAV
============================================================ */

.nav {
    position: sticky;
    top: 0;

    z-index: 100;

    background: rgba(0,
            27,
            62,
            .94);

    backdrop-filter: blur(14px);

    border-bottom:
        1px solid rgba(195,
            221,
            65,
            .14);
}


.nav-inner {
    max-width: 1240px;

    margin: 0 auto;

    padding:
        15px 28px;

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 20px;
}


.brand {
    display: flex;

    align-items: center;

    gap: 10px;

    color: var(--paper);

    flex-shrink: 0;
}


.brand-mark {
    width: 32px;
    height: 32px;

    display: flex;

    align-items: center;
    justify-content: center;
}


.brand-word {
    font-family:
        'Space Grotesk',
        sans-serif;

    font-size: 19px;

    font-weight: 700;

    letter-spacing: -.02em;
}


.brand-word span {
    color: var(--lime);
}


.nav-links {
    display: flex;

    align-items: center;

    gap: 32px;
}


.nav-links a {
    color:
        rgba(244,
            247,
            234,
            .65);

    font-size: 13px;

    font-weight: 500;

    transition:
        color .2s ease;
}


.nav-links a:hover,
.nav-links a.router-link-active {
    color: var(--lime);
}


.nav-book-btn {
    display: inline-flex;

    align-items: center;

    gap: 9px;

    padding:
        10px 15px;

    border-radius: 9px;

    background: var(--lime);

    color: var(--navy);

    font-size: 12px;

    font-weight: 700;

    cursor: pointer;

    transition:
        transform .2s ease,
        background .2s ease;
}


.nav-book-btn:hover {
    transform:
        translateY(-1px);

    background:
        #D4ED5B;
}


.nav-book-btn span {
    font-size: 15px;
}


@media(max-width: 760px) {

    .nav-links {
        display: none;
    }

    .nav-inner {
        padding:
            13px 20px;
    }

}


/* ============================================================
   VENUE HERO
============================================================ */

.venue-hero {
    position: relative;

    overflow: hidden;

    background:
        radial-gradient(1000px 500px at 8% -15%,
            #06305E 0%,
            transparent 68%),
        var(--navy);

    color: var(--paper);

    padding:
        32px 0 58px;
}


.hero-grid {
    position: absolute;

    inset: 0;

    pointer-events: none;

    background-image:
        repeating-linear-gradient(0deg,
            rgba(195,
                221,
                65,
                .045) 0 1px,
            transparent 1px 64px),
        repeating-linear-gradient(90deg,
            rgba(195,
                221,
                65,
                .045) 0 1px,
            transparent 1px 64px);
}


.hero-grid::after {
    content: "";

    position: absolute;

    width: 450px;
    height: 450px;

    border-radius: 50%;

    background:
        radial-gradient(circle,
            rgba(195,
                221,
                65,
                .12),
            transparent 68%);

    right: -120px;
    top: -180px;
}


.hero-content {
    position: relative;

    z-index: 2;
}


.breadcrumb {
    display: flex;

    align-items: center;

    gap: 8px;

    margin-bottom: 42px;

    color:
        rgba(244,
            247,
            234,
            .45);

    font-size: 10px;

    letter-spacing: .1em;
}


.breadcrumb a {
    color: var(--lime);

    transition:
        opacity .2s ease;
}


.breadcrumb a:hover {
    opacity: .7;
}


.breadcrumb span {
    opacity: .7;
}


.venue-header-grid {
    display: grid;

    grid-template-columns:
        auto 1fr;

    align-items: center;

    gap: 28px;
}


.venue-logo-wrap {
    display: flex;

    flex-direction: column;

    align-items: center;

    gap: 10px;
}


.venue-logo {
    width: 108px;
    height: 108px;

    object-fit: cover;

    border-radius: 22px;

    background: var(--paper);

    border:
        1px solid rgba(195,
            221,
            65,
            .35);

    box-shadow:
        0 25px 50px rgba(0,
            0,
            0,
            .35);
}


.logo-status {
    display: flex;

    align-items: center;

    gap: 6px;

    color:
        rgba(244,
            247,
            234,
            .5);

    font-family:
        'JetBrains Mono',
        monospace;

    font-size: 8px;

    letter-spacing: .08em;

    white-space: nowrap;
}


.logo-status span {
    width: 6px;
    height: 6px;

    border-radius: 50%;

    background: var(--success);

    box-shadow:
        0 0 0 3px rgba(86,
            200,
            120,
            .12);
}


.venue-eyebrow {
    color: var(--lime);

    font-size: 10px;

    letter-spacing: .16em;

    margin-bottom: 12px;
}


.venue-main-info h1 {
    margin: 0;

    max-width: 800px;

    font-family:
        'Space Grotesk',
        sans-serif;

    font-size:
        clamp(38px,
            6vw,
            68px);

    line-height: .96;

    font-weight: 700;

    letter-spacing: -.035em;

    text-transform: uppercase;
}


.venue-address {
    margin-top: 16px;

    display: flex;

    align-items: center;

    gap: 8px;

    color:
        rgba(244,
            247,
            234,
            .6);

    font-size: 14px;
}


.location-dot {
    color: var(--lime);

    font-size: 18px;
}


.venue-tags {
    display: flex;

    flex-wrap: wrap;

    gap: 8px;

    margin-top: 22px;
}


.vbadge {
    display: inline-flex;

    align-items: center;

    padding:
        7px 11px;

    border:
        1px solid rgba(244,
            247,
            234,
            .14);

    background:
        rgba(244,
            247,
            234,
            .045);

    border-radius: 999px;

    color:
        rgba(244,
            247,
            234,
            .7);

    font-family:
        'JetBrains Mono',
        monospace;

    font-size: 9px;

    letter-spacing: .06em;
}


.vbadge.primary {
    background:
        rgba(195,
            221,
            65,
            .12);

    border-color:
        rgba(195,
            221,
            65,
            .32);

    color: var(--lime);
}


.venue-loading {
    padding: 70px 0;

    font-family:
        'JetBrains Mono',
        monospace;

    color:
        rgba(244,
            247,
            234,
            .55);
}


@media(max-width: 600px) {

    .venue-header-grid {
        grid-template-columns: 1fr;

        align-items: start;

        gap: 20px;
    }

    .venue-logo {
        width: 82px;
        height: 82px;

        border-radius: 16px;
    }

    .venue-logo-wrap {
        align-items: flex-start;
    }

    .venue-main-info h1 {
        font-size: 40px;
    }

}


/* ============================================================
   SECTIONS
============================================================ */

.sec {
    padding:
        92px 0;
}


.courts-section {
    background: var(--paper);
}


.section-heading-row {
    display: flex;

    align-items: flex-end;

    justify-content: space-between;

    gap: 40px;

    margin-bottom: 46px;
}


.sec-head {
    max-width: 690px;
}


.eyebrow {
    display: block;

    color: var(--lime-2);

    font-family:
        'JetBrains Mono',
        monospace;

    font-size: 10px;

    letter-spacing: .15em;

    margin-bottom: 12px;
}


.sec-head h2 {
    margin: 0;

    color: var(--navy);

    font-family:
        'Space Grotesk',
        sans-serif;

    font-size:
        clamp(34px,
            5vw,
            52px);

    line-height: .98;

    font-weight: 700;

    letter-spacing: -.035em;

    text-transform: uppercase;
}


.sec-head h2 em {
    color: var(--lime-2);

    font-style: normal;
}


.sec-head p {
    max-width: 580px;

    margin-top: 17px;

    color: var(--ink-soft);

    font-size: 15px;

    line-height: 1.7;
}


.court-count-box {
    min-width: 125px;

    padding:
        16px 18px;

    border:
        1px solid var(--line);

    border-radius: 14px;

    background: white;

    display: flex;

    flex-direction: column;

    gap: 5px;
}


.court-count-box span {
    color: var(--ink-soft);

    font-size: 9px;

    letter-spacing: .08em;
}


.court-count-box strong {
    color: var(--navy);

    font-family:
        'Space Grotesk',
        sans-serif;

    font-size: 30px;
}


@media(max-width: 700px) {

    .sec {
        padding: 65px 0;
    }

    .section-heading-row {
        align-items: flex-start;

        flex-direction: column;

        gap: 22px;
    }

}

.court-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 300px));
    gap: 20px;
}


.court-card {
    display: flex;
    flex-direction: column;
    width: 100%;
    text-align: left;
    padding: 0;
    overflow: hidden;
    cursor: pointer;
    border: 1px solid rgba(0, 27, 62, .10);
    border-radius: 18px;
    background: white;
    box-shadow: 0 8px 30px rgba(0, 27, 62, .04);
    transition: transform .22s ease, border-color .22s ease, box-shadow .22s ease;
}

.court-card:hover {
    transform:
        translateY(-5px);

    box-shadow:
        0 22px 45px rgba(0,
            27,
            62,
            .13);
}


.court-card.is-selected {
    border-color: var(--lime);

    box-shadow:
        0 0 0 3px rgba(195,
            221,
            65,
            .18),
        0 22px 45px rgba(0,
            27,
            62,
            .13);
}


.court-thumb {
    position: relative;
    aspect-ratio: 1.55;
    overflow: hidden;
    background: var(--navy);
}


.court-thumb svg {
    display: block;

    width: 100%;
    height: 100%;

    transition:
        transform .3s ease;
}


.court-card:hover .court-thumb svg {
    transform: scale(1.025);
}


.court-number {
    position: absolute;

    bottom: 12px;
    left: 13px;

    padding:
        6px 9px;

    border-radius: 7px;

    background:
        rgba(0,
            15,
            38,
            .68);

    color:
        rgba(244,
            247,
            234,
            .7);

    font-size: 8px;

    letter-spacing: .08em;
}


.selected-pill {
    position: absolute;

    top: 12px;
    right: 12px;

    padding:
        7px 10px;

    border-radius: 999px;

    background: var(--lime);

    color: var(--navy);

    font-family:
        'JetBrains Mono',
        monospace;

    font-size: 8px;

    font-weight: 700;

    letter-spacing: .06em;

    box-shadow:
        0 7px 15px rgba(0,
            0,
            0,
            .2);
}


.court-body {
    padding:
        17px 18px 19px;
}


.court-topline {
    display: flex;

    justify-content: space-between;

    align-items: center;

    margin-bottom: 12px;
}


.court-index {
    color: var(--lime-2);

    font-size: 9px;

    letter-spacing: .08em;
}


.court-status {
    display: flex;

    align-items: center;

    gap: 5px;

    color: var(--success);

    font-size: 9px;

    font-weight: 600;

    text-transform: uppercase;
}


.court-status span {
    width: 5px;
    height: 5px;

    border-radius: 50%;

    background: var(--success);
}


.cname {
    color: var(--navy);

    font-family:
        'Space Grotesk',
        sans-serif;

    font-size: 19px;

    line-height: 1.15;

    font-weight: 700;

    letter-spacing: -.02em;
}


.ctype {
    display: flex;

    align-items: center;

    gap: 6px;

    margin-top: 6px;

    color: var(--ink-soft);

    font-size: 9px;

    letter-spacing: .04em;

    text-transform: uppercase;
}


.court-card-footer {
    display: flex;

    justify-content: space-between;

    align-items: flex-end;

    margin-top: 20px;

    padding-top: 14px;

    border-top:
        1px solid rgba(0,
            27,
            62,
            .08);
}


.price-block {
    display: flex;

    align-items: baseline;

    gap: 4px;

    flex-wrap: wrap;
}


.price-label {
    display: block;

    width: 100%;

    color: var(--ink-faint);

    font-family:
        'JetBrains Mono',
        monospace;

    font-size: 8px;

    letter-spacing: .08em;
}


.price-block strong {
    color: var(--navy);

    font-family:
        'Space Grotesk',
        sans-serif;

    font-size: 23px;
}


.price-unit {
    color: var(--ink-soft);

    font-size: 9px;
}


.court-arrow {
    width: 38px;
    height: 38px;

    display: flex;

    align-items: center;
    justify-content: center;

    border-radius: 10px;

    background:
        rgba(0,
            27,
            62,
            .05);

    color: var(--navy);

    font-size: 18px;

    transition:
        background .2s ease,
        color .2s ease;
}


.court-arrow.active,
.court-card:hover .court-arrow {
    background: var(--navy);

    color: var(--lime);
}


/* ============================================================
   EMPTY / LOADING
============================================================ */

.empty-state {
    min-height: 280px;

    display: flex;

    flex-direction: column;

    align-items: center;
    justify-content: center;

    text-align: center;

    border:
        1px dashed rgba(0,
            27,
            62,
            .16);

    border-radius: 18px;

    background:
        rgba(0,
            27,
            62,
            .02);
}


.empty-icon {
    width: 52px;
    height: 52px;

    display: flex;

    align-items: center;
    justify-content: center;

    margin-bottom: 15px;

    border-radius: 14px;

    background:
        rgba(195,
            221,
            65,
            .15);

    color: var(--lime-2);

    font-size: 25px;
}


.empty-state h3 {
    margin-bottom: 6px;

    font-family:
        'Space Grotesk',
        sans-serif;

    color: var(--navy);

    font-size: 20px;
}


.empty-state p {
    color: var(--ink-soft);

    font-size: 13px;
}


.loading-spinner {
    width: 34px;
    height: 34px;

    margin-bottom: 15px;

    border:
        3px solid rgba(0,
            27,
            62,
            .08);

    border-top-color: var(--lime-2);

    border-radius: 50%;

    animation:
        spin .8s linear infinite;
}


@keyframes spin {

    to {
        transform:
            rotate(360deg);
    }

}


/* ============================================================
   LOCATION
============================================================ */

.location-section {
    padding:
        95px 0;

    background:
        #F0F3E8;
}


.location-heading {
    margin-bottom: 40px;
}


.map-layout {
    display: grid;

    grid-template-columns:
        1.35fr .65fr;

    gap: 22px;

    align-items: stretch;
}


.map-box {
    min-height: 500px;

    padding: 10px;

    overflow: hidden;

    border:
        1px solid rgba(0,
            27,
            62,
            .10);

    border-radius: 20px;

    background: white;

    box-shadow:
        0 15px 45px rgba(0,
            27,
            62,
            .08);
}


.map-header {
    height: 65px;

    padding:
        7px 12px 11px;

    display: flex;

    align-items: center;

    justify-content: space-between;
}


.map-header>div:first-child {
    display: flex;

    flex-direction: column;

    gap: 3px;
}


.map-label {
    color: var(--lime-2);

    font-size: 8px;

    letter-spacing: .1em;
}


.map-header strong {
    color: var(--navy);

    font-size: 13px;
}


.map-live {
    display: flex;

    align-items: center;

    gap: 6px;

    padding:
        6px 9px;

    border-radius: 999px;

    background:
        rgba(86,
            200,
            120,
            .08);

    color: #318C4B;

    font-family:
        'JetBrains Mono',
        monospace;

    font-size: 8px;

    letter-spacing: .04em;
}


.map-live span {
    width: 5px;
    height: 5px;
    border-radius: 50%;
    background: var(--success);
}


.leaflet-container {
    width: 100%;
    height: 405px;
    z-index: 1;
    overflow: hidden;
    border-radius: 13px;
}


.map-detail {
    padding:
        30px 28px;

    border:
        1px solid rgba(0,
            27,
            62,
            .10);

    border-radius: 20px;

    background: white;

    box-shadow:
        0 15px 45px rgba(0,
            27,
            62,
            .06);
}


.selected-court-title {
    display: flex;

    gap: 13px;

    margin-top: 17px;

    align-items: center;
}


.selected-court-icon {
    width: 48px;
    height: 48px;

    display: flex;

    align-items: center;
    justify-content: center;

    flex-shrink: 0;

    border-radius: 12px;

    background:
        rgba(195,
            221,
            65,
            .16);

    color: var(--lime-2);

    font-size: 24px;
}


.selected-court-title h3 {
    margin: 0;

    color: var(--navy);

    font-family:
        'Space Grotesk',
        sans-serif;

    font-size: 21px;

    line-height: 1.1;
}


.selected-court-title p {
    margin-top: 4px;

    color: var(--ink-soft);

    font-size: 11px;
}


.detail-price {
    display: flex;

    justify-content: space-between;

    align-items: flex-end;

    margin-top: 26px;

    padding:
        16px 0;

    border-top:
        1px solid var(--line);

    border-bottom:
        1px solid var(--line);
}


.detail-price>span {
    color: var(--ink-faint);

    font-size: 8px;

    letter-spacing: .08em;
}


.detail-price strong {
    color: var(--navy);

    font-family:
        'Space Grotesk',
        sans-serif;

    font-size: 28px;
}


.detail-price small {
    color: var(--ink-soft);

    font-family: Inter, sans-serif;

    font-size: 9px;
}


.detail-list {
    margin-top: 8px;
}


.detail-row {
    display: flex;

    justify-content: space-between;

    gap: 20px;

    padding:
        12px 0;

    border-bottom:
        1px dashed rgba(0,
            27,
            62,
            .10);

    font-size: 12px;
}


.detail-row span {
    color: var(--ink-soft);
}


.detail-row strong {
    color: var(--navy);

    text-align: right;
}


.btn {
    display: inline-flex;

    align-items: center;

    justify-content: center;

    gap: 10px;

    border: 0;

    cursor: pointer;

    border-radius: 9px;

    padding:
        13px 18px;

    font-family:
        'Inter',
        sans-serif;

    font-size: 12px;

    font-weight: 700;

    transition:
        transform .18s ease,
        background .18s ease,
        opacity .18s ease;
}


.btn:hover:not(:disabled) {
    transform:
        translateY(-1px);
}


.btn:disabled {
    opacity: .45;

    cursor: not-allowed;
}


.btn-lime {
    background: #D4ED5B;
    color: var(--navy);
}

.btn-lime:hover:not(:disabled) {
    background: #D4ED5B;
}

.map-cta {
    width: 100%;

    margin-top: 23px;

    padding:
        15px 18px;
}


.map-note {
    display: flex;

    align-items: flex-start;

    gap: 7px;

    margin-top: 12px;

    color: var(--ink-faint);

    font-size: 10px;

    line-height: 1.5;
}


.map-note span {
    color: var(--success);
}


@media(max-width: 900px) {

    .map-layout {
        grid-template-columns: 1fr;
    }

    .map-box {
        min-height: auto;
    }

}


@media(max-width: 520px) {

    .map-header {
        height: auto;

        padding-bottom: 12px;

        align-items: flex-start;

        gap: 10px;
    }

    .map-live {
        display: none;
    }

    .leaflet-container {
        height: 330px;
    }

}


/* ============================================================
   OFFCANVAS
============================================================ */

.offcanvas-backdrop {
    position: fixed;

    inset: 0;

    z-index: 1000;

    background:
        rgba(0,
            15,
            38,
            .66);

    backdrop-filter:
        blur(5px);
}


.offcanvas {
    position: fixed;

    top: 0;
    right: 0;
    bottom: 0;

    z-index: 1010;

    width:
        min(510px,
            100vw);

    display: flex;

    flex-direction: column;

    overflow: hidden;

    background:
        #F5F7EF;

    color: var(--ink);

    border-left:
        1px solid rgba(195,
            221,
            65,
            .2);

    box-shadow:
        -20px 0 60px rgba(0,
            0,
            0,
            .25);
}


/* Drawer header */

.offcanvas-header {
    flex-shrink: 0;

    padding:
        20px 24px;

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 20px;

    background: var(--navy);

    color: var(--paper);

    border-bottom:
        1px solid rgba(195,
            221,
            65,
            .14);
}


.drawer-eyebrow {
    display: block;

    margin-bottom: 5px;

    color: var(--lime);

    font-size: 8px;

    letter-spacing: .12em;
}


.offcanvas-title {
    margin: 0;

    font-family:
        'Space Grotesk',
        sans-serif;

    font-size: 22px;

    font-weight: 700;
}

.btn-close {
    width: 36px;
    height: 36px;
    color: black;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    border: 1px solid rgba(244, 247, 234, .15);
    border-radius: 9px;
    background: rgba(244, 247, 234, .06);
    cursor: pointer;
    transition: background .2s ease, color .2s ease;
}

.btn-close:hover {
    background:
        rgba(195,
            221,
            65,
            .15);

    color: var(--lime);
}


/* Drawer body */

.offcanvas-body {
    flex: 1;

    overflow-y: auto;

    padding:
        22px 24px 28px;

    scrollbar-width: thin;

    scrollbar-color:
        rgba(0,
            27,
            62,
            .15) transparent;
}


.offcanvas-body::-webkit-scrollbar {
    width: 5px;
}


.offcanvas-body::-webkit-scrollbar-thumb {
    background:
        rgba(0,
            27,
            62,
            .15);

    border-radius: 999px;
}


/* ============================================================
   BOOKING COURT MINI CARD
============================================================ */

.booking-court-card {
    display: grid;

    grid-template-columns:
        78px 1fr auto;

    gap: 13px;

    align-items: center;

    padding:
        11px;

    margin-bottom: 28px;

    border:
        1px solid rgba(0,
            27,
            62,
            .09);

    border-radius: 13px;

    background: white;

    box-shadow:
        0 8px 20px rgba(0,
            27,
            62,
            .04);
}


.booking-court-visual {
    width: 78px;
    height: 56px;

    overflow: hidden;

    border-radius: 8px;
}


.booking-court-visual svg {
    display: block;

    width: 100%;
    height: 100%;
}


.booking-court-info {
    display: flex;

    flex-direction: column;

    min-width: 0;
}


.booking-court-info>span {
    color: var(--lime-2);

    font-size: 7px;

    letter-spacing: .08em;
}


.booking-court-info strong {
    margin-top: 3px;

    color: var(--navy);

    font-family:
        'Space Grotesk',
        sans-serif;

    font-size: 14px;

    overflow: hidden;

    text-overflow: ellipsis;

    white-space: nowrap;
}


.booking-court-info small {
    margin-top: 2px;

    color: var(--ink-soft);

    font-size: 9px;

    overflow: hidden;

    text-overflow: ellipsis;

    white-space: nowrap;
}


.booking-court-price {
    color: var(--navy);

    font-family:
        'Space Grotesk',
        sans-serif;

    font-size: 17px;

    font-weight: 700;

    white-space: nowrap;
}


.booking-court-price small {
    color: var(--ink-soft);

    font-family: Inter, sans-serif;

    font-size: 8px;

    font-weight: 500;
}


/* ============================================================
   BOOKING STEPS
============================================================ */

.step {
    padding-top: 25px;

    margin-top: 25px;

    border-top:
        1px dashed rgba(0,
            27,
            62,
            .12);
}


.step-head {
    display: flex;

    align-items: flex-start;

    gap: 12px;

    margin-bottom: 15px;
}


.step-number {
    width: 31px;
    height: 31px;

    display: flex;

    align-items: center;
    justify-content: center;

    flex-shrink: 0;

    border-radius: 9px;

    background: var(--navy);

    color: var(--lime);

    font-family:
        'JetBrains Mono',
        monospace;

    font-size: 9px;

    font-weight: 600;
}


.step-tag {
    display: block;

    margin-bottom: 3px;

    color: var(--lime-2);

    font-size: 8px;

    letter-spacing: .1em;
}


.step-title {
    margin: 0;

    color: var(--navy);

    font-family:
        'Space Grotesk',
        sans-serif;

    font-size: 22px;

    line-height: 1;

    font-weight: 700;
}


/* ============================================================
   CALENDAR
============================================================ */

.cal-card {
    padding:
        16px;

    border:
        1px solid rgba(0,
            27,
            62,
            .09);

    border-radius: 14px;

    background: white;
}


.cal-head {
    display: flex;

    align-items: center;

    justify-content: space-between;

    margin-bottom: 15px;
}


.calendar-label {
    display: block;

    margin-bottom: 3px;

    color: var(--ink-faint);

    font-size: 7px;

    letter-spacing: .08em;
}


.cal-month {
    color: var(--navy);

    font-family:
        'Space Grotesk',
        sans-serif;

    font-size: 19px;

    font-weight: 700;
}


.cal-nav {
    display: flex;

    gap: 5px;
}


.cal-nav button {
    width: 32px;
    height: 32px;

    display: flex;

    align-items: center;
    justify-content: center;

    border:
        1px solid rgba(0,
            27,
            62,
            .10);

    border-radius: 8px;

    background: white;

    color: var(--navy);

    cursor: pointer;

    font-size: 18px;

    transition:
        border-color .2s ease,
        background .2s ease;
}


.cal-nav button:hover:not(:disabled) {
    border-color: var(--lime-2);

    background:
        rgba(195,
            221,
            65,
            .08);
}


.cal-nav button:disabled {
    opacity: .3;

    cursor: not-allowed;
}


.cal-weekdays {
    display: grid;

    grid-template-columns:
        repeat(7, 1fr);

    margin-bottom: 7px;
}


.cal-weekdays span {
    text-align: center;

    color: var(--ink-faint);

    font-family:
        'JetBrains Mono',
        monospace;

    font-size: 8px;
}


.cal-grid {
    display: grid;

    grid-template-columns:
        repeat(7, 1fr);

    gap: 4px;
}


.cal-day {
    width: 100%;
    aspect-ratio: 1;

    max-width: 42px;

    margin: auto;

    display: flex;

    align-items: center;
    justify-content: center;

    border-radius: 10px;

    color: var(--navy);

    font-family:
        'JetBrains Mono',
        monospace;

    font-size: 10px;

    cursor: pointer;

    position: relative;

    transition:
        background .18s ease,
        color .18s ease;
}


.cal-day:hover:not(.disabled):not(.selected) {
    background:
        rgba(195,
            221,
            65,
            .14);
}


.cal-day.disabled {
    color: var(--ink-faint);

    opacity: .3;

    cursor: not-allowed;
}


.cal-day.blocked {
    color: var(--danger);

    text-decoration:
        line-through;
}


.cal-day.today::after {
    content: "";

    position: absolute;

    bottom: 4px;

    left: 50%;

    width: 4px;
    height: 4px;

    transform:
        translateX(-50%);

    border-radius: 50%;

    background: var(--lime-2);
}


.cal-day.selected {
    background: var(--navy);

    color: var(--lime);

    font-weight: 700;

    box-shadow:
        0 4px 12px rgba(0,
            27,
            62,
            .2);
}


.cal-day.selected::after {
    background: var(--lime);
}


.selected-date {
    display: flex;

    align-items: center;

    gap: 6px;

    margin-top: 13px;

    padding:
        8px 10px;

    border-radius: 8px;

    background:
        rgba(195,
            221,
            65,
            .12);

    color: var(--navy);

    font-family:
        'JetBrains Mono',
        monospace;

    font-size: 8px;

    font-weight: 600;
}


.selected-date span {
    color: var(--lime-2);
}


/* ============================================================
   TIME
============================================================ */

.time-date-label {
    margin-bottom: 10px;

    color: var(--ink-faint);

    font-family:
        'JetBrains Mono',
        monospace;

    font-size: 8px;

    letter-spacing: .08em;
}


.time-empty {
    display: flex;

    align-items: center;

    gap: 10px;

    padding:
        15px;

    border:
        1px dashed rgba(0,
            27,
            62,
            .14);

    border-radius: 12px;

    color: var(--ink-soft);

    background:
        rgba(0,
            27,
            62,
            .025);

    font-size: 11px;

    line-height: 1.5;
}


.time-empty span {
    width: 25px;
    height: 25px;

    display: flex;

    align-items: center;
    justify-content: center;

    border-radius: 7px;

    background: var(--navy);

    color: var(--lime);

    font-family:
        'JetBrains Mono',
        monospace;

    font-size: 8px;
}


.slot-grid {
    display: grid;

    grid-template-columns:
        repeat(3, 1fr);

    gap: 7px;
}


.slot {
    min-height: 55px;

    padding:
        9px 5px;

    display: flex;

    flex-direction: column;

    align-items: center;

    justify-content: center;

    gap: 3px;

    border:
        1px solid rgba(0,
            27,
            62,
            .09);

    border-radius: 9px;

    background: white;

    color: var(--navy);

    cursor: pointer;

    transition:
        transform .15s ease,
        background .15s ease,
        border-color .15s ease;
}


.slot:hover:not(:disabled):not(.selected) {
    transform:
        translateY(-1px);

    border-color: var(--lime-2);

    background:
        rgba(195,
            221,
            65,
            .09);
}


.slot-time {
    font-family:
        'JetBrains Mono',
        monospace;

    font-size: 10px;

    font-weight: 600;
}


.slot-status {
    color: var(--ink-faint);

    font-size: 7px;

    text-transform: uppercase;

    letter-spacing: .04em;
}


.slot.selected {
    border-color: var(--lime);

    background: var(--lime);

    color: var(--navy);

    box-shadow:
        0 0 0 2px rgba(195,
            221,
            65,
            .18);
}


.slot.selected .slot-status {
    color: rgba(0,
            27,
            62,
            .65);
}


.slot.taken {
    opacity: .38;

    cursor: not-allowed;

    text-decoration: line-through;

    background:
        rgba(0,
            27,
            62,
            .04);
}


.time-selection-summary {
    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 15px;

    margin-top: 10px;

    padding:
        11px 12px;

    border-radius: 10px;

    background: var(--navy);

    color: var(--paper);
}


.time-selection-summary>div {
    display: flex;

    flex-direction: column;

    gap: 3px;
}


.time-selection-summary .mono {
    color: var(--lime);

    font-size: 7px;

    letter-spacing: .08em;
}


.time-selection-summary strong {
    font-size: 11px;
}


.time-selection-summary button {
    padding: 5px 8px;

    border:
        1px solid rgba(244,
            247,
            234,
            .15);

    border-radius: 6px;

    background: transparent;

    color:
        rgba(244,
            247,
            234,
            .7);

    font-size: 9px;

    cursor: pointer;
}


/* ============================================================
   FORM
============================================================ */

.form-card {
    display: flex;

    flex-direction: column;

    gap: 13px;

    padding:
        17px;

    border:
        1px solid rgba(0,
            27,
            62,
            .09);

    border-radius: 14px;

    background: white;
}


.form-field {
    display: flex;

    flex-direction: column;

    gap: 6px;
}


.form-label {
    color: var(--ink-soft);

    font-size: 8px;

    letter-spacing: .07em;

    text-transform: uppercase;

    font-weight: 700;
}


.form-label .optional {
    color: var(--ink-faint);

    font-family: Inter, sans-serif;

    font-size: 8px;

    font-weight: 400;

    letter-spacing: 0;

    text-transform: lowercase;
}


.form-input {
    width: 100%;

    padding:
        11px 12px;

    border:
        1px solid rgba(0,
            27,
            62,
            .10);

    border-radius: 8px;

    background:
        #FAFBF7;

    color: var(--ink);

    outline: none;

    font-size: 12px;

    transition:
        border-color .2s ease,
        box-shadow .2s ease,
        background .2s ease;
}


.form-input::placeholder {
    color: #A2AAB1;
}


.form-input:focus {
    border-color: var(--lime-2);

    background: white;

    box-shadow:
        0 0 0 3px rgba(195,
            221,
            65,
            .13);
}


.form-textarea {
    resize: vertical;

    min-height: 75px;

    line-height: 1.5;
}


/* ============================================================
   STEPPER
============================================================ */

.stepper {
    width: fit-content;

    display: flex;

    align-items: center;

    gap: 7px;

    padding: 4px;

    border:
        1px solid rgba(0,
            27,
            62,
            .10);

    border-radius: 10px;

    background:
        #FAFBF7;
}


.stepper-btn {
    width: 32px;
    height: 32px;

    display: flex;

    align-items: center;
    justify-content: center;

    border-radius: 7px;

    background: var(--navy);

    color: var(--lime);

    font-size: 17px;

    cursor: pointer;

    transition:
        background .15s ease,
        opacity .15s ease;
}


.stepper-btn:hover:not(:disabled) {
    background: var(--navy-3);
}


.stepper-btn:disabled {
    opacity: .3;

    cursor: not-allowed;
}


.stepper-value {
    min-width: 65px;

    display: flex;

    flex-direction: column;

    align-items: center;

    gap: 1px;
}


.stepper-value strong {
    color: var(--navy);

    font-family:
        'Space Grotesk',
        sans-serif;

    font-size: 16px;
}


.stepper-value span {
    color: var(--ink-faint);

    font-family:
        'JetBrains Mono',
        monospace;

    font-size: 6px;

    letter-spacing: .05em;
}


/* ============================================================
   BOOKING HINT
============================================================ */

.booking-hint {
    display: flex;

    gap: 10px;

    margin-top: 8px;

    padding:
        12px;

    border:
        1px solid rgba(195,
            221,
            65,
            .25);

    border-radius: 10px;

    background:
        rgba(195,
            221,
            65,
            .08);
}


.booking-hint>span {
    width: 23px;
    height: 23px;

    display: flex;

    align-items: center;
    justify-content: center;

    flex-shrink: 0;

    border-radius: 7px;

    background: var(--lime);

    color: var(--navy);

    font-weight: 800;

    font-size: 11px;
}


.booking-hint strong {
    display: block;

    color: var(--navy);

    font-size: 10px;
}


.booking-hint p {
    margin-top: 3px;

    color: var(--ink-soft);

    font-size: 9px;

    line-height: 1.45;
}


/* ============================================================
   DRAWER FOOTER
============================================================ */

.offcanvas-footer {
    flex-shrink: 0;

    padding:
        14px 20px 18px;

    background: white;

    border-top:
        1px solid rgba(0,
            27,
            62,
            .10);

    box-shadow:
        0 -10px 30px rgba(0,
            27,
            62,
            .04);
}


.booking-summary {
    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 15px;

    margin-bottom: 12px;
}


.summary-main {
    display: flex;

    flex-direction: column;

    gap: 2px;
}


.summary-label {
    color: var(--lime-2);

    font-size: 7px;

    letter-spacing: .1em;
}


.summary-main strong {
    color: var(--navy);

    font-family:
        'Space Grotesk',
        sans-serif;

    font-size: 15px;
}


.summary-main small {
    color: var(--ink-soft);

    font-size: 9px;
}


.summary-price {
    display: flex;

    flex-direction: column;

    align-items: flex-end;

    gap: 1px;
}


.summary-price span {
    color: var(--ink-faint);

    font-family:
        'JetBrains Mono',
        monospace;

    font-size: 7px;

    letter-spacing: .08em;
}


.summary-price strong {
    color: var(--navy);

    font-family:
        'Space Grotesk',
        sans-serif;

    font-size: 24px;
}


.confirm-btn {
    width: 100%;

    min-height: 48px;

    border-radius: 10px;

    font-size: 12px;
}


.secure-note {
    margin-top: 8px;

    text-align: center;

    color: var(--ink-faint);

    font-size: 8px;
}


/* ============================================================
   TRANSITIONS
============================================================ */

.slide-right-enter-active,
.slide-right-leave-active {
    transition:
        transform .32s cubic-bezier(.16,
            1,
            .3,
            1);
}


.slide-right-enter-from,
.slide-right-leave-to {
    transform:
        translateX(100%);
}


.fade-enter-active,
.fade-leave-active {
    transition:
        opacity .25s ease;
}


.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}


/* ============================================================
   FOOTER
============================================================ */

footer {
    background: #000F26;

    color:
        rgba(244,
            247,
            234,
            .55);
}


.footer-inner {
    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 40px;

    padding:
        50px 0 35px;
}


.footer-brand {
    max-width: 330px;
}


.footer-brand .brand-word {
    color: var(--paper);
}


.footer-brand p {
    margin-top: 12px;

    color:
        rgba(244,
            247,
            234,
            .45);

    font-size: 11px;

    line-height: 1.6;
}


.footer-links {
    display: flex;

    align-items: center;

    gap: 25px;

    font-size: 11px;
}


.footer-links a {
    transition:
        color .2s ease;
}


.footer-links a:hover {
    color: var(--lime);
}


.foot-bottom {
    display: flex;

    justify-content: space-between;

    gap: 20px;

    padding:
        18px 0 22px;

    border-top:
        1px solid rgba(244,
            247,
            234,
            .08);

    font-family:
        'JetBrains Mono',
        monospace;

    font-size: 8px;

    color:
        rgba(244,
            247,
            234,
            .28);
}


@media(max-width: 700px) {

    .footer-inner {
        align-items: flex-start;

        flex-direction: column;

        gap: 25px;
    }

    .footer-links {
        flex-wrap: wrap;

        gap: 15px;
    }

    .foot-bottom {
        flex-direction: column;

        gap: 7px;
    }

}


/* ============================================================
   MOBILE DRAWER
============================================================ */

@media(max-width: 520px) {

    .offcanvas {
        width: 100vw;
    }

    .offcanvas-header {
        padding:
            17px 18px;
    }

    .offcanvas-body {
        padding:
            18px 17px 25px;
    }

    .offcanvas-footer {
        padding:
            13px 17px 16px;
    }

    .booking-court-card {
        grid-template-columns:
            65px 1fr;

        gap: 10px;
    }

    .booking-court-visual {
        width: 65px;
        height: 48px;
    }

    .booking-court-price {
        display: none;
    }

    .slot-grid {
        grid-template-columns:
            repeat(2, 1fr);
    }

}


/* ============================================================
   SMALL MOBILE
============================================================ */

@media(max-width: 380px) {

    .wrap {
        width:
            calc(100% - 32px);
    }

    .venue-main-info h1 {
        font-size: 34px;
    }

    .slot-grid {
        gap: 6px;
    }

    .slot {
        min-height: 51px;
    }

    .cal-day {
        font-size: 9px;
    }

}
</style>