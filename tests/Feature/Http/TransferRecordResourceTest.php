<?php

beforeEach(function () {
    $this->user = $this->actingAsNewUser();
    $this->coin = $this->createCoin('tusdt-erc');
    $this->walletAccount = $this->createWalletAccount($this->coin->wallet, $this->user);

    $this->creditWalletAccount($this->walletAccount, 10);
});

describe('index', function () {
    it('can retrieve TransferRecord resources', function () {
        $response = $this->getJson(route('transfer-records.index'));

        $response->assertOk()->assertPaginatedStructure(['id', 'hash', 'type', 'address'], 1);
    });
});
