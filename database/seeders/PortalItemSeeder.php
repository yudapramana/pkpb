<?php

namespace Database\Seeders;

use App\Models\PortalItem;
use Illuminate\Database\Seeder;

/**
 * Item portal halaman depan.
 *
 * - Disimpan dengan updateOrCreate berdasarkan judul, sehingga item yang
 *   ditambahkan lewat panel admin tidak ikut terhapus saat seeder dijalankan ulang.
 * - Ikon "/images/portal/*.svg" adalah ikon buatan sendiri (glyph Bootstrap Icons, MIT)
 *   yang disimpan di folder public agar tidak bergantung pada situs luar.
 * - Item lama yang gambarnya sudah tidak ada (404) atau masih memakai ikon bawaan
 *   dibuat nonaktif; bisa diaktifkan lagi dari admin setelah diberi gambar.
 */
class PortalItemSeeder extends Seeder
{
    public function run()
    {
        foreach ($this->items() as $item) {
            PortalItem::updateOrCreate(
                ['title' => $item['title']],
                $item + ['is_modal' => false, 'is_active' => true]
            );
        }
    }

    private function items()
    {
        $cloudinary = 'https://res.cloudinary.com/kemenagpessel/image/upload/';

        return [
            // ---------- Aplikasi Kankemenag Pesisir Selatan ----------
            [
                'title' => 'PTSP Kemenag',
                'image_url' => $cloudinary . 'v1727799638/profile_picture_pegawai/vvljcxw7z6mxxlgvnwzl.png',
                'destination_url' => 'https://ptsp.kemenagpessel.com',
                'sort_order' => 10,
            ],
            [
                'title' => 'PPID',
                'image_url' => '/images/portal/ppid.svg',
                'destination_url' => 'https://ppid.kemenagpessel.com',
                'sort_order' => 20,
            ],
            [
                'title' => 'Berita',
                'image_url' => $cloudinary . 'v1727800512/profile_picture_pegawai/qzrm4pk5wrlagnd2e9sn.png',
                'destination_url' => 'https://berita.kemenagpessel.com',
                'sort_order' => 30,
            ],
            [
                'title' => 'SIAPDI (Agenda Pimpinan)',
                'image_url' => '/images/portal/siapdi.svg',
                'destination_url' => 'https://agenda.kemenagpessel.com',
                'sort_order' => 40,
            ],
            [
                'title' => 'SIGAP',
                'image_url' => '/images/portal/sigap.svg',
                'destination_url' => 'https://sigap.kemenagpessel.com',
                'sort_order' => 50,
            ],
            [
                'title' => 'SIGARDA (Arsip Kepegawaian)',
                'image_url' => '/images/portal/sigarda.svg',
                'destination_url' => 'https://sigarda.kemenagpessel.com',
                'sort_order' => 60,
            ],
            [
                'title' => 'Laporan Kinerja',
                'image_url' => '/images/portal/lapkin.svg',
                'destination_url' => 'https://lapkin.kemenagpessel.com',
                'sort_order' => 70,
            ],
            [
                'title' => 'SIKM (Kepuasan Masyarakat)',
                'image_url' => '/images/portal/sikm.svg',
                'destination_url' => 'https://sikm.kemenagpessel.com',
                'sort_order' => 80,
            ],
            [
                'title' => 'SIMBUTU (Buku Tamu)',
                'image_url' => '/images/portal/simbutu.svg',
                'destination_url' => 'https://simbutu.kemenagpessel.com',
                'sort_order' => 90,
            ],
            [
                'title' => 'FAQ Srikandi',
                'image_url' => '/images/portal/faq-srikandi.svg',
                'destination_url' => 'https://faq-srikandi.kemenagpessel.com',
                'sort_order' => 100,
            ],
            [
                'title' => 'MTQN Pesisir Selatan',
                'image_url' => '/images/portal/mtqn.svg',
                'destination_url' => 'https://mtqnpesisirselatan.com',
                'sort_order' => 110,
            ],

            // ---------- Aplikasi Kementerian Agama RI (umum) ----------
            [
                'title' => 'Kemenag RI',
                'image_url' => '/arsha/assets/img/logokmg2.png',
                'destination_url' => 'https://kemenag.go.id',
                'sort_order' => 200,
            ],
            [
                'title' => 'Kanwil Kemenag Sumbar',
                'image_url' => '/images/portal/kanwil.svg',
                'destination_url' => 'https://sumbar.kemenag.go.id',
                'sort_order' => 202,
            ],
            [
                'title' => 'Layanan Publik Kemenag',
                'image_url' => '/images/portal/layanan.svg',
                'destination_url' => 'https://kemenag.go.id/layanan',
                'sort_order' => 204,
            ],
            [
                'title' => 'SSO Kemenag',
                'image_url' => '/images/portal/sso.svg',
                'destination_url' => 'https://sso.kemenag.go.id',
                'sort_order' => 206,
            ],
            [
                'title' => 'Pusaka SuperApps',
                'image_url' => '/images/portal/pusaka.svg',
                'destination_url' => 'https://pusaka-v3.kemenag.go.id',
                'sort_order' => 210,
            ],
            [
                'title' => 'Presensi Online',
                'image_url' => '/images/portal/presensi.svg',
                'destination_url' => 'https://absensi.kemenag.go.id',
                'sort_order' => 212,
            ],
            [
                'title' => 'Satu Data',
                'image_url' => $cloudinary . 'v1727800201/profile_picture_pegawai/awvc1vvoohdvohkbgig1.png',
                'destination_url' => 'https://satudata.kemenag.go.id',
                'sort_order' => 220,
            ],
            [
                'title' => 'PINTAR (Pelatihan)',
                'image_url' => '/images/portal/pintar.svg',
                'destination_url' => 'https://pintar.kemenag.go.id',
                'sort_order' => 230,
            ],
            [
                'title' => 'SIMDIKLAT',
                'image_url' => '/images/portal/simdiklat.svg',
                'destination_url' => 'https://simdiklat.kemenag.go.id',
                'sort_order' => 232,
            ],
            [
                'title' => "Qur'an Kemenag",
                'image_url' => '/images/portal/quran.svg',
                'destination_url' => 'https://quran.kemenag.go.id',
                'sort_order' => 234,
            ],
            [
                'title' => 'JDIH Kemenag',
                'image_url' => '/images/portal/jdih.svg',
                'destination_url' => 'https://jdih.kemenag.go.id',
                'sort_order' => 236,
            ],
            [
                'title' => 'SIAPI (Pengawasan Internal)',
                'image_url' => '/images/portal/siapi.svg',
                'destination_url' => 'https://siapi.kemenag.go.id',
                'sort_order' => 238,
            ],
            [
                'title' => 'LAPOR! (Pengaduan)',
                'image_url' => '/images/portal/lapor.svg',
                'destination_url' => 'https://lapor.go.id',
                'sort_order' => 240,
            ],

            // Sub Bagian Tata Usaha (kepegawaian & persuratan)
            [
                'title' => 'SIMPEG 5',
                'image_url' => '/images/portal/simpeg.svg',
                'destination_url' => 'https://simpeg5.kemenag.go.id',
                'sort_order' => 300,
            ],
            [
                'title' => 'MyASN (BKN)',
                'image_url' => '/images/portal/myasn.svg',
                'destination_url' => 'https://myasn.bkn.go.id',
                'sort_order' => 302,
            ],
            [
                'title' => 'e-Kinerja (BKN)',
                'image_url' => '/images/portal/ekinerja.svg',
                'destination_url' => 'https://kinerja.bkn.go.id',
                'sort_order' => 304,
            ],
            [
                'title' => 'SRIKANDI',
                'image_url' => '/images/portal/srikandi.svg',
                'destination_url' => 'https://srikandi.arsip.go.id',
                'sort_order' => 310,
            ],

            // Seksi Pendidikan Madrasah / PD Pontren / PAIS
            [
                'title' => 'EMIS',
                'image_url' => '/images/portal/emis.svg',
                'destination_url' => 'https://emis.kemenag.go.id',
                'sort_order' => 400,
            ],
            [
                'title' => 'SIMPATIKA',
                'image_url' => '/images/portal/simpatika.svg',
                'destination_url' => 'https://simpatika.kemenag.go.id',
                'sort_order' => 410,
            ],
            [
                'title' => 'PIP Madrasah',
                'image_url' => '/images/portal/pip.svg',
                'destination_url' => 'https://pip.kemenag.go.id',
                'sort_order' => 415,
            ],
            [
                'title' => 'RDM (Rapor Digital Madrasah)',
                'image_url' => '/images/portal/rdm.svg',
                'destination_url' => 'https://rdm.kemenag.go.id',
                'sort_order' => 420,
            ],
            [
                'title' => 'SIKAP (Pesantren)',
                'image_url' => '/images/portal/sikap.svg',
                'destination_url' => 'https://sikap.kemenag.go.id',
                'sort_order' => 430,
            ],
            [
                'title' => 'SIAGA Pendis',
                'image_url' => '/images/portal/siaga.svg',
                'destination_url' => 'https://siagapendis.kemenag.go.id',
                'sort_order' => 440,
            ],

            // Seksi Bimas Islam / Penyelenggara Zakat dan Wakaf / Haji
            [
                'title' => 'SIMAS (Masjid)',
                'image_url' => '/images/portal/simas.svg',
                'destination_url' => 'https://simas.kemenag.go.id',
                'sort_order' => 500,
            ],
            [
                'title' => 'SIMKAH (Nikah)',
                'image_url' => '/images/portal/simkah.svg',
                'destination_url' => 'https://simkah4.kemenag.go.id',
                'sort_order' => 510,
            ],
            [
                'title' => 'SIMZAT (Zakat)',
                'image_url' => '/images/portal/simzat.svg',
                'destination_url' => 'https://simzat.kemenag.go.id',
                'sort_order' => 520,
            ],
            [
                'title' => 'SIWAK (Wakaf)',
                'image_url' => '/images/portal/siwak.svg',
                'destination_url' => 'https://siwak.kemenag.go.id',
                'sort_order' => 530,
            ],
            [
                'title' => 'Haji Pintar',
                'image_url' => $cloudinary . 'v1727795831/profile_picture_pegawai/pqhrwtmzskwzlwptgwpu.png',
                'destination_url' => null,
                'sort_order' => 540,
            ],

            // ---------- Nonaktif: gambar sudah tidak ada / masih ikon bawaan ----------
            ['title' => 'Madrasah', 'image_url' => 'https://bantul.kemenag.go.id/web2/images/madrasah.png', 'destination_url' => null, 'sort_order' => 900, 'is_active' => false],
            ['title' => 'KUA', 'image_url' => 'https://bantul.kemenag.go.id/web2/images/icon-paki3.png', 'destination_url' => null, 'sort_order' => 910, 'is_active' => false],
            ['title' => 'Inovasi', 'image_url' => 'https://bantul.kemenag.go.id/web2/images/simpega.png', 'destination_url' => null, 'sort_order' => 920, 'is_active' => false],
            ['title' => 'Media Sosial', 'image_url' => 'https://bantul.kemenag.go.id/web2/images/medoso.png', 'destination_url' => null, 'sort_order' => 930, 'is_active' => false],
            ['title' => 'Kemenag Dalam Angka', 'image_url' => null, 'destination_url' => null, 'sort_order' => 940, 'is_active' => false],
            ['title' => 'Data & Informasi', 'image_url' => 'https://bantul.kemenag.go.id/web2/images/data.png', 'destination_url' => 'https://ppid.kemenagpessel.com', 'sort_order' => 950, 'is_active' => false],
            ['title' => 'Pengaduan', 'image_url' => 'https://bantul.kemenag.go.id/web2/images/pengaduan.png', 'destination_url' => null, 'sort_order' => 960, 'is_active' => false],
            ['title' => 'Standar Pelayanan', 'image_url' => 'https://bantul.kemenag.go.id/web2/images/petition.png', 'destination_url' => null, 'sort_order' => 970, 'is_active' => false],
        ];
    }
}
