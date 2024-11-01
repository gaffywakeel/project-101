<?php

namespace Database\Seeders;

use App\Models\FeatureLimit;
use Illuminate\Database\Seeder;

class FeatureLimitSeeder extends Seeder
{
    /**
     * Feature data
     */
    protected array $data = [
        'payments_deposit' => [
            'advanced_limit' => 10000,
            'period' => 'month',
        ],

        'payments_withdrawal' => [
            'advanced_limit' => 10000,
            'period' => 'month',
        ],

        'wallet_exchange' => [
            'basic_limit' => 10000,
            'advanced_limit' => 200000,
            'period' => 'day',
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->cleanUp();

        foreach ($this->data as $name => $limit) {
            FeatureLimit::firstOrCreate(compact('name'), $limit);
        }
    }

    /**
     * Remove deprecated features
     */
    protected function cleanUp(): void
    {
        $features = array_keys($this->data);

        FeatureLimit::whereNotIn('name', $features)->delete();
    }
}
