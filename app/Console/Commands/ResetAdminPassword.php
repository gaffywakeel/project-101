<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Console\Command\Command as CommandAlias;

class ResetAdminPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:reset-admin-password';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset admin password if they get locked out.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->warn('This will reset administrator password and 2FA.');

        if ($this->confirm('Do you still wish to proceed?')) {
            $user = User::administrator()->firstOrFail();

            $password = Str::random(10);

            $user->password = bcrypt($password);
            $user->two_factor_enable = false;

            $user->save();

            $this->info("New Password: $password");
        }

        return CommandAlias::SUCCESS;
    }
}
