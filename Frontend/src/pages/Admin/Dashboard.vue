<template>
    <div class="court-bg" aria-hidden="true">
        <div class="court-glow"></div>
    </div>

    <div class="app-shell">
        <header class="site-header">
            <div class="brand">
                <div class="brand-mark-fallback">DY</div>
                <span class="brand-word">Dink<span class="brand-word-accent">Yard</span></span>
            </div>
            <span class="header-tag mono">
                <button class="logout-btn" @click="handleLogout">Log out</button></span>
        </header>

        <nav class="admin-tabs">
            <button v-for="t in TABS" :key="t.key" class="admin-tab" :class="{ active: activeTab === t.key }"
                @click="activeTab = t.key">
                {{ t.label }}
            </button>
        </nav>

        <transition name="fade" mode="out-in">
            <section v-if="activeTab === 'overview'" key="overview" class="tab-panel">
                <div class="stat-grid">
                    <div class="stat-card">
                        <div class="stat-label mono">Total bookings</div>
                        <div class="stat-value">{{ stats.totalBookings }}</div>
                        <div class="stat-sub mono" :class="stats.bookingsDeltaClass">{{ stats.bookingsDeltaLabel }}
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label mono">Revenue (30d)</div>
                        <div class="stat-value">₱{{ stats.revenue30d.toLocaleString() }}</div>
                        <div class="stat-sub mono">Avg ₱{{ stats.avgBookingValue }} / booking</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label mono">Today's bookings</div>
                        <div class="stat-value">{{ stats.todayCount }}</div>
                        <div class="stat-sub mono">{{ stats.todayUpcoming }} upcoming</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label mono">Occupancy</div>
                        <div class="stat-value">{{ stats.occupancyPct }}%</div>
                        <div class="stat-sub mono">slots booked, next 7d</div>
                    </div>
                </div>

                <div class="panel-card">
                    <div class="panel-head">
                        <h3 class="panel-title">Bookings by venue</h3>
                        <span class="panel-sub mono">last 30 days</span>
                    </div>
                    <div class="venue-bars">
                        <div v-for="row in venueBreakdown" :key="row.name" class="venue-bar-row">
                            <div class="venue-bar-label">
                                <span>{{ row.name }}</span>
                                <span class="mono venue-bar-count">{{ row.count }} · ₱{{ row.revenue.toLocaleString()
                                }}</span>
                            </div>
                            <div class="venue-bar-track">
                                <div class="venue-bar-fill" :style="{ width: row.pct + '%' }"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel-card">
                    <div class="panel-head">
                        <h3 class="panel-title">Recent activity</h3>
                        <button class="link-btn mono" @click="activeTab = 'bookings'">View all →</button>
                    </div>
                    <div class="mini-list">
                        <div v-for="b in recentBookings" :key="b.code" class="mini-row" @click="openBooking(b)">
                            <div class="mini-main">
                                <span class="mini-name">{{ b.customerName }}</span>
                                <span class="mini-meta mono">{{ b.venueName }} · {{ b.courtName }} · {{ b.dateLabel
                                }}</span>
                            </div>
                            <span class="status-badge" :class="b.status">{{ b.status }}</span>
                        </div>
                    </div>
                </div>
            </section>

            <section v-else key="bookings" class="tab-panel">
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

                <div class="booking-count mono">{{ filteredBookings.length }} booking{{ filteredBookings.length === 1 ?
                    '' : 's' }}</div>

                <div class="booking-list">
                    <div v-if="filteredBookings.length === 0" class="empty-state">No bookings match those filters.</div>

                    <div v-for="b in filteredBookings" :key="b.code" class="booking-row">
                        <div class="booking-row-main" @click="toggleExpand(b.code)">
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
                            </div>
                        </div>

                        <div class="booking-expand" v-if="expandedCode === b.code">
                            <div class="expand-row"><span class="mono">Phone</span><span>{{ b.phone }}</span></div>
                            <div class="expand-row"><span class="mono">Players</span><span>{{ b.players }}</span></div>
                            <div class="expand-row" v-if="b.notes"><span class="mono">Notes</span><span>{{ b.notes
                            }}</span></div>
                            <div class="expand-actions">
                                <button v-if="b.status === 'confirmed'" class="mini-action-btn danger"
                                    @click="cancelBooking(b)">
                                    Cancel booking
                                </button>
                                <button v-if="b.status === 'cancelled'" class="mini-action-btn"
                                    @click="restoreBooking(b)">
                                    Restore booking
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </section>
        </transition>
    </div>
</template>

<script setup>
import { useAuthStore } from '@/stores/UseAuth';
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';

const VENUES = [
    { id: 'hoopsdome', name: 'Hoopsdome Lapu-Lapu City', price: 200 },
    { id: 'corhigh', name: 'Cordova High School Complex', price: 150 },
    { id: 'cuteys-racquet', name: 'Cuteys Racquet Sports Center', price: 200 },
];

const COURT_NAMES = ['Court 1', 'Court 2', 'Court 3'];
const FIRST_NAMES = ['Juan', 'Maria', 'Jose', 'Ana', 'Mark', 'Liza', 'Paolo', 'Grace', 'Ramon', 'Bea', 'Carlo', 'Nicole'];
const LAST_NAMES = ['Dela Cruz', 'Santos', 'Reyes', 'Bautista', 'Garcia', 'Torres', 'Flores', 'Mendoza'];
const TABS = [
    { key: 'overview', label: 'Overview' },
    { key: 'bookings', label: 'Bookings' },
];

function pad(n) { return String(n).padStart(2, '0'); }
function rand(seed) { seed = (seed * 9301 + 49297) % 233280; return seed / 233280; }

function generateMockBookings(count = 48) {
    const list = [];
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    for (let i = 0; i < count; i++) {
        let s = i * 7 + 13;
        const r1 = rand(s); s++;
        const r2 = rand(s); s++;
        const r3 = rand(s); s++;
        const r4 = rand(s); s++;
        const r5 = rand(s); s++;

        const venue = VENUES[Math.floor(r1 * VENUES.length)];
        const court = COURT_NAMES[Math.floor(r2 * COURT_NAMES.length)];
        const dayOffset = Math.floor(r3 * 21) - 14; // -14 to +6 days
        const dt = new Date(today); dt.setDate(dt.getDate() + dayOffset);
        const hour = 6 + Math.floor(r4 * 15); // 6am - 8pm
        const durationHrs = 1 + Math.floor(r5 * 2); // 1-2 hrs

        const first = FIRST_NAMES[Math.floor(rand(s++) * FIRST_NAMES.length)];
        const last = LAST_NAMES[Math.floor(rand(s++) * LAST_NAMES.length)];
        const players = 2 + Math.floor(rand(s++) * 3);
        const amount = venue.price * durationHrs;

        let status = 'confirmed';
        if (dayOffset < 0) {
            status = rand(s++) < 0.12 ? 'cancelled' : 'completed';
        } else {
            status = rand(s++) < 0.08 ? 'cancelled' : 'confirmed';
        }

        const startLabel = formatHour(hour);
        const endLabel = formatHour(hour + durationHrs);

        list.push({
            code: 'CT-' + (100000 + i * 37).toString(36).toUpperCase().slice(-6),
            customerName: `${first} ${last}`,
            phone: `09${17 + (i % 9)}${String(1000000 + i * 913 % 8999999).padStart(7, '0')}`,
            venueId: venue.id,
            venueName: venue.name,
            courtName: court,
            date: dt,
            dateLabel: dt.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' }),
            timeLabel: `${startLabel} - ${endLabel}`,
            players,
            amount,
            status,
            notes: rand(s) < 0.15 ? 'Bringing own paddles' : '',
        });
    }
    return list.sort((a, b) => b.date - a.date);
}

function formatHour(h24) {
    const period = h24 >= 12 ? 'PM' : 'AM';
    let h = h24 % 12; if (h === 0) h = 12;
    return `${h}:00 ${period}`;
}

const allBookings = ref(generateMockBookings());
const activeTab = ref('overview');
const search = ref('');
const venueFilter = ref('');
const statusFilter = ref('');
const expandedCode = ref(null);
const useAuth = useAuthStore();
const router = useRouter();

function toggleExpand(code) {
    expandedCode.value = expandedCode.value === code ? null : code;
}

function openBooking(b) {
    activeTab.value = 'bookings';
    expandedCode.value = b.code;
}

const handleLogout = async () => {
    await useAuth.logout();
    router.push({ name: 'admin-login' });
}

function cancelBooking(b) {
    const target = allBookings.value.find(x => x.code === b.code);
    if (target) target.status = 'cancelled';
}

function restoreBooking(b) {
    const target = allBookings.value.find(x => x.code === b.code);
    if (target) target.status = target.date < new Date() ? 'completed' : 'confirmed';
}

const filteredBookings = computed(() => {
    const q = search.value.trim().toLowerCase();
    return allBookings.value.filter(b => {
        if (venueFilter.value && b.venueId !== venueFilter.value) return false;
        if (statusFilter.value && b.status !== statusFilter.value) return false;
        if (q && !(b.customerName.toLowerCase().includes(q) || b.phone.includes(q) || b.code.toLowerCase().includes(q))) return false;
        return true;
    });
});

const recentBookings = computed(() => allBookings.value.slice(0, 6));

const stats = computed(() => {
    const now = new Date();
    const thirtyAgo = new Date(now); thirtyAgo.setDate(thirtyAgo.getDate() - 30);
    const todayKey = new Date(now); todayKey.setHours(0, 0, 0, 0);

    const last30 = allBookings.value.filter(b => b.date >= thirtyAgo && b.status !== 'cancelled');
    const revenue30d = last30.reduce((sum, b) => sum + b.amount, 0);
    const totalBookings = allBookings.value.filter(b => b.status !== 'cancelled').length;
    const todayCount = allBookings.value.filter(b => b.date.getTime() === todayKey.getTime()).length;
    const todayUpcoming = allBookings.value.filter(b => b.date.getTime() === todayKey.getTime() && b.status === 'confirmed').length;
    const cancelledCount = allBookings.value.filter(b => b.status === 'cancelled').length;
    const cancelPct = allBookings.value.length ? Math.round((cancelledCount / allBookings.value.length) * 100) : 0;

    const next7 = new Date(now); next7.setDate(next7.getDate() + 7);
    const upcomingSlots = allBookings.value.filter(b => b.date >= todayKey && b.date <= next7 && b.status === 'confirmed').length;
    const capacity = VENUES.length * COURT_NAMES.length * 7 * 1.6; // rough capacity estimate
    const occupancyPct = Math.min(100, Math.round((upcomingSlots / capacity) * 100));

    return {
        totalBookings,
        revenue30d,
        avgBookingValue: last30.length ? Math.round(revenue30d / last30.length) : 0,
        todayCount,
        todayUpcoming,
        occupancyPct,
        bookingsDeltaLabel: `${cancelPct}% cancelled`,
        bookingsDeltaClass: cancelPct > 15 ? 'warn' : 'ok',
    };
});

const venueBreakdown = computed(() => {
    const now = new Date();
    const thirtyAgo = new Date(now); thirtyAgo.setDate(thirtyAgo.getDate() - 30);
    const rows = VENUES.map(v => {
        const bs = allBookings.value.filter(b => b.venueId === v.id && b.date >= thirtyAgo && b.status !== 'cancelled');
        return {
            name: v.name,
            count: bs.length,
            revenue: bs.reduce((s, b) => s + b.amount, 0),
        };
    });
    const max = Math.max(1, ...rows.map(r => r.count));
    return rows.map(r => ({ ...r, pct: Math.round((r.count / max) * 100) })).sort((a, b) => b.count - a.count);
});
</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Space+Mono:wght@400;700&family=Inter:wght@400;500;600;700&display=swap');

:root {
    --court: #1E4F50;
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
    --success: #4E9A6B;
    --success-dim: rgba(78, 154, 107, 0.16);
    --warn: #E8A23A;
    --warn-dim: rgba(232, 162, 58, 0.16);
    --danger: #D9534F;
    --danger-dim: rgba(217, 83, 79, 0.14);
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
    -webkit-font-smoothing: antialiased;
}

.mono {
    font-family: var(--font-mono);
}

button {
    font: inherit;
}

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

.app-shell {
    max-width: 760px;
    margin: 0 auto;
    padding: max(22px, env(safe-area-inset-top)) 18px 60px;
    position: relative;
}

.site-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 22px;
    gap: 12px;
}

.brand {
    display: flex;
    align-items: center;
    gap: 9px;
}

.brand-mark-fallback {
    width: 30px;
    height: 30px;
    border-radius: 7px;
    background: var(--mango);
    color: var(--chalk);
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: var(--font-display);
    font-size: 13px;
}

.brand-word {
    font-family: var(--font-display);
    font-weight: 400;
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

.admin-tabs {
    display: flex;
    gap: 8px;
    margin-bottom: 22px;
}

.admin-tab {
    background: rgba(251, 248, 239, 0.06);
    border: 1px solid var(--line-onteal-strong);
    color: var(--chalk);
    padding: 10px 18px;
    border-radius: 20px;
    cursor: pointer;
    font-size: 13px;
    font-weight: 500;
    transition: background .2s ease, border-color .2s ease;
}

.admin-tab:hover {
    background: rgba(251, 248, 239, 0.1);
}

.admin-tab.active {
    background: var(--mango);
    border-color: var(--mango);
    color: var(--chalk);
}

.tab-panel {
    display: flex;
    flex-direction: column;
    gap: 18px;
}

.stat-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 10px;
}

.stat-card {
    background: var(--chalk);
    border-radius: var(--radius-md);
    padding: 16px;
    box-shadow: 0 8px 20px -14px rgba(0, 0, 0, 0.6);
}

.stat-label {
    font-size: 10px;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--ink-soft);
    margin-bottom: 6px;
}

.stat-value {
    font-family: var(--font-display);
    font-size: 28px;
    color: var(--ink);
    line-height: 1;
}

.stat-sub {
    font-size: 10.5px;
    color: var(--ink-faint);
    margin-top: 6px;
}

.stat-sub.ok {
    color: var(--success);
}

.stat-sub.warn {
    color: var(--mango-deep);
}

.panel-card {
    background: var(--chalk);
    border-radius: var(--radius-lg);
    padding: 18px 16px;
    box-shadow: 0 8px 20px -14px rgba(0, 0, 0, 0.6);
}

.panel-head {
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    margin-bottom: 14px;
}

.panel-title {
    font-family: var(--font-display);
    font-weight: 400;
    font-size: 18px;
    color: var(--ink);
    margin: 0;
    letter-spacing: 0.01em;
}

.panel-sub {
    font-size: 10.5px;
    color: var(--ink-faint);
}

.link-btn {
    background: none;
    border: none;
    color: var(--mango-deep);
    font-size: 11px;
    cursor: pointer;
    padding: 0;
}

.venue-bars {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.venue-bar-row {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.venue-bar-label {
    display: flex;
    justify-content: space-between;
    font-size: 13px;
    color: var(--ink);
}

.venue-bar-count {
    color: var(--ink-soft);
    font-size: 10.5px;
}

.venue-bar-track {
    height: 8px;
    background: var(--chalk-dim);
    border-radius: 8px;
    overflow: hidden;
}

.venue-bar-fill {
    height: 100%;
    background: var(--mango);
    border-radius: 8px;
    transition: width .4s ease;
}

.mini-list {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.mini-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 4px;
    border-bottom: 1px solid var(--line-onchalk);
    cursor: pointer;
    gap: 10px;
}

.mini-row:last-child {
    border-bottom: none;
}

.mini-main {
    display: flex;
    flex-direction: column;
    gap: 2px;
    min-width: 0;
}

.mini-name {
    font-size: 13.5px;
    font-weight: 600;
    color: var(--ink);
}

.mini-meta {
    font-size: 10px;
    color: var(--ink-soft);
}

.status-badge {
    font-size: 9.5px;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    padding: 3px 9px;
    border-radius: 20px;
    white-space: nowrap;
    font-weight: 700;
}

.status-badge.confirmed {
    background: var(--success-dim);
    color: var(--success);
}

.status-badge.completed {
    background: var(--success-dim);
    color: var(--success);
}

.status-badge.cancelled {
    background: var(--danger-dim);
    color: var(--danger);
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
    border: none;
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
    border: none;
    border-radius: var(--radius-sm);
    padding: 12px 10px;
    font-size: 12.5px;
    color: var(--ink);
}

.booking-count {
    font-size: 11px;
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

.booking-row-mid {
    display: flex;
    flex-direction: column;
    gap: 2px;
    text-align: right;
    min-width: 100px;
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
    color: var(--ink);
}

.booking-expand {
    padding: 12px 14px 14px;
    border-top: 1px dashed var(--line-onchalk);
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.expand-row {
    display: flex;
    justify-content: space-between;
    font-size: 12.5px;
    color: var(--ink);
}

.expand-row span:first-child {
    color: var(--ink-soft);
    text-transform: uppercase;
    font-size: 10px;
    letter-spacing: 0.06em;
}

.expand-actions {
    display: flex;
    gap: 8px;
    margin-top: 4px;
}

.mini-action-btn {
    flex: 1;
    padding: 10px;
    border-radius: var(--radius-sm);
    border: 1px solid var(--line-onchalk);
    background: transparent;
    color: var(--ink);
    font-size: 12px;
    cursor: pointer;
}

.mini-action-btn.danger {
    border-color: var(--danger);
    color: var(--danger);
}

.mini-action-btn:hover {
    background: var(--chalk-dim);
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity .2s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

@media (min-width: 640px) {
    .app-shell {
        padding-top: 40px;
    }

    .stat-grid {
        grid-template-columns: repeat(4, 1fr);
    }
}
</style>