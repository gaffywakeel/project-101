<?php

namespace Tests;

use App\CoinAdapters\Contracts\Adapter;
use Illuminate\Support\Arr;
use Tests\Fakes\CoinAdapterFake;
use Tests\Fakes\TokenAdapterFake;

trait MockCoinAdapters
{
    /**
     * Coin adapters to mock
     */
    protected array $fakeCoins = [
        [
            'name' => 'Bitcoin',
            'identifier' => 'tbtc',
            'symbol' => 'BTC',
            'baseUnit' => '100000000',
            'precision' => 8,
            'currencyPrecision' => 2,
        ],
        [
            'name' => 'Ethereum',
            'identifier' => 'teth',
            'symbol' => 'ETH',
            'baseUnit' => '1000000000000000000',
            'precision' => 8,
            'currencyPrecision' => 2,
        ],
    ];

    /**
     * Token adapters to mock
     */
    protected array $fakeTokens = [
        [
            'name' => 'Tether',
            'identifier' => 'tusdt-erc',
            'symbol' => 'USDT',
            'baseUnit' => '1000000',
            'nativeAssetId' => 'eth',
            'precision' => 6,
            'currencyPrecision' => 4,
        ],
        [
            'name' => 'Binance USD',
            'identifier' => 'tbusd-bep',
            'symbol' => 'BUSD',
            'baseUnit' => '1000000000000000000',
            'nativeAssetId' => 'bnb',
            'precision' => 8,
            'currencyPrecision' => 4,
        ],
    ];

    /**
     * SetUp MockCoinAdapters
     */
    public function setUpMockCoinAdapters(): void
    {
        $manager = $this->app->make('coin.manager');

        collect($this->fakeCoins)->map(function (array $coin) {
            $adapter = new CoinAdapterFake(
                $coin['name'],
                $coin['identifier'],
                $coin['symbol'],
                $coin['baseUnit'],
                $coin['precision'],
                $coin['currencyPrecision'],
            );

            return $this->setCoinAdapterProperties($adapter, $coin);
        })->each(function (Adapter $adapter) use ($manager) {
            $manager->addAdapter($adapter);
        });

        collect($this->fakeTokens)->map(function (array $coin) {
            $adapter = new TokenAdapterFake(
                $coin['name'],
                $coin['identifier'],
                $coin['symbol'],
                $coin['baseUnit'],
                $coin['nativeAssetId'],
                $coin['precision'],
                $coin['currencyPrecision'],
            );

            return $this->setCoinAdapterProperties($adapter, $coin);
        })->each(function (Adapter $adapter) use ($manager) {
            $manager->addAdapter($adapter);
        });
    }

    /**
     * Set coin adapter properties
     */
    protected function setCoinAdapterProperties(CoinAdapterFake $adapter, array $data): CoinAdapterFake
    {
        return tap($adapter, function (CoinAdapterFake $adapter) use ($data) {
            if (Arr::has($data, 'dollarPrice')) {
                $adapter->setDollarPrice($data['dollarPrice']);
            }

            if (Arr::has($data, 'dollarPriceChange')) {
                $adapter->setDollarPriceChange($data['dollarPriceChange']);
            }

            if (Arr::has($data, 'feeRate')) {
                $adapter->setFeeRate($data['feeRate']);
            }

            if (Arr::has($data, 'minimumUnit')) {
                $adapter->setMinimumUnit($data['minimumUnit']);
            }

            if (Arr::has($data, 'maximumUnit')) {
                $adapter->setMaximumUnit($data['maximumUnit']);
            }
        });
    }
}
