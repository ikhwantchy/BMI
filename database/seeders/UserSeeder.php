<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name'     => 'Admin Manajer',
                'username' => 'manajer',
                'email'    => 'admin@bmi.sendr.web.id',
                'password' => Hash::make('@Bukan123'),
                'role'     => 'manajer',
                'status'   => 'active',
            ],
            [
                'name'     => 'Asisten Manajer',
                'username' => 'asisten',
                'email'    => 'asisten@koperasibmi.co.id',
                'password' => Hash::make('@Bukan123'),
                'role'     => 'asisten_manajer',
                'status'   => 'active',
            ],
            [
                'name'     => 'Petugas Lapangan',
                'username' => 'budi',
                'email'    => 'anggota01@bmi.sendr.web.id',
                'password' => Hash::make('@Bukan123'),
                'role'     => 'petugas_lapangan',
                'status'   => 'active',
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['username' => $userData['username']],
                $userData
            );
        }

        $this->command->info('✅ User default berhasil dibuat (password: @Bukan123)');
    }
}
