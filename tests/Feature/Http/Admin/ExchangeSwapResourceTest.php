<?php

beforeEach(function () {
    $this->user = $this->actingAsUserWithPermissionTo([
        'access:control_panel',
        'view:exchange_swaps',
    ]);

    // TODO: add ExchangeSwap resources
});

describe('paginate', function () {
    it('can retrieve ExchangeSwap resources', function () {
        $response = $this->getJson(route('admin.exchange-swaps.paginate'));

        $response->assertOk()->assertPaginatedStructure([
            'sell_value',
            'sell_wallet',
            'buy_value',
            'buy_wallet',
        ]);
    });

    it('can filter ExchangeSwap resources by user name', function () {
        $response = $this->getJson(route('admin.exchange-swaps.paginate', [
            'search_user' => fake()->userName, // TODO: use real username
        ]));

        $response->assertOk()->assertPaginatedStructure([
            'sell_value',
            'sell_wallet',
            'buy_value',
            'buy_wallet',
        ]);
    });

    it('should prevent unauthorized access', function () {
        $this->actingAsNewUser();

        $response = $this->getJson(route('admin.exchange-swaps.paginate'));

        $response->assertForbidden();
    });
});

describe('approve', function () {
    it('can approve ExchangeSwap')->todo();
});

describe('reject', function () {
    it('can reject ExchangeSwap')->todo();
});
