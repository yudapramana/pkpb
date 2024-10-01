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
                'image_url' => null,
                'is_modal' => false,
                'destination_url' => 'https://ptsp.kemenagpessel.com'
            ],
            [
                'title' => 'Madrasah',
                'image_url' => null,
                'is_modal' => false,
                'destination_url' => null
            ],
            [
                'title' => 'KUA',
                'image_url' => null,
                'is_modal' => false,
                'destination_url' => null
            ],
            [
                'title' => 'Haji Pintar',
                'image_url' => null,
                'is_modal' => false,
                'destination_url' => null
            ],
            [
                'title' => 'Satu Data',
                'image_url' => null,
                'is_modal' => false,
                'destination_url' => null
            ],
            [
                'title' => 'Inovasi',
                'image_url' => null,
                'is_modal' => false,
                'destination_url' => null
            ],
            [
                'title' => 'Berita',
                'image_url' => null,
                'is_modal' => false,
                'destination_url' => null
            ],
            [
                'title' => 'Media Sosial',
                'image_url' => null,
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
                'image_url' => null,
                'is_modal' => false,
                'destination_url' => 'https://ppid.kemenagpessel.com'
            ],
            [
                'title' => 'Pengaduan',
                'image_url' => null,
                'is_modal' => false,
                'destination_url' => null
            ],
            [
                'title' => 'Standar Pelayanan',
                'image_url' => null,
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
