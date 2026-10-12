/**
 * AUDrive — Main JS Entry
 * Alpine.js + Livewire initialization
 */

import Alpine from 'alpinejs';

// Register Alpine plugins if needed
// import collapse from '@alpinejs/collapse';
// import persist from '@alpinejs/persist';
// Alpine.plugin(collapse);
// Alpine.plugin(persist);

// Start Alpine
window.Alpine = Alpine;
Alpine.start();

// ── Global Utilities ──

/**
 * Format currency to Indonesian Rupiah
 */
window.formatRupiah = (amount) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(amount);
};

/**
 * Format date to Indonesian locale
 */
window.formatDate = (date, options = {}) => {
    const defaultOptions = {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        ...options,
    };
    return new Date(date).toLocaleDateString('id-ID', defaultOptions);
};

/**
 * Format datetime to Indonesian locale with timezone
 */
window.formatDateTime = (date) => {
    return new Date(date).toLocaleString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        timeZone: 'Asia/Jakarta',
    });
};

// ── CSRF Token for AJAX ──
const token = document.querySelector('meta[name="csrf-token"]')?.content;
if (token) {
    window.axiosDefaults = {
        headers: {
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
    };
}

console.log('%c AUDrive ', 'background: #FFD100; color: #0A0A0B; font-weight: bold; padding: 4px 8px; font-family: Orbitron;');
console.log('%c Premium Automotive Dealer Platform ', 'color: #C7CCD1; font-family: Inter;');