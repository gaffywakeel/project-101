<?php

namespace Database\Seeders;

use App\Models\OperatingCountry;
use Illuminate\Database\Seeder;

class OperatingCountrySeeder extends Seeder
{
    /**
     * Set default operating countries
     *
     * @var array|string[]
     */
    protected array $countries = [
        'NG',
        'US',
        'GB',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (OperatingCountry::doesntExist()) {
            foreach ($this->countries as $country) {
                OperatingCountry::create([
                    'name' => config('countries')[$country],
                    'code' => $country,
                ]);
            }
        }
    }
}
