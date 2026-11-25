<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $this->call(PortalItemSeeder::class);
        // Pegawai dulu, agar akun user bisa ditautkan ke data pegawainya.
        $this->call(OrganizationStructureSeeder::class);
        $this->call(UserTableSeeder::class);


    }
}
