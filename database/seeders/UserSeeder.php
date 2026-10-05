<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $branch = \App\Models\Branch::updateOrCreate(
            ['code' => 'B01'],
            ['name' => 'Cabang Pusat', 'address' => 'Kantor Pusat Kopsyah BMI']
        );

        $users = [
            [
                'name'      => 'Admin Manajer',
                'username'  => 'manajer',
                'email'     => 'admin@bmi.sendr.web.id',
                'password'  => Hash::make('@Bukan123'),
                'role'      => 'manajer',
                'status'    => 'active',
                'branch_id' => $branch->id,
            ],
            [
                'name'      => 'Asisten Manajer',
                'username'  => 'asisten',
                'email'     => 'asisten@koperasibmi.co.id',
                'password'  => Hash::make('@Bukan123'),
                'role'      => 'asisten_manajer',
                'status'    => 'active',
                'branch_id' => $branch->id,
            ],
            [
                'name'      => 'Petugas Lapangan',
                'username'  => 'budi',
                'email'     => 'anggota01@bmi.sendr.web.id',
                'password'  => Hash::make('@Bukan123'),
                'role'      => 'petugas_lapangan',
                'status'    => 'active',
                'branch_id' => $branch->id,
            ],
        ];

        foreach ($users as $userData) {
            $roleName = $userData['role'];
            $user = User::updateOrCreate(
                ['username' => $userData['username']],
                $userData
            );
            
            // Assign Spatie role
            if (!$user->hasRole($roleName)) {
                $user->assignRole($roleName);
            }
        }

        $this->command->info('✅ User default berhasil dibuat (password: @Bukan123)');
    }
}
