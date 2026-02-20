<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Genre;
use Carbon\Carbon;

class GenreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        
        $genres = [
            [
                'name' => 'Fiction',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Non-Fiction',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Science Fiction',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Fantasy',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Mystery',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Thriller',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Romance',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Horror',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Historical Fiction',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Biography',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Autobiography',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Memoir',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Self-Help',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Science',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Technology',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Business',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Young Adult',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Children\'s',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Poetry',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Drama',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        // Using insert for better performance with timestamps
        Genre::insert($genres);
    }
}