<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            [
                'email' => 'superadmin@kemenag.test',
            ],
            [
                'name' => 'Super Admin',
                'nip' => 'SUPERADMIN001',
                'password' => Hash::make('password123'),
                'role' => 'super_admin',
            ]
        );
    }
}
