<?php

use App\Models\User;
use Database\Seeders\DatabaseSeeder;

beforeEach(function () {
    $this->credential = config('auth.credential', 'email');
});

it('can authenticate using session', function () {
    $user = User::factory()->create();

    $response = $this->postJson(route('auth.login'), [
        $this->credential => $user->getAttribute($this->credential),
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertNoContent();
});

it('should rate limit many login attempts', function () {
    $maxAttempts = 6;

    $user = User::factory()->create();

    for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
        $response = $this->postJson(route('auth.login'), [
            $this->credential => $user->getAttribute($this->credential),
            'password' => 'wrong-password',
        ]);
    }

    $this->assertGuest();
    $response->assertInvalid([
        'email' => 'many login attempts',
    ]);
});

it('should require two-factor token when enabled', function () {
    $user = User::factory()->create([
        'two_factor_enable' => true,
    ]);

    $response = $this->postJson(route('auth.login'), [
        $this->credential => $user->getAttribute($this->credential),
        'password' => 'password',
    ]);

    $this->assertGuest();
    $response->assertInvalid(['token']);
});

it('should authenticate with two-factor token', function () {
    $user = User::factory()->create([
        'two_factor_enable' => true,
    ]);

    $response = $this->postJson(route('auth.login'), [
        $this->credential => $user->getAttribute($this->credential),
        'password' => 'password',
        'token' => $user->generateTwoFactorToken(),
    ]);

    $this->assertAuthenticated();
    $response->assertNoContent();
});

it('should fail to authenticate when deactivated', function () {
    $user = User::factory()->create([
        'deactivated_until' => now()->addDay(),
    ]);

    $response = $this->postJson(route('auth.login'), [
        $this->credential => $user->getAttribute($this->credential),
        'password' => 'password',
    ]);

    $this->assertGuest();
    $response->assertInvalid([
        $this->credential => 'deactivated until',
    ]);
});

it('can destroy user authentication', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = $this->postJson(route('auth.logout'));

    $this->assertGuest();

    $response->assertNoContent();
});

describe('demoLogin', function () {
    it('can authenticate as demo user', function () {
        config(['app.demo' => true]);

        $this->seed(DatabaseSeeder::class);

        $response = $this->postJson(route('auth.demo-login'));

        $this->assertAuthenticated();
        $response->assertNoContent();
    });

    it('should not allow demo login when unavailable', function () {
        $response = $this->postJson(route('auth.demo-login'));

        $response->assertForbidden();
    });
});
