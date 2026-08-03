<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoAdminSeeder extends Seeder
{
    /**
     * Akun admin demo untuk pengujian alur import multi-admin.
     */
    public function run(): void
    {
        foreach ([
            // Untuk mengganti username, ubah `nip`. Isi `current_nip` dengan
            // username lama hanya pada saat pertama kali melakukan penggantian.
            ['name' => 'Faiz', 'nip' => 'FAIZ001', 'email' => 'faiz001@kemenag.local'],
            ['name' => 'Rahman', 'nip' => 'RAHMAN001', 'email' => 'rahman001@kemenag.local'],
        ] as $admin) {
            $currentNip = $admin['current_nip'] ?? $admin['nip'];
            $user = User::firstOrNew(['nip' => $currentNip]);

            $user->name = $admin['name'];
            $user->nip = $admin['nip'];
            $user->email = $admin['email'];
            $user->role = 'admin';

            // Password awal hanya dibuat untuk akun baru; seed ulang tidak
            // mengembalikan password yang telah diganti oleh pemilik akun.
            if (! $user->exists) {
                $user->password = Hash::make('password123');
            }

            $user->save();
        }
    }
}
