<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;


class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'name' => 'Pramana Yuda Sayeti, S.Kom',
                'email' => '199407292022031002@kemenag.go.id',
                'password' => Hash::make('superadmin'),
            ],
        ];

        foreach ($data as $key => $item) {
            \App\Models\User::firstOrCreate(
                ['name' => $item['name']],
                $item
            );
        }
    }
}
