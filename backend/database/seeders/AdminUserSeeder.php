<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Super Admin (Programmer)',
                'email' => 'superadmin@sanasini.id',
                'password' => 'SuperAdmin2026!',
                'role' => 'SUPER_ADMIN',
            ],
            [
                'name' => 'Admin EO Sanasini',
                'email' => 'admin@sanasini.id',
                'password' => 'AdminSanasini2026!',
                'role' => 'ADMIN',
            ],
        ];

        foreach ($users as $u) {
            User::updateOrCreate(
                ['email' => $u['email']],
                [
                    'name' => $u['name'],
                    'password' => $u['password'], // hashed via cast
                    'role' => $u['role'],
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]
            );
            $this->command->info("  ✓ {$u['role']}: {$u['email']} / {$u['password']}");
        }
    }
}
