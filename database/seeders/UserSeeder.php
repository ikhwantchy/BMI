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
                'username'  => 'admin',
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
                'username'  => 'anggota01',
                'email'     => 'anggota01@bmi.sendr.web.id',
                'password'  => Hash::make('@Bukan123'),
                'role'      => 'petugas_lapangan',
                'status'    => 'active',
                'branch_id' => $branch->id,
            ],
            [
                'name'      => 'Pengurus / Pimpinan',
                'username'  => 'pengurus',
                'email'     => 'pengurus@koperasibmi.co.id',
                'password'  => Hash::make('@Bukan123'),
                'role'      => 'pengurus',
                'status'    => 'active',
                'branch_id' => null, // kantor pusat / semua cabang
            ],
            [
                'name'      => 'Auditor / Pengawas',
                'username'  => 'pengawas',
                'email'     => 'pengawas@koperasibmi.co.id',
                'password'  => Hash::make('@Bukan123'),
                'role'      => 'pengawas',
                'status'    => 'active',
                'branch_id' => null, // kantor pusat / semua cabang
            ],
        ];

        foreach ($users as $userData) {
            $roleName = $userData['role'];
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
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
