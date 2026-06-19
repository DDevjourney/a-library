<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Genre;
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
        // Create test user
        $testUser = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Create genres
        $genres = Genre::factory(10)->create();

        // Create books for test user
        Book::factory(15)->for($testUser)->create()->each(function ($book) use ($genres) {
            $book->genres()->attach($genres->random(rand(1, 3)));
        });

        // Create additional users with books
        User::factory(3)->create()->each(function ($user) use ($genres) {
            Book::factory(10)->for($user)->create()->each(function ($book) use ($genres) {
                $book->genres()->attach($genres->random(rand(1, 3)));
            });
        });
    }
}
