<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PortalItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('portal_items')->delete();

        $data = [
            [
                'title' => 'PTSP Kemenag',
                // 'image_url' => "http://res.cloudinary.com/kemenagpessel/image/upload/v1727795062/profile_picture_pegawai/amf3ywqorha981wrabyt.png",
                'image_url' => "http://res.cloudinary.com/kemenagpessel/image/upload/v1727799638/profile_picture_pegawai/vvljcxw7z6mxxlgvnwzl.png",
                'is_modal' => false,
                'destination_url' => 'https://ptsp.kemenagpessel.com'
            ],
            [
                'title' => 'Madrasah',
                'image_url' => 'https://bantul.kemenag.go.id/web2/images/madrasah.png',
                'is_modal' => false,
                'destination_url' => null
            ],
            [
                'title' => 'KUA',
                'image_url' => 'https://bantul.kemenag.go.id/web2/images/icon-paki3.png',
                'is_modal' => false,
                'destination_url' => null
            ],
            [
                'title' => 'Haji Pintar',
                'image_url' => "http://res.cloudinary.com/kemenagpessel/image/upload/v1727795831/profile_picture_pegawai/pqhrwtmzskwzlwptgwpu.png",
                'is_modal' => false,
                'destination_url' => null
            ],
            [
                'title' => 'Satu Data',
                'image_url' => "http://res.cloudinary.com/kemenagpessel/image/upload/v1727800201/profile_picture_pegawai/awvc1vvoohdvohkbgig1.png",
                'is_modal' => false,
                'destination_url' => null
            ],
            [
                'title' => 'Inovasi',
                'image_url' => 'https://bantul.kemenag.go.id/web2/images/simpega.png',
                'is_modal' => false,
                'destination_url' => null
            ],
            [
                'title' => 'Berita',
                'image_url' => "http://res.cloudinary.com/kemenagpessel/image/upload/v1727800512/profile_picture_pegawai/qzrm4pk5wrlagnd2e9sn.png",
                'is_modal' => false,
                'destination_url' => null
            ],
            [
                'title' => 'Media Sosial',
                'image_url' => 'https://bantul.kemenag.go.id/web2/images/medoso.png',
                'is_modal' => false,
                'destination_url' => null
            ],
            [
                'title' => 'Kemenag Dalam Angka',
                'image_url' => null,
                'is_modal' => false,
                'destination_url' => null
            ],
            [
                'title' => 'Data & Informasi',
                'image_url' => 'https://bantul.kemenag.go.id/web2/images/data.png',
                'is_modal' => false,
                'destination_url' => 'https://ppid.kemenagpessel.com'
            ],
            [
                'title' => 'Pengaduan',
                'image_url' => 'https://bantul.kemenag.go.id/web2/images/pengaduan.png',
                'is_modal' => false,
                'destination_url' => null
            ],
            [
                'title' => 'Standar Pelayanan',
                'image_url' => 'https://bantul.kemenag.go.id/web2/images/petition.png',
                'is_modal' => false,
                'destination_url' => null
            ],

        ];

        foreach ($data as $key => $item) {
            \App\Models\PortalItem::firstOrCreate(
                ['title' => $item['title']],
                $item
            );
        }
    }
}
