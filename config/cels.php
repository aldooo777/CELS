<?php

/*
| Konten yang mudah diganti tanpa menyentuh view.
| GANTI data kontak di bawah dengan data asli tim sebelum dipublikasikan.
*/

return [
    'name'     => 'CELS',
    'longname' => 'Campus External Learning System',
    'tagline'  => 'Connecting Campus to the World',

    'contact' => [
        'email'    => env('CELS_CONTACT_EMAIL', 'kerjasama@cels.ac.id'),
        'whatsapp' => env('CELS_CONTACT_WA', '+62 811-2345-6789'),
        'hours'    => 'Senin - Jumat, 08.00 - 17.00 WIB',
        'address'  => 'Gedung Pusat Riset Teknologi & Pembelajaran Tinggi, Lantai 4, Jakarta Pusat, Indonesia',
        'instagram'=> 'https://instagram.com',
        'linkedin' => 'https://linkedin.com',
    ],

    // Statistik di bawah hero (cek ulang angka & sumbernya sebelum dipublikasikan)
    'stats' => [
        ['9.967.487', 'Mahasiswa'],
        ['4.416', 'Perguruan Tinggi'],
        ['33.741', 'Program Studi'],
        ['125', 'PTN Target Awal'],
    ],
    'stats_source' => 'Sumber: PDDikti, Kemdiktisaintek 2026',
];
