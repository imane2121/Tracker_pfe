<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            WasteTypesSeeder::class,
            MarineWasteTypesSeeder::class,
            SignalSeeder::class,
            ArticleSeeder::class, // Articles before Collectes as they're independent
            CollecteSeeder::class, // Collectes depend on Signals and Users
        ]);
    }
}
