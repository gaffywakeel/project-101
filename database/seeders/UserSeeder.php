<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedAdministrator();
        $this->seedOperator();
    }

    /**
     * Seed Administrator user
     */
    protected function seedAdministrator(): void
    {
        if (User::administrator()->doesntExist()) {
            $user = User::firstOrCreate([
                'name' => 'administrator',
            ], [
                'email' => 'administrator@cryptitan.live',
                'password' => bcrypt('neoscrypts'),
            ]);

            $user->assignRole(Role::administrator());
        }
    }

    /**
     * Seed operator user
     */
    protected function seedOperator(): void
    {
        if (User::operator()->doesntExist()) {
            $user = User::firstOrCreate([
                'name' => 'operator',
            ], [
                'email' => 'operator@cryptitan.live',
                'password' => bcrypt('neoscrypts'),
            ]);

            $user->assignRole(Role::operator());
        }
    }
}
