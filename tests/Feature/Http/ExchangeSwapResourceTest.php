<?php

beforeEach(function () {
    $this->user = $this->actingAsNewUser();
    // TODO: add ExchangeSwap resources
});

describe('paginate', function () {
    it('can retrieve ExchangeSwap resources', function () {
        $response = $this->getJson(route('exchange-swaps.paginate'));

        $response->assertOk()->assertPaginatedStructure([
            'sell_value',
            'sell_wallet',
            'buy_value',
            'buy_wallet',
        ]);
    });
});

describe('calculate', function () {
    it('can calculate ExchangeSwap values')->todo();
});

describe('store', function () {
    it('can create ExchangeSwap resource')->todo();
});
