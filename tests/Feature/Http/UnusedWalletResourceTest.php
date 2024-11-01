<?php

describe('index', function () {
    beforeEach(function () {
        $this->coin = $this->createCoin('tusdt-erc');
    });

    it('can retrieve unused wallet resources', function () {
        $this->actingAsNewUser();

        $response = $this->getJson(route('unused-wallets.index'));

        $response->assertOk()->assertJsonStructure([
            '*' => ['id', 'min_conf', 'coin'],
        ])->assertJsonCount(1);
    });
});
