<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

use function Laravel\Prompts\password;

#[Signature('app:create-admin-user {--p|password= : The password for the admin user}')]
#[Description('Create the admin user with email admin@d39.org')]
class CreateAdminUser extends Command
{
    public function handle(): int
    {
        $password = $this->option('password') ?? password('Enter a password for the admin user');

        $user = User::updateOrCreate(
            // Ensure this is a valid email and use a password without special characters (shell may interpret them)
            ['email' => 'admin@d39.org'],
            [
                'name' => 'Admin',
                'password' => $password,
            ],
        );

        $this->info("Admin user created: {$user->email}");

        return self::SUCCESS;
    }
}
