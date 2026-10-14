<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);

        $admin = User::firstOrCreate(
            ['email' => 'admin@sembada.test'],
            [
                'name' => 'Admin Sembada',
                'password' => Hash::make('password'),
            ]
        );
        $admin->assignRole('admin');

        $panitia = User::firstOrCreate(
            ['email' => 'panitia@sembada.test'],
            [
                'name' => 'Panitia Demo',
                'password' => Hash::make('password'),
            ]
        );
        $panitia->assignRole('panitia');

        $petugas = User::firstOrCreate(
            ['email' => 'petugas@sembada.test'],
            [
                'name' => 'Petugas Demo',
                'password' => Hash::make('password'),
            ]
        );
        $petugas->assignRole('petugas');
    }
}
