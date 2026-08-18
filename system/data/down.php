<?php

/**
 * Mode maintenance.
 *
 * Selama `down` bernilai true, seluruh permintaan HTTP dijawab halaman
 * maintenance dengan status 503, dan queue worker berhenti mengambil job.
 *
 * Ada dua cara menembusnya untuk memeriksa aplikasi selagi tutup:
 *
 * 1. **Tautan rahasia** — isi `secret`, lalu buka `https://situs/{secret}`
 *    satu kali. Cookie terpasang sendiri dan peramban itu melihat situs
 *    seperti biasa selama 12 jam. Ini yang dianjurkan: rahasianya acak
 *    per-insiden dan dapat dikirim sebagai tautan.
 * 2. **Cookie bernama tertentu** lewat `cookie` — bentuk lama. Yang diperiksa
 *    hanya keberadaan cookie, sehingga NAMANYA yang menjadi rahasia. Karena
 *    itu ia dibaca dari `env.php`, bukan ditulis di sini: nilai yang tertulis
 *    di repo bukan lagi rahasia begitu ada yang membaca kodenya.
 *
 * Kunci `down` bawaannya **false**, jadi berkas yang tidak lengkap tidak
 * menjatuhkan aplikasi.
 */
return [
    'down' => false,
    'view' => 'system.maintenance',
    'secret' => c::env('MAINTENANCE_SECRET', ''),
    'cookie' => c::env('MAINTENANCE_BYPASS', ''),
];
