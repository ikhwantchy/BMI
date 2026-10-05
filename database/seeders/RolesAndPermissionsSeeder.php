<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        $permissions = [
            'members.view', 'members.create', 'members.update', 'members.delete',
            'businesses.view', 'businesses.create', 'businesses.update', 'businesses.delete',
            'visits.view', 'visits.create', 'visits.update', 'visits.delete',
            'evaluations.view', 'evaluations.create', 'evaluations.update', 'evaluations.submit', 'evaluations.approve', 'evaluations.reject', 'evaluations.revise',
            'coaching.view', 'coaching.create', 'coaching.update',
            'reports.view', 'reports.export',
            'users.view', 'users.create', 'users.update', 'users.delete',
            'roles.manage',
            'branches.manage',
            'audit.view', 'audit.export',
            'backup.view', 'backup.run',
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // create roles and assign existing permissions
        $roleOfficer = Role::updateOrCreate(['name' => 'petugas_lapangan', 'guard_name' => 'web']);
        $roleOfficer->syncPermissions([
            'members.view', 'members.create', 'members.update',
            'businesses.view', 'businesses.create', 'businesses.update',
            'visits.view', 'visits.create', 'visits.update',
            'evaluations.view', 'evaluations.create', 'evaluations.update', 'evaluations.submit',
            'coaching.view', 'coaching.create', 'coaching.update',
        ]);

        $roleAssistantManager = Role::updateOrCreate(['name' => 'asisten_manajer', 'guard_name' => 'web']);
        $roleAssistantManager->syncPermissions([
            'members.view', 'businesses.view', 'visits.view', 'evaluations.view',
            'evaluations.approve', 'evaluations.reject', 'evaluations.revise',
            'coaching.view', 'reports.view', 'reports.export'
        ]);

        $roleManager = Role::updateOrCreate(['name' => 'manajer', 'guard_name' => 'web']);
        $roleManager->syncPermissions([
            'members.view', 'businesses.view', 'visits.view', 'evaluations.view',
            'evaluations.approve', 'evaluations.reject', 'evaluations.revise',
            'coaching.view', 'reports.view', 'reports.export',
            'audit.view', 'audit.export',
        ]);

        $rolePengurus = Role::updateOrCreate(['name' => 'pengurus', 'guard_name' => 'web']);
        $rolePengurus->syncPermissions([
            'members.view', 'businesses.view', 'visits.view', 'evaluations.view',
            'coaching.view', 'reports.view', 'reports.export',
            'audit.view', 'audit.export',
        ]);

        $rolePengawas = Role::updateOrCreate(['name' => 'pengawas', 'guard_name' => 'web']);
        $rolePengawas->syncPermissions([
            'members.view', 'businesses.view', 'visits.view', 'evaluations.view',
            'coaching.view', 'reports.view', 'reports.export',
            'audit.view', 'audit.export',
        ]);

        $roleAdmin = Role::updateOrCreate(['name' => 'system_admin', 'guard_name' => 'web']);
        $roleAdmin->syncPermissions(Permission::all());
    }
}
