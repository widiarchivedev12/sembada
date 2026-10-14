<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'manage users',
            'view all events',
            'create events',
            'update events',
            'delete events',
            'view registrations',
            'cancel tickets',
            'export reports',
            'view checkin logs',
            'checkin tickets',
            'view failed jobs',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions($permissions);

        $panitia = Role::firstOrCreate(['name' => 'panitia', 'guard_name' => 'web']);
        $panitia->syncPermissions([
            'create events',
            'update events',
            'delete events',
            'view registrations',
            'cancel tickets',
            'export reports',
            'view checkin logs',
            'checkin tickets',
        ]);

        $petugas = Role::firstOrCreate(['name' => 'petugas', 'guard_name' => 'web']);
        $petugas->syncPermissions(['checkin tickets']);
    }
}
