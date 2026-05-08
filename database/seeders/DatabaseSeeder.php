<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->command->info('1. Generating Shield Permissions...');
        Artisan::call('shield:generate', [
            '--panel' => 'admin',
            '--all' => true,
            '--option' => 'policies_and_permissions',
        ], $this->command->getOutput());

        $this->command->info('2. Creating Admin User...');
        $this->call([
            AdminSeeder::class,
        ]);

        $this->command->info('3. Database seeding completed successfully');
    }
}
