<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateTestUsers extends Command
{
    protected $signature = 'users:create-test';
    protected $description = 'Create test users for the vending machine application';

    public function handle()
    {
        $this->info('Creating test users...');

        // Create admin user
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        $this->info('✓ Admin user created/updated: admin@example.com / password');

        // Create regular user
        $user = User::updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
                'role' => 'user',
                'email_verified_at' => now(),
            ]
        );

        $this->info('✓ Regular user created/updated: user@example.com / password');

        $this->newLine();
        $this->info('Test users are ready!');
        $this->info('You can now login with:');
        $this->info('  Admin: admin@example.com / password');
        $this->info('  User:  user@example.com / password');

        return Command::SUCCESS;
    }
}
