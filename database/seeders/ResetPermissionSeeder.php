<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class ResetPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call(PermissionSeeder::class);

        foreach (config('permission.defaults.roles') as $data) {
            $role = Role::whereName($data['name'])->firstOrFail();
            $role?->syncPermissions($data['permissions']);
        }
    }
}
