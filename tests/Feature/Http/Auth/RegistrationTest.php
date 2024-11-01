<?php

beforeEach(function () {
    $this->credential = config('auth.credential', 'email');
});

it('can register new user', function () {
    $response = $this->postJson(route('auth.register'), [
        'name' => $name = fake()->userName(),
        'email' => $email = fake()->freeEmail(),
        'password' => $password = fake()->password(8) . '@1',
        'password_confirmation' => $password,
    ]);

    $this->assertAuthenticated();
    $response->assertNoContent();

    Auth::guard('web')->logout();

    $this->assertGuest();

    $this->postJson(route('auth.login'), [
        $this->credential => $this->credential === 'email' ? $email : $name,
        'password' => $password,
    ]);

    $this->assertAuthenticated();
});
