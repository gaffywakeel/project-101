<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Default permission guard
     */
    protected string $guard;

    /**
     * Construct PermissionSeeder
     */
    public function __construct()
    {
        $this->guard = config('permission.defaults.guard');
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedPermissions();
        $this->seedRoles();
    }

    /**
     * Seed Permissions Table
     */
    protected function seedPermissions(): void
    {
        $permissions = config('permission.defaults.permissions');

        foreach ($permissions as $order => $name) {
            Permission::updateOrCreate([
                'name' => $name,
            ], [
                'guard_name' => $this->guard,
                'order' => $order,
            ]);
        }

        if (!empty($permissions)) { /** Remove unused permissions */
            Permission::whereNotIn('name', $permissions)->delete();
        }
    }

    /**
     * Seed roles table
     */
    protected function seedRoles(): void
    {
        $roles = config('permission.defaults.roles');
        $administrator = $roles['administrator'];

        /** Rename 'Super Admin' to 'Administrator' */
        if ($administratorRole = Role::whereName('Super Admin')->first()) {
            Role::where('name', $administrator['name'])->delete();
            $administratorRole->update(['name' => $administrator['name']]);
        }

        foreach ($roles as $key => $data) {
            if ($key !== 'demo' || config('app.demo')) {
                $role = Role::updateOrCreate([
                    'name' => $data['name'],
                ], [
                    'guard_name' => $this->guard,
                    'order' => $data['order'],
                ]);

                if ($role->wasRecentlyCreated && !empty($data['permissions'])) {
                    $role->syncPermissions($data['permissions']);
                }
            }
        }

        $records = Role::query()
            ->whereNot('name', $administrator['name'])
            ->orderBy('order')->get();

        $order = $administrator['order'];

        $records->each(function (Role $role) use (&$order) {
            $role->update(['order' => ++$order]);
        });
    }
}
