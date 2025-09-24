<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Fina\Database\Seeders\FinaDatabaseSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::factory()->count(1)->create([
            'email' => 'test@example.com',
        ]);

        $this->call(FinaDatabaseSeeder::class);
    }
}
