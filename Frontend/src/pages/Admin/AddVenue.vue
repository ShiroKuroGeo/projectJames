<template>
    <div class="admin-shell">
        <div class="admin-app">

            <header class="admin-header">
                <div class="admin-brand">
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
                        Add a new sports venue to DinkYard.
                    </p>
                </div>


                <form @submit.prevent="createVenue">
                    <div class="form-group">
                        <label class="form-label">
                            Venue Name
                        </label>

                        <input v-model="form.name" type="text" class="form-input" placeholder="e.g. Hoops Dome" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Area
                        </label>

                        <input v-model="form.area" type="text" class="form-input" placeholder="e.g. Bangkal" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Full Address
                        </label>

                        <textarea v-model="form.address" class="form-input textarea" placeholder="e.g. Bangkal, Lapu-Lapu City" rows="3" required></textarea>
                    </div>

                    <div class="form-row">

                        <div class="form-group">
                            <label class="form-label">
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
                        <div class="form-group">
                            <label class="form-label">
                                Venue Location
                            </label>

                            <div class="location-map-wrapper">
                                <div id="venue-map"></div>
                            </div>

                            <div class="location-coordinates">
                                <div>
                                    <span class="coordinate-label">LATITUDE</span>
                                    <span class="coordinate-value">
                                        {{ form.latitude || '—' }}
                                    </span>
                                </div>

                                <div>
                                    <span class="coordinate-label">LONGITUDE</span>
                                    <span class="coordinate-value">
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
                            <div class="form-label">
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


<style>
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
    letter-spacing: .12em;
    text-transform: uppercase;

    color: var(--ink-faint);

    border: 1px solid var(--line-onteal-strong);

    padding: 2px 7px;
    border-radius: 20px;
}


.back-btn {
    font-family: var(--font-mono);
    font-size: 10.5px;

    color: var(--chalk);

    background: transparent;

    border: 1px solid var(--line-onteal-strong);

    padding: 8px 13px;

    border-radius: 20px;

    cursor: pointer;
}


.admin-card {
    background: var(--chalk);

    border-radius: var(--radius-lg);

    padding: 24px 20px;
    margin-top: 20px;

    box-shadow:
        0 8px 20px -14px rgba(0, 0, 0, .6);
}


.card-head {
    margin-bottom: 26px;
}


.card-tag {
    display: block;

    font-size: 10px;

    letter-spacing: .1em;

    text-transform: uppercase;

    color: var(--mango);

    margin-bottom: 4px;
}


.card-title {
    font-family: var(--font-display);

    font-weight: 400;

    font-size: 28px;

    color: var(--ink);

    margin: 0 0 5px;
}


.card-description {
    margin: 0;

    color: var(--ink-soft);

    font-size: 13px;
}


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

    font-family: var(--font-mono);

    font-size: 10.5px;

    text-transform: uppercase;

    letter-spacing: .07em;

    color: var(--ink-soft);

    margin-bottom: 7px;
}


.form-input {
    width: 100%;

    box-sizing: border-box;

    background: var(--chalk-dim);

    border: 1px solid transparent;

    border-radius: var(--radius-sm);

    padding: 11px 12px;

    font-size: 13px;

    color: var(--ink);
}


.form-input:focus {
    outline: none;

    border-color: var(--mango);

    background: var(--chalk);
}


.textarea {
    resize: vertical;

    font-family: inherit;
}


.form-hint {
    display: block;

    margin-top: 5px;

    font-size: 11px;

    color: var(--ink-faint);
}


.price-input-wrapper {
    position: relative;
}


.price-input {
    padding-left: 32px;
}


.peso {
    position: absolute;

    left: 12px;
    top: 50%;

    transform: translateY(-50%);

    color: var(--ink-soft);

    z-index: 1;
}


.status-row {
    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 20px;

    padding: 16px 0;

    margin-top: 8px;

    border-top: 1px solid var(--line-onchalk);

    border-bottom: 1px solid var(--line-onchalk);
}


.status-row .form-label {
    margin-bottom: 3px;
}


/* TOGGLE */

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

    background: #d1d5db;

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

    background: white;

    border-radius: 50%;

    transition: .2s;
}


.toggle input:checked+.toggle-slider {
    background: var(--success);
}


.toggle input:checked+.toggle-slider::before {
    transform: translateX(20px);
}


/* ACTIONS */

.form-actions {
    display: flex;

    justify-content: flex-end;

    gap: 10px;

    margin-top: 24px;
}


.cancel-btn,
.create-btn {
    font-family: var(--font-mono);

    font-size: 11px;

    text-transform: uppercase;

    letter-spacing: .04em;

    padding: 11px 16px;

    border-radius: var(--radius-sm);

    cursor: pointer;
}


.cancel-btn {
    background: transparent;

    border: 1px solid var(--line-onchalk);

    color: var(--ink-soft);
}


.create-btn {
    background: var(--mango);

    border: none;

    color: var(--chalk);
}


.create-btn:disabled {
    opacity: .5;

    cursor: default;
}

.location-map-wrapper {
    width: 100%;
    height: 320px;

    border-radius: var(--radius-sm);

    overflow: hidden;

    border: 1px solid var(--line-onchalk);

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
    background: var(--chalk-dim);

    border-radius: var(--radius-sm);

    padding: 10px 12px;
}

.coordinate-label {
    display: block;

    font-family: var(--font-mono);

    font-size: 8px;

    letter-spacing: .08em;

    color: var(--ink-faint);

    margin-bottom: 3px;
}

.coordinate-value {
    font-family: var(--font-mono);

    font-size: 12px;

    color: var(--ink);
}


@media (max-width: 560px) {

    .form-row {
        grid-template-columns: 1fr;
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