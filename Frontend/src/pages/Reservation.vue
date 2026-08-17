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
            <div class="brand" @click="clear();">
                <img :src="dinkYard" alt="" class="brand-mark">
                <span class="brand-word">Court<span class="brand-word-accent">tesy</span></span>
            </div>
            <button v-if="screenKey !== 'confirmed' && hasProgress" class="start-over-link" @click="router.push({ name: 'checkreservation' })">
                Check Reservation
            </button>
            <span v-else-if="screenKey !== 'confirmed'" class="header-tag mono">Reserve a court</span>
        </header>

        <transition name="fade" mode="out-in">
            <div :key="screenKey" class="screen">

                <template v-if="screenKey === 'wizard' || screenKey === 'details'">

                    <div class="rail">
                        <div class="rail-track">
                            <div class="rail-fill" :style="{ width: progressPercent + '%' }"></div>
                        </div>
                        <div class="rail-posts">
                            <div v-for="(label, i) in STEP_LABELS" :key="label" class="rail-post" :class="postClass(i)">
                                <span class="rail-dot"></span>
                                <span class="rail-label mono">{{ label }}</span>
                            </div>
                        </div>
                    </div>

                    <template v-if="screenKey === 'wizard'">
                        <section class="step" v-show="!activeSlug" id="location-section">
                            <div class="step-head">
                                <span class="step-tag mono">On the sideline</span>
                                <h2 class="step-title">Where are you playing?</h2>
                            </div>

                            <button class="locate-btn" @click="findMyLocation" :disabled="locating">
                                <span v-if="!locating">📍 Use my location</span>
                                <span v-else>Locating…</span>
                            </button>
                            <div class="locate-status" :class="{ ok: locateState === 'ok', err: locateState === 'err' }">
                                <span v-if="locateState === 'ok'">Found you — courts sorted nearest first.</span>
                                <span v-else-if="locateState === 'err'">Couldn't get your location. Pick a venue
                                    below.</span>
                                <span v-else>&nbsp;</span>
                            </div>

                            <div class="venue-list">
                                <div v-for="v in paginatedVenues" :key="v.id" class="venue-card" :class="{ selected: venue && venue.id === v.id }" @click="selectVenue(v)">
                                    <div class="venue-main">
                                        <div class="venue-name">
                                            {{ v.name }}
                                            <span class="nearest-badge" v-if="userCoords && v.id === nearestId">NEAREST</span>
                                        </div>
                                        <div class="venue-area">{{ v.area }}</div>
                                        <span class="price-badge">₱{{ v.price }} {{ v.priceDef }}</span>
                                    </div>
                                    <div class="venue-dist mono" v-if="userCoords">{{ distanceTo(v) }}<small>km</small>
                                    </div>
                                    <div class="venue-dist mono" v-else>→</div>
                                </div>

                                <div class="pagination-controls" v-if="totalPage > 1">
                                    <button :disabled="currentPage === 1" @click="currentPage--">‹ Prev</button>
                                    <span class="mono">{{ currentPage }} / {{ totalPage }}</span>
                                    <button :disabled="currentPage === totalPage" @click="currentPage++">Next ›</button>
                                </div>
                            </div>
                        </section>

                        <transition name="fade">
                            <section class="step kitchen-divider" id="court-section" v-show="venue">
                                <div class="step-head">
                                    <span class="step-tag mono">{{ venue?.name }}</span>
                                    <h2 class="step-title">Choose a court</h2>
                                </div>
                                <div class="court-list">
                                    <div v-for="c in courts" :key="c.id" class="court-card" :class="{ selected: court && court.id === c.id }" @click="selectCourt(c)">
                                        <div class="court-thumb">
                                            <svg viewBox="0 0 80 80" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
                                                <rect x="0" y="0" width="80" height="80" :fill="c.base" />
                                                <rect x="8" y="8" width="64" height="64" rx="2" fill="none" :stroke="c.line" stroke-width="2" />
                                                <line x1="8" y1="40" x2="72" y2="40" :stroke="c.line" stroke-width="2" />
                                                <line x1="28" y1="8" x2="28" y2="72" :stroke="c.line" stroke-width="1.2" stroke-dasharray="2 3" />
                                                <line x1="52" y1="8" x2="52" y2="72" :stroke="c.line" stroke-width="1.2" stroke-dasharray="2 3" />
                                                <circle cx="40" cy="40" r="4" :fill="c.ball" />
                                            </svg>
                                        </div>
                                        <div class="court-info">
                                            <div class="court-name">{{ c.name }}</div>
                                            <div class="court-tag mono">{{ c.tag }}</div>
                                            <span class="court-avail mono">{{ c.openCount }} / {{ c.total }} slots open
                                                today</span>
                                        </div>
                                        <div class="court-check" aria-hidden="true">✓</div>
                                    </div>
                                </div>
                            </section>
                        </transition>

                        <transition name="fade">
                            <section class="step kitchen-divider" id="pickDate-section" v-show="court">
                                <div class="step-head">
                                    <span class="step-tag mono">{{ court?.name }}</span>
                                    <h2 class="step-title">Pick a date</h2>
                                </div>
                                <div class="cal-card">
                                    <div class="cal-head">
                                        <div class="cal-month">{{ monthLabel }}</div>
                                        <div class="cal-nav">
                                            <button @click="shiftMonth(-1)" :disabled="!canGoPrevMonth">‹</button>
                                            <button @click="shiftMonth(1)" :disabled="!canGoNextMonth">›</button>
                                        </div>
                                    </div>
                                    <div class="cal-weekdays">
                                        <span v-for="(d, i) in ['S', 'M', 'T', 'W', 'T', 'F', 'S']" :key="i">{{ d
                                            }}</span>
                                    </div>
                                    <div class="cal-grid">
                                        <div v-for="(cell, i) in calendarCells" :key="i" class="cal-day" :class="cellClass(cell)" @click="cell && !isDateBlocked(cell) && selectDate(cell)">
                                            {{ cell ? cell.day : '' }}
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </transition>

                        <transition name="fade">
                            <section class="step kitchen-divider" id="pickTime-section" v-show="date">
                                <div class="step-head">
                                    <span class="step-tag mono">{{ dateLabel }}</span>
                                    <h2 class="step-title" style="color: black !important;">Pick a time{{ timeLabel ? ' — ' + timeLabel : '' }}</h2>
                                </div>
                                <div class="slot-grid">
                                    <button v-for="s in slots" :key="s.time" class="slot" :class="{
                                        taken: s.taken,
                                        reserved: s.reserved,
                                        blocked: s.blocked,
                                        selected: selectedSlots.some(selected => selected.time === s.time)
                                    }" @click="!s.taken && selectSlot(s)">
                                        {{ s.time }}
                                    </button>
                                </div>
                            </section>
                        </transition>
                    </template>

                    <template v-else-if="screenKey === 'details'">
                        <section class="step">
                            <div class="step-head">
                                <span class="step-tag mono">Match card</span>
                                <h2 class="step-title">Confirm the details</h2>
                            </div>
                            <div class="recap-card">
                                <div class="recap-row">
                                    <span class="recap-label mono">Venue</span>
                                    <span class="recap-value">{{ venue?.name }}</span>
                                </div>
                                <div class="recap-row">
                                    <span class="recap-label mono">Court</span>
                                    <span class="recap-value">{{ court?.name }}</span>
                                </div>
                                <div class="recap-row">
                                    <span class="recap-label mono">Date</span>
                                    <span class="recap-value">{{ dateLabel }}</span>
                                </div>
                                <div class="recap-row">
                                    <span class="recap-label mono">Time</span>
                                    <span class="recap-value">{{ timeLabel }}</span>
                                </div>
                            </div>
                        </section>

                        <section class="step kitchen-divider">
                            <div class="step-head">
                                <span class="step-tag mono">Who's playing</span>
                                <h2 class="step-title">Contact & players</h2>
                            </div>
                            <div class="form-card">
                                <div class="form-row two">
                                    <div class="form-field">
                                        <label class="form-label mono">First name</label>
                                        <input v-model="firstName" type="text" class="form-input" placeholder="Juan" autocomplete="given-name">
                                    </div>
                                    <div class="form-field">
                                        <label class="form-label mono">Last name</label>
                                        <input v-model="lastName" type="text" class="form-input" placeholder="Dela Cruz" autocomplete="family-name">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-field">
                                        <label class="form-label mono">Phone number</label>
                                        <input v-model="phone" type="tel" class="form-input" placeholder="09XX XXX XXXX" autocomplete="tel">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-field">
                                        <label class="form-label mono">Email <span class="optional">(optional)</span></label>
                                        <input v-model="email" type="email" class="form-input" placeholder="juan@email.com" autocomplete="email">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-field">
                                        <label class="form-label mono">Players</label>
                                        <div class="stepper">
                                            <button type="button" class="stepper-btn" @click="decPlayers" :disabled="players <= 2">−</button>
                                            <span class="stepper-val mono">{{ players }}</span>
                                            <button type="button" class="stepper-btn" @click="incPlayers" :disabled="players >= 4">+</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-field">
                                        <label class="form-label mono">Notes <span class="optional">(optional)</span></label>
                                        <textarea v-model="notes" class="form-input form-textarea" rows="3" placeholder="Anything the court staff should know?"></textarea>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </template>
                </template>

                <template v-else-if="screenKey === 'confirmed'">
                    <div class="ticket">
                        <div class="ticket-notch left"></div>
                        <div class="ticket-notch right"></div>
                        <div class="ticket-top">
                            <div>
                                <div class="ticket-label mono">Booking confirmed</div>
                                <h2 class="ticket-title">You're on<br><span class="accent">the court.</span></h2>
                            </div>
                        </div>
                        <table class="ticket-table">
                            <tbody>
                                <tr>
                                    <th>Sport</th>
                                    <td>Pickleball</td>
                                </tr>
                                <tr>
                                    <th>Venue</th>
                                    <td>{{ venue.name }}</td>
                                </tr>
                                <tr>
                                    <th>Area</th>
                                    <td>{{ venue.area }}</td>
                                </tr>
                                <tr>
                                    <th>Court</th>
                                    <td>{{ court.name }}</td>
                                </tr>
                                <tr>
                                    <th>Date</th>
                                    <td>{{ dateLabel }}</td>
                                </tr>
                                <tr>
                                    <th>Time</th>
                                    <td>{{ timeLabel }}</td>
                                </tr>
                                <tr>
                                    <th>Total Amount</th>
                                    <td class="amount-cell">
                                        <span class="amount-main">₱{{ court.price * totalHours }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Pay Online</th>
                                    <td class="amount-breakdown">
                                        <div class="amount-line">
                                            <span class="amount-line-label mono">Down Payment</span>
                                            <span class="amount-line-value mono">₱{{ totalHours <= 2 ? (court.price * totalHours) : 350 }}</span>
                                        </div>
                                        <div class="amount-line">
                                            <span class="amount-line-label mono">Reservation Fee</span>
                                            <span class="amount-line-value mono">₱{{ 10 }}</span>
                                        </div>
                                        <div class="amount-line amount-line-total">
                                            <span class="amount-line-label mono">Total Payment Online</span>
                                            <span class="amount-main accent-amount">₱{{ (totalHours <= 2 ? (court.price * totalHours) : 350) + 10 }}</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th style="border: 0;"></th>
                                </tr>
                                <tr>
                                    <th style="font-size: 16px; font-weight: bolder;">Personal Details</th>
                                </tr>
                                <tr>
                                    <th>Booked by</th>
                                    <td>{{ firstName }} {{ lastName }}</td>
                                </tr>
                                <tr>
                                    <th>Contact</th>
                                    <td>{{ phone }}</td>
                                </tr>
                                <tr>
                                    <th>Players</th>
                                    <td>{{ players }}</td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="ticket-code mono">{{ bookingCode }}</div>
                    </div>
                    <div class="post-actions">
                        <button class="action-btn primary" @click="saveBooking">Confirm booking</button>
                        <button class="action-btn" @click="startOver">Book another court</button>
                    </div>
                </template>

            </div>
        </transition>

        <div v-if="screenKey !== 'confirmed' && (venue || court || date || selectedSlots.length > 0)" class="summary-bar">
            <div class="summary-inner">
                <div class="summary-text">
                    <b>Pickleball</b>
                    <span v-if="venue"> · {{ venue.name }}</span>
                    <span v-if="court"> · {{ court.name }}</span>
                    <span v-if="date"> · {{ dateLabel }}</span>
                    <span v-if="selectedSlots.length > 0"> · {{ timeLabel }}</span>
                    <div v-if="selectedSlots.length === 0">Complete the steps above to reserve</div>
                </div>

                <button class="confirm-btn" v-if="screenKey !== 'details' && selectedSlots.length >= 1" @click="confirmSelection">
                    Next
                </button>
                <button class="confirm-btn" v-else :disabled="!canConfirm" @click="confirmBooking">
                    Reserve
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, nextTick, watch, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import dinkYard from '@/component/assets/logo.jpg'
import { TIMES } from '@/constants/times.js'
import { STEP_LABELS } from '@/constants/steplabels';
import { COURT_COLOR_CYCLE } from '@/constants/courtcolor';
import { usePaymentStore } from '@/stores/UsePayment';
import { useVenueStore } from '@/stores/UseVenues';
import { useCourtStore } from '@/stores/UseCourt';
import { useBookingStore } from '@/stores/UseBooking';

const venue = ref(null);
const court = ref(null);
const date = ref(null);
const bookingCode = ref('');
const currentPage = ref(1);

const selectedVenueId = ref(0);
const selectedCourtId = ref(0);

const pageSize = ref(5);
const selectedSlots = ref([]);
const blockedTimes = ref([]);
const reservedTimes = ref([]);
const venueClosedDates = ref([]);
const timeLabel = ref('');
const firstName = ref('');
const lastName = ref('');
const email = ref('');
const phone = ref('');
const players = ref(4);
const notes = ref('');
const totalHours = ref(0)
const activeSlug = ref(null)
const route = useRoute();
const router = useRouter();
const screenKey = ref('wizard');
const usePayment = usePaymentStore();
const useVenue = useVenueStore();
const useCourt = useCourtStore();
const useBooking = useBookingStore();

const VENUES = ref([]);

const hasProgress = computed(() => !!(venue.value || court.value || date.value || selectedSlots.value.length > 0 || firstName.value || lastName.value || phone.value));

const normalize = (t) => t.toString().trim().toUpperCase().replace(/^0/, '');
const totalPage = computed(() => Math.ceil(filteredVenues.value.length / pageSize.value) || 1);
const paginatedVenues = computed(() => {
    const start = (currentPage.value - 1) * pageSize.value;
    const end = start + pageSize.value;
    return filteredVenues.value.slice(start, end);
});
const progressIndex = computed(() => {
    if (!venue.value) return 0;
    if (!court.value) return 1;
    if (!date.value) return 2;
    if (selectedSlots.value.length === 0) return 3;
    return 4;
});
const progressPercent = computed(() => (progressIndex.value / (STEP_LABELS.length - 1)) * 100);
function postClass(i) {
    return {
        done: i < progressIndex.value || (i === 4 && screenKey.value === 'details' && detailsValid.value),
        active: i === progressIndex.value && !(i === 4 && detailsValid.value)
    };
}

function selectSlot(s) {
    if (s.taken) return;
    const alreadySelected = selectedSlots.value.some(selected => selected.time === s.time);
    if (alreadySelected) {
        selectedSlots.value = [];
        timeLabel.value = '';
        return;
    }
    if (selectedSlots.value.length === 0) {
        selectedSlots.value = [s];
        updateTimeLabel();
        return;
    }
    const first = selectedSlots.value[0];
    const firstMinutes = convertToMinutes(first.time);
    const currentMinutes = convertToMinutes(s.time);
    const start = Math.min(firstMinutes, currentMinutes);
    const end = Math.max(firstMinutes, currentMinutes);
    const rangeSlots = slots.value.filter(slot => {
        const minutes = convertToMinutes(slot.time);
        return minutes >= start && minutes <= end;
    });
    if (rangeSlots.some(slot => slot.taken)) return;
    selectedSlots.value = rangeSlots;
    updateTimeLabel();
}

// function selectSlot(s) {
//     if (s.status !== 'open') return;

//     const alreadySelected = selectedSlots.value.some(selected => selected.time === s.time);

//     if (alreadySelected) {
//         selectedSlots.value = [];
//         timeLabel.value = '';
//         return;
//     }

//     if (selectedSlots.value.length === 0) {
//         selectedSlots.value = [s];
//         updateTimeLabel();
//         return;
//     }

//     const first = selectedSlots.value[0];
//     const firstMinutes = convertToMinutes(first.time);
//     const currentMinutes = convertToMinutes(s.time);
//     const start = Math.min(firstMinutes, currentMinutes);
//     const end = Math.max(firstMinutes, currentMinutes);

//     const rangeSlots = slots.value.filter(slot => {
//         const minutes = convertToMinutes(slot.time);
//         return minutes >= start && minutes <= end;
//     });

//     if (rangeSlots.some(slot => slot.status !== 'open')) return;

//     selectedSlots.value = rangeSlots;
//     updateTimeLabel();
// }

const confirmSelection = () => {
    nextTick(() => {
        screenKey.value = 'details';
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
}

function updateTimeLabel() {
    if (selectedSlots.value.length === 0) {
        timeLabel.value = '';
        return;
    }
    const sorted = [...selectedSlots.value].sort((a, b) => convertToMinutes(a.time) - convertToMinutes(b.time));
    const start = sorted[0].time;
    const end = sorted[sorted.length - 1].time;
    const duration = (convertToMinutes(end) - convertToMinutes(start)) / 60 + 1;
    totalHours.value = duration;
    timeLabel.value = `${start} - ${end} (${duration} hours)`;
}

function convertToMinutes(time) {
    const [timePart, period] = time.split(' ');
    let [hour, minute] = timePart.split(':').map(Number);
    if (period === 'PM' && hour !== 12) hour += 12;
    if (period === 'AM' && hour === 12) hour = 0;
    return hour * 60 + minute;
}

function clearSelections() {
    venue.value = null;
    court.value = null;
    date.value = null;
    selectedSlots.value = [];
    timeLabel.value = '';
    firstName.value = '';
    lastName.value = '';
    email.value = '';
    phone.value = '';
    players.value = 4;
    notes.value = '';
    courts.value = [];
}

function incPlayers() {
    if (players.value < 4) players.value++;
}

function decPlayers() {
    if (players.value > 2) players.value--;
}

const clear = async () => {
    localStorage.removeItem('slug');
    activeSlug.value = null;
    await router.push({ name: 'homepage' });
}

function startOver() {
    clearSelections();
    screenKey.value = 'wizard';
    const match = VENUES.value.find(v => v.slug === activeSlug.value); // ✅ fixed
    if (match) selectVenue(match);
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

const userCoords = ref(null);
const locating = ref(false);
const locateState = ref(null);

const today = new Date();
today.setHours(0, 0, 0, 0);
const MAX_DAYS_AHEAD = 21;
const maxDate = new Date(today); maxDate.setDate(maxDate.getDate() + MAX_DAYS_AHEAD);

const viewYear = ref(today.getFullYear());
const viewMonth = ref(today.getMonth());

const filteredVenues = computed(() => {
    let list = VENUES.value;
    if (userCoords.value) {
        list = [...list].sort((a, b) => haversine(userCoords.value, a) - haversine(userCoords.value, b));
    }
    return list;
});

const nearestId = computed(() => {
    if (!userCoords.value || filteredVenues.value.length === 0) return null;
    return filteredVenues.value[0].id;
});

// v.latitude v.longitude
function haversine(u, v) {
    // 1. Guard against missing/null inputs
    if (!u?.latitude || !u?.longitude || !v?.latitude || !v?.longitude) {
        return 0;
    }

    const R = 6371; // Earth radius in kilometers (use 3958.8 for miles)
    const toRad = (deg) => (Number(deg) * Math.PI) / 180;

    const uLat = toRad(u.latitude);
    const vLat = toRad(v.latitude);
    const dLat = toRad(v.latitude - u.latitude);
    const dLng = toRad(v.longitude - u.longitude);

    const a =
        Math.sin(dLat / 2) ** 2 +
        Math.cos(uLat) * Math.cos(vLat) * Math.sin(dLng / 2) ** 2;

    // 2. Clamp `a` strictly between 0 and 1 to prevent NaN from float precision
    const safeA = Math.min(1, Math.max(0, a));

    return R * 2 * Math.atan2(Math.sqrt(safeA), Math.sqrt(1 - safeA));
}

function distanceTo(v) {
    return haversine(userCoords.value, v).toFixed(1);
}

function findMyLocation() {
    if (!navigator.geolocation) {
        locateState.value = 'err';
        return;
    }
    locating.value = true;
    locateState.value = null;
    navigator.geolocation.getCurrentPosition(
        (pos) => {
            userCoords.value = {
                latitude: pos.coords.latitude,
                longitude: pos.coords.longitude
            };
            locating.value = false;
            locateState.value = 'ok';
        },
        () => {
            locating.value = false;
            locateState.value = 'err';
        },
        { timeout: 8000 }
    );
}

function selectVenue(v) {
    venue.value = v;
    selectedVenueId.value = v.id;
    court.value = null;
    date.value = null;
    selectedSlots.value = [];
    timeLabel.value = '';
    loadCourts(v);
    nextTick(() => {
        document.getElementById('court-section')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
}

const courts = ref([]);

async function loadCourts(v) {
    if (!v) {
        courts.value = [];
        return;
    }
    try {
        const response = await useCourt.getCourts({ venue_id: v.id });
        const list = response ?? [];

        courts.value = list.map((c, i) => {
            const color = COURT_COLOR_CYCLE[i % COURT_COLOR_CYCLE.length];
            return {
                ...c,
                price: Number(c.price),
                base: color.base,
                line: color.line,
                ball: color.ball,
                openCount: c.openCount ?? 0,
                total: c.total ?? 0
            };
        });
    } catch (error) {
        console.error(error);
        courts.value = [];
    }
}

const fetchVenueClosedDates = async (id) => {
    try {
        const response = await useVenue.getVenueCloseDateById({ venue_id: id });
        venueClosedDates.value = response ?? [];
    } catch (error) {
        console.error("Failed to fetch venue closed dates:", error);
        venueClosedDates.value = [];
    }
};

function isDateBlocked(cell) {
    if (!cell) return true;
    const isClosed = venueClosedDates.value.some(item => item.closed_date === cell.key);
    return isClosed || cell.disabled;
}

const cellClass = (cell) => {
    if (!cell) return 'empty';
    const closed = venueClosedDates.value.some(item => item.closed_date === cell.key);
    return {
        disabled: isDateBlocked(cell),
        today: cell.isToday,
        selected: date.value === cell.key,
        blocked: closed
    };
};

const slotStatus = (t) => {
    if (!t) return { taken: false, reserved: false, blocked: false };

    const formatted = normalize(t);
    const isReserved = reservedTimes.value.some(slot => normalize(slot) === formatted);
    const isBlocked = !isReserved && blockedTimes.value.some(slot => normalize(slot) === formatted);

    return {
        taken: isReserved || isBlocked,
        reserved: isReserved,
        blocked: isBlocked
    };
};

const fetchCourtSchedule = async (id) => {
    blockedTimes.value = [];
    reservedTimes.value = [];
    try {
        const [closeRes, reserveRes] = await Promise.all([
            useCourt.courtCloseTime({ court_id: id, schedule: date.value }),
            useBooking.getReservation({ venue_id: selectedVenueId.value, court_id: id, booking_date: date.value })
        ]);
        blockedTimes.value = closeRes?.[0]?.closed_times ?? closeRes?.closed_times ?? [];
        reservedTimes.value = reserveRes ?? [];
    } catch (error) {
        console.error(error);
        blockedTimes.value = [];
        reservedTimes.value = [];
    }
}

function selectCourt(c) {
    court.value = c;
    selectedCourtId.value = c.id;
    date.value = null;
    selectedSlots.value = [];
    timeLabel.value = '';
    fetchVenueClosedDates(c.venue_id)
    nextTick(() => {
        document.getElementById('pickDate-section')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
}

function pad(n) { return String(n).padStart(2, '0'); }
function keyOf(y, m, d) { return `${y}-${pad(m + 1)}-${pad(d)}`; }

const monthLabel = computed(() => {
    return new Date(viewYear.value, viewMonth.value, 1).toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
});

const canGoPrevMonth = computed(() => !(viewYear.value === today.getFullYear() && viewMonth.value === today.getMonth()));
const canGoNextMonth = computed(() => {
    const firstOfView = new Date(viewYear.value, viewMonth.value, 1);
    return firstOfView < new Date(maxDate.getFullYear(), maxDate.getMonth(), 1);
});

function shiftMonth(dir) {
    let m = viewMonth.value + dir;
    let y = viewYear.value;
    if (m < 0) { m = 11; y -= 1; }
    if (m > 11) { m = 0; y += 1; }
    viewMonth.value = m;
    viewYear.value = y;
}

const calendarCells = computed(() => {
    const firstDay = new Date(viewYear.value, viewMonth.value, 1);
    const startOffset = firstDay.getDay();
    const daysInMonth = new Date(viewYear.value, viewMonth.value + 1, 0).getDate();
    const cells = [];
    for (let i = 0; i < startOffset; i++) cells.push(null);
    for (let d = 1; d <= daysInMonth; d++) {
        const cellDate = new Date(viewYear.value, viewMonth.value, d);
        cellDate.setHours(0, 0, 0, 0);
        cells.push({
            day: d,
            key: keyOf(viewYear.value, viewMonth.value, d),
            disabled: cellDate < today || cellDate > maxDate,
            isToday: cellDate.getTime() === today.getTime()
        });
    }
    return cells;
});

function selectDate(cell) {
    date.value = cell.key;
    selectedSlots.value = [];
    timeLabel.value = '';
    fetchCourtSchedule(court.value.id);
    nextTick(() => {
        document.getElementById('pickTime-section')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
}

const dateLabel = computed(() => {
    if (!date.value) return '';
    const [y, m, d] = date.value.split('-').map(Number);
    const dt = new Date(y, m - 1, d);
    const isToday = dt.getTime() === today.getTime();
    const label = dt.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' });
    return isToday ? `Today, ${label}` : label;
});

const slots = computed(() => {
    if (!venue.value || !court.value || !date.value) return [];
    return TIMES.map(t => ({
        time: t,
        ...slotStatus(t)
    }));
});

const detailsValid = computed(() => !!(firstName.value.trim() && lastName.value.trim() && phone.value.trim()));

const canConfirm = computed(() => !!(venue.value && court.value && date.value && selectedSlots.value.length > 0 && detailsValid.value));

const saveBooking = async () => {
    try {
        const amount = totalHours.value <= 2 ? (courts.value.price * totalHours.value) : 350;
        const times = selectedSlots.value.map(slot => slot.time);
        const payload = {
            "booking_code": bookingCode.value,
            "venue_id": selectedVenueId.value,
            "court_id": selectedCourtId.value,
            "customer_name": `${firstName.value} ${lastName.value}`,
            "customer_email": email.value,
            "customer_phone": phone.value,
            "booking_date": date.value,
            "start_time": times[0],
            "end_time": times[times.length - 1],
            "hours": totalHours.value,
            "amount": amount,
            "notes": `Player Notes: ${notes.value}. System Notes: Online Payment is ₱${amount} using ONLINE PAYMENT with the total amount of ₱${court.value.price * totalHours.value} and a total player of ${players.value}`,
        }

        await useBooking.createBooking(payload)
        const result = await usePayment.submitPayment(amount * 100, bookingCode.value);

        if (result?.data?.checkout_url) {
            window.location.href = result.data.checkout_url;
        } else {
            console.error("Checkout URL missing from response:", result);
        }
    } catch (error) {
        console.error("Payment submission failed:", error);
    }
};

const generateBookingCode = () => {
    const now = new Date();

    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');

    const hour = String(now.getHours()).padStart(2, '0');

    const letters = Array.from({ length: 3 }, () =>
        String.fromCharCode(65 + Math.floor(Math.random() * 26))
    ).join('');

    const mins = String(now.getMinutes()).padStart(2, '0');
    const secs = String(now.getSeconds()).padStart(2, '0');

    return `${letters}-${year}${hour}${mins}${secs}`;
};

function confirmBooking() {
    if (!canConfirm.value) return;
    bookingCode.value = generateBookingCode();
    screenKey.value = 'confirmed';
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

const todayKey = keyOf(today.getFullYear(), today.getMonth(), today.getDate());

const getListVenues = async () => {
    try {
        const response = await useVenue.getList();
        VENUES.value = response;
    } catch (error) {
        console.error(error)
    }
}

watch(filteredVenues, () => {
    currentPage.value = 1;
});

onMounted(async () => {
    localStorage.removeItem('slug');
    await getListVenues();
})
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Space+Mono:wght@400;700&family=Inter:wght@400;500;600;700&display=swap');

:root {
    /* --court: #1E4F50;
    --court-2: #163C3D;
    --chalk: #FBF8EF;
    --chalk-dim: #F0E9D6;
    --ink: #142523;
    --ink-soft: #587069;
    --ink-faint: #8FA39B;
    --line-onteal: rgba(251, 248, 239, 0.16);
    --line-onteal-strong: rgba(251, 248, 239, 0.4);
    --line-onchalk: rgba(20, 37, 35, 0.10);
    --mango: #F2691C;
    --mango-deep: #C94F0F;
    --mango-dim: rgba(242, 105, 28, 0.14);
    --mango-ink: #2A0F00;
    --success: #4E9A6B;
    --success-dim: rgba(78, 154, 107, 0.16);
    --warn: #E8A23A; */
    --court: #E9E3D3;
    --court-2: #DDD5C0;
    --chalk: #1E2B29;
    --chalk-dim: #33443F;
    --ink: #142523;
    --ink-soft: #587069;
    --ink-faint: #8FA39B;
    --line-onteal: rgba(20, 37, 35, 0.14);
    --line-onteal-strong: rgba(20, 37, 35, 0.32);
    --line-onchalk: rgba(20, 37, 35, 0.10);
    --mango: #F2691C;
    --mango-deep: #C94F0F;
    --mango-dim: rgba(242, 105, 28, 0.14);
    --mango-ink: #2A0F00;
    --success: #4E9A6B;
    --success-dim: rgba(78, 154, 107, 0.16);
    --danger: #D65B5B;
    --danger-dim: rgba(214, 91, 91, 0.14);
    --warn: #E8A23A;
    --radius-lg: 20px;
    --radius-md: 14px;
    --radius-sm: 10px;
    --font-display: 'Bebas Neue', 'Archivo Narrow', sans-serif;
    --font-body: 'Inter', sans-serif;
    --font-mono: 'Space Mono', 'IBM Plex Mono', monospace;


    --radius-lg: 20px;
    --radius-md: 14px;
    --radius-sm: 10px;
    --font-display: 'Bebas Neue', 'Archivo Narrow', sans-serif;
    --font-body: 'Inter', sans-serif;
    --font-mono: 'Space Mono', 'IBM Plex Mono', monospace;
}

* {
    box-sizing: border-box;
    -webkit-tap-highlight-color: transparent;
}

html,
body {
    margin: 0;
    padding: 0;
}

body {
    background: var(--court);
    color: var(--chalk);
    font-family: var(--font-body);
    min-height: 100vh;
    min-height: 100dvh;
    -webkit-font-smoothing: antialiased;
}

.mono {
    font-family: var(--font-mono);
}

button {
    font: inherit;
    -webkit-tap-highlight-color: transparent;
}

button:focus-visible,
.venue-card:focus-visible,
.court-card:focus-visible,
.cal-day:focus-visible {
    outline: 2px solid var(--mango);
    outline-offset: 2px;
}

/* ambient court background */
.court-bg {
    position: fixed;
    inset: 0;
    z-index: -1;
    background: radial-gradient(circle at 18% -8%, var(--court-2) 0%, var(--court) 46%, #123638 100%);
    overflow: hidden;
}

.court-glow {
    position: absolute;
    top: -140px;
    right: -80px;
    width: 380px;
    height: 380px;
    border-radius: 50%;
    background: var(--mango);
    opacity: 0.14;
    filter: blur(90px);
}

.court-lines {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    opacity: 0.5;
}

.court-lines line {
    stroke: var(--line-onteal);
    stroke-width: 2;
}

.app-shell {
    max-width: 640px;
    margin: 0 auto;
    padding: max(22px, env(safe-area-inset-top)) 18px 100px;
    position: relative;
}

/* header */
.site-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 28px;
    gap: 12px;
}

.brand {
    display: flex;
    align-items: center;
    gap: 9px;
}

.brand-mark {
    width: 30px;
    height: 30px;
    flex-shrink: 0;
    display: block;
    border-radius: 7px;
}

.brand-word {
    font-family: var(--font-display);
    font-weight: 400;
    letter-spacing: 0.01em;
    font-size: 22px;
    color: var(--chalk);
    line-height: 1;
}

.brand-word-accent {
    color: var(--mango);
}

.header-tag {
    font-size: 10px;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    color: var(--ink-faint);
}

.start-over-link {
    font-family: var(--font-mono);
    font-size: 10.5px;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    color: var(--chalk);
    background: transparent;
    border: 1px solid var(--line-onteal-strong);
    padding: 9px 14px;
    border-radius: 20px;
    cursor: pointer;
    min-height: 36px;
    transition: border-color .2s ease, background .2s ease;
}

.start-over-link:hover {
    background: rgba(251, 248, 239, 0.08);
    border-color: var(--chalk);
}

/* progress rail — a court sideline with zone markers */
.rail {
    margin-bottom: 30px;
}

.rail-track {
    position: relative;
    height: 2px;
    background-image: linear-gradient(to right, var(--line-onteal-strong) 0 6px, transparent 6px 12px);
    background-size: 12px 2px;
    background-repeat: repeat-x;
    margin: 0 10px 12px;
    border-radius: 2px;
}

.rail-fill {
    position: absolute;
    top: -1px;
    left: 0;
    height: 4px;
    background: var(--mango);
    border-radius: 2px;
    transition: width .5s cubic-bezier(.4, 0, .2, 1);
}

.rail-posts {
    display: flex;
    justify-content: space-between;
}

.rail-post {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    flex: 1;
}

.rail-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: var(--court-2);
    border: 1.5px solid var(--line-onteal-strong);
    transition: background .2s ease, border-color .2s ease, box-shadow .2s ease;
}

.rail-post.done .rail-dot {
    background: var(--mango);
    border-color: var(--mango);
}

.rail-post.active .rail-dot {
    background: var(--court);
    border-color: var(--mango);
    box-shadow: 0 0 0 4px var(--mango-dim);
}

.rail-label {
    font-size: 8.5px;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--ink-faint);
    text-align: center;
}

.rail-post.done .rail-label,
.rail-post.active .rail-label {
    color: var(--chalk);
}

/* step sections */
section.step {
    margin-bottom: 30px;
    animation: rise .35s cubic-bezier(.16, 1, .3, 1);
}

.kitchen-divider {
    padding-top: 26px;
    position: relative;
}

.kitchen-divider::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 1px;
    background-image: linear-gradient(to right, var(--line-onteal-strong) 0 8px, transparent 8px 16px);
    background-size: 16px 1px;
}

@keyframes rise {
    from {
        opacity: 0;
        transform: translateY(6px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (prefers-reduced-motion: reduce) {
    section.step {
        animation: none;
    }
}

.step-head {
    margin-bottom: 14px;
}

.step-tag {
    display: block;
    font-size: 10px;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: var(--mango);
    margin-bottom: 4px;
}

.step-title {
    font-family: var(--font-display);
    font-weight: 400;
    font-size: 26px;
    letter-spacing: 0.01em;
    line-height: 1.05;
    color: var(--chalk);
    margin: 0;
}

.accent {
    color: var(--mango);
}

/* location */
.locate-btn {
    width: 100%;
    background: rgba(251, 248, 239, 0.06);
    border: 1px solid var(--line-onteal-strong);
    color: var(--chalk);
    font-family: var(--font-body);
    font-weight: 500;
    font-size: 13.5px;
    padding: 14px;
    border-radius: var(--radius-sm);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin-bottom: 12px;
    min-height: 50px;
    transition: border-color .2s ease, background .2s ease;
}

.locate-btn:hover {
    border-color: var(--chalk);
    background: rgba(251, 248, 239, 0.1);
}

.locate-btn:disabled {
    opacity: 0.55;
    cursor: default;
}

.locate-status {
    font-family: var(--font-mono);
    font-size: 11.5px;
    color: var(--ink-faint);
    margin: -2px 0 14px;
    min-height: 14px;
}

.locate-status.err {
    color: var(--warn);
}

.locate-status.ok {
    color: #7FD9A8;
}

.venue-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.venue-card {
    background: var(--chalk);
    border: 1px solid transparent;
    border-radius: var(--radius-md);
    padding: 15px 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    cursor: pointer;
    min-height: 66px;
    box-shadow: 0 8px 20px -14px rgba(0, 0, 0, 0.6);
    transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
}

.venue-card:hover {
    transform: translateY(-1px);
}

.venue-card.selected {
    border-color: var(--mango);
    box-shadow: 0 0 0 3px var(--mango-dim);
}

.venue-name {
    font-weight: 700;
    font-size: 14.5px;
    color: var(--ink);
}

.venue-area {
    font-family: var(--font-mono);
    font-size: 10.5px;
    color: var(--ink-soft);
    margin-top: 3px;
}

.nearest-badge {
    color: var(--chalk);
    background: var(--mango);
    padding: 1px 6px;
    border-radius: 20px;
    font-size: 7.5px;
    font-weight: 700;
    letter-spacing: 0.04em;
    margin-left: 6px;
    vertical-align: middle;
}

.price-badge {
    color: var(--success);
    background: var(--success-dim);
    border: 1px solid var(--success);
    padding: 2px 7px;
    border-radius: 20px;
    font-size: 9.5px;
    font-family: var(--font-mono);
    display: inline-block;
    margin-top: 7px;
}

.venue-dist {
    font-size: 15px;
    font-weight: 700;
    color: var(--ink-soft);
    white-space: nowrap;
    text-align: right;
}

.venue-dist small {
    font-size: 9px;
    margin-left: 2px;
}

.venue-card.selected .venue-dist {
    color: var(--mango);
}

.pagination-controls {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 6px 2px 0;
    gap: 8px;
}

.pagination-controls button {
    padding: 8px 14px;
    cursor: pointer;
    border: 1px solid var(--line-onteal-strong);
    background: transparent;
    color: var(--chalk);
    border-radius: var(--radius-sm);
    min-height: 40px;
}

.pagination-controls button:disabled {
    opacity: 0.4;
    cursor: not-allowed;
}

.pagination-controls span {
    color: var(--ink-faint);
    font-size: 11.5px;
}

/* courts */
.court-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.court-card {
    display: flex;
    align-items: center;
    gap: 14px;
    background: var(--chalk);
    border: 1px solid transparent;
    border-radius: var(--radius-md);
    padding: 12px 14px;
    cursor: pointer;
    min-height: 78px;
    box-shadow: 0 8px 20px -14px rgba(0, 0, 0, 0.6);
    transition: transform .15s ease, border-color .15s ease;
}

.court-card:hover {
    transform: translateY(-1px);
}

.court-card.selected {
    border-color: var(--mango);
    box-shadow: 0 0 0 3px var(--mango-dim);
}

.court-thumb {
    width: 58px;
    height: 58px;
    border-radius: 9px;
    overflow: hidden;
    flex-shrink: 0;
}

.court-thumb svg {
    width: 100%;
    height: 100%;
    display: block;
}

.court-info {
    flex: 1;
    min-width: 0;
}

.court-name {
    font-family: var(--font-display);
    font-weight: 400;
    letter-spacing: 0.01em;
    font-size: 17px;
    color: var(--ink);
}

.court-tag {
    font-size: 10.5px;
    color: var(--ink-soft);
    margin-top: 1px;
}

.court-avail {
    display: inline-block;
    margin-top: 7px;
    font-size: 9.5px;
    color: var(--success);
    background: var(--success-dim);
    border: 1px solid var(--success);
    padding: 2px 8px;
    border-radius: 20px;
}

.court-check {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    border: 1.5px solid var(--line-onchalk);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: 700;
    flex-shrink: 0;
    color: transparent;
    transition: background .2s ease, border-color .2s ease, color .2s ease;
}

.court-card.selected .court-check {
    background: var(--mango);
    border-color: var(--mango);
    color: var(--chalk);
}

/* calendar */
.cal-card {
    background: var(--chalk);
    border-radius: var(--radius-lg);
    padding: 18px 16px 20px;
    box-shadow: 0 8px 20px -14px rgba(0, 0, 0, 0.6);
}

.cal-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 14px;
}

.cal-month {
    font-family: var(--font-display);
    font-weight: 400;
    font-size: 19px;
    color: var(--ink);
    letter-spacing: 0.01em;
}

.cal-nav {
    display: flex;
    gap: 6px;
}

.cal-nav button {
    width: 34px;
    height: 34px;
    border-radius: 9px;
    border: 1px solid var(--line-onchalk);
    background: transparent;
    color: var(--ink-soft);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    transition: border-color .2s ease, color .2s ease;
}

.cal-nav button:hover:not(:disabled) {
    border-color: var(--ink-soft);
    color: var(--ink);
}

.cal-nav button:disabled {
    opacity: 0.3;
    cursor: default;
}

.cal-weekdays {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    margin-bottom: 6px;
}

.cal-weekdays span {
    text-align: center;
    font-family: var(--font-mono);
    font-size: 10px;
    color: var(--ink-faint);
    text-transform: uppercase;
}

.cal-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    row-gap: 6px;
}

.cal-day {
    aspect-ratio: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    width: 38px;
    height: 38px;
    border-radius: 50%;
    font-family: var(--font-mono);
    font-size: 13px;
    color: var(--ink);
    cursor: pointer;
    position: relative;
    transition: background .18s ease, color .18s ease;
}

.cal-day:hover:not(.disabled):not(.selected) {
    background: var(--chalk-dim);
}

.cal-day.disabled {
    color: var(--ink-faint);
    cursor: default;
    opacity: 0.4;
}

.cal-day.today::after {
    content: '';
    position: absolute;
    bottom: 4px;
    left: 50%;
    transform: translateX(-50%);
    width: 4px;
    height: 4px;
    border-radius: 50%;
    background: var(--ink-soft);
}

.cal-day.selected {
    color: var(--chalk);
    font-weight: 700;
    background: var(--mango);
}

.cal-day.empty {
    cursor: default;
}

/* time slots */
.slot-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 9px;
}

.slot {
    font-family: var(--font-mono);
    font-size: 12.5px;
    text-align: center;
    padding: 13px 6px;
    border-radius: var(--radius-sm);
    border: none;
    background: var(--chalk);
    cursor: pointer;
    color: var(--ink);
    min-height: 46px;
    box-shadow: 0 6px 16px -12px rgba(0, 0, 0, 0.6);
    transition: box-shadow .2s ease, background .2s ease, color .2s ease;
}

.slot:hover:not(.taken):not(.selected) {
    background: var(--chalk-dim);
}

.slot.taken {
    opacity: 0.35;
    cursor: not-allowed;
    text-decoration: line-through;
    box-shadow: none;
}

.slot.selected {
    background: var(--mango);
    color: var(--chalk);
    box-shadow: 0 0 0 3px var(--mango-dim);
}

/* details form */
.recap-card {
    background: var(--chalk);
    border-radius: var(--radius-lg);
    padding: 6px 18px;
    box-shadow: 0 8px 20px -14px rgba(0, 0, 0, 0.6);
}

.recap-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 13px 0;
    border-bottom: 1px solid var(--line-onchalk);
    gap: 12px;
}

.recap-row:last-child {
    border-bottom: none;
}

.recap-label {
    font-size: 10.5px;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--ink-soft);
}

.recap-value {
    font-weight: 600;
    font-size: 14px;
    color: var(--ink);
    text-align: right;
}

.form-card {
    background: var(--chalk);
    border-radius: var(--radius-lg);
    padding: 18px 16px 20px;
    display: flex;
    flex-direction: column;
    gap: 16px;
    box-shadow: 0 8px 20px -14px rgba(0, 0, 0, 0.6);
}

.form-row {
    display: flex;
    gap: 12px;
}

.form-field {
    display: flex;
    flex-direction: column;
    gap: 6px;
    flex: 1;
    min-width: 0;
}

.form-label {
    font-size: 10.5px;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--ink-soft);
}

.form-label .optional {
    text-transform: none;
    letter-spacing: 0;
    opacity: 0.75;
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

.form-textarea {
    resize: vertical;
    min-height: 72px;
    font-family: var(--font-body);
    line-height: 1.5;
}


.amount-cell {
    display: flex;
    align-items: baseline;
    justify-content: flex-end;
    gap: 8px;
    white-space: nowrap;
}

.amount-main {
    font-size: 15px;
    font-weight: 700;
    color: var(--ink);
}

.amount-sub {
    font-size: 10.5px;
    font-weight: 500;
    color: var(--mango-deep);
    background: var(--mango-dim);
    padding: 2px 7px;
    border-radius: 20px;
}

.stepper {
    display: inline-flex;
    align-items: center;
    gap: 14px;
    background: var(--chalk-dim);
    border-radius: var(--radius-sm);
    padding: 6px 14px;
    width: fit-content;
}

.stepper-btn {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    border: 1px solid var(--line-onchalk);
    background: var(--chalk);
    color: var(--ink);
    font-size: 16px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: border-color .2s ease, color .2s ease, opacity .2s ease;
}

.stepper-btn:hover:not(:disabled) {
    border-color: var(--mango);
    color: var(--mango);
}

.stepper-btn:disabled {
    opacity: 0.35;
    cursor: default;
}

.stepper-val {
    font-size: 15px;
    font-weight: 700;
    min-width: 14px;
    text-align: center;
    color: var(--ink);
}

/* summary bar */
.summary-bar {
    position: fixed;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(18, 54, 56, 0.92);
    backdrop-filter: blur(18px);
    border-top: 1px solid var(--line-onteal);
    padding: 14px 20px calc(14px + env(safe-area-inset-bottom));
}

.summary-inner {
    max-width: 640px;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
}

.summary-text {
    font-size: 12.5px;
    color: var(--ink-faint);
    line-height: 1.4;
}

.summary-text b {
    color: var(--chalk);
    font-weight: 700;
}

.confirm-btn {
    font-family: var(--font-display);
    font-weight: 400;
    letter-spacing: 0.02em;
    font-size: 16px;
    background: var(--mango);
    color: var(--chalk);
    border: none;
    padding: 13px 26px;
    border-radius: var(--radius-sm);
    cursor: pointer;
    white-space: nowrap;
    min-height: 48px;
    transition: opacity .2s ease, transform .15s ease;
}

.confirm-btn:hover:not(:disabled) {
    transform: translateY(-1px);
    opacity: 0.92;
}

.confirm-btn:disabled {
    background: var(--court-2);
    color: var(--ink-faint);
    cursor: not-allowed;
}

/* ticket / confirmation */
.ticket {
    background: var(--chalk);
    border-radius: 22px;
    padding: 28px 22px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 20px 40px -20px rgba(0, 0, 0, 0.6);
}

.ticket-notch {
    position: absolute;
    top: 50%;
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: var(--court);
    transform: translateY(-50%);
}

.ticket-notch.left {
    left: -11px;
}

.ticket-notch.right {
    right: -11px;
}

.ticket-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 18px;
    position: relative;
}

.ticket-label {
    font-size: 10.5px;
    letter-spacing: 0.16em;
    text-transform: uppercase;
    color: var(--ink-faint);
}

.ticket-title {
    font-family: var(--font-display);
    font-weight: 400;
    font-size: 30px;
    line-height: 1.02;
    letter-spacing: 0.01em;
    color: var(--ink);
    margin: 6px 0 0;
}

.ticket-status {
    font-size: 10.5px;
    color: var(--success);
    border: 1px solid var(--success);
    padding: 4px 10px;
    border-radius: 20px;
    white-space: nowrap;
}

.ticket-table {
    width: 100%;
    border-collapse: collapse;
    margin: 18px 0;
}

.ticket-table th,
.ticket-table td {
    padding: 11px 0;
    border-bottom: 1px dashed var(--line-onchalk);
    font-size: 14px;
}

.ticket-table tr:last-child th,
.ticket-table tr:last-child td {
    border-bottom: none;
}

.ticket-table th {
    text-align: left;
    font-weight: 500;
    color: var(--ink-soft);
}

.ticket-table td {
    text-align: right;
    font-weight: 700;
    font-family: var(--font-mono);
    color: var(--ink);
}

.ticket-code {
    margin-top: 6px;
    padding: 14px;
    text-align: center;
    font-size: 20px;
    letter-spacing: 0.24em;
    font-weight: 700;
    color: var(--mango-deep);
    background: var(--mango-dim);
    border-radius: var(--radius-sm);
}

.post-actions {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-top: 20px;
}

.action-btn {
    width: 100%;
    background: transparent;
    border: 1px solid var(--line-onteal-strong);
    color: var(--chalk);
    font-family: var(--font-body);
    font-weight: 500;
    font-size: 13px;
    padding: 14px;
    border-radius: var(--radius-sm);
    cursor: pointer;
    min-height: 50px;
    transition: border-color .2s ease, background .2s ease;
}

.action-btn:hover {
    border-color: var(--chalk);
    background: rgba(251, 248, 239, 0.08);
}

.action-btn.primary {
    background: var(--mango);
    color: var(--chalk);
    border: none;
    font-family: var(--font-display);
    font-weight: 400;
    letter-spacing: 0.02em;
    font-size: 16px;
}

.action-btn.primary:hover {
    opacity: 0.92;
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity .25s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

.slot.reserved {
    opacity: 0.35;
    cursor: not-allowed;
    text-decoration: line-through;
    box-shadow: none;
}

.slot.blocked {
    opacity: 0.35;
    cursor: not-allowed;
    background: repeating-linear-gradient(45deg, var(--chalk-dim), var(--chalk-dim) 4px, var(--chalk) 4px, var(--chalk) 8px);
    box-shadow: none;
}

.slot:hover:not(.reserved):not(.blocked):not(.selected) {
    background: var(--chalk-dim);
}

/* ---- responsiveness ---- */
@media (max-width: 380px) {
    .slot-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .form-row.two {
        flex-direction: column;
    }

    .step-title {
        font-size: 22px;
    }
}

@media (min-width: 640px) {
    .app-shell {
        padding-top: 40px;
    }

    .venue-list {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }

    .pagination-controls {
        grid-column: 1 / -1;
    }

    .court-list {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }
}

@media (min-width: 860px) {
    .app-shell {
        max-width: 760px;
    }

    .cal-day {
        width: 42px;
        height: 42px;
    }
}

.amount-breakdown {
    display: flex;
    flex-direction: column;
    gap: 6px;
    text-align: right;
}

.amount-line {
    display: flex;
    align-items: baseline;
    justify-content: flex-end;
    gap: 10px;
}

.amount-line-label {
    font-size: 10px;
    color: var(--ink-soft);
    text-transform: uppercase;
    letter-spacing: 0.06em;
}

.amount-line-value {
    font-size: 12.5px;
    font-weight: 700;
    color: var(--ink);
}

.amount-line-total {
    margin-top: 4px;
    padding-top: 8px;
    border-top: 1px dashed var(--line-onchalk);
}

.amount-line-total .amount-line-label {
    font-size: 10.5px;
    font-weight: 700;
    color: var(--ink);
}

.amount-main.accent-amount {
    color: var(--mango-deep);
    font-size: 16px;
}
</style>
