<?php

namespace Database\Seeders;

use App\Models\PeerPaymentCategory;
use App\Models\PeerPaymentMethod;
use Illuminate\Database\Seeder;

class PeerPaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (app()->environment('local')) {
            PeerPaymentCategory::factory()
                ->has(PeerPaymentMethod::factory()->count(200), 'methods')
                ->count(3)->create();
        }
    }
}
