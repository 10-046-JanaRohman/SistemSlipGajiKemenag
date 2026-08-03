<?php

use App\Models\Pegawai;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('pegawai named routes are not captured as pegawai model ids', function () {
    $user = User::factory()->create(['role' => 'pegawai']);
    Pegawai::create([
        'user_id' => $user->id,
        'nip' => $user->nip,
        'nama' => $user->name,
    ]);

    Sanctum::actingAs($user);

    $this->getJson('/api/pegawai/dashboard')
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.pegawai.nip', $user->nip);

    $this->getJson('/api/pegawai/riwayat-slip')
        ->assertOk()
        ->assertJsonPath('success', true);

    $this->getJson('/api/pegawai/slip')
        ->assertOk()
        ->assertJsonPath('success', true);
});
