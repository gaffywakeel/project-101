<?php

use App\Models\Role;
use App\Models\User;

beforeEach(function () {
    $this->user = $this->actingAsUserWithPermissionTo([
        'access:control_panel',
        'view:roles',
        'create:roles',
        'update:roles',
        'delete:roles',
    ]);
});

describe('paginate', function () {
    it('can retrieve Role resources', function () {
        $response = $this->getJson(route('admin.roles.paginate'));

        $response->assertOk()->assertPaginatedStructure([
            'name',
            'guard_name',
            'order',
            'permissions',
            'users_count',
        ]);
    });

    it('should prevent unauthorized access', function () {
        $this->actingAsNewUser();

        $response = $this->getJson(route('admin.roles.paginate'));

        $response->assertForbidden();
    });
});

describe('store', function () {
    it('can create Role resource', function () {
        $response = $this->postJson(route('admin.roles.store'), [
            'name' => $name = fake()->userName,
            'permissions' => [
                ['name' => 'update:roles', 'value' => true],
                ['name' => 'create:roles', 'value' => true],
            ],
        ]);

        $this->assertDatabaseHas(Role::class, [
            'name' => $name,
        ]);

        $response->assertCreated()->assertJsonStructure([
            'name',
            'guard_name',
            'order',
        ]);
    });

    it('should prevent unauthorized access', function () {
        $this->actingAsNewUser();

        $response = $this->postJson(route('admin.roles.store'), [
            'name' => fake()->userName,
            'permissions' => [],
        ]);

        $response->assertForbidden();
    });
});

describe('moveUp', function () {
    beforeEach(function () {
        $this->role = $this->createRole(fake()->userName);
    });

    it('can move role up the hierarchy order', function () {
        $previous = $this->role->previous();

        $response = $this->postJson(route('admin.roles.move-up', [
            'role' => $this->role->id,
        ]));

        expect($previous->fresh())->toMatchArray([
            'order' => $this->role->order,
        ]);

        expect($this->role->fresh())->toMatchArray([
            'order' => $previous->order,
        ]);

        $response->assertNoContent();
    });

    it('should prevent moving up the administrator order', function () {
        $response = $this->postJson(route('admin.roles.move-up', [
            'role' => Role::operator()->id,
        ]));

        $response->assertForbidden();
    });

    it('should prevent unauthorized access', function () {
        $this->actingAsNewUser();

        $response = $this->postJson(route('admin.roles.move-up', [
            'role' => $this->role->id,
        ]));

        $response->assertForbidden();
    });
});

describe('moveDown', function () {
    beforeEach(function () {
        $this->role = $this->createRole(fake()->userName);
        $this->createRole(fake()->userName);
    });

    it('can move Role down the hierarchy order', function () {
        $next = $this->role->next();

        $response = $this->postJson(route('admin.roles.move-down', [
            'role' => $this->role->id,
        ]));

        expect($next->fresh())->toMatchArray([
            'order' => $this->role->order,
        ]);

        expect($this->role->fresh())->toMatchArray([
            'order' => $next->order,
        ]);

        $response->assertNoContent();
    });

    it('should prevent moving the last Role down the hierarchy order', function () {
        $role = $this->createRole('Promote');

        $response = $this->postJson(route('admin.roles.move-down', [
            'role' => $role->id,
        ]));

        $response->assertForbidden();
    });

    it('should prevent unauthorized access', function () {
        $this->actingAsNewUser();

        $response = $this->postJson(route('admin.roles.move-down', [
            'role' => $this->role->id,
        ]));

        $response->assertForbidden();
    });
});

describe('update', function () {
    beforeEach(function () {
        $this->role = $this->createRole(fake()->userName);
    });

    it('can update Role resource', function () {
        $response = $this->putJson(route('admin.roles.update', [
            'role' => $this->role->id,
        ]), [
            'name' => $name = fake()->userName,
            'permissions' => [
                ['name' => 'update:roles', 'value' => true],
                ['name' => 'create:roles', 'value' => true],
            ],
        ]);

        expect($this->role->fresh())->toMatchArray([
            'name' => $name,
        ]);

        expect($this->role->hasAllPermissions('update:roles'))->toBeTrue();
        expect($this->role->hasAllPermissions('create:roles'))->toBeTrue();

        $response->assertOk()->assertJsonStructure([
            'name',
            'guard_name',
            'order',
        ]);
    });

    it('cannot update protected Role name', function () {
        $response = $this->putJson(route('admin.roles.update', [
            'role' => Role::operator()->id,
        ]), [
            'name' => fake()->userName,
            'permissions' => [
                ['name' => 'update:roles', 'value' => true],
                ['name' => 'create:roles', 'value' => true],
            ],
        ]);

        expect(Role::operator()->fresh())->toMatchArray([
            'name' => Role::operator()->name,
        ]);

        $response->assertOk();
    });

    it('should prevent unauthorized access', function () {
        $this->actingAsNewUser();

        $response = $this->putJson(route('admin.roles.update', [
            'role' => $this->role->id,
        ]), [
            'name' => fake()->userName,
        ]);

        $response->assertForbidden();
    });
});

describe('assign', function () {
    beforeEach(function () {
        $this->actingAsUserWithPermissionTo([
            'access:control_panel',
            'assign:roles',
            'manage:users',
        ]);
    });

    it('can assign Role to user', function () {
        $user = User::factory()->create();

        $response = $this->postJson(route('admin.role.assign'), [
            'user' => $user->id,
            'roles' => [$role = Role::operator()->name],
        ]);

        expect($user->hasAllRoles($role))->toBeTrue();

        $response->assertNoContent();
    });

    it('should prevent unauthorized access user management', function () {
        $user = User::factory()->create();

        $user->assignRole(Role::administrator());

        $response = $this->postJson(route('admin.role.assign'), [
            'user' => $user->id,
            'roles' => [Role::operator()->name],
        ]);

        $response->assertForbidden();
    });

    it('should prevent unauthorized access', function () {
        $user = User::factory()->create();

        $this->actingAsNewUser();

        $response = $this->postJson(route('admin.role.assign'), [
            'user' => $user->id,
            'roles' => [Role::administrator()->name],
        ]);

        $response->assertForbidden();
    });
});

describe('destroy', function () {
    beforeEach(function () {
        $this->role = $this->createRole(fake()->userName);
    });

    it('can delete Role resource', function () {
        $response = $this->deleteJson(route('admin.roles.destroy', [
            'role' => $this->role->id,
        ]));

        $response->assertNoContent();
    });

    it('cannot delete Protected role', function () {
        $response = $this->deleteJson(route('admin.roles.destroy', [
            'role' => Role::operator()->id,
        ]));

        $response->assertForbidden();
    });

    it('should prevent unauthorized access', function () {
        $this->actingAsNewUser();

        $response = $this->deleteJson(route('admin.roles.destroy', [
            'role' => $this->role->id,
        ]));

        $response->assertForbidden();
    });
});
