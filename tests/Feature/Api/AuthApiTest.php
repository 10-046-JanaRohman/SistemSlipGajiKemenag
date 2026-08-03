<?php

use App\Models\User;

test('user can login to api using nip', function () {
    $user = User::factory()->create(['role' => 'admin']);

    $response = $this->postJson('/api/login', [
        'nip' => $user->nip,
        'password' => 'password',
    ]);

    $response
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('user.id', $user->id)
        ->assertJsonPath('user.role', 'admin')
        ->assertJsonStructure(['token']);

    expect($user->tokens()->count())->toBe(1);
});

test('api login rejects invalid credentials', function () {
    $user = User::factory()->create();

    $this->postJson('/api/login', [
        'nip' => $user->nip,
        'password' => 'salah',
    ])->assertUnauthorized()->assertJsonPath('success', false);
});

test('protected api rejects requests without token', function () {
    $this->getJson('/api/admin/dashboard')->assertUnauthorized();
});
