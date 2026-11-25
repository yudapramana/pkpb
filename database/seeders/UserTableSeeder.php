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
        // 'nip' dipakai untuk menautkan akun ke data pegawai (employees.nip).
        $data = [
            [
                'name' => 'Pramana Yuda Sayeti, S.Kom',
                'email' => '199407292022031002@kemenag.go.id',
                'password' => Hash::make('superadmin'),
                'nip' => '199407292022031002',
            ],
        ];

        foreach ($data as $key => $item) {
            $nip = $item['nip'] ?? null;
            unset($item['nip']);

            $user = \App\Models\User::firstOrCreate(
                ['name' => $item['name']],
                $item
            );

            if ($nip && !$user->employee_id) {
                $employee = \App\Models\Employee::where('nip', $nip)->first();
                if ($employee) {
                    $user->update(['employee_id' => $employee->id]);
                }
            }
        }
    }
}
