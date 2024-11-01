<?php

beforeEach(function () {
    $this->user = $this->actingAsUserWithPermissionTo(['view:wallets']);
});

describe('index', function () {
    it('can retrieve coin adapter resources', function () {
        $response = $this->getJson(route('coin-adapters.index'));

        $response->assertOk()->assertJsonStructure([
            '*' => ['name', 'identifier', 'symbol'],
        ]);
    });

    it('should fail with unauthorized user', function () {
        $this->actingAsNewUser();

        $response = $this->getJson(route('coin-adapters.index'));

        $response->assertForbidden();
    });
});
