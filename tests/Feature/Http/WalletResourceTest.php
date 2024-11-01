<?php

beforeEach(function () {
    $this->user = $this->actingAsUserWithPermissionTo([
        'view:wallets',
        'create:wallets',
        'update:wallets',
        'delete:wallets',
    ]);
});

describe('index', function () {
    beforeEach(function () {
        $this->coin = $this->createCoin('tusdt-erc');
    });

    it('can fetch wallet resources', function () {
        $response = $this->getJson(route('wallets.index'));

        $response->assertOk()->assertPaginatedStructure(['id', 'min_conf', 'coin'], 1);
    });

    it('should fail with unauthorized user', function () {
        $this->actingAsNewUser();

        $response = $this->getJson(route('wallets.index'));

        $response->assertForbidden();
    });
});

describe('create', function () {
    it('can create wallet resource', function () {
        $response = $this->postJson(route('wallets.store'), [
            'identifier' => 'tbtc',
            'min_conf' => 3,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('coin.identifier', 'tbtc');
    });

    it('should fail with unauthorized user', function () {
        $this->actingAsNewUser();

        $response = $this->postJson(route('wallets.store'));

        $response->assertForbidden();
    });
});

describe('destroy', function () {
    beforeEach(function () {
        $this->coin = $this->createCoin('tbtc');
    });

    it('can delete wallet resource', function () {
        $response = $this->deleteJson(route('wallets.destroy', [
            'wallet' => $this->coin->wallet->id,
        ]));

        $response->assertNoContent();
    });

    it('should fail with unauthorized user', function () {
        $this->actingAsNewUser();

        $response = $this->deleteJson(route('wallets.destroy', [
            'wallet' => $this->coin->wallet->id,
        ]));

        $response->assertForbidden();
    });
});

describe('showFeeAddress', function () {
    beforeEach(function () {
        $this->coin = $this->createCoin('tusdt-erc');
    });

    it('can retrieve wallet fee address', function () {
        $response = $this->getJson(route('wallets.show-fee-address', [
            'wallet' => $this->coin->wallet->id,
        ]));

        $response->assertOk()->assertJsonStructure(['address']);
    });

    it('should fail with unauthorized user', function () {
        $this->actingAsNewUser();

        $response = $this->getJson(route('wallets.show-fee-address', [
            'wallet' => $this->coin->wallet->id,
        ]));

        $response->assertForbidden();
    });
});

describe('consolidate', function () {
    beforeEach(function () {
        $this->coin = $this->createCoin('tusdt-erc');
        $this->walletAccount = $this->createWalletAccount($this->coin->wallet, $this->user);
        $this->walletAddress = $this->walletAccount->addresses()->firstOrFail();
    });

    it('can consolidate wallet address', function () {
        $response = $this->postJson(route('wallets.consolidate', [
            'wallet' => $this->coin->wallet->id,
        ]), [
            'address' => $this->walletAddress->address,
        ]);

        $response->assertNoContent();
    });

    it('should fail with unauthorized user', function () {
        $this->actingAsNewUser();

        $response = $this->postJson(route('wallets.consolidate', [
            'wallet' => $this->coin->wallet->id,
        ]), [
            'address' => $this->walletAddress->address,
        ]);

        $response->assertForbidden();
    });
});

describe('relayTransaction', function () {
    beforeEach(function () {
        $this->coin = $this->createCoin('tusdt-erc');
        $this->walletAccount = $this->createWalletAccount($this->coin->wallet, $this->user);
        $this->walletAddress = $this->walletAccount->addresses()->firstOrFail();
    });

    it('can relay wallet transaction')->todo(); // when transaction hash is available

    it('should fail with unauthorized user', function () {
        $this->actingAsNewUser();

        $response = $this->postJson(route('wallets.relay-transaction', [
            'wallet' => $this->coin->wallet->id,
        ]));

        $response->assertForbidden();
    });
});

describe('resetWebhook', function () {
    beforeEach(function () {
        $this->coin = $this->createCoin('tusdt-erc');
    });

    it('can reset webhook', function () {
        $response = $this->postJson(route('wallets.reset-webhook', [
            'wallet' => $this->coin->wallet->id,
        ]));

        $response->assertNoContent();
    });

    it('should fail with unauthorized user', function () {
        $this->actingAsNewUser();

        $response = $this->postJson(route('wallets.reset-webhook', [
            'wallet' => $this->coin->wallet->id,
        ]));

        $response->assertForbidden();
    });
});

describe('showPrice', function () {
    beforeEach(function () {
        $this->coin = $this->createCoin('tusdt-erc');
    });

    it('can retrieve wallet price', function () {
        $this->actingAsNewUser();

        $response = $this->getJson(route('wallets.show-price', [
            'wallet' => $this->coin->wallet->id,
        ]));

        $response->assertOk()->assertJsonStructure([
            'price', 'formatted_price', 'change',
        ]);
    });
});

describe('showMarketChart', function () {
    beforeEach(function () {
        $this->coin = $this->createCoin('tusdt-erc');
    });

    it('can retrieve wallet price', function () {
        $this->actingAsNewUser();

        $response = $this->getJson(route('wallets.show-market-chart', [
            'wallet' => $this->coin->wallet->id,
            'range' => 'hour',
        ]));

        $response->assertOk()->assertJsonStructure();
    });
});
