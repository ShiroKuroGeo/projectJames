import { ref } from 'vue';

const STORAGE_KEY = 'dinkyard_venues_v1';

const SEED = [
    {
        id: 'hoopsdome',
        slug: 'dodongvenue',
        name: 'Hoopsdome Lapu-Lapu City',
        area: 'Humay-Humay Rd (Hoops Dome Rd), Barangay Gun-ob, Lapu-Lapu City',
        lat: 10.30535,
        lng: 123.95729,
        gcashNumber: '',
        mayaNumber: '',
        adminIds: ['admin-dodong'],
        closedDates: [],
        courts: [
            {
                id: 'court-1',
                price: 200,
                priceDef: 'Per Hour',
                name: 'Court 1',
                tag: 'Indoor hardcourt',
                openTime: '07:00',
                closeTime: '21:00',
                slotMinutes: 60
            }
        ]
    },
    {
        id: 'corhigh',
        slug: 'cordova-high',
        name: 'Cordova High School Complex',
        area: 'Cordova National High School',
        lat: 10.2506,
        lng: 123.9493,
        gcashNumber: '',
        mayaNumber: '',
        adminIds: ['admin-grace'],
        closedDates: [],
        courts: [
            {
                id: 'court-1',
                price: 150,
                priceDef: 'Per Hour',
                name: 'Court 1',
                tag: 'Outdoor court',
                openTime: '06:00',
                closeTime: '20:00',
                slotMinutes: 60
            }
        ]
    },
    {
        id: 'cuteys-racquet',
        slug: 'cuteys-racquet',
        name: 'Cuteys Racquet Sports Center',
        area: 'Camolinas Housing, Cordova, Cebu',
        lat: 10.2489,
        lng: 123.9515,
        gcashNumber: '',
        mayaNumber: '',
        adminIds: ['admin-grace'],
        closedDates: [],
        courts: [
            {
                id: 'court-1',
                price: 200,
                priceDef: 'Per Hour',
                name: 'Court 1',
                tag: 'Night-lit court',
                openTime: '16:00',
                closeTime: '22:00',
                slotMinutes: 60
            }
        ]
    }
];

function clone(x) {
    return typeof structuredClone === 'function' ? structuredClone(x) : JSON.parse(JSON.stringify(x));
}

function load() {
    try {
        const raw = localStorage.getItem(STORAGE_KEY);
        if (raw) return JSON.parse(raw);
    } catch {
        // fall through to seed
    }
    return clone(SEED);
}

export const venues = ref(load());

function persist() {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(venues.value));
}

function slugify(str) {
    return String(str).toLowerCase().trim().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '') || 'venue';
}

function uniqueSlug(base) {
    let slug = base;
    let i = 2;
    while (venues.value.some(v => v.slug === slug)) {
        slug = `${base}-${i++}`;
    }
    return slug;
}

export function getVenueBySlug(slug) {
    return venues.value.find(v => v.slug === slug) || null;
}

export function getVenuesForAdmin(adminId) {
    return venues.value.filter(v => v.adminIds.includes(adminId));
}

export function createVenue({ name, area, price, gcashNumber, mayaNumber, adminId }) {
    const venue = {
        id: 'venue-' + Date.now().toString(36),
        slug: uniqueSlug(slugify(name)),
        name: name.trim(),
        area: (area || '').trim(),
        lat: null,
        lng: null,
        price: Number(price) || 0,
        priceDef: 'Per Hour',
        gcashNumber: gcashNumber || '',
        mayaNumber: mayaNumber || '',
        adminIds: [adminId],
        closedDates: [],
        courts: []
    };
    venues.value = [...venues.value, venue];
    persist();
    return venue;
}

export function updateVenueSettings(venueId, patch) {
    venues.value = venues.value.map(v => (v.id === venueId ? { ...v, ...patch } : v));
    persist();
}

export function addCourt(venueId, { name, tag, openTime, closeTime, slotMinutes }) {
    venues.value = venues.value.map(v => {
        if (v.id !== venueId) return v;
        const court = {
            id: 'court-' + Date.now().toString(36),
            name: name?.trim() || `Court ${v.courts.length + 1}`,
            tag: tag?.trim() || '',
            openTime: openTime || '07:00',
            closeTime: closeTime || '21:00',
            slotMinutes: Number(slotMinutes) || 60
        };
        return { ...v, courts: [...v.courts, court] };
    });
    persist();
}

export function updateCourt(venueId, courtId, patch) {
    venues.value = venues.value.map(v => {
        if (v.id !== venueId) return v;
        return { ...v, courts: v.courts.map(c => (c.id === courtId ? { ...c, ...patch } : c)) };
    });
    persist();
}

export function removeCourt(venueId, courtId) {
    venues.value = venues.value.map(v => {
        if (v.id !== venueId) return v;
        return { ...v, courts: v.courts.filter(c => c.id !== courtId) };
    });
    persist();
}

// --- venue-wide date open/close ---
export function isDateClosed(venue, dateKey) {
    return venue.closedDates.includes(dateKey);
}

export function toggleClosedDate(venueId, dateKey) {
    venues.value = venues.value.map(v => {
        if (v.id !== venueId) return v;
        const closed = v.closedDates.includes(dateKey)
            ? v.closedDates.filter(d => d !== dateKey)
            : [...v.closedDates, dateKey].sort();
        return { ...v, closedDates: closed };
    });
    persist();
}

// --- time slots, generated from a court's own open/close hours ---
export function generateSlots(court) {
    if (!court?.openTime || !court?.closeTime) return [];
    const [openH, openM] = court.openTime.split(':').map(Number);
    const [closeH, closeM] = court.closeTime.split(':').map(Number);
    const start = openH * 60 + openM;
    const end = closeH * 60 + closeM;
    const step = Number(court.slotMinutes) || 60;
    const out = [];
    for (let t = start; t < end; t += step) out.push(minutesToLabel(t));
    return out;
}

function minutesToLabel(totalMinutes) {
    let h = Math.floor(totalMinutes / 60);
    const m = totalMinutes % 60;
    const period = h >= 12 ? 'PM' : 'AM';
    h = h % 12;
    if (h === 0) h = 12;
    return `${h}:${String(m).padStart(2, '0')} ${period}`;
}