<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CriteriaSeeder::class,
            AdminAndAlternativesSeeder::class,
        ]);

        $this->command->newLine();
        $this->command->info('═══════════════════════════════════════');
        $this->command->info('  Risk Master — database seed selesai  ');
        $this->command->info('═══════════════════════════════════════');
        $this->command->line('  Login admin: admin@riskmaster.id');
        $this->command->line('  Password   : password');
        $this->command->warn('  Segera ganti password setelah login pertama!');
    }
}
