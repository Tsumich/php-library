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
         \App\Models\Book::factory(10)->create()->each(
            function($book) {
                $numberReviews = random_int(5, 30);
                \App\Models\Review::factory()->count($numberReviews)
                    ->good()
                    ->for($book)
                    ->create();
            }
         );

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
