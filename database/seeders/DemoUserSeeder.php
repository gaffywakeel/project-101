<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoUserSeeder extends Seeder
{
    /**
     * Attributes of demo User
     */
    protected array $attributes = [
        'name' => 'demo',
        'phone' => '+18088635342',
        'email' => 'demo@cryptitan.live',
        'password' => 'cryptitan',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (User::demo()->doesntExist()) {
            /** Remove conflicting users */
            User::whereNot('name', $this->attributes['name'])->where(function ($query) {
                $query->where('email', $this->attributes['email'])
                    ->orWhere('phone', $this->attributes['phone']);
            })->delete();

            $user = User::updateOrCreate([
                'name' => $this->attributes['name'],
            ], [
                'email' => $this->attributes['email'],
                'phone' => $this->attributes['phone'],
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
                'password' => bcrypt($this->attributes['password']),
            ]);

            $user->assignRole(Role::demo());
        }
    }
}
