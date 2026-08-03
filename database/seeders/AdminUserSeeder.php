<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(

            [
                'email' => 'admin@kemenag.test',
            ],

            [
                'name' => 'Admin Keuangan',

                'nip' => 'ADMIN001',

                'password' => Hash::make('password123'),

                'role' => 'admin',
            ]

        );
    }
}