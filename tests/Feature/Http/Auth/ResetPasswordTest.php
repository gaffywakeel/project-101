<?php

use App\Models\User;
use App\Notifications\Auth\EmailToken;

beforeEach(function () {
    $this->credential = config('auth.credential', 'email');
});

it('can reset password with email code', function () {
    Notification::fake();

    $user = User::factory()->create();

    $response = $this->postJson(route('auth.reset-password.send-email-code'), [
        'email' => $user->email,
    ]);

    $testCase = $this;

    Notification::assertSentTo($user, function (EmailToken $notification) use ($user, $testCase) {
        $parameters = $notification->parameters($user);

        expect($parameters)->toHaveKeys([
            'token',
            'expires_at',
            'duration',
        ]);

        $tokenResponse = $testCase->postJson(route('auth.reset-password.request-token'), [
            'email' => $user->email,
            'code' => $parameters['token'],
        ]);

        $tokenResponse->assertOk()
            ->assertJsonStructure(['token']);

        $resetResponse = $testCase->postJson(route('auth.reset-password.reset'), [
            'email' => $user->email,
            'token' => $tokenResponse->json('token'),
            'password' => $password = fake()->password(8) . '@1',
            'password_confirmation' => $password,
        ]);

        $resetResponse->assertOk()->assertJsonStructure();

        $this->postJson(route('auth.login'), [
            $this->credential => $user->getAttribute($this->credential),
            'password' => $password,
        ]);

        $this->assertAuthenticated();

        return true;
    });

    $response->assertNoContent();
});

it('should not send email code with unknown email', function () {
    Notification::fake();

    $response = $this->postJson(route('auth.reset-password.send-email-code'), [
        'email' => fake()->safeEmail(),
    ]);

    Notification::assertNothingSent();

    $response->assertNoContent();
});

it('should fail to request token with wrong code or email', function () {
    $user = User::factory()->create();

    $tokenResponse = $this->postJson(route('auth.reset-password.request-token'), [
        'email' => $user->email,
        'code' => Str::random(6),
    ]);

    $tokenResponse->assertForbidden();

    $tokenResponse = $this->postJson(route('auth.reset-password.request-token'), [
        'email' => fake()->safeEmail(),
        'code' => Str::random(6),
    ]);

    $tokenResponse->assertForbidden();
});

it('should fail to reset with invalid token', function () {
    $user = User::factory()->create();

    $response = $this->postJson(route('auth.reset-password.reset'), [
        'email' => $user->email,
        'token' => Str::random(20),
        'password' => $password = fake()->password(8) . '@1',
        'password_confirmation' => $password,
    ]);

    $response->assertInvalid([
        'email' => 'token is invalid',
    ]);
});
