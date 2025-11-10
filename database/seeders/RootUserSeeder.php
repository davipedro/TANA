<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RootUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Root Admin',
            'email' => 'root@tana.com',
            'password' => Hash::make('password'),
            'role' => 'root',
            'email_verified_at' => now(),
        ]);

        $this->command->info('✅ Root user created: root@tana.com / password');
    }
}
