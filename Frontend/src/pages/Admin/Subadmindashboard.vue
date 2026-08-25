<template>
    <div class="admin-shell">
        <div class="admin-app">
            <header class="admin-header">
                <div class="admin-brand">
                    <img :src="logo" class="brand-logo-mark" alt="" />
                    <span class="admin-brand-word">Dink<span class="accent">Yard</span></span>
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
                            {{ currentVenueId }}
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
                <button class="booking-venue-btn" @click="viewBooking(currentVenueId)">
                    <span v-if="tab === 'view'">Booking</span>
                    <span v-else>View</span>
                </button>

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
                                            <span class="fact-label mono">Down Payment Status</span>
                                            <span class="fact-value" style="text-transform: capitalize;">{{ b.paymentStatus }}</span>
                                        </div>
                                        <div class="fact">
                                            <span class="fact-label mono">Status</span>
                                            <span class="payment-pill" :class="b.status">{{ b.status }}</span>
                                        </div>
                                    </div>

                                    <div class="expand-note" v-if="b.notes">
                                        <span class="note-label mono">Note from customer</span>
                                        <p class="note-text" v-html="b.notes"></p>
                                    </div>


                                    <div class="expand-note payment-proof" v-if="b.raw?.submitted_payment?.image">
                                        <img :src="image(b.raw.submitted_payment.image)" alt="Payment proof" class="payment-proof-image" @click="openPaymentModal(b.raw.submitted_payment.image)">
                                    </div>

                                    <div v-if="showPaymentModal" class="payment-modal" @click.self="closePaymentModal">
                                        <div class="payment-modal-content">
                                            <button class="payment-modal-close" type="button" @click="closePaymentModal">
                                                ×
                                            </button>

                                            <img v-if="selectedPaymentImage" :src="image(selectedPaymentImage)" alt="Payment proof" class="payment-modal-image">
                                        </div>
                                    </div>

                                    <!-- <div class="expand-note">
                                        <img :src="image(b.submitted_payment.image)" alt="">
                                    </div> -->

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
import logo from '@/component/assets/logo.jpg';
import { image } from '@/utils/image';

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

const showPaymentModal = ref(false);
const selectedPaymentImage = ref(null);

const openPaymentModal = (image) => {
    selectedPaymentImage.value = image;
    showPaymentModal.value = true;
};

const closePaymentModal = () => {
    showPaymentModal.value = false;
    selectedPaymentImage.value = null;
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

            const result = await Swal.fire({
                title: 'Are you sure?',
                text: 'Do you want to set this venue close date?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, set it!',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33'
            });

            if (result.isConfirmed) {
                try {
                    const response = await useVenue.setVenueCloseDate(payload);

                    const newRecord = response?.data ?? { ...payload, id: response };
                    venueClosedDates.value.push(newRecord);

                    await Swal.fire({
                        title: 'Success!',
                        text: 'Venue close date has been set.',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } catch (error) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Failed to set venue close date.',
                        icon: 'error'
                    });
                }
            }

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

    // Dynamic action text based on whether we are blocking or unblocking
    const action = isCurrentlyBlocked ? 'unblock' : 'block';

    // 1. Show confirmation alert
    const result = await Swal.fire({
        title: 'Are you sure?',
        text: `Do you want to ${action} ${t} on ${scheduleDate.value}?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: `Yes, ${action} it!`,
        cancelButtonText: 'Cancel',
        confirmButtonColor: isCurrentlyBlocked ? '#3085d6' : '#d33'
    });

    if (!result.isConfirmed) return;

    // 2. Prepare payload
    const next = isCurrentlyBlocked
        ? blockedTimes.value.filter(x => normalize(x) !== formattedSlot)
        : [...blockedTimes.value, t];

    // Optimistic UI update
    const previousBlockedTimes = [...blockedTimes.value];
    blockedTimes.value = next;

    const payload = {
        court_id: activeCourtId.value,
        closed_date: scheduleDate.value,
        closed_times: next
    };

    // 3. Perform API call
    try {
        await useCourt.setCourtClosedTime(payload);
    } catch (error) {
        // Revert state and refresh schedule on error
        blockedTimes.value = previousBlockedTimes;
        await fetchCourtSchedule(activeCourtId.value);

        Swal.fire({
            title: 'Error!',
            text: 'Failed to update court closed times.',
            icon: 'error'
        });
    }
};


// const handleSlotClick = async (t) => {
//     if (!t || !activeCourtId.value || !scheduleDate.value) return;

//     const formattedSlot = normalize(t);

//     const isReserved = reservedTimes.value.some(x => normalize(x) === formattedSlot);
//     if (isReserved) return;

//     const isCurrentlyBlocked = blockedTimes.value.some(x => normalize(x) === formattedSlot);

//     const next = isCurrentlyBlocked
//         ? blockedTimes.value.filter(x => normalize(x) !== formattedSlot)
//         : [...blockedTimes.value, t];

//     blockedTimes.value = next;

//     const payload = {
//         court_id: activeCourtId.value,
//         closed_date: scheduleDate.value,
//         closed_times: next
//     };

//     try {
//         await useCourt.setCourtClosedTime(payload);
//     } catch (error) {
//         await fetchCourtSchedule(activeCourtId.value);
//     }
// };

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
    submitted_payment: b.submitted_payment,
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

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500;600&display=swap');

.admin-shell {
    --navy: #001B3E;
    --navy-2: #04264F;
    --navy-3: #0B3568;
    --lime: #C3DD41;
    --lime-2: #9FB92F;
    --amber: #E2A73E;
    --amber-dim: rgba(226, 167, 62, 0.16);
    --danger: #C33C29;
    --danger-dim: rgba(196, 60, 41, 0.1);
    --cream: #F4F7EA;
    --paper: #FBFCF7;
    --ink: #04101F;
    --ink-soft: #4A5A6B;
    --ink-faint: rgba(4, 16, 31, 0.4);
    --line: rgba(0, 27, 62, 0.09);
    --radius-lg: 16px;
    --radius-md: 12px;
    --radius-sm: 8px;

    min-height: 100vh;
    min-height: 100dvh;

    background: var(--paper);
    color: var(--ink);
    font-family: 'Inter', sans-serif;
    -webkit-font-smoothing: antialiased;

    padding: max(24px, env(safe-area-inset-top)) 18px 60px;
}

.admin-shell h1,
.admin-shell h2,
.admin-shell h3,
.admin-brand-word {
    font-family: 'Space Grotesk', sans-serif;
}

.mono {
    font-family: 'JetBrains Mono', monospace;
    letter-spacing: 0.06em;
}

.admin-shell a {
    color: var(--lime-2);
    font-weight: 600;
}

.admin-app {
    max-width: 980px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    gap: 18px;
}

.accent {
    color: var(--lime);
}

/* =========================================================
   HEADER
   ========================================================= */

.admin-header {
    position: relative;
    overflow: hidden;

    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;

    padding: 18px 22px;
    border-radius: var(--radius-lg);

    background: radial-gradient(900px 400px at 10% -30%, #06305e 0%, var(--navy) 55%), var(--navy);
    border: 1px solid rgba(196, 221, 65, 0.16);
}

.admin-header::after {
    content: "";
    position: absolute;
    inset: 0;
    background-image:
        repeating-linear-gradient(0deg, rgba(196, 221, 65, 0.05) 0 1px, transparent 1px 48px),
        repeating-linear-gradient(90deg, rgba(196, 221, 65, 0.05) 0 1px, transparent 1px 48px);
    pointer-events: none;
}

.admin-brand {
    position: relative;
    z-index: 1;
    display: flex;
    align-items: center;
    gap: 10px;
}

.brand-logo-mark {
    width: 30px;
    height: 30px;
    border-radius: 8px;
    background: var(--lime);
}

.admin-brand-word {
    font-size: 19px;
    font-weight: 700;
    letter-spacing: -0.01em;
    color: var(--paper);
}

.admin-brand-tag {
    font-size: 9.5px;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: var(--lime);
    border: 1px solid rgba(196, 221, 65, 0.35);
    background: rgba(196, 221, 65, 0.1);
    padding: 3px 9px;
    border-radius: 20px;
}

.admin-who {
    position: relative;
    z-index: 1;
    display: flex;
    align-items: center;
    gap: 12px;
}

.admin-who-name {
    font-size: 13px;
    color: var(--paper);
    font-weight: 600;
}

.logout-btn {
    font-family: 'JetBrains Mono', monospace;
    font-size: 10.5px;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    color: var(--paper);
    background: rgba(11, 53, 104, 0.6);
    border: 1px solid rgba(196, 221, 65, 0.3);
    padding: 8px 13px;
    border-radius: 20px;
    cursor: pointer;
    transition: border-color .2s ease, background .2s ease;
}

.logout-btn:hover {
    background: var(--lime);
    color: var(--navy);
    border-color: var(--lime);
}

/* =========================================================
   CARD SHELLS (notice-card / admin-card)
   ========================================================= */

.notice-card,
.admin-card {
    position: relative;
    overflow: hidden;

    background: #fff;
    border: 1px solid var(--line);
    border-radius: var(--radius-lg);
    padding: 24px 22px;
    box-shadow: 0 10px 26px -20px rgba(0, 27, 62, 0.35);
}

.notice-card::before,
.admin-card::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--lime);
}

/* =========================================================
   VENUE PICKER
   ========================================================= */

.venue-pick-head {
    padding-bottom: 16px;
    margin-bottom: 16px;
    border-bottom: 1px dashed var(--line);
}

.venue-pick-head .card-tag {
    margin-bottom: 8px;
}

.venue-pick-head .notice-title {
    margin-bottom: 6px;
}

/* =========================================================
   EMPTY VENUE STATE
   ========================================================= */

.notice-content {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    padding-bottom: 20px;
    border-bottom: 1px dashed var(--line);
}

.notice-icon {
    width: 38px;
    height: 38px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    background: var(--cream);
    border: 1px solid var(--line);
    color: var(--lime-2);
    font-family: 'JetBrains Mono', monospace;
    font-size: 18px;
}

.notice-title {
    font-size: 23px;
    font-weight: 700;
    letter-spacing: -0.01em;
    color: var(--navy);
    margin: 0 0 6px;
}

.notice-body {
    color: var(--ink-soft);
    font-size: 13.5px;
    line-height: 1.6;
    margin: 0;
    max-width: 500px;
}

.card-tag {
    display: block;
    font-size: 10px;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: var(--lime-2);
    margin-bottom: 4px;
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
    font-size: 18px;
    font-weight: 700;
    color: var(--navy);
    margin: 0 0 4px;
}

.venue-action-description {
    color: var(--ink-soft);
    font-size: 12px;
    line-height: 1.6;
    margin: 0;
    max-width: 440px;
}

/* =========================================================
   CARD HEAD / TITLES
   ========================================================= */

.card-head {
    margin-bottom: 14px;
}

.card-title {
    font-weight: 700;
    font-size: 22px;
    letter-spacing: -0.01em;
    color: var(--navy);
    margin: 0;
    text-transform: uppercase;
}

.card-title.small {
    font-size: 17px;
}

.schedule-head {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
}

.date-input {
    background: var(--cream);
    border: 1px solid var(--line);
    border-radius: var(--radius-sm);
    padding: 9px 11px;
    font-size: 12.5px;
    color: var(--ink);
}

.date-input:focus {
    outline: none;
    border-color: var(--lime-2);
}

/* =========================================================
   TABS
   ========================================================= */

.court-tabs {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin: 14px 0 12px;
}

.court-tab {
    flex: 1;
    min-width: 96px;
    font-family: 'JetBrains Mono', monospace;
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.02em;
    background: var(--cream);
    border: 1px solid transparent;
    color: var(--ink-soft);
    padding: 10px 8px;
    border-radius: var(--radius-sm);
    cursor: pointer;
    transition: background .2s ease, color .2s ease, border-color .2s ease;
}

.court-tab:hover {
    border-color: rgba(196, 221, 65, 0.4);
}

.court-tab.active {
    background: var(--lime);
    color: var(--navy);
    border-color: var(--lime);
}

/* =========================================================
   LEGEND
   ========================================================= */

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
    background: var(--lime-2);
}

.dot.reserved {
    background: var(--ink-faint);
}

.dot.blocked {
    background: var(--danger);
}

.cal-card {
    border: 1px solid var(--line);
    border-radius: var(--radius-md);
    padding: 14px;
    background: var(--paper);
}

.cal-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 10px;
}

.cal-month {
    font-family: 'Space Grotesk', sans-serif;
    font-weight: 700;
    font-size: 15px;
    color: var(--navy);
}

.cal-nav {
    display: flex;
    gap: 6px;
}

.cal-nav button {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    border: 1px solid var(--line);
    background: #fff;
    color: var(--navy);
    font-size: 15px;
    line-height: 1;
    cursor: pointer;
    transition: background .15s ease, border-color .15s ease;
}

.cal-nav button:hover:not(:disabled) {
    background: var(--cream);
    border-color: var(--lime-2);
}

.cal-nav button:disabled {
    opacity: .3;
    cursor: default;
}

.cal-weekdays {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    margin-bottom: 6px;
}

.cal-weekdays span {
    text-align: center;
    font-family: 'JetBrains Mono', monospace;
    font-size: 10px;
    letter-spacing: 0.08em;
    color: var(--ink-faint);
}

.cal-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 5px;
}

.cal-day {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 40px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 13px;
    color: var(--ink);
    border: 1px solid transparent;
    transition: transform .12s ease, background .15s ease;
}

.cal-day.open {
    background: rgba(196, 221, 65, 0.14);
    color: var(--lime-2);
    border-color: rgba(196, 221, 65, 0.45);
}

.cal-day.open:hover {
    transform: translateY(-1px);
}

.cal-day.blocked {
    background: var(--danger-dim);
    color: var(--danger);
    border-color: rgba(196, 60, 41, 0.35);
}

.cal-day.disabled {
    background: var(--cream);
    color: var(--ink-faint);
    cursor: not-allowed;
    opacity: 0.7;
}

.cal-day.today {
    font-weight: 700;
}

.cal-day.selected {
    outline: 2px solid var(--navy);
    outline-offset: 2px;
}

/* =========================================================
   SCHEDULE SLOTS
   ========================================================= */

.schedule-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 9px;
}

@media (min-width: 640px) {
    .schedule-grid {
        grid-template-columns: repeat(4, 1fr);
    }
}

.schedule-slot {
    border-radius: var(--radius-sm);
    padding: 11px 8px;
    background: rgba(196, 221, 65, 0.1);
    border: 1px solid rgba(196, 221, 65, 0.4);
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
    background-color: rgb(203, 255, 202);
    border-color: var(--line);
    cursor: default;
}

.schedule-slot.reserved:hover {
    transform: none;
}

.schedule-slot.blocked {
    background-color: rgb(255, 169, 169);
    border-color: rgba(196, 60, 41, 0.35);
}

.slot-time {
    font-size: 12px;
    font-weight: 700;
    color: var(--navy);
}

.slot-state {
    font-size: 9px;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: var(--ink-soft);
}

/* =========================================================
   BOOKING VENUE / VIEW TOGGLE
   ========================================================= */

.booking-venue-btn {
    align-self: flex-start;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    font-family: 'JetBrains Mono', monospace;
    font-size: 10.5px;
    font-weight: 700;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    color: var(--paper);
    background: var(--navy-3);
    border: 1px solid var(--navy-3);
    border-radius: var(--radius-sm);
    padding: 11px 16px;
    cursor: pointer;
    transition: background 0.18s ease, transform 0.12s ease;
}

.booking-venue-btn:hover {
    background: var(--navy);
    transform: translateY(-1px);
}

.booking-venue-btn:active {
    transform: translateY(0);
}

/* =========================================================
   BOOKING FILTERS
   ========================================================= */

.filter-bar {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.search-input,
.filter-select {
    background: #fff;
    border: 1px solid rgba(0, 27, 62, 0.14);
    border-radius: var(--radius-sm);
    padding: 12px 14px;
    font-size: 13px;
    color: var(--ink);
    transition: border-color .18s ease, box-shadow .18s ease;
}

.search-input {
    flex: 1;
    min-width: 160px;
}

.search-input::placeholder {
    color: var(--ink-faint);
}

.search-input:focus,
.filter-select:focus {
    outline: none;
    border-color: var(--lime-2);
    box-shadow: 0 0 0 3px rgba(196, 221, 65, 0.18);
}

.booking-count {
    font-size: 11px;
    margin-top: 10px;
    margin-bottom: 10px;
    color: var(--ink-faint);
}

/* =========================================================
   BOOKING LIST
   ========================================================= */

.booking-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.empty-state {
    text-align: center;
    padding: 30px 10px;
    color: var(--ink-faint);
    background: var(--cream);
    border-radius: var(--radius-md);
    font-size: 13px;
}

.booking-row {
    display: flex;
    background: #fff;
    border-radius: var(--radius-md);
    overflow: hidden;
    border: 1px solid var(--line);
    box-shadow: 0 6px 16px -14px rgba(0, 27, 62, 0.4);
}

.status-rail {
    width: 4px;
    flex-shrink: 0;
}

.status-rail.confirmed {
    background: var(--lime-2);
}

.status-rail.completed {
    background: var(--ink-faint);
}

.status-rail.cancelled {
    background: var(--danger);
}

.status-rail.pending {
    background: var(--amber);
}

.booking-row-body {
    flex: 1;
    min-width: 0;
}

.booking-row-main {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    padding: 13px 14px;
    cursor: pointer;
    flex-wrap: wrap;
    transition: background .15s ease;
}

.booking-row-main:hover {
    background: rgba(196, 221, 65, 0.06);
}

.booking-row-left {
    display: flex;
    flex-direction: column;
    gap: 2px;
    min-width: 120px;
}

.booking-code {
    font-size: 10px;
    color: var(--lime-2);
}

.booking-name {
    font-size: 14px;
    font-weight: 700;
    color: var(--navy);
}

.booking-meta {
    font-size: 10.5px;
    color: var(--ink-soft);
}

.booking-date {
    font-size: 12.5px;
    color: var(--ink);
}

.booking-time {
    font-size: 10.5px;
    color: var(--ink-soft);
}

.booking-row-right {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 6px;
}

.booking-amount {
    font-size: 13px;
    font-weight: 700;
    color: var(--navy);
}

.row-chevron {
    font-size: 11px;
    color: var(--ink-faint);
    transition: transform .15s ease;
}

.booking-row-main.open .row-chevron {
    transform: rotate(180deg);
}

.status-badge,
.payment-pill {
    display: inline-flex;
    width: fit-content;
    font-family: 'JetBrains Mono', monospace;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    padding: 3px 9px;
    border-radius: 20px;
}

.status-badge.confirmed,
.payment-pill.confirmed {
    background: rgba(196, 221, 65, 0.16);
    color: var(--lime-2);
}

.status-badge.completed,
.payment-pill.completed {
    background: var(--cream);
    color: var(--ink-soft);
}

.status-badge.cancelled,
.payment-pill.cancelled {
    background: var(--danger-dim);
    color: var(--danger);
}

.status-badge.pending,
.payment-pill.pending {
    background: var(--amber-dim);
    color: #97701f;
}

/* =========================================================
   BOOKING EXPAND DETAILS
   ========================================================= */

.booking-expand {
    background: var(--cream);
    border-top: 1px dashed var(--line);
    padding: 16px 14px;
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
    border-left: 1px solid var(--line);
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
    color: var(--navy);
    word-break: break-word;
}

.expand-note {
    font-size: 13px;
    color: var(--ink-soft);
    line-height: 1.5;
    padding-left: 10px;
    border-left: 2px solid var(--lime-2);
    margin-bottom: 14px;
}

.note-label {
    display: block;
    font-size: 9.5px;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--lime-2);
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
    font-family: 'JetBrains Mono', monospace;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.03em;
    text-transform: uppercase;
    padding: 10px 14px;
    border-radius: var(--radius-sm);
    border: 1px solid transparent;
    cursor: pointer;
    transition: opacity .15s ease, transform .12s ease;
}

.action-btn:hover {
    transform: translateY(-1px);
}

.action-btn.primary {
    background: var(--navy-3);
    color: var(--paper);
}

.action-btn.success {
    background: var(--lime);
    color: var(--navy);
}

.action-btn.ghost-danger {
    background: transparent;
    border-color: var(--danger);
    color: var(--danger);
}

/* =========================================================
   RESPONSIVE
   ========================================================= */

@media (max-width: 560px) {

    .notice-card,
    .admin-card {
        padding: 20px 16px;
    }

    .notice-content {
        gap: 11px;
    }

    .notice-title {
        font-size: 20px;
    }

    .venue-action {
        align-items: stretch;
        flex-direction: column;
        gap: 14px;
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
        border-top: 1px solid var(--line);
        padding-left: 0;
        padding-top: 10px;
    }
}


.payment-proof-image {
    width: 180px;
    max-height: 180px;
    object-fit: cover;
    border-radius: 8px;
    cursor: pointer;
    transition: transform 0.2s ease;
}

.payment-proof-image:hover {
    transform: scale(1.03);
}

.payment-modal {
    position: fixed;
    inset: 0;
    z-index: 9999;

    display: flex;
    align-items: center;
    justify-content: center;

    padding: 24px;
    background: rgba(0, 0, 0, 0.75);
}

.payment-modal-content {
    position: relative;
    max-width: 90vw;
    max-height: 90vh;
}

.payment-modal-image {
    display: block;
    max-width: 90vw;
    max-height: 85vh;
    object-fit: contain;
    border-radius: 8px;
}

.payment-modal-close {
    position: absolute;
    top: -40px;
    right: 0;

    border: 0;
    background: transparent;

    color: white;
    font-size: 32px;
    cursor: pointer;
}
</style>