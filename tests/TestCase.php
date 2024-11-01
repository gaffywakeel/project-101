<?php

namespace Tests;

use App\CoinAdapters\Resources\Transaction;
use App\Jobs\ProcessWalletTransaction;
use App\Models\Coin;
use App\Models\Role;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletAccount;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Str;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshValueStore, MockCoinAdapters, TestResponseMacros;

    /**
     * Indicates whether the default seeder should run before each test.
     */
    protected bool $seed = true;

    /**
     * Authenticate a new user
     */
    public function actingAsNewUser(): User
    {
        return tap(User::factory()->create(), function (User $user) {
            $this->actingAs($user);
        });
    }

    /**
     * Authenticate a new user with permissions
     */
    public function actingAsUserWithPermissionTo(array $permissions): User
    {
        $role = $this->createRole(fake()->name);
        $role->givePermissionTo(...$permissions);

        return tap(User::factory()->create(), function (User $user) use ($role) {
            $this->actingAs(tap($user)->assignRole($role));
        });
    }

    /**
     * Create Role
     */
    public function createRole(string $name, string $guardName = null): Role
    {
        return Role::create([
            'name' => $name,
            'guard_name' => $guardName ?: config('permission.defaults.guard'),
        ]);
    }

    /**
     * Create Coin model
     */
    public function createCoin(string $identifier, int $minConf = 6): Coin
    {
        return $this->app->make('coin.manager')->register($identifier, $minConf);
    }

    /**
     * Create WalletAccount model
     */
    public function createWalletAccount(Wallet $wallet, User $user): WalletAccount
    {
        return $wallet->getAccount($user);
    }

    /**
     * Credit WalletAccount
     */
    public function creditWalletAccount(WalletAccount $walletAccount, float $amount): void
    {
        $address = $walletAccount->addresses()->firstOrFail();

        $payload = [
            'id' => (string) Str::uuid(),
            'type' => 'receive',
            'hash' => (string) Str::ulid(),
            'from' => (string) Str::ulid(),
            'to' => $address->address,
            'value' => $amount,
            'confirmations' => $walletAccount->wallet->min_conf,
            'date' => now()->toJSON(),
        ];

        $resource = $walletAccount->wallet->coin->adapter->handleTransactionWebhook($walletAccount->wallet->resource, $payload);

        if ($resource instanceof Transaction) {
            ProcessWalletTransaction::dispatch($resource, $walletAccount->wallet);
        }
    }
}
