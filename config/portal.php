<?php

/*
|--------------------------------------------------------------------------
| Halaman depan portal
|--------------------------------------------------------------------------
| Lokasi dipakai untuk menghitung jadwal shalat (hisab) dan arah kiblat.
| Default: Kantor Kementerian Agama Kabupaten Pesisir Selatan, Painan.
*/

return [
    'location' => [
        'name' => env('PORTAL_LOCATION_NAME', 'Painan, Pesisir Selatan'),
        'latitude' => (float) env('PORTAL_LATITUDE', -1.3489),
        'longitude' => (float) env('PORTAL_LONGITUDE', 100.5694),
        'timezone' => 'Asia/Jakarta',
        'utc_offset' => 7,
        'timezone_label' => 'WIB',
    ],

    // Kriteria hisab Kementerian Agama RI.
    'prayer' => [
        'fajr_angle' => 20,   // Subuh: matahari -20°
        'isha_angle' => 18,   // Isya : matahari -18°
        'ihtiyat' => 2,       // menit pengaman
        'imsak_minutes' => 10, // Imsak = Subuh - 10 menit
    ],

    // Koordinat Ka'bah, Makkah.
    'kaaba' => [
        'latitude' => 21.4225,
        'longitude' => 39.8262,
    ],
];
