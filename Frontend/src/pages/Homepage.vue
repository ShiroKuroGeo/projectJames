<script setup>

import { useCourtStore } from '@/stores/UseCourt'
import { useVenueStore } from '@/stores/UseVenues';
import { image } from '@/utils/image';
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router';
import logo from '@/component/assets/logo.jpg'

const stats = ref([])

const useCourt = useCourtStore();
const useVenue = useVenueStore();
const route = useRoute();
const router = useRouter();

const featuredFacility = ref([])
const courts = ref([])

const features = ref([
    {
        icon: '◱',
        title: 'Court Availability',
        description: 'Check available courts and time slots before making a reservation.',
    },
    {
        icon: '◎',
        title: 'Quick Reservations',
        description: 'Book your preferred court and schedule through a simple and straightforward process.',
    },
    {
        icon: '▤',
        title: 'Booking Management',
        description: 'View, track, and manage your reservations with booking details and reference numbers.',
    },
    {
        icon: '▲',
        title: 'Venue Management',
        description: 'Help court owners manage their facilities, courts, schedules, and incoming reservations.',
    },
])


// Done
const roles = ref([
    {
        num: 'PLAYERS',
        title: 'Players',
        sub: 'For players',
        items: [
            'Find and book available courts with ease',
            'Manage reservations and booking references in one place',
            'Spend less time coordinating, more time playing',
        ],
    },
    {
        num: 'OWNERS',
        title: 'Court Owners',
        sub: 'For court owners',
        items: [
            'Manage courts, schedules, and reservations from one dashboard',
            'Keep bookings organized and easy to verify',
            'Improve court occupancy and daily operations',
        ],
    },
    {
        num: 'ADMINS',
        title: 'Platform Admins',
        sub: 'For administrators',
        items: [
            'Manage venues, owners, courts, and user accounts',
            'Monitor reservations and platform activity',
            'Keep the entire booking system organized and reliable',
        ],
    },
    {
        num: 'COMMUNITIES',
        title: 'Sports Communities',
        sub: 'For sports communities',
        items: [
            'Discover local courts and active sports facilities',
            'Make group and recurring games easier to organize',
            'Connect more players through a simpler booking experience',
        ],
    },
])

const fetchVenues = async () => {
    const response = await useVenue.getList();
    featuredFacility.value = response.find(ar => ar.venue.is_featured === true || ar.venue.is_featured === 1);
    const lowestStartingPrice = response.flatMap(venueAdmin =>
        venueAdmin.venue?.courts?.map(court => Number(court.price)) ?? []
    )

    stats.value = [
        {
            label: 'FACILITIES',
            value: response.length
        }
        // ,
        // {
        //     label: 'COURTS LISTED',
        //     value: response.reduce((total, venueAdmin) => {
        //         return total + (venueAdmin.venue?.courts?.length ?? 0)
        //     }, 0)
        // },
        // {
        //     label: 'STARTING RATE',
        //     value: lowestStartingPrice.length
        //         ? Math.min(...lowestStartingPrice)
        //         : 350,
        //     isCurrency: true
        // },
    ]
    courts.value = response;
}

const NoMoreFacilities = () => {
    alert('There is no more such facilities. Please stay tuned!');
}

onMounted(async () => {
    await fetchVenues();
})

</script>

<template>
    <div class="book-the-court-page">
        <header>
            <nav>
                <div class="logo">
                    <img :src="logo" class="logo-mark" alt="">
                    <span>Court-<span style="color: white;">tesy</span></span>
                </div>
                <div class="nav-actions">
                    <button class="btn btn-solid" href="#" @click="router.push({ name: 'checkreservation' })">Confirm Booking</button>
                    <button class="btn btn-solid" href="#" @click="router.push({ name: 'admin-login' })">Login</button>
                </div>
            </nav>
        </header>

        <section class="hero">
            <div class="wrap hero-inner">
                <div>
                    <span class="eyebrow mono">Cordova Cebu</span>
                    <h1>Play courts.<br>Book <em>fast.</em></h1>
                    <p class="lede">
                        Browse facilities, lock in open time slots, and manage every booking through one clean platform built for players and owners alike.
                    </p>
                    <div class="hero-cta">
                        <a class="btn btn-lime" href="#courts">Book a Court Now</a>
                    </div>

                    <div class="stat-row">
                        <div v-for="stat in stats" :key="stat.label" class="stat-cell">
                            <div class="k mono">{{ stat.label }}</div>
                            <div class="v">
                                <span v-if="stat.isCurrency">₱</span>{{ stat.value }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="court-card">
                    <div class="tag">
                        <span class="tag-label mono">FEATURED FACILITY</span>
                        <span class="price">₱{{ featuredFacility.venue?.courts[0]?.price }} / hr</span>
                    </div>
                    <h3>{{ featuredFacility.venue?.name }}</h3>
                    <p class="loc">{{ featuredFacility.venue?.area }}</p>

                    <div class="court-svg-wrap">
                        <svg viewBox="0 0 300 170" width="100%" height="auto">
                            <rect x="6" y="6" width="288" height="158" rx="6" fill="none" stroke="#C3DD41" stroke-width="2" opacity="0.85" />
                            <line x1="150" y1="6" x2="150" y2="164" stroke="#C3DD41" stroke-width="2" stroke-dasharray="5 5" opacity="0.5" />
                            <line x1="97" y1="6" x2="97" y2="164" stroke="#C3DD41" stroke-width="1.4" opacity="0.35" />
                            <line x1="203" y1="6" x2="203" y2="164" stroke="#C3DD41" stroke-width="1.4" opacity="0.35" />
                            <line x1="6" y1="85" x2="294" y2="85" stroke="#C3DD41" stroke-width="1" opacity="0.2" />
                            <circle class="ball" cx="20" cy="80" r="5" fill="#C3DD41" />
                        </svg>
                    </div>

                    <div class="court-foot">
                        <div>
                            <div class="k mono">COURTS</div>
                            <div class="v">{{ featuredFacility.venue?.courts.length }}</div>
                        </div>
                        <div>
                            <div class="k mono">OPEN SLOTS</div>
                            <div class="v">{{ featuredFacility.venue?.courts.length }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="courts">
            <div class="wrap">
                <div class="section-head">
                    <span class="kicker mono">FIND YOUR COURT</span>
                    <h2>Your next game starts here</h2>
                    <p>
                        Browse available courts, explore different venues, and book your preferred
                        time without the usual back-and-forth.
                    </p>
                </div>
                <div class="courts-grid">
                    <div v-for="court in courts" :key="court.id" :class="{ 'court-item': court.venue?.courts?.length >= 1 }">
                        <div class="court-banner" v-if="court.venue.courts.length >= 1">
                            <span class="chip">{{ court.venue.courts.length }} open courts</span>
                            <div class="monogram"><img :src="image(court.user.image)" alt=""></div>
                        </div>
                        <div class="court-body" v-if="court.venue.courts.length >= 1">
                            <h4>{{ court.venue.name }}</h4>
                            <p class="loc">{{ court.venue.area }}</p>
                            <div class="court-foot-row">
                                <span class="rate">From <b>₱{{ court.venue.courts[0]?.price }}</b>/hr</span>
                                <a class="view-link" :href="'linkme/' + court.venue?.slugs">
                                    View Schedule →
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="center-cta">
                    <a class="btn btn-outline-navy" href="#" @click="NoMoreFacilities">View All Facilities</a>
                </div>
            </div>
        </section>

        <section class="features" id="how">
            <div class="wrap">
                <div class="section-head">
                    <span class="kicker mono">BUILT FOR THE GAME</span>
                    <h2>Smarter court booking, from search to play</h2>
                    <p>
                        DinkYard brings players and court owners together with a simpler way to discover,
                        reserve, and manage courts.
                    </p>
                </div>

                <div class="feat-grid">
                    <div class="flow-panel">
                        <div class="flow-top">
                            <span class="flow-badge mono">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2" />
                                    <line x1="3" y1="10" x2="21" y2="10" />
                                </svg>
                                HOW IT WORKS
                            </span>

                            <span class="flow-live">Simple & Fast</span>
                        </div>

                        <div class="flow-card">
                            <div class="" v-for="(feat, index) in features" :key="feat.title">
                                <div class="flow-step">
                                    <div class="" style="display:flex; align-items: center; gap: 8px;">
                                        <div class="feat-icon">{{ feat.icon }}</div>
                                        <div class="step-number">0{{ index + 1 }}</div>
                                    </div>
                                    <div class="step-content">
                                        <div class="flow-note">{{ feat.title }}</div>
                                        <div class="flow-desc">{{ feat.description }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flow-footer">
                            <span class="flow-status-dot"></span>
                            <span>Booking system ready</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section>
            <div class="wrap">
                <div class="section-head">
                    <span class="kicker mono">BUILT TOGETHER</span>
                    <h2>One place for every game</h2>
                    <p>
                        DinkYard connects players, court owners, and communities through a simpler
                        way to discover, book, and manage courts.
                    </p>
                </div>

                <div class="roles-grid">
                    <div v-for="role in roles" :key="role.num" class="role-card">
                        <div class="role-num mono">{{ role.num }}</div>
                        <h3>{{ role.title }}</h3>
                        <p class="sub">{{ role.sub }}</p>
                        <ul>
                            <li v-for="(item, idx) in role.items" :key="idx">{{ item }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
        <footer id="faq">
            <div class="wrap">
                <div class="simple-footer">

                    <div class="footer-main">
                        <div class="footer-brand">
                            <div class="logo">
                                <div class="logo-mark"></div>
                                Dink<span>Yard</span>
                            </div>

                            <p>
                                A simpler way to discover, book, and manage your favorite courts.
                            </p>
                        </div>

                        <div class="footer-links">
                            <a href="#" @click="NoMoreFacilities">Courts</a>
                            <a href="#" @click="NoMoreFacilities">How It Works</a>
                            <a href="#" @click="NoMoreFacilities">FAQ</a>
                            <a href="#" @click="NoMoreFacilities">Contact</a>
                        </div>
                    </div>

                    <div class="foot-bottom">
                        <span>© 2026 DinkYard. All rights reserved.</span>

                        <div class="footer-legal">
                            <a href="#" @click="NoMoreFacilities">Privacy</a>
                            <a href="#" @click="NoMoreFacilities">Terms</a>
                        </div>
                    </div>

                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500;600&display=swap');

.book-the-court-page {
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
    -webkit-font-smoothing: antialiased;
}

h1,
h2,
h3,
.display {
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

ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

img {
    max-width: 100%;
    display: block;
}

.wrap {
    max-width: 1240px;
    margin: -50px auto;
    padding: 0 32px;
}

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

.logo {
    display: flex;
    align-items: center;
    gap: 10px;
    color: var(--paper);
    font-weight: 700;
    font-size: 18px;
}

.logo-mark {
    width: 32px;
    height: 32px;
    border-radius: 9px;
    background: var(--lime);
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
}

.logo-mark::before {
    content: "";
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid var(--navy);
}

.logo span {
    color: var(--lime);
}

.nav-links {
    display: flex;
    gap: 36px;
    align-items: center;
}

.nav-links a {
    color: rgba(244, 247, 234, 0.72);
    font-size: 14px;
    font-weight: 500;
    transition: color .2s;
}

.nav-links a:hover {
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
    transition: transform .15s ease, background .2s ease, border-color .2s ease;
    display: inline-block;
}

.btn:hover {
    transform: translateY(-1px);
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

.btn-lime:hover {
    background: #d3ec5c;
}

@media (max-width: 600px) {
    nav {
        padding: 12px 16px;
        gap: 12px;
    }

    .logo {
        gap: 7px;
        font-size: 15px;
        flex-shrink: 1;
        min-width: 0;
    }

    .logo-mark {
        width: 26px;
        height: 26px;
        border-radius: 7px;
        flex-shrink: 0;
    }

    .logo-mark::before {
        width: 10px;
        height: 10px;
    }

    .nav-actions {
        gap: 6px;
        flex-shrink: 0;
    }

    .btn {
        padding: 8px 10px;
        font-size: 11px;
        border-radius: 7px;
    }
}

/* ===== HERO ===== */
.hero {
    background: radial-gradient(1200px 600px at 12% -10%, #06305e 0%, var(--navy) 55%), var(--navy);
    color: var(--paper);
    position: relative;
    overflow: hidden;
    padding-bottom: 64px;
}

.hero::after {
    content: "";
    position: absolute;
    inset: 0;
    background-image:
        repeating-linear-gradient(0deg, rgba(196, 221, 65, 0.05) 0 1px, transparent 1px 64px),
        repeating-linear-gradient(90deg, rgba(196, 221, 65, 0.05) 0 1px, transparent 1px 64px);
    pointer-events: none;
}

.hero-inner {
    position: relative;
    z-index: 2;
    display: grid;
    grid-template-columns: 1.15fr 0.85fr;
    gap: 40px;
    align-items: start;
}

@media(max-width: 960px) {
    .hero-inner {
        grid-template-columns: 1fr;
    }
}

.eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 7px 14px;
    border-radius: 999px;
    background: rgba(196, 221, 65, 0.1);
    border: 1px solid rgba(196, 221, 65, 0.35);
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.14em;
    color: var(--lime);
    margin-bottom: 26px;
}

.eyebrow::before {
    content: "";
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: var(--lime);
}

.hero h1 {
    font-size: clamp(42px, 6vw, 76px);
    line-height: 0.98;
    font-weight: 700;
    letter-spacing: -0.02em;
    text-transform: uppercase;
}

.hero h1 em {
    font-style: normal;
    color: var(--lime);
}

.hero p.lede {
    margin-top: 22px;
    max-width: 480px;
    font-size: 16px;
    line-height: 1.65;
    color: rgba(244, 247, 234, 0.68);
}

.hero-cta {
    display: flex;
    gap: 12px;
    margin-top: 32px;
}

.hero-cta .btn {
    padding: 14px 24px;
    font-size: 14px;
}

.stat-row {
    display: grid;
    grid-template-columns: repeat(1, 1fr);
    gap: 1px;
    margin-top: 56px;
    background: rgba(196, 221, 65, 0.16);
    border: 1px solid rgba(196, 221, 65, 0.16);
    border-radius: 14px;
    overflow: hidden;
    max-width: 360px;
}

.stat-cell {
    background: rgba(4, 16, 31, 0.55);
    padding: 18px 22px;
}

.stat-cell .k {
    font-size: 11px;
    color: rgba(244, 247, 234, 0.5);
    letter-spacing: 0.12em;
    margin-bottom: 6px;
}

.stat-cell .v {
    font-size: 24px;
    font-weight: 700;
    font-family: 'Space Grotesk', sans-serif;
}

.stat-cell .v span {
    color: var(--lime);
}

/* ---- court card diagram ---- */
.court-card {
    background: linear-gradient(165deg, #052A54, #001B3E 70%);
    border: 1px solid rgba(196, 221, 65, 0.22);
    border-radius: 20px;
    padding: 20px;
    box-shadow: 0 30px 60px -20px rgba(0, 0, 0, 0.6);
}

.court-card .tag {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 14px;
}

.court-card .tag-label {
    font-size: 10px;
    color: rgba(196, 221, 65, 0.75);
    letter-spacing: 0.14em;
}

.court-card .price {
    background: var(--lime);
    color: var(--navy);
    font-size: 12px;
    font-weight: 700;
    padding: 5px 10px;
    border-radius: 7px;
}

.court-card h3 {
    font-size: 22px;
    margin-top: 2px;
    margin-bottom: 4px;
    color: var(--paper);
}

.court-card .loc {
    font-size: 12px;
    color: rgba(244, 247, 234, 0.55);
    margin-bottom: 16px;
}

.court-svg-wrap {
    background: #031C3A;
    border-radius: 12px;
    border: 1px solid rgba(196, 221, 65, 0.15);
    padding: 14px;
    margin-bottom: 14px;
}

.court-foot {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
}

.court-foot div {
    background: rgba(255, 255, 255, 0.04);
    border: 1px solid rgba(255, 255, 255, 0.06);
    border-radius: 10px;
    padding: 10px 12px;
}

.court-foot .k {
    font-size: 10px;
    color: rgba(244, 247, 234, 0.45);
    letter-spacing: 0.1em;
}

.court-foot .v {
    font-size: 18px;
    font-weight: 700;
    color: var(--lime);
    font-family: 'Space Grotesk', sans-serif;
}

@keyframes serve {
    0% {
        transform: translate(0, 0);
        opacity: 0;
    }

    8% {
        opacity: 1;
    }

    50% {
        transform: translate(148px, -58px);
    }

    92% {
        opacity: 1;
    }

    100% {
        transform: translate(266px, -8px);
        opacity: 0;
    }
}

.ball {
    animation: serve 3.2s ease-in-out infinite;
}

/* ===== SECTION SHARED ===== */
section {
    padding: 96px 0;
}

.section-head {
    max-width: 640px;
    margin-bottom: 52px;
}

.kicker {
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.16em;
    color: var(--lime-2);
    margin-bottom: 14px;
    display: block;
}

.section-head h2 {
    font-size: clamp(30px, 4vw, 46px);
    line-height: 1.05;
    text-transform: uppercase;
    color: var(--navy);
    letter-spacing: -0.01em;
}

.section-head p {
    margin-top: 16px;
    color: var(--ink-soft);
    font-size: 15.5px;
    line-height: 1.65;
}

/* ===== COURTS GRID ===== */
.courts-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 22px;
}

@media(max-width: 900px) {
    .courts-grid {
        grid-template-columns: 1fr;
    }
}

.court-item {
    border-radius: 16px;
    overflow: hidden;
    background: var(--navy);
    border: 1px solid rgba(0, 27, 62, 0.08);
    display: flex;
    flex-direction: column;
    transition: transform .2s ease, box-shadow .2s ease;
}

.court-item:hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 40px -18px rgba(0, 27, 62, 0.35);
}

.court-banner {
    height: 150px;
    position: relative;
    padding: 16px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    background:
        radial-gradient(circle at 85% 20%, rgba(196, 221, 65, 0.25), transparent 55%),
        repeating-linear-gradient(45deg, rgba(196, 221, 65, 0.06) 0 10px, transparent 10px 20px);
}

.court-banner .chip {
    align-self: flex-start;
    background: rgba(196, 221, 65, 0.14);
    color: var(--lime);
    border: 1px solid rgba(196, 221, 65, 0.35);
    font-size: 10.5px;
    font-weight: 600;
    padding: 5px 10px;
    border-radius: 999px;
}

.monogram {
    align-self: flex-end;
    width: 52px;
    height: 52px;
    border-radius: 12px;
    background: var(--lime);
    color: var(--navy);
    font-family: 'Space Grotesk', sans-serif;
    font-weight: 700;
    font-size: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.court-body {
    padding: 18px 18px 20px;
    background: var(--paper);
    flex: 1;
    display: flex;
    flex-direction: column;
}

.court-body h4 {
    font-size: 17px;
    color: var(--navy);
    margin-bottom: 4px;
}

.court-body .loc {
    font-size: 12.5px;
    color: var(--ink-soft);
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.court-body .loc::before {
    content: "◎";
    color: var(--lime-2);
}

.court-foot-row {
    margin-top: auto;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.court-foot-row .rate {
    font-size: 11px;
    color: var(--ink-soft);
}

.court-foot-row .rate b {
    color: var(--navy);
    font-size: 14px;
}

.view-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: var(--navy);
    color: var(--paper);
    font-size: 12.5px;
    font-weight: 600;
    padding: 9px 14px;
    border-radius: 8px;
    cursor: pointer;
}

.view-link:hover {
    background: var(--navy-3);
}

.center-cta {
    display: flex;
    justify-content: center;
    margin-top: 40px;
}

.btn-outline-navy {
    background: transparent;
    border: 1px solid rgba(0, 27, 62, 0.2);
    color: var(--navy);
}

.btn-outline-navy:hover {
    border-color: var(--navy);
    background: rgba(0, 27, 62, 0.04);
}

/* ===== FEATURES / PROCESS ===== */
.features {
    background: var(--navy);
    color: var(--paper);
}

.features .kicker {
    color: var(--lime);
}

.features .section-head h2 {
    color: var(--paper);
}

.features .section-head p {
    color: rgba(244, 247, 234, 0.6);
}

.feat-grid {
    grid-template-columns: 1fr;
    align-items: center;
}

@media(max-width: 900px) {
    .feat-grid {
        grid-template-columns: 1fr;
    }
}

.flow-panel {
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(196, 221, 65, 0.16);
    border-radius: 18px;
    padding: 22px;
}

.flow-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;
}

.flow-badge {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 11px;
    font-weight: 600;
    color: var(--lime);
    background: rgba(196, 221, 65, 0.1);
    padding: 6px 12px;
    border-radius: 999px;
    letter-spacing: 0.08em;
}

.flow-live {
    font-size: 10.5px;
    color: #7CE28B;
    display: flex;
    align-items: center;
    gap: 5px;
}

.flow-live::before {
    content: "";
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #7CE28B;
}

.flow-card {
    background: rgba(255, 255, 255, 0.04);
    border: 1px solid rgba(255, 255, 255, 0.06);
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 12px;
}

.flow-card .row {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 13px;
    color: rgba(244, 247, 234, 0.85);
    padding: 9px 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}

.flow-card .row:last-child {
    border-bottom: none;
}

.flow-card .row::before {
    content: "";
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: var(--lime);
    flex-shrink: 0;
}

.flow-note {
    font-size: 11px;
    color: rgba(244, 247, 234, 0.4);
    letter-spacing: 0.1em;
    margin-bottom: 8px;
}

.flow-desc {
    font-size: 13px;
    color: rgba(244, 247, 234, 0.6);
    line-height: 1.6;
    margin-bottom: 10px;
}

.synced {
    font-size: 12px;
    color: var(--lime);
    font-weight: 600;
}

.feat-list {
    display: flex;
    flex-direction: column;
    gap: 26px;
}

.feat-row {
    display: flex;
    gap: 16px;
}

.feat-icon {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    flex-shrink: 0;
    background: rgba(196, 221, 65, 0.1);
    border: 1px solid rgba(196, 221, 65, 0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--lime);
    font-size: 16px;
}

.feat-row h4 {
    font-size: 16px;
    margin-bottom: 4px;
}

.feat-row p {
    font-size: 13.5px;
    color: rgba(244, 247, 234, 0.55);
    line-height: 1.6;
}

/* ===== ROLES ===== */
.roles-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 22px;
}

@media(max-width: 900px) {
    .roles-grid {
        grid-template-columns: 1fr;
    }
}

.role-card {
    background: var(--paper);
    border: 1px solid rgba(0, 27, 62, 0.08);
    border-radius: 16px;
    padding: 28px 24px;
    position: relative;
    overflow: hidden;
}

.role-card::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--lime);
}

.role-num {
    font-family: 'JetBrains Mono', monospace;
    font-size: 11px;
    color: var(--lime-2);
    letter-spacing: 0.1em;
    margin-bottom: 16px;
}

.role-card h3 {
    font-size: 20px;
    color: var(--navy);
    margin-bottom: 2px;
}

.role-card .sub {
    font-size: 12.5px;
    color: var(--ink-soft);
    font-style: italic;
    margin-bottom: 18px;
}

.role-card ul {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.role-card li {
    font-size: 13.5px;
    color: var(--ink-soft);
    display: flex;
    gap: 9px;
    line-height: 1.5;
}

.role-card li::before {
    content: "→";
    color: var(--lime-2);
    flex-shrink: 0;
    font-weight: 700;
}

/* ===== CTA ===== */
.cta-band {
    background: var(--navy);
    color: var(--paper);
    text-align: center;
    padding: 80px 0;
    position: relative;
    overflow: hidden;
}

.cta-band::before {
    content: "";
    position: absolute;
    width: 600px;
    height: 600px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(196, 221, 65, 0.14), transparent 70%);
    top: -300px;
    left: 50%;
    transform: translateX(-50%);
}

.cta-band h2 {
    font-size: clamp(28px, 4vw, 42px);
    text-transform: uppercase;
    position: relative;
    z-index: 1;
}

.cta-band h2 span {
    color: var(--lime);
}

.cta-band p {
    margin: 14px 0 30px;
    color: rgba(244, 247, 234, 0.6);
    font-size: 15px;
    position: relative;
    z-index: 1;
}

.cta-band .hero-cta {
    justify-content: center;
    position: relative;
    z-index: 1;
}

/* ===== FOOTER ===== */
footer {
    background: #000F26;
    color: rgba(244, 247, 234, 0.55);
    padding: 64px 0 28px;
    font-size: 13.5px;
}

.foot-grid {
    display: grid;
    grid-template-columns: 1.6fr 1fr 1fr 1fr;
    gap: 32px;
    margin-bottom: 48px;
}

@media(max-width: 800px) {
    .foot-grid {
        grid-template-columns: 1fr 1fr;
    }
}

.foot-grid h5 {
    color: var(--paper);
    font-size: 12px;
    letter-spacing: 0.1em;
    margin-bottom: 16px;
    font-weight: 600;
}

.flow-line {
    width: 1px;
    height: 22px;
    margin: 5px 0 5px 17px;

    background: #dcdcd8;
}

.foot-grid ul {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.foot-grid ul a:hover {
    color: var(--lime);
}

.foot-brand p {
    margin-top: 14px;
    max-width: 280px;
    line-height: 1.6;
}

.foot-bottom {
    border-top: 1px solid rgba(255, 255, 255, 0.08);
    padding-top: 22px;
    display: flex;
    justify-content: space-between;
    font-size: 12px;
    color: rgba(244, 247, 234, 0.35);
}

@media(max-width: 600px) {
    .foot-bottom {
        flex-direction: column;
        gap: 8px;
    }
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
    border-bottom: 1px solid rgba(0, 0, 0, 0.08);
}

.footer-brand {
    max-width: 360px;
}

.footer-brand .logo {
    margin-bottom: 12px;
}

.footer-brand p {
    margin: 0;
    color: #777;
    font-size: 13px;
    line-height: 1.6;
}

.footer-links {
    display: flex;
    align-items: center;
    gap: 26px;
}

.footer-links a,
.footer-legal a {
    color: #666;
    text-decoration: none;
    font-size: 12px;
    transition: color 0.2s ease;
}

.footer-links a:hover,
.footer-legal a:hover {
    color: #222;
}

.foot-bottom {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;

    padding-top: 20px;

    color: #999;
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
</style>