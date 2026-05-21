<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call other seeders (order matters for foreign keys)
        $this->call([
            CategorySeeder::class,
            ColumnConfigSeeder::class,
            ProductSeeder::class,
            IngredientSeeder::class,
            ProductionScheduleSeeder::class,
        ]);

        // Your existing user seeders
        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'remember_token' => Str::random(10),
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin@pcclsu.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'is_admin' => true,
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]
        );
    }
}