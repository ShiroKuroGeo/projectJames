<template>
    <div class="admin-shell">
        <div class="admin-app">
            <header class="admin-header">
                <div class="admin-brand">
                    <span class="admin-brand-word">Court<span class="accent">tesy</span></span>
                    <span class="admin-brand-tag mono">Staff</span>
                </div>
                <div class="admin-who">
                    <span class="admin-who-name">{{ currentAdmin?.name }}</span>
                    <button class="logout-btn" @click="handleLogout">Log out</button>
                </div>
            </header>

            <template v-if="!activeVenue">
                <div class="notice-card">
                    <template v-if="managedVenues.length > 0">
                        <div class="venue-pick-head">
                            <span class="card-tag mono">Select Venue</span>
                            <h2 class="notice-title">Choose a venue to manage</h2>
                            <p class="notice-body">
                                Pick one of your assigned venues below to view its schedule and bookings.
                            </p>
                        </div>
                        <div class="court-tabs">
                            <button v-for="c in managedVenues" :key="c.id" class="court-tab" :class="{ active: currentVenueId === c.id }" @click="changeVenue(c.id)">
                                {{ c.name }}
                            </button>
                        </div>
                    </template>

                    <template v-else>
                        <div class="notice-content">
                            <div class="notice-icon">
                                !
                            </div>
                            <div>
                                <h2 class="notice-title">
                                    No venue assigned
                                </h2>
                                <p class="notice-body">
                                    You don't have a venue assigned to your account yet. <br>
                                    Please contact the developer or the person in charge to have your venue added and
                                    assigned to your account.
                                </p>
                            </div>
                        </div>

                        <div class="venue-action">
                            <div class="venue-action-info">
                                <span class="card-tag mono">
                                    Need Access?
                                </span>
                                <h3 class="venue-action-title">
                                    Venue Assignment Required
                                </h3>
                                <p class="venue-action-description">
                                    Contact the developer or the administrator responsible for managing venues to add
                                    your venue and grant you access. <br>
                                    <a href="mailto:info.alfeser.shiro@gmail.com">Contact Here.</a>
                                </p>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            <template v-else>
                <button class="booking-venue-btn" @click="viewBooking(currentVenueId)" style="margin-bottom: 10px;">
                    <span v-if="tab === 'view'">Booking</span>
                    <span v-else>View</span>
                </button>
                <hr>
                <section class="admin-card" v-show="tab === 'view'">

                    <div class="venue-pick-head">
                        <span class="card-tag mono">Select Venue</span>
                        <h2 class="notice-title">Choose a venue to manage</h2>
                        <p class="notice-body">
                            Pick one of your assigned venues below to view its schedule and bookings.
                        </p>
                    </div>

                    <div class="court-tabs">
                        <button v-for="c in managedVenues" :key="c.id" class="court-tab" :class="{ active: currentVenueId === c.id }" @click="changeVenue(c.id)">
                            {{ c.name }}
                        </button>
                    </div>
                </section>

                <section class="admin-card" v-show="tab === 'view'">
                    <div class="card-head schedule-head">
                        <div>
                            <span class="card-tag mono">Month's Schedule</span>
                            <h2 class="card-title small">{{ scheduleDateLabel }}</h2>
                        </div>
                    </div>

                    <div class="legend">
                        <span class="legend-item"><i class="dot open"></i> Open</span>
                        <span class="legend-item"><i class="dot blocked"></i> Blocked — tap Open to block/unblock</span>
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
                            <div v-for="(cell, i) in calendarCells" :key="i" class="cal-day" :class="cellClass(cell)" @click="cell && !cell.disabled && toggleSchedule(cell)">
                                {{ cell ? cell.day : '' }}
                            </div>
                        </div>
                    </div>
                </section>

                <section class="admin-card" v-show="tab === 'view'">
                    <div v-if="activeCourtId !== 0">
                        <div class="card-head schedule-head">
                            <div>
                                <span class="card-tag mono">Today's schedule</span>
                                <h2 class="card-title small">{{ scheduleDateLabel }}</h2>
                            </div>
                            <input type="date" class="date-input mono" :min="todayKey" :max="maxKey" v-model="scheduleDate" @change="fetchCourtSchedule(activeCourtId)">
                        </div>

                        <div class="court-tabs">
                            <button v-for="c in COURT_NAMES" :key="c.id" class="court-tab" :class="{ active: activeCourtId === c.id }" @click="selectedCourt(c.id)">
                                {{ c.name }}
                            </button>
                        </div>

                        <div class="legend">
                            <span class="legend-item"><i class="dot open"></i> Open</span>
                            <span class="legend-item"><i class="dot reserved"></i> Reserved</span>
                            <span class="legend-item"><i class="dot blocked"></i> Blocked — tap Open to block/unblock</span>
                        </div>

                        <div class="schedule-grid">
                            <div v-for="t in TIMES" :key="t" class="schedule-slot" :class="slotStatus(t)" @click="handleSlotClick(t)">
                                <span class="slot-time mono">{{ t }}</span>
                                <span class="slot-state mono">{{ slotStatusLabel(t) }}</span>
                            </div>
                        </div>
                    </div>
                    <div v-else>
                        <div class="card-head schedule-head">
                            <h2 class="card-title small">Select Court to update closing time</h2>
                        </div>

                        <div class="court-tabs">
                            <button v-for="c in COURT_NAMES" :key="c.id" class="court-tab" :class="{ active: activeCourtId === c.id }" @click="selectedCourt(c.id)">
                                {{ c.name }}
                            </button>
                        </div>

                    </div>
                </section>

                <div class="admin-card" v-show="tab === 'booking'">
                    <div class="filter-bar">
                        <input v-model="search" type="text" class="search-input" placeholder="Search name, phone, or code…">
                        <select v-model="venueFilter" class="filter-select">
                            <option value="">All venues</option>
                            <option v-for="v in VENUES" :key="v.id" :value="v.id">{{ v.name }}</option>
                        </select>
                        <select v-model="statusFilter" class="filter-select">
                            <option value="">All statuses</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>

                    <div class="booking-count mono">{{ filteredBookings.length }} booking{{ filteredBookings.length === 1 ? '' : 's' }}</div>

                    <div class="booking-list">
                        <div v-if="filteredBookings.length === 0" class="empty-state">No bookings match those filters.
                        </div>

                        <div v-for="b in filteredBookings" :key="b.code" class="booking-row">
                            <div class="status-rail" :class="b.status"></div>

                            <div class="booking-row-body">
                                <div class="booking-row-main" :class="{ open: expandedCode === b.code }" @click="toggleExpand(b.code)">
                                    <div class="booking-row-left">
                                        <div class="booking-code mono">{{ b.code }}</div>
                                        <div class="booking-name">{{ b.customerName }}</div>
                                        <div class="booking-meta mono">{{ b.venueName }} · {{ b.courtName }}</div>
                                    </div>
                                    <div class="booking-row-mid">
                                        <div class="booking-date">{{ b.dateLabel }}</div>
                                        <div class="booking-time mono">{{ b.timeLabel }}</div>
                                    </div>
                                    <div class="booking-row-right">
                                        <span class="status-badge" :class="b.status">{{ b.status }}</span>
                                        <div class="booking-amount mono">₱{{ b.amount.toLocaleString() }}</div>
                                        <span class="row-chevron">⌄</span>
                                    </div>
                                </div>

                                <div class="booking-expand" v-if="expandedCode === b.code">
                                    <div class="facts">
                                        <div class="fact">
                                            <span class="fact-label mono">Phone</span>
                                            <span class="fact-value mono">{{ b.phone }}</span>
                                        </div>
                                        <div class="fact">
                                            <span class="fact-label mono">Players</span>
                                            <span class="fact-value">{{ b.players }}</span>
                                        </div>
                                        <div class="fact">
                                            <span class="fact-label mono">Status</span>
                                            <span class="payment-pill" :class="b.status">{{ b.status }}</span>
                                        </div>
                                    </div>

                                    <div class="expand-note" v-if="b.notes">
                                        <span class="note-label mono">Note from customer</span>
                                        <p class="note-text">{{ b.notes }}</p>
                                    </div>

                                    <div class="expand-actions">
                                        <button v-if="b.paymentStatus === 'downpayment'" class="action-btn primary" @click="markFullyPaid(b)">
                                            Mark fully paid
                                        </button>
                                        <button v-if="b.status === 'confirmed'" class="action-btn success" @click="completeBooking(b)">
                                            Mark complete
                                        </button>
                                        <button v-if="b.status === 'pending' || b.paymentStatus === 'pending'" class="action-btn ghost-danger" @click="cancelBooking(b)">
                                            Cancel booking
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </template>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { VENUES, COURT_META } from '@/component/booking/Venues';
import formatHour from '@/composables/useFormat';
import { TIMES } from '@/constants/times';
import { useVenueStore } from '@/stores/UseVenues';
import { useAuthStore } from '@/stores/UseAuth';
import { useCourtStore } from '@/stores/UseCourt';
import { useBookingStore } from '@/stores/UseBooking';
import Swal from 'sweetalert2';

const router = useRouter();
const useVenue = useVenueStore();
const useAuth = useAuthStore();
const useCourt = useCourtStore();
const useBooking = useBookingStore();

const schedules = ref([]);

const COURT_NAMES = ref([]);
const allBookings = ref([]);

const blockedTimes = ref([]);
const reservedTimes = ref([]);

const venueClosedDates = ref([]);

const tab = ref('view');
const search = ref('');
const venueFilter = ref('');
const statusFilter = ref('');
const managedVenues = ref([]);
const activeVenue = ref('');
const expandedCode = ref(null);

const normalize = (t) => t.toString().trim().toUpperCase().replace(/^0/, '');
const currentVenueId = computed(() => activeVenue.value ? activeVenue.value.id : null);

const currentAdmin = computed(() => useAuth.user);

const getListVenues = async () => {
    const response = await useVenue.getAdminVenue();
    managedVenues.value = response;
};

function toggleExpand(code) {
    expandedCode.value = expandedCode.value === code ? null : code;
}



const cancelBooking = async (b) => {
    const result = await Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    });

    if (result.isConfirmed) {
        const target = allBookings.value.find(x => x.code === b.code);
        const response = await useBooking.changeBookingStatus({
            id: target.raw.id,
            status: 'cancelled',
        });
        Swal.fire({
            title: 'Deleted!',
            text: 'Your file has been deleted.',
            icon: 'success'
        });
        if (response) target.status = 'cancelled';

    }
}

const markFullyPaid = async (b) => {
    const result = await Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes confirmed!',
        cancelButtonText: 'Cancel'
    });

    if (result.isConfirmed) {
        const target = allBookings.value.find(x => x.code === b.code);
        const response = await useBooking.changeBookingStatus({
            id: target.raw.id,
            status: 'confirmed',
        });
        if (response) target.status = 'confirmed';
    };
};

const completeBooking = async (b) => {
    const result = await Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, complete it!',
        cancelButtonText: 'Cancel'
    });

    if (result.isConfirmed) {
        const target = allBookings.value.find(x => x.code === b.code);
        const response = await useBooking.changeBookingStatus({
            id: target.raw.id,
            status: 'completed',
        });
        if (response) target.status = 'completed';
    };
};

const filteredBookings = computed(() => {
    const q = search.value.trim().toLowerCase();

    return allBookings.value.filter(b => {
        if (venueFilter.value && b.venueId !== venueFilter.value) return false;
        if (statusFilter.value && b.status !== statusFilter.value) return false;
        if (q && !(
            b.customerName.toLowerCase().includes(q) ||
            b.phone.includes(q) ||
            b.code.toLowerCase().includes(q)
        )) return false;
        return true;
    });
});

async function handleLogout() {
    await useAuth.logout();
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

const monthLabel = computed(() => {
    const date = new Date(viewYear.value, viewMonth.value, 1);
    return date.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
});

const canGoPrevMonth = computed(() => {
    const current = new Date(viewYear.value, viewMonth.value, 1);
    const firstAllowed = new Date(today.getFullYear(), today.getMonth(), 1);
    return current > firstAllowed;
});

const changeVenue = async (id) => {
    const match = managedVenues.value.find(v => String(v.id) === String(id));
    if (!match) return;

    const response = await useCourt.getCourts({ venue_id: id });

    activeVenue.value = match;
    COURT_NAMES.value = response;

    activeCourtId.value = response?.[0]?.id ?? 0;

    fetchCourtSchedule(response?.[0]?.id ?? 0)
    fetchVenueClosedDates(id);
};

const selectedCourt = async (id) => {
    if (activeCourtId.value === id) return;

    activeCourtId.value = id;
    await fetchCourtSchedule(id);
};

const fetchCourtSchedule = async (id) => {
    blockedTimes.value = [];
    reservedTimes.value = [];

    const closeRes = await useCourt.courtCloseTime({
        court_id: id,
        schedule: scheduleDate.value
    });

    const reserveRes = await useBooking.getReservation({
        venue_id: currentVenueId.value,
        booking_date: scheduleDate.value,
        court_id: id
    });

    blockedTimes.value = closeRes?.[0]?.closed_times ?? closeRes?.closed_times ?? [];
    reservedTimes.value = reserveRes
}

const canGoNextMonth = computed(() => {
    const current = new Date(viewYear.value, viewMonth.value, 1);
    const maxMonth = new Date(maxDate.getFullYear(), maxDate.getMonth(), 1);
    return current < maxMonth;
});

const shiftMonth = (amount) => {
    const newDate = new Date(viewYear.value, viewMonth.value + amount, 1);
    viewYear.value = newDate.getFullYear();
    viewMonth.value = newDate.getMonth();
};

const toggleSchedule = async (cell) => {
    if (!cell || cell.disabled || !activeVenue.value?.id) return;

    const existingIndex = venueClosedDates.value.findIndex(
        item => item.closed_date === cell.key
    );

    if (existingIndex !== -1) {
        const existingRecord = venueClosedDates.value[existingIndex];

        try {
            await useVenue.deleteVenueCloseDate({ id: existingRecord.id });

            venueClosedDates.value.splice(existingIndex, 1);
        } catch (error) {
            console.error('Failed to unblock date:', error);
        }

    } else {
        const payload = {
            venue_id: activeVenue.value.id,
            closed_date: cell.key,
            reason: 'Default Reason'
        };

        try {
            const response = await useVenue.setVenueCloseDate(payload);

            const newRecord = response?.data ?? { ...payload, id: response };
            venueClosedDates.value.push(newRecord);

        } catch (error) {
            console.error('Failed to block date:', error);
        }
    }
};

const fetchVenueClosedDates = async (id) => {
    try {
        const response = await useVenue.getVenueCloseDateById({ venue_id: id });
        venueClosedDates.value = response ?? [];
    } catch (error) {
        console.error("Failed to fetch venue closed dates:", error);
        venueClosedDates.value = [];
    }
};

const cellClass = (cell) => {
    if (!cell) return {};

    const isClosed = venueClosedDates.value.some(
        item => item.closed_date === cell.key
    );

    return {
        disabled: cell.disabled,
        today: cell.isToday,
        selected: scheduleDate.value === cell.key,
        open: !isClosed,
        blocked: isClosed
    };
};

const today = new Date();
today.setHours(0, 0, 0, 0);

const maxDate = new Date(today);
maxDate.setDate(maxDate.getDate() + 21);

const viewYear = ref(today.getFullYear());
const viewMonth = ref(today.getMonth());

const keyOf = (year, month, day) => {
    return `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
};

const todayKey = keyOf(today.getFullYear(), today.getMonth(), today.getDate());
const maxKey = keyOf(maxDate.getFullYear(), maxDate.getMonth(), maxDate.getDate());

const scheduleDate = ref(todayKey);
const activeCourtId = ref(0);

const scheduleDateLabel = computed(() => {
    const [y, m, d] = scheduleDate.value.split('-').map(Number);
    return new Date(y, m - 1, d).toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' });
});

const isReserved = async (t) => {
    return false;
    // if (!activeVenue.value) return false;
    // const response = await useCourt.courtCloseTime(activeCourtId.value)
    // console.log('is reserved? ', response[0].closed_times)
}

const isBlocked = async (t) => {
    if (!activeCourtId.value || !t) return false;

    const response = await useCourt.courtCloseTime(activeCourtId.value);

    const blockedTimes = response?.[0]?.closed_times ?? response?.closed_times ?? [];

    const normalize = (t) => t.toString().trim().toUpperCase().replace(/^0/, '');

    const formattedSlot = normalize(t);

    return blockedTimes.some(blocked => normalize(blocked) === formattedSlot);
};

const slotStatus = (t) => {
    if (!t) return 'open';
    const formatted = normalize(t);

    const isReserved = reservedTimes.value.some(slot => normalize(slot) === formatted);
    if (isReserved) return 'reserved';

    const isBlocked = blockedTimes.value.some(slot => normalize(slot) === formatted);
    if (isBlocked) return 'blocked';

    return 'open';
};

function slotStatusLabel(t) {
    const s = slotStatus(t);
    if (s === 'reserved') return 'Reserved';
    if (s === 'blocked') return 'Blocked';
    return 'Open';
}

// getReservation

const handleSlotClick = async (t) => {
    if (!t || !activeCourtId.value || !scheduleDate.value) return;

    const formattedSlot = normalize(t);

    const isReserved = reservedTimes.value.some(x => normalize(x) === formattedSlot);
    if (isReserved) return;

    const isCurrentlyBlocked = blockedTimes.value.some(x => normalize(x) === formattedSlot);

    const next = isCurrentlyBlocked
        ? blockedTimes.value.filter(x => normalize(x) !== formattedSlot)
        : [...blockedTimes.value, t];

    blockedTimes.value = next;

    const payload = {
        court_id: activeCourtId.value,
        closed_date: scheduleDate.value,
        closed_times: next
    };

    try {
        await useCourt.setCourtClosedTime(payload);
    } catch (error) {
        await fetchCourtSchedule(activeCourtId.value);
    }
};

const formatDateLabel = (dateStr) => {
    const [y, m, d] = dateStr.split('-').map(Number);
    const dt = new Date(y, m - 1, d);
    return dt.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' });
};

const mapBooking = (b) => ({
    code: b.booking_code,
    customerName: b.customer_name,
    phone: b.customer_phone,
    email: b.customer_email,
    venueId: b.venue_id,
    venueName: b.venue?.name ?? '—',
    courtName: b.court?.name ?? '—',
    dateLabel: formatDateLabel(b.booking_date),
    timeLabel: `${b.start_time} - ${b.end_time} (${b.hours} hrs)`,
    amount: Number(b.amount),
    status: b.status,
    paymentStatus: b.payment_status,
    paymentMethod: b.payment_method,
    notes: b.notes,
    players: extractPlayers(b.notes),
    raw: b,
});

const extractPlayers = (notes) => {
    const match = notes?.match(/total player of (\d+)/i);
    return match ? Number(match[1]) : null;
};

const viewBooking = async (id) => {
    tab.value = tab.value === 'view' ? 'booking' : 'view';

    try {
        const response = await useBooking.getBookingByVenueId({ id });
        allBookings.value = (response ?? []).map(mapBooking);
    } catch (error) {
        console.error(error);
        allBookings.value = [];
    }
};

watch(scheduleDate, (newDate) => {
    if (!newDate) return;
    const [year, month, day] = newDate.split('-');
    viewYear.value = Number(year);
    viewMonth.value = Number(month) - 1;
});

onMounted(() => {
    getListVenues();
});
</script>
<style>
.admin-shell {
    min-height: 100vh;
    min-height: 100dvh;
    background: radial-gradient(circle at 20% -10%, var(--court-2) 0%, var(--court) 50%, #123638 100%);
    padding: max(24px, env(safe-area-inset-top)) 18px 60px;
}

.admin-app {
    max-width: 720px;
    margin: 0 auto;
}

.accent {
    color: var(--mango);
}

.admin-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 24px;
}

.admin-brand {
    display: flex;
    align-items: baseline;
    gap: 9px;
}

.admin-brand-word {
    font-family: var(--font-display);
    font-size: 22px;
    color: var(--chalk);
}

.admin-brand-tag {
    font-size: 9.5px;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: var(--ink-faint);
    border: 1px solid var(--line-onteal-strong);
    padding: 2px 7px;
    border-radius: 20px;
}

.admin-who {
    display: flex;
    align-items: center;
    gap: 12px;
}

.admin-who-name {
    font-size: 13px;
    color: var(--chalk);
    font-weight: 600;
}

.logout-btn {
    font-family: var(--font-mono);
    font-size: 10.5px;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    color: var(--chalk);
    background: transparent;
    border: 1px solid var(--line-onteal-strong);
    padding: 8px 13px;
    border-radius: 20px;
    cursor: pointer;
    transition: border-color .2s ease, background .2s ease;
}

.logout-btn:hover {
    background: rgba(251, 248, 239, 0.08);
    border-color: var(--chalk);
}

.notice-card {
    background: var(--chalk);
    border-radius: var(--radius-lg);
    padding: 22px 20px;
    box-shadow:
        0 8px 20px -14px rgba(0, 0, 0, 0.6);
    margin-bottom: 16px;
}

/* =========================
   VENUE PICKER STATE
   (managedVenues.length > 0, none selected yet)
========================= */

.venue-pick-head {
    padding-bottom: 18px;
    margin-bottom: 18px;
    border-bottom: 1px solid var(--line-onchalk);
}

.venue-pick-head .card-tag {
    margin-bottom: 8px;
}

.venue-pick-head .notice-title {
    margin-bottom: 6px;
}

.venue-pick-chips {
    gap: 10px;
}

.venue-pick-chips .switch-chip {
    font-size: 13px;
    padding: 10px 18px;
}

/* =========================
   EMPTY VENUE STATE
========================= */

.notice-content {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    padding-bottom: 20px;
    border-bottom: 1px solid var(--line-onchalk);
}

.notice-icon {
    width: 38px;
    height: 38px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    background: var(--chalk-dim);
    border: 1px solid var(--line-onchalk);
    color: var(--ink-faint);
    font-family: var(--font-mono);
    font-size: 18px;
}

.notice-title {
    font-family: var(--font-display);
    font-size: 24px;
    font-weight: 400;
    color: var(--ink);
    margin: 0 0 6px;
}

.notice-body {
    color: var(--ink-soft);
    font-size: 13.5px;
    line-height: 1.6;
    margin: 0;
    max-width: 500px;
}

.venue-switcher {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 16px;
}

/* Layout Wrapper */
.venue-switcher-bar {
    display: flex;
    align-items: center;
    gap: 12px;
    padding-bottom: 20px;
    margin-bottom: 24px;
    border-bottom: 1px solid rgba(21, 67, 68, 0.12);
}

.switcher-label {
    font-family: var(--font-mono);
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.08em;
    color: rgba(21, 67, 68, 0.5);
    text-transform: uppercase;
}

.chip-group {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}

/* Individual Chip Styling */
.switch-chip {
    font-family: var(--font-mono);
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.03em;
    color: #154344;
    background: rgba(21, 67, 68, 0.05);
    border: 1px solid rgba(21, 67, 68, 0.15);
    padding: 5px 12px;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.15s ease;
}

/* .switch-chip {
    color: #154344;                    
    background: rgba(21, 67, 68, 0.05);
    border: 1px solid rgba(21, 67, 68, 0.15);
} */

.switch-chip:hover:not(.active) {
    background: rgba(21, 67, 68, 0.12);
    border-color: rgba(21, 67, 68, 0.3);
}

/* Active State */
.switch-chip.active {
    background: var(--mango, #e66239);
    color: #ffffff;
    border-color: var(--mango, #e66239);
}

.venue-switcher--ondark .switch-chip {
    color: var(--chalk);
    background: rgba(251, 248, 239, 0.08);
    border: 1px solid var(--line-onteal-strong);
}

.venue-switcher--ondark .switch-chip:hover:not(.active) {
    background: rgba(251, 248, 239, 0.16);
    border-color: var(--chalk);
}

.venue-switcher--ondark .switch-chip.active {
    background: var(--mango, #e66239);
    color: #ffffff;
    border-color: var(--mango, #e66239);
}

.chip-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background-color: currentColor;
}

.card-head {
    margin-bottom: 14px;
}

.card-tag {
    display: block;
    font-size: 10px;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: var(--mango);
    margin-bottom: 4px;
}

.card-title {
    font-family: var(--font-display);
    font-weight: 400;
    font-size: 24px;
    letter-spacing: 0.01em;
    color: var(--ink);
    margin: 0;
}

.card-title.small {
    font-size: 19px;
}

.price-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    flex-wrap: wrap;
    padding-top: 4px;
    border-top: 1px solid var(--line-onchalk);
}

.price-row {
    padding-top: 16px;
    margin-top: 4px;
}

.price-label {
    font-size: 10.5px;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--ink-soft);
}

.price-hint {
    font-size: 12px;
    color: var(--ink-faint);
    margin-top: 2px;
}

.price-edit {
    display: flex;
    align-items: center;
    gap: 8px;
}

.peso {
    color: var(--ink-soft);
    font-size: 14px;
}

.price-input {
    width: 84px;
    background: var(--chalk-dim);
    border: 1px solid transparent;
    border-radius: var(--radius-sm);
    padding: 10px 10px;
    font-size: 14px;
    color: var(--ink);
    text-align: right;
}

.price-input:focus {
    outline: none;
    border-color: var(--mango);
    background: var(--chalk);
}

.save-price-btn {
    font-family: var(--font-mono);
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    background: var(--mango);
    color: var(--chalk);
    border: none;
    padding: 10px 14px;
    border-radius: var(--radius-sm);
    cursor: pointer;
    transition: opacity .2s ease;
}

.save-price-btn:disabled {
    background: var(--chalk-dim);
    color: var(--ink-faint);
    cursor: default;
}

.schedule-head {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
}

.date-input {
    background: var(--chalk-dim);
    border: 1px solid transparent;
    border-radius: var(--radius-sm);
    padding: 9px 11px;
    font-size: 12.5px;
    color: var(--ink);
}

.date-input:focus {
    outline: none;
    border-color: var(--mango);
}

.court-tabs {
    display: flex;
    gap: 8px;
    margin: 14px 0 12px;
}

.court-tab {
    flex: 1;
    font-family: var(--font-mono);
    font-size: 11.5px;
    background: var(--chalk-dim);
    border: none;
    color: var(--ink-soft);
    padding: 10px 8px;
    border-radius: var(--radius-sm);
    cursor: pointer;
    transition: background .2s ease, color .2s ease;
}

.court-tab.active {
    background: var(--mango);
    color: var(--chalk);
}

.legend {
    display: flex;
    flex-wrap: wrap;
    gap: 14px;
    margin-bottom: 14px;
}

.legend-item {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 11px;
    color: var(--ink-soft);
}

.dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    display: inline-block;
}

.dot.open {
    background: var(--success);
}

.dot.reserved {
    background: var(--ink-faint);
}

.dot.blocked {
    background: var(--danger);
}

.schedule-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 9px;
}

.schedule-slot {
    border-radius: var(--radius-sm);
    padding: 11px 8px;
    background: var(--success-dim);
    border: 1px solid var(--success);
    cursor: pointer;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 3px;
    transition: transform .12s ease;
}

.schedule-slot:hover {
    transform: translateY(-1px);
}

.schedule-slot.reserved {
    background: var(--chalk-dim);
    border-color: var(--line-onchalk);
    cursor: default;
}

.schedule-slot.reserved:hover {
    transform: none;
}

.schedule-slot.blocked {
    background: var(--danger-dim);
    border-color: var(--danger);
}

.slot-time {
    font-size: 12px;
    font-weight: 700;
    color: var(--ink);
}

.slot-state {
    font-size: 9px;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: var(--ink-soft);
}

.cal-day {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 42px;
    border-radius: 8px;
    cursor: pointer;
    transition: 0.2s;
}

.cal-day.open {
    background: #d1fae5;
    color: #047857;
    border: 1px solid #10b981;
}

.cal-day.blocked {
    background: #fee2e2;
    color: #dc2626;
    border: 1px solid #ef4444;
}

.cal-day.disabled {
    background: #f3f4f6;
    color: #9ca3af;
    cursor: not-allowed;
    opacity: 0.6;
}

.cal-day.today {
    font-weight: 700;
}

.cal-day.selected {
    outline: 2px solid #111827;
    outline-offset: 2px;
}

.venue-action {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    padding-top: 20px;
}

.venue-action-info {
    min-width: 0;
}

.venue-action-title {
    font-family: var(--font-display);
    font-size: 19px;
    font-weight: 400;
    color: var(--ink);
    margin: 0 0 4px;
}

.venue-action-description {
    color: var(--ink-faint);
    font-size: 11.5px;
    line-height: 1.5;
    margin: 0;
    max-width: 440px;
}

.create-venue-btn {
    flex-shrink: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    font-family: var(--font-mono);
    font-size: 10.5px;
    font-weight: 600;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    color: var(--chalk);
    background: var(--mango);
    border: 1px solid var(--mango);
    border-radius: var(--radius-sm);
    padding: 11px 15px;
    cursor: pointer;
    transition:
        background 0.18s ease,
        border-color 0.18s ease,
        transform 0.12s ease,
        box-shadow 0.18s ease;
}

.create-venue-btn:hover {
    background: #e99a24;
    border-color: #e99a24;
    transform: translateY(-1px);
    box-shadow:
        0 6px 14px -7px rgba(0, 0, 0, 0.5);
}

.create-venue-btn:active {
    transform: translateY(0);
    box-shadow: none;
}

.create-venue-btn:focus-visible {
    outline: 2px solid var(--mango);
    outline-offset: 3px;
}

.plus-icon {
    font-size: 16px;
    line-height: 1;
    font-weight: 400;
}

.booking-venue-btn {
    flex-shrink: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    font-family: var(--font-mono);
    font-size: 10.5px;
    font-weight: 600;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    color: var(--chalk);
    background: #176b5f;
    border: 1px solid #176b5f;
    border-radius: var(--radius-sm);
    padding: 11px 15px;
    cursor: pointer;
    box-shadow:
        0 4px 10px -7px rgba(0, 0, 0, 0.45);
    transition:
        background 0.18s ease,
        border-color 0.18s ease,
        transform 0.12s ease,
        box-shadow 0.18s ease;
}

.booking-venue-btn:hover {
    background: #12584f;
    border-color: #12584f;
    transform: translateY(-1px);
    box-shadow:
        0 6px 14px -7px rgba(0, 0, 0, 0.5);
}

.booking-venue-btn:active {
    transform: translateY(0);
    box-shadow:
        0 3px 8px -6px rgba(0, 0, 0, 0.5);
}

.booking-venue-btn:focus-visible {
    outline: 2px solid #176b5f;
    outline-offset: 3px;
}

.booking-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    line-height: 1;
}

.filter-bar {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.search-input {
    flex: 1;
    min-width: 160px;
    background: var(--chalk);
    border: 1px solid rgb(185, 185, 185);
    border-radius: var(--radius-sm);
    padding: 12px 14px;
    font-size: 13.5px;
    color: var(--ink);
}

.search-input::placeholder {
    color: var(--ink-faint);
}

.filter-select {
    background: var(--chalk);
    border: 1px solid rgb(185, 185, 185);
    border-radius: var(--radius-sm);
    padding: 12px 15px;
    font-size: 12.5px;
    color: var(--ink);
}

.booking-count {
    font-size: 11px;
    margin-top: 10px;
    margin-bottom: 10px;
    color: var(--ink-faint);
}

.booking-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.empty-state {
    text-align: center;
    padding: 30px 10px;
    color: var(--ink-faint);
    background: var(--chalk);
    border-radius: var(--radius-md);
    font-size: 13px;
}

.booking-row {
    display: flex;
    background: var(--chalk);
    border-radius: var(--radius-md);
    overflow: hidden;
    box-shadow: 0 6px 16px -12px rgba(0, 0, 0, 0.6);
}

.booking-row-main {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    padding: 13px 14px;
    border: 1px solid rgb(177, 177, 177);
    cursor: pointer;
    flex-wrap: wrap;
}

.booking-row-left {
    display: flex;
    flex-direction: column;
    gap: 2px;
    min-width: 120px;
}

.booking-code {
    font-size: 10px;
    color: var(--mango-deep);
}

.booking-name {
    font-size: 14px;
    font-weight: 700;
    color: var(--ink);
}

.booking-meta {
    font-size: 10.5px;
    color: var(--ink-soft);
}

.status-rail {
    width: 3px;
    flex-shrink: 0;
}

.status-rail.confirmed {
    background: var(--success);
}

.status-rail.completed {
    background: var(--ink-faint);
}

.status-rail.cancelled {
    background: var(--danger);
}

.status-rail.pending {
    background: var(--mango);
}

.booking-row-body {
    flex: 1;
    min-width: 0;
}

.booking-row-right {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 6px;
}

.row-chevron {
    font-size: 11px;
    color: var(--ink-faint);
    transition: transform .15s ease;
}

.booking-row-main.open .row-chevron {
    transform: rotate(180deg);
}

.booking-expand {
    background: var(--chalk-dim);
    border-top: 1px solid var(--line-onchalk);
    padding: 0 14px 16px;
}

.facts {
    display: flex;
    margin-bottom: 14px;
}

.fact {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 4px;
    padding-right: 14px;
}

.fact+.fact {
    border-left: 1px solid var(--line-onchalk);
    padding-left: 14px;
}

.fact-label {
    font-size: 9.5px;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--ink-faint);
}

.fact-value {
    font-size: 14px;
    font-weight: 600;
    color: var(--ink);
    word-break: break-word;
}

.payment-pill {
    display: inline-flex;
    width: fit-content;
    font-family: var(--font-mono);
    font-size: 10.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    padding: 3px 9px;
    border-radius: 20px;
}

.payment-pill.confirmed,
.payment-pill.completed {
    background: var(--success-dim);
    color: #15803d;
}

.payment-pill.cancelled {
    background: var(--danger-dim);
    color: var(--danger);
}

.payment-pill.pending {
    background: #fff4d6;
    color: var(--mango-deep);
}

.expand-note {
    font-size: 13px;
    color: var(--ink-soft);
    line-height: 1.5;
    padding-left: 10px;
    border-left: 2px solid var(--mango);
    margin-bottom: 14px;
}

.note-label {
    display: block;
    font-size: 9.5px;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--mango-deep);
    margin-bottom: 3px;
}

.note-text {
    margin: 0;
}

.expand-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.action-btn {
    font-family: var(--font-mono);
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.03em;
    text-transform: uppercase;
    padding: 10px 14px;
    border-radius: var(--radius-sm);
    border: 1px solid transparent;
    cursor: pointer;
    transition: opacity .15s ease;
}

.action-btn:hover {
    opacity: .85;
}

.action-btn.primary {
    background: #176b5f;
    color: var(--chalk);
}

.action-btn.success {
    background: var(--success);
    color: var(--chalk);
}

.action-btn.ghost-danger {
    background: transparent;
    border-color: var(--danger);
    color: var(--danger);
}

.booking-date {
    font-size: 12.5px;
    color: var(--ink);
}

.booking-time {
    font-size: 10.5px;
    color: var(--ink-soft);
}

.booking-amount {
    font-size: 13px;
    font-weight: 700;
    color: var(--ink);
}

.status-badge {
    display: inline-flex;
    align-items: center;
    font-family: var(--font-mono);
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    padding: 3px 9px;
    border-radius: 20px;
}

.status-badge.confirmed {
    background: var(--success-dim);
    color: #15803d;
}

.status-badge.completed {
    background: var(--chalk-dim);
    color: var(--ink-soft);
}

.status-badge.cancelled {
    background: var(--danger-dim);
    color: var(--danger);
}

.status-badge.pending {
    background: #fff4d6;
    color: var(--mango-deep);
}

@media (max-width: 560px) {

    .notice-card {
        padding: 20px 16px;
    }

    .notice-content {
        gap: 11px;
    }

    .notice-title {
        font-size: 21px;
    }

    .venue-action {
        align-items: stretch;
        flex-direction: column;
        gap: 14px;
    }

    .create-venue-btn {
        width: 100%;
    }

    .booking-venue-btn {
        width: 100%;
    }

    .facts {
        flex-direction: column;
        gap: 10px;
    }

    .fact {
        padding-right: 0;
    }

    .fact+.fact {
        border-left: none;
        border-top: 1px solid var(--line-onchalk);
        padding-left: 0;
        padding-top: 10px;
    }

    .venue-pick-chips .switch-chip {
        flex: 1;
        min-width: 88px;
        text-align: center;
    }
}

@media (min-width: 640px) {
    .schedule-grid {
        grid-template-columns: repeat(4, 1fr);
    }
}
</style>
