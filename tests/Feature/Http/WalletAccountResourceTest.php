<?php

beforeEach(function () {
    $this->actingAsNewUser();
    $this->coin = $this->createCoin('tusdt-erc');
});

describe('store', function () {
    it('can create WalletAccount resource', function () {
        $response = $this->postJson(route('wallets.accounts.store', [
            'wallet' => $this->coin->wallet->id,
        ]));

        $response->assertOk()->assertJsonStructure([
            'id', 'price', 'user', 'wallet',
        ]);
    });
});
