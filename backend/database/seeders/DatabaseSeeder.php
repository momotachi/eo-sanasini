<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('👤 Seeding admin users...');
        $this->call(AdminUserSeeder::class);

        $this->command->info('');
        $this->command->info('🏟️ Seeding organization & demo event...');
        $this->call(OrganizationEventSeeder::class);

        $this->command->info('');
        $this->command->info('✅ Seed complete!');
    }
}
