<?php

namespace Database\Seeders;

use App\Models\Grid;
use Illuminate\Database\Seeder;

class GridSeeder extends Seeder
{
    /**
     * index.home Widgets
     */
    protected array $index = [
        'price_chart',
        'payment_account_chart',
        'wallet_account_chart',
        'recent_activity',
        'feature_limits',
        'active_peer_trade_sell',
        'active_peer_trade_buy',
    ];

    /**
     * admin.home widgets
     */
    protected array $admin = [
        'pending_verification',
        'pending_deposits',
        'pending_withdrawals',
        'earning_summary',
        'system_status',
        'registration_chart',
        'latest_users',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->cleanUp();
        $this->seedIndexHomePage();
        $this->seedAdminHomePage();
    }

    /**
     * Send Index Home Page
     */
    protected function seedIndexHomePage(): void
    {
        foreach ($this->index as $order => $name) {
            Grid::updateOrCreate([
                'page' => 'index.home',
                'name' => $name,
            ], [
                'order' => $order,
            ]);
        }
    }

    /**
     * Send Admin Home Page
     */
    protected function seedAdminHomePage(): void
    {
        foreach ($this->admin as $order => $name) {
            Grid::updateOrCreate([
                'page' => 'admin.home',
                'name' => $name,
            ], [
                'order' => $order,
            ]);
        }
    }

    /**
     * Remove deprecated grids
     */
    protected function cleanUp(): void
    {
        $grids = array_merge($this->index, $this->admin);

        Grid::whereNotIn('name', $grids)->delete();
    }
}
