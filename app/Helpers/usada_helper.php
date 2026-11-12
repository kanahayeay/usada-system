<?php

/**
 * Fungsi bantu tampilan. Dimuat dengan helper('usada') di controller.
 */

if (!function_exists('teks_atau_strip')) {
    /** Teks aman untuk ditampilkan; kosong/null menjadi "-". */
    function teks_atau_strip(?string $teks): string
    {
        $teks = trim((string) $teks);
        return $teks === '' ? '-' : esc($teks);
    }
}

if (!function_exists('lembar_label')) {
    /** "Lembar 2a" atau "Lembar 2a – 3a" dari data lembar_awal / lembar_akhir. */
    function lembar_label(array $u): string
    {
        $awal  = $u['lembar_awal'] ?? null;
        $akhir = $u['lembar_akhir'] ?? null;
        if (!$awal && !$akhir) {
            return 'Lembar -';
        }
        if (!$awal || !$akhir || $awal === $akhir) {
            return 'Lembar ' . ($akhir ?: $awal);
        }
        return "Lembar {$awal} – {$akhir}";
    }
}

if (!function_exists('tanggal_indonesia')) {
    /** "2026-11-11" -> "Rabu, 11 November 2026". */
    function tanggal_indonesia(string $tanggal): string
    {
        $hari  = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $bulan = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $t = new DateTimeImmutable($tanggal, new DateTimeZone('Asia/Makassar'));
        return $hari[(int) $t->format('w')] . ', ' . $t->format('j') . ' ' . $bulan[(int) $t->format('n')] . ' ' . $t->format('Y');
    }
}

if (!function_exists('nama_saptawara')) {
    /** Nama Sanskerta untuk hari. Data tetap, bukan data lontar. */
    function nama_saptawara(string $hari): string
    {
        return [
            'Minggu' => 'Redite', 'Senin' => 'Soma', 'Selasa' => 'Anggara', 'Rabu' => 'Buda',
            'Kamis' => 'Wrespati', 'Jumat' => 'Sukra', 'Sabtu' => 'Saniscara',
        ][$hari] ?? '';
    }
}

if (!function_exists('arah_pancawara')) {
    /** Arah mata angin pancawara. Data tetap, bukan data lontar. */
    function arah_pancawara(string $pancawara): string
    {
        return [
            'Umanis' => 'Timur (Iswara)', 'Pahing' => 'Selatan (Brahma)', 'Pwon' => 'Barat (Mahadewa)',
            'Wage' => 'Utara (Wisnu)', 'Kliwon' => 'Tengah (Siwa)',
        ][$pancawara] ?? '';
    }
}