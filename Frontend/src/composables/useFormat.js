export default function formatHour(h24) {
    const period = h24 >= 12 ? 'PM' : 'AM';
    let h = h24 % 12; if (h === 0) h = 12;
    return `${h}:00 ${period}`;
}