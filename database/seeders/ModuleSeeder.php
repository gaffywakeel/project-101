<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    /**
     * Available modules
     */
    protected array $data = [
        'stake' => 'Stake',
        'exchange' => 'Exchange',
        'payment' => 'Payment',
        'peer' => 'P2P',
        'commerce' => 'Commerce',
        'wallet' => 'Wallet',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->cleanUp();

        foreach ($this->data as $name => $title) {
            Module::updateOrCreate(compact('name'), compact('title'));
        }
    }

    /**
     * Remove deprecated modules
     */
    protected function cleanUp(): void
    {
        $modules = array_keys($this->data);
        Module::whereNotIn('name', $modules)->delete();
    }
}
