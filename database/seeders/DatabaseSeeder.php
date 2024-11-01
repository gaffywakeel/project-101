<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(GridSeeder::class);
        $this->call(PermissionSeeder::class);
        $this->call(SupportedCurrencySeeder::class);
        $this->call(FeatureLimitSeeder::class);
        $this->call(OperatingCountrySeeder::class);
        $this->call(ModuleSeeder::class);

        if (config('database.seed_user')) {
            $this->call(UserSeeder::class);
        }

        if (config('app.demo')) {
            $this->call(DemoUserSeeder::class);
        }
    }
}
