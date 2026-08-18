<template>
    <div class="admin-shell">
        <div class="admin-app">

            <header class="admin-header">
                <div class="admin-brand">
                    <img :src="logo" class="brand-logo-mark" alt="" />
                    <span class="admin-brand-word">
                        Dink<span class="accent">Yard</span>
                    </span>

                    <span class="admin-brand-tag mono">
                        Super Admin
                    </span>
                </div>

                <button class="back-btn" @click="router.back()">
                    ← Back
                </button>
            </header>


            <section class="admin-card">

                <div class="card-head">
                    <span class="card-tag mono">Venue Management</span>
                    <h1 class="card-title">
                        Create Location
                    </h1>

                    <p class="card-description">
                        Add a new sports venue to Courttesy.
                    </p>
                </div>


                <form @submit.prevent="createVenue">
                    <div class="form-group">
                        <label class="form-label mono">
                            Venue Name
                        </label>

                        <input v-model="form.name" type="text" class="form-input" placeholder="e.g. Hoops Dome" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label mono">
                            Area
                        </label>

                        <input v-model="form.area" type="text" class="form-input" placeholder="e.g. Bangkal" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label mono">
                            Full Address
                        </label>

                        <textarea v-model="form.address" class="form-input textarea" placeholder="e.g. Bangkal, Lapu-Lapu City" rows="3" required></textarea>
                    </div>

                    <div class="form-row">

                        <div class="form-group">
                            <label class="form-label mono">
                                Sport
                            </label>

                            <select v-model="form.sport" class="form-input" required>
                                <option value="" disabled>
                                    Select sport
                                </option>

                                <option value="basketball">
                                    Basketball
                                </option>

                                <option value="badminton">
                                    Badminton
                                </option>

                                <option value="pickleball">
                                    Pickleball
                                </option>

                                <option value="volleyball">
                                    Volleyball
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="status-row">
                        <div class="form-group" style="margin-bottom: 0; width: 100%;">
                            <label class="form-label mono">
                                Venue Location
                            </label>

                            <div class="location-map-wrapper">
                                <div id="venue-map"></div>
                            </div>

                            <div class="location-coordinates">
                                <div>
                                    <span class="coordinate-label mono">LATITUDE</span>
                                    <span class="coordinate-value mono">
                                        {{ form.latitude || '—' }}
                                    </span>
                                </div>

                                <div>
                                    <span class="coordinate-label mono">LONGITUDE</span>
                                    <span class="coordinate-value mono">
                                        {{ form.longitude || '—' }}
                                    </span>
                                </div>
                            </div>

                            <div class="form-hint">
                                Click on the map or drag the marker to set the exact venue location.
                            </div>
                        </div>
                    </div>

                    <div class="status-row">
                        <div>
                            <div class="form-label mono">
                                Venue Status
                            </div>
                            <div class="form-hint">
                                Inactive venues won't appear to customers.
                            </div>
                        </div>
                        <label class="toggle">
                            <input v-model="form.active" type="checkbox">
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    <div class="form-actions">

                        <button type="button" class="cancel-btn" @click="router.back()">
                            Cancel
                        </button>

                        <button type="submit" class="create-btn" :disabled="saving">
                            {{ saving ? 'Creating...' : 'Create Venue' }}
                        </button>

                    </div>

                </form>

            </section>

        </div>
    </div>
</template>


<script setup>
import { reactive, ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import logo from '@/component/assets/logo.jpg';

const router = useRouter();
let map = null;
let marker = null;
const defaultLocation = [10.2439, 123.9422];
const saving = ref(false);
const defaultIcon = L.icon({
    iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
    iconRetinaUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png',
    shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',

    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41]
});

const form = reactive({
    name: '',
    slug: '',
    area: '',
    address: '',
    sport: 'pickleball',
    price: 0,
    courts: 1,
    active: true,

    latitude: '',
    longitude: ''
});

async function createVenue() {

    saving.value = true;

    try {

        const existing =
            JSON.parse(
                localStorage.getItem('dinkyard_venues') || '[]'
            );

        const venue = {
            id: `venue-${Date.now()}`,

            name: form.name,
            slug: form.slug,
            area: form.area,
            address: form.address,

            sport: form.sport,

            price: form.price,
            courts: form.courts,

            active: form.active,

            latitude: form.latitude
                ? Number(form.latitude)
                : null,

            longitude: form.longitude
                ? Number(form.longitude)
                : null
        };


        existing.push(venue);

        localStorage.setItem(
            'dinkyard_venues',
            JSON.stringify(existing)
        );


        alert('Venue created successfully!');

        router.push('/admin');

    } catch (error) {

        console.error(error);

        alert('Failed to create venue.');

    } finally {

        saving.value = false;

    }
}

function setLocation(lat, lng) {

    form.latitude = Number(lat.toFixed(6));
    form.longitude = Number(lng.toFixed(6));

    if (marker) {

        marker.setLatLng([lat, lng]);

    } else {

        marker = L.marker(
            [lat, lng],
            {
                draggable: true,
                icon: defaultIcon
            }
        ).addTo(map);

        marker.on('dragend', () => {

            const position = marker.getLatLng();

            form.latitude =
                Number(position.lat.toFixed(6));

            form.longitude =
                Number(position.lng.toFixed(6));
        });
    }

    map.setView(
        [lat, lng],
        16
    );
}

onMounted(() => {


    map = L.map('venue-map').setView(
        defaultLocation,
        13
    );

    L.tileLayer(
        'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
        {
            attribution: '&copy; OpenStreetMap contributors'
        }
    ).addTo(map);

    map.on('click', (event) => {
        setLocation(
            event.latlng.lat,
            event.latlng.lng
        );
    });
});
</script>


<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500;600&display=swap');

/* =========================================================
   TOKENS (shared with Homepage / AdminLogin / Admin dashboard)
   ========================================================= */

.admin-shell {
    --navy: #001B3E;
    --navy-2: #04264F;
    --navy-3: #0B3568;
    --lime: #C3DD41;
    --lime-2: #9FB92F;
    --danger: #C33C29;
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
.admin-brand-word {
    font-family: 'Space Grotesk', sans-serif;
}

.mono {
    font-family: 'JetBrains Mono', monospace;
    letter-spacing: 0.06em;
}

.admin-app {
    max-width: 640px;
    margin: 0 auto;
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
    margin-bottom: 18px;

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

.back-btn {
    position: relative;
    z-index: 1;

    font-family: 'JetBrains Mono', monospace;
    font-size: 10.5px;
    letter-spacing: 0.05em;

    color: var(--paper);
    background: rgba(11, 53, 104, 0.6);
    border: 1px solid rgba(196, 221, 65, 0.3);

    padding: 8px 13px;
    border-radius: 20px;

    cursor: pointer;
    transition: background .2s ease, color .2s ease, border-color .2s ease;
}

.back-btn:hover {
    background: var(--lime);
    color: var(--navy);
    border-color: var(--lime);
}

/* =========================================================
   FORM CARD
   ========================================================= */

.admin-card {
    position: relative;
    overflow: hidden;

    background: #fff;
    border: 1px solid var(--line);
    border-radius: var(--radius-lg);

    padding: 26px 24px;

    box-shadow: 0 10px 26px -20px rgba(0, 27, 62, 0.35);
}

.admin-card::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--lime);
}

.card-head {
    margin-bottom: 26px;
}

.card-tag {
    display: block;
    font-size: 10px;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: var(--lime-2);
    margin-bottom: 6px;
}

.card-title {
    font-weight: 700;
    font-size: 27px;
    letter-spacing: -0.01em;
    color: var(--navy);
    margin: 0 0 5px;
    text-transform: uppercase;
}

.card-description {
    margin: 0;
    color: var(--ink-soft);
    font-size: 13px;
}

/* =========================================================
   FORM FIELDS
   ========================================================= */

.form-group {
    margin-bottom: 18px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
}

.form-label {
    display: block;
    font-size: 10.5px;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: var(--ink-soft);
    margin-bottom: 7px;
}

.form-input {
    width: 100%;
    box-sizing: border-box;

    background: var(--cream);
    border: 1px solid rgba(0, 27, 62, 0.1);
    border-radius: var(--radius-sm);

    padding: 12px 13px;

    font-family: 'Inter', sans-serif;
    font-size: 13.5px;
    color: var(--ink);

    transition: border-color .18s ease, box-shadow .18s ease, background .18s ease;
}

.form-input:focus {
    outline: none;
    border-color: var(--lime-2);
    background: #fff;
    box-shadow: 0 0 0 3px rgba(196, 221, 65, 0.18);
}

.textarea {
    resize: vertical;
    font-family: inherit;
}

.form-hint {
    display: block;
    margin-top: 6px;
    font-size: 11px;
    color: var(--ink-faint);
}

/* =========================================================
   MAP / COORDINATES
   ========================================================= */

.location-map-wrapper {
    width: 100%;
    height: 320px;

    border-radius: var(--radius-sm);
    overflow: hidden;

    border: 1px solid var(--line);
    margin-bottom: 10px;
}

#venue-map {
    width: 100%;
    height: 100%;
}

.location-coordinates {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    margin-top: 10px;
}

.location-coordinates>div {
    background: var(--cream);
    border-radius: var(--radius-sm);
    padding: 10px 12px;
}

.coordinate-label {
    display: block;
    font-size: 8px;
    letter-spacing: 0.1em;
    color: var(--ink-faint);
    margin-bottom: 3px;
}

.coordinate-value {
    font-size: 12px;
    color: var(--navy);
    font-weight: 600;
}

/* =========================================================
   STATUS ROW / TOGGLE
   ========================================================= */

.status-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;

    padding: 16px 0;
    margin-top: 8px;

    border-top: 1px dashed var(--line);
    border-bottom: 1px dashed var(--line);
}

.status-row .form-label {
    margin-bottom: 3px;
}

.toggle {
    position: relative;
    width: 44px;
    height: 24px;
    flex-shrink: 0;
}

.toggle input {
    opacity: 0;
    width: 0;
    height: 0;
}

.toggle-slider {
    position: absolute;
    inset: 0;

    background: rgba(0, 27, 62, 0.16);
    border-radius: 20px;

    cursor: pointer;
    transition: .2s;
}

.toggle-slider::before {
    content: '';
    position: absolute;

    width: 18px;
    height: 18px;

    left: 3px;
    top: 3px;

    background: #fff;
    border-radius: 50%;

    transition: .2s;
}

.toggle input:checked+.toggle-slider {
    background: var(--lime);
}

.toggle input:checked+.toggle-slider::before {
    transform: translateX(20px);
}

/* =========================================================
   ACTIONS
   ========================================================= */

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 24px;
}

.cancel-btn,
.create-btn {
    font-family: 'JetBrains Mono', monospace;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;

    padding: 12px 18px;
    border-radius: var(--radius-sm);

    cursor: pointer;
    transition: background .18s ease, border-color .18s ease, transform .12s ease;
}

.cancel-btn {
    background: transparent;
    border: 1px solid var(--line);
    color: var(--ink-soft);
}

.cancel-btn:hover {
    border-color: var(--danger);
    color: var(--danger);
}

.create-btn {
    background: var(--lime);
    border: 1px solid var(--lime);
    color: var(--navy);
}

.create-btn:hover:not(:disabled) {
    background: #d3ec5c;
    transform: translateY(-1px);
}

.create-btn:disabled {
    opacity: .5;
    cursor: default;
    transform: none;
}

/* =========================================================
   RESPONSIVE
   ========================================================= */

@media (max-width: 560px) {

    .admin-card {
        padding: 22px 18px;
    }

    .form-row {
        grid-template-columns: 1fr;
    }

    .location-map-wrapper {
        height: 260px;
    }

    .form-actions {
        flex-direction: column-reverse;
    }

    .cancel-btn,
    .create-btn {
        width: 100%;
    }
}
</style>