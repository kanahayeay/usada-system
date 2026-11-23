<?php

namespace App\Libraries;

use DateTimeImmutable;
use DateTimeZone;

/**
 * Mengubah tanggal Masehi menjadi hari ke-1..210 dalam siklus pawukon.
 * Patokan: Rabu 23 April 2025 = Buda Kliwon Dungulan (Galungan) = hari ke-74.
 */
class Pawukon
{
    private const PATOKAN = '2025-04-23';
    private const PATOKAN_INDEKS = 73; // 0-based

    public static function hariKe(string $tanggal): int
    {
        $tz = new DateTimeZone('Asia/Makassar');
        $a  = new DateTimeImmutable(self::PATOKAN, $tz);
        $b  = new DateTimeImmutable($tanggal, $tz);
        $selisih = (int) $a->diff($b)->format('%r%a');

        return ((($selisih + self::PATOKAN_INDEKS) % 210) + 210) % 210 + 1;
    }
}